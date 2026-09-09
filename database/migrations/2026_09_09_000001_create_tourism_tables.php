<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->nullable()->unique();
            $t->enum('role', ['superadmin', 'admin', 'korisnik'])->default('korisnik');
            $t->boolean('active')->default(true);
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
        });
        Schema::create('permissions', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('description');
        });
        Schema::create('permission_role', function (Blueprint $t) {
            $t->foreignId('role_id')->constrained()->cascadeOnDelete();
            $t->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $t->primary(['role_id', 'permission_id']);
        });
        Schema::create('role_user', function (Blueprint $t) {
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('role_id')->constrained()->cascadeOnDelete();
            $t->primary(['user_id', 'role_id']);
        });
        Schema::create('cities', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->text('description');
            $t->string('image')->nullable();
            $t->decimal('latitude', 9, 6);
            $t->decimal('longitude', 9, 6);
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });
        Schema::create('offers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('city_id')->constrained()->restrictOnDelete();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->string('title');
            $t->text('description');
            $t->string('image')->nullable();
            $t->string('brochure')->nullable();
            $t->decimal('price', 8, 2);
            $t->unsignedInteger('duration');
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
        Schema::create('trip_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->date('visit_date')->nullable();
            $t->unsignedSmallInteger('persons')->default(1);
            $t->string('note', 500)->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'offer_id']);
        });
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating');
            $t->text('comment');
            $t->timestamps();
            $t->unique(['user_id', 'offer_id']);
        });
    }

    public function down(): void
    {
        foreach (['reviews', 'trip_items', 'offers', 'categories', 'cities', 'role_user', 'permission_role', 'permissions', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['username', 'role', 'active']));
    }
};
