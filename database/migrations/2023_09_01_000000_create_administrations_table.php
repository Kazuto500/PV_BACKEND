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
        Schema::create('administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users', 'id');
            $table->string('adminName');
            $table->string('workTeamName');
            $table->string('numberAgents');
            $table->string('totalCampaigns');
            $table->string('activeAgents');
            $table->string('inactiveAgents');
            $table->string('weeklySatisfaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrations');
    }
};
