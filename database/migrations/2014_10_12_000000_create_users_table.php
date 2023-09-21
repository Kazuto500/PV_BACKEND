<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('profilePhotoUrl')->default('');
            $table->string('companyName');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->timestamp('emailVerifiedAt')->nullable();
            $table->string('countryCode');
            $table->string('dialCode');
            $table->string('telephone');
            $table->string('password');
            $table->string('role');
            $table->string('campaign')->nullable();
            $table->boolean('authenticationEnabled')->default(FALSE);
            $table->string('authenticationCode')->nullable();
            $table->boolean('emailNotificationsEnabled')->default(FALSE);
            $table->timestamp('lastPasswordUpdate')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
