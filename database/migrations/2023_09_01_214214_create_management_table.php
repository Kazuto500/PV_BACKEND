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
        Schema::create('management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users', 'id');
            $table->boolean('state');
            $table->string('name');
            $table->string('lastName');
            $table->string('contactNumber');
            $table->string('contactType');
            $table->string('scrip');
            $table->string('attachAudio');
            $table->string('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management');
    }
};
