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
        Schema::create('weekly_records', function (Blueprint $table) {
            $table->id();
            $table->string('weeklyRecordName');
            $table->string('download');
            $table->string('weeklySatisfaction');
            $table->foreignId('user_id')->constrained('users');
            //$table->foreignId('campaign_id')->constrained('campaigns');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_records');
    }
};
