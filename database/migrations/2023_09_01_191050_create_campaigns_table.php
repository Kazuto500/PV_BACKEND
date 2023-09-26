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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users', 'id');
            $table->string('campaignName');
            $table->timestamp('createDate');
            $table->string('planName');
            $table->string('minutesPlan');
            $table->string('restoreMinutesPlan')->nullable();
            $table->boolean('state')->default(false);
            $table->string('realizeCalls')->nullable();
            $table->string('averageCalls')->nullable();
            $table->string('preview');
            $table->string('download');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
