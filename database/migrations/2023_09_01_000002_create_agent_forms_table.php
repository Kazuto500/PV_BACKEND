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
        Schema::create('agentForms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users', 'id');
            $table->foreignId('agentId')->constrained('agents', 'id');
            $table->string('userFirstName');
            $table->string('userLastName');
            $table->string('numberContact');
            $table->string('contactType');
            $table->string('callResult');
            $table->string('audioFile');
            $table->string('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_forms');
    }
};
