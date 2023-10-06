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
            $table->string('agentName');
            $table->string('monthRecords');
            $table->string('numberAssignedCampaigns');
            $table->string('completedCampaigns');
            $table->string('averageServiceRating');
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
