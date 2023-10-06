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
        Schema::create('callRecords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agentFormId')->constrained('agentForms');
            $table->string('clientName');
            $table->string('recordingDate');
            $table->string('recordingTime');
            $table->string('audioFile');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callRecords');
    }
};
