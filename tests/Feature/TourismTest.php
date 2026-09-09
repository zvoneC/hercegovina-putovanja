<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Offer;
use App\Models\Review;
use App\Models\TripItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TourismTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $role = 'korisnik'): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    private function offerData(): array
    {
        return ['title' => 'Probni izlet u Mostar', 'city_id' => City::first()->id, 'category_id' => Category::first()->id, 'description' => '<p>Obilazak starog grada uz lokalne priče.</p>', 'price' => 35, 'duration' => 3, 'active' => 1];
    }

    public function test_public_pages_and_pagination_render(): void
    {
        foreach (['/', '/?page=2', '/gradovi', '/prijava', '/registracija', '/o-projektu', '/ponude/12'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/ponude/99999')->assertNotFound();
        $this->get('/nepostojeca-stranica')->assertNotFound()->assertSee('Ova stranica nije pronađena.');
    }

    public function test_four_filters_are_combined_and_ajax_returns_only_matches(): void
    {
        $offer = Offer::where('title', 'Mostar s druge strane mosta')->first();
        $query = http_build_query(['q' => 'Mostar', 'city' => $offer->city_id, 'category' => $offer->category_id, 'price' => 26, 'duration' => 3]);
        $result = $this->getJson('/?'.$query)->assertOk()->assertJsonPath('count', 1);
        $this->assertStringContainsString($offer->title, $result->json('html'));
        $this->getJson('/?'.$query.'&price=0')->assertOk()->assertJsonPath('count', 0);
        $this->getJson('/?price=-1')->assertUnprocessable();
        $this->getJson('/?q=%27%20OR%201%3D1--')->assertOk()->assertJsonPath('count', 0);
    }

    public function test_registration_cannot_choose_privileged_role(): void
    {
        $this->post('/registracija', ['name' => 'Test Putnik', 'username' => 'testputnik', 'email' => 'test@example.test', 'password' => 'Sigurna123!', 'password_confirmation' => 'Sigurna123!', 'role' => 'superadmin', 'active' => false])
            ->assertRedirect(route('catalog'));
        $user = User::where('email', 'test@example.test')->firstOrFail();
        $this->assertSame('korisnik', $user->role);
        $this->assertTrue(Hash::check('Sigurna123!', $user->password));
        $this->assertTrue($user->hasPermission('plan_trip'));
        $this->assertFalse($user->hasPermission('manage_catalog'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_logout_and_inactive_accounts(): void
    {
        $user = $this->user();
        $this->post('/prijava', ['email' => $user->email, 'password' => 'pogresna'])->assertSessionHasErrors('email');
        $this->post('/prijava', ['email' => $user->email, 'password' => 'Hercegovina2026!'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->post('/odjava')->assertRedirect();
        $this->assertGuest();
        $user->update(['active' => false]);
        $this->post('/prijava', ['email' => $user->email, 'password' => 'Hercegovina2026!'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guest_and_user_cannot_access_admin_even_by_direct_request(): void
    {
        $this->get('/admin')->assertForbidden();
        $this->get('/moj-plan')->assertRedirect(route('login'));
        $this->actingAs($this->user())->get('/admin')->assertForbidden();
        $this->post('/admin/ponude', $this->offerData())->assertForbidden();
        $this->post('/admin/korisnici', [])->assertForbidden();
        $this->assertDatabaseCount('offers', 12);
    }

    public function test_admin_can_edit_catalog_but_not_users(): void
    {
        $this->actingAs($this->user('admin'));
        foreach (['/admin', '/admin/ponude/nova', '/admin/ponude/1/uredi', '/admin/sifrarnici', '/admin/sifrarnici?city=1', '/admin/sifrarnici?category=1'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/admin/korisnici')->assertForbidden();
        $this->put('/admin/korisnici/1', ['role' => 'admin'])->assertForbidden();
    }

    public function test_offer_crud_sanitizes_editor_and_archives(): void
    {
        $this->actingAs($this->user('admin'));
        $data = $this->offerData();
        $data['description'] = '<p onclick="alert(1)">Siguran opis ponude za testiranje.<script>alert(1)</script><img src=x onerror=alert(1)><strong>Važno</strong></p>';
        $this->post('/admin/ponude', $data)->assertRedirect(route('admin.index'));
        $offer = Offer::where('title', $data['title'])->firstOrFail();
        $this->assertStringNotContainsString('alert', $offer->description);
        $this->assertStringNotContainsString('<img', $offer->description);
        $this->assertStringContainsString('<strong>Važno</strong>', $offer->description);
        $this->put('/admin/ponude/'.$offer->id, array_replace($this->offerData(), ['price' => 45]))->assertRedirect();
        $this->assertEquals(45, $offer->fresh()->price);
        $this->delete('/admin/ponude/'.$offer->id)->assertRedirect();
        $this->assertFalse($offer->fresh()->active);
        $this->actingAs($this->user())->get('/ponude/'.$offer->id)->assertNotFound();
        $this->post('/moj-plan/'.$offer->id)->assertNotFound();
    }

    public function test_upload_accepts_image_and_pdf_and_rejects_executable(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user('admin'));
        $data = $this->offerData() + ['image' => UploadedFile::fake()->image('kravice.jpg', 400, 300), 'brochure' => UploadedFile::fake()->create('opis.pdf', 12, 'application/pdf')];
        $this->post('/admin/ponude', $data)->assertRedirect(route('admin.index'));
        $offer = Offer::where('title', $data['title'])->firstOrFail();
        Storage::disk('public')->assertExists([$offer->image, $offer->brochure]);
        $this->get('/mediji/'.basename($offer->image))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/mediji/'.basename($offer->brochure))->assertDownload('brosura.pdf');
        $this->post('/admin/ponude', $this->offerData() + ['image' => UploadedFile::fake()->createWithContent('evil.php', '<?php echo 1;')])->assertSessionHasErrors('image');
        $this->get('/mediji/evil.php')->assertNotFound();
    }

    public function test_plan_is_private_and_duplicate_add_is_idempotent(): void
    {
        $owner = $this->user();
        $this->actingAs($owner)->post('/moj-plan/1')->assertRedirect(route('trip'));
        $this->post('/moj-plan/1')->assertRedirect();
        $this->assertDatabaseCount('trip_items', 1);
        $item = TripItem::first();
        $this->put('/moj-plan/'.$item->id, ['visit_date' => now()->addDay()->format('Y-m-d'), 'persons' => 3, 'note' => 'Polazak ujutro'])->assertRedirect();
        $this->assertSame(3, $item->fresh()->persons);
        $this->get('/moj-plan')->assertOk()->assertSee('Polazak ujutro');
        $this->get('/ispis-plana')->assertOk()->assertSee('195,00 KM');
        $this->put('/moj-plan/'.$item->id, ['visit_date' => '2020-01-01', 'persons' => 0])->assertSessionHasErrors(['visit_date', 'persons']);
        $this->actingAs($this->user('admin'))->get('/moj-plan')->assertDontSee('Polazak ujutro');
        $this->put('/moj-plan/'.$item->id, ['persons' => 7])->assertForbidden();
        $this->delete('/moj-plan/'.$item->id)->assertForbidden();
        $this->actingAs($owner)->delete('/moj-plan/'.$item->id)->assertRedirect();
        $this->assertDatabaseCount('trip_items', 0);
    }

    public function test_archived_offer_is_excluded_from_total(): void
    {
        $this->actingAs($this->user())->post('/moj-plan/1');
        Offer::find(1)->update(['active' => false]);
        $this->get('/ispis-plana')->assertOk()->assertSee('Ukupna procjena: 0,00 KM');
    }

    public function test_review_can_be_updated_only_by_its_owner(): void
    {
        $owner = $this->user();
        $this->actingAs($owner)->post('/ponude/1/recenzije', ['rating' => 5, 'comment' => 'Lijep izlet uz rijeku.'])->assertRedirect();
        $this->post('/ponude/1/recenzije', ['rating' => 4, 'comment' => 'Promijenjeni dojam nakon posjeta.'])->assertRedirect();
        $this->assertDatabaseCount('reviews', 1);
        $this->assertSame(4, Review::first()->rating);
        $this->post('/ponude/1/recenzije', ['rating' => 6, 'comment' => 'Provjera'])->assertSessionHasErrors('rating');
        $other = User::create(['name' => 'Drugi putnik', 'username' => 'drugi', 'email' => 'drugi@example.test', 'password' => 'Sigurna123!']);
        $other->assignRole('korisnik');
        $this->actingAs($other)->delete('/recenzije/1')->assertForbidden();
        $this->actingAs($owner)->delete('/recenzije/1')->assertRedirect();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_html_is_displayed_as_text(): void
    {
        $this->actingAs($this->user())->post('/ponude/1/recenzije', ['rating' => 4, 'comment' => '<script>alert(1)</script>']);
        $this->get('/ponude/1')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_city_and_category_crud_preserves_foreign_keys(): void
    {
        $this->actingAs($this->user('admin'));
        $data = ['name' => 'Test grad', 'description' => 'Opis novog grada za test.', 'latitude' => 43, 'longitude' => 17];
        $this->post('/admin/gradovi', $data)->assertRedirect();
        $city = City::where('name', 'Test grad')->firstOrFail();
        $this->put('/admin/gradovi/'.$city->id, array_replace($data, ['name' => 'Uređeni grad']))->assertRedirect();
        $this->delete('/admin/gradovi/'.$city->id)->assertRedirect();
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
        $this->delete('/admin/gradovi/1')->assertSessionHasErrors('city');
        $this->post('/admin/kategorije', ['name' => 'Nova kategorija'])->assertRedirect();
        $category = Category::where('name', 'Nova kategorija')->firstOrFail();
        $this->put('/admin/kategorije/'.$category->id, ['name' => 'Druga kategorija'])->assertRedirect();
        $this->delete('/admin/kategorije/'.$category->id)->assertRedirect();
        $this->delete('/admin/kategorije/1')->assertSessionHasErrors('category');
    }

    public function test_superadmin_manages_users_and_cannot_disable_self(): void
    {
        $super = $this->user('superadmin');
        $this->actingAs($super)->get('/admin/korisnici')->assertOk();
        $data = ['name' => 'Novi urednik', 'username' => 'urednik', 'email' => 'urednik@example.test', 'password' => 'Sigurna123!', 'role' => 'admin', 'active' => 1];
        $this->post('/admin/korisnici', $data)->assertRedirect();
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertTrue($user->hasPermission('manage_catalog'));
        $this->get('/admin/korisnici?edit='.$user->id)->assertOk();
        $this->put('/admin/korisnici/'.$user->id, array_replace($data, ['role' => 'korisnik', 'password' => '']))->assertRedirect();
        $this->assertFalse($user->fresh()->hasPermission('manage_catalog'));
        $this->delete('/admin/korisnici/'.$user->id)->assertRedirect();
        $this->assertFalse($user->fresh()->active);
        $this->delete('/admin/korisnici/'.$super->id)->assertSessionHasErrors('user');
        $this->put('/admin/korisnici/'.$super->id, ['name' => $super->name, 'username' => $super->username, 'email' => $super->email, 'role' => 'korisnik', 'active' => 1])->assertSessionHasErrors('role');
        $this->assertTrue($super->fresh()->hasPermission('manage_users'));
    }

    public function test_deactivated_session_loses_permission_immediately(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->get('/admin')->assertOk();
        $admin->update(['active' => false]);
        $this->get('/admin')->assertForbidden();
        $this->get('/moj-plan')->assertForbidden();
    }

    public function test_weather_service_response_and_outage(): void
    {
        Http::fake(fn ($request) => $request['latitude'] == City::find(1)->latitude
            ? Http::response(['current' => ['temperature_2m' => 23.4, 'wind_speed_10m' => 7, 'weather_code' => 0]])
            : Http::response([], 500));
        $this->getJson('/vrijeme/1')->assertOk()->assertJsonPath('description','Vedro')->assertJsonPath('temperature',23.4);
        $this->getJson('/vrijeme/2')->assertStatus(503)->assertJsonStructure(['message']);
        $this->getJson('/vrijeme/9999')->assertNotFound();
    }
}
