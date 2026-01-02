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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // Module folder name
            $table->string('display_name');             // Human readable name
            $table->text('description')->nullable();    // Module description
            $table->string('version')->nullable();      // Version from composer.json
            $table->string('namespace');                // PSR-4 namespace
            $table->string('provider');                 // Main provider class
            $table->string('path');                     // Path to module folder
            $table->enum('status', ['pending', 'installed', 'active', 'disabled'])->default('pending');
            $table->json('dependencies')->nullable();   // Required dependencies
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
