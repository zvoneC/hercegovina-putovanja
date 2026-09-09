<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Offer;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use App\Support\CleanHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? '';

        return view('admin.index', [
            'offers' => Offer::with(['city', 'category'])->when($q, fn ($query) => $query->where('title', 'like', '%'.$q.'%'))->latest()->paginate(15)->withQueryString(),
            'stats' => ['Ponude' => Offer::count(), 'Gradovi' => City::count(), 'Korisnici' => User::count(), 'Recenzije' => Review::count()],
        ]);
    }

    public function offerForm(?Offer $offer = null)
    {
        return view('admin.offer-form', ['offer' => $offer ?? new Offer, 'cities' => City::orderBy('name')->get(), 'categories' => Category::all()]);
    }

    public function saveOffer(Request $request, ?Offer $offer = null)
    {
        $data = $request->validate([
            'title' => 'required|string|min:3|max:150', 'city_id' => 'required|exists:cities,id', 'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:15000', 'price' => 'required|numeric|min:0|max:99999', 'duration' => 'required|integer|min:1|max:168',
            'active' => 'nullable|boolean', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096|dimensions:max_width=6000,max_height=6000', 'brochure' => 'nullable|file|mimes:pdf|max:5120',
        ]);
        $data['description'] = CleanHtml::clean($data['description']);
        if (mb_strlen(trim(strip_tags($data['description']))) < 15) {
            throw ValidationException::withMessages(['description' => 'Opis mora imati barem 15 znakova teksta.']);
        }
        $data['active'] = $request->boolean('active');
        unset($data['image'], $data['brochure']);
        $newFiles = [];
        $oldFiles = [];
        try {
            foreach (['image', 'brochure'] as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $request->file($field)->store('uploads', 'public');
                    $newFiles[] = $data[$field];
                    if ($offer?->$field && str_starts_with($offer->$field, 'uploads/')) {
                        $oldFiles[] = $offer->$field;
                    }
                }
            }
            $offer ? $offer->update($data) : Offer::create($data);
        } catch (\Throwable $error) {
            Storage::disk('public')->delete($newFiles);
            throw $error;
        }
        Storage::disk('public')->delete($oldFiles);

        return redirect()->route('admin.index')->with('success', 'Ponuda je spremljena.');
    }

    public function deleteOffer(Offer $offer)
    {
        // Ponuda ostaje u spremljenim planovima, ali se više ne može dodati u nove.
        $offer->update(['active' => false]);

        return back()->with('success', 'Ponuda je arhivirana. Možete je vratiti kroz uređivanje.');
    }

    public function lists(Request $request)
    {
        return view('admin.lists', ['cities' => City::withCount('offers')->get(), 'categories' => Category::withCount('offers')->get(), 'editCity' => City::find($request->integer('city')), 'editCategory' => Category::find($request->integer('category'))]);
    }

    public function saveCity(Request $request, ?City $city = null)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80', Rule::unique('cities')->ignore($city)], 'description' => 'required|string|min:10|max:2000', 'latitude' => 'required|numeric|between:-90,90', 'longitude' => 'required|numeric|between:-180,180']);
        $city ? $city->update($data) : City::create($data);

        return redirect()->route('admin.lists')->with('success', 'Grad je spremljen.');
    }

    public function deleteCity(City $city)
    {
        if ($city->offers()->exists()) {
            return back()->withErrors(['city' => 'Grad ima povezane ponude. Najprije ih premjestite u drugi grad.']);
        }
        $city->delete();

        return back()->with('success', 'Grad je obrisan.');
    }

    public function saveCategory(Request $request, ?Category $category = null)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80', Rule::unique('categories')->ignore($category)]]);
        $category ? $category->update($data) : Category::create($data);

        return redirect()->route('admin.lists')->with('success', 'Kategorija je spremljena.');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->offers()->exists()) {
            return back()->withErrors(['category' => 'Kategorija ima povezane ponude. Najprije ih premjestite u drugu kategoriju.']);
        }
        $category->delete();

        return back()->with('success', 'Kategorija je obrisana.');
    }

    public function users(Request $request)
    {
        $q = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? '';

        return view('admin.users', ['users' => User::when($q, fn ($query) => $query->where(fn ($q2) => $q2->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%')->orWhere('username', 'like', '%'.$q.'%')))->orderBy('id')->paginate(15)->withQueryString(), 'editUser' => User::find($request->integer('edit')), 'roles' => Role::with('permissions')->get()]);
    }

    public function saveUser(Request $request, ?User $user = null)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:80', 'username' => ['required', 'alpha_dash', 'min:3', 'max:40', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:72'],
            'role' => 'required|in:korisnik,admin,superadmin', 'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        if ($user?->id === $request->user()->id && (! $data['active'] || $data['role'] !== 'superadmin')) {
            throw ValidationException::withMessages(['role' => 'Vlastitom računu ne možete ukloniti administratorski pristup.']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        DB::transaction(function () use ($user, $data) {
            if ($user) {
                $user->update($data);
            } else {
                $user = User::create($data);
            }
            $user->assignRole($data['role']);
        });

        return redirect()->route('admin.users')->with('success', 'Korisnik je spremljen.');
    }

    public function deleteUser(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Ne možete deaktivirati vlastiti račun.']);
        }
        $user->update(['active' => false]);

        return back()->with('success', 'Račun je deaktiviran. Korisnik više nema pristup zaštićenim dijelovima.');
    }
}
