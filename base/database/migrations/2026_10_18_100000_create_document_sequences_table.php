<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One counter per document prefix (BH, PT, TRH…). Numbers are taken under a
 * row lock so two tills saving at the same moment never get the same code.
 * Counters start from the highest code already in use the first time a
 * prefix is needed, so no existing data is read or changed here.
 */
return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('document_sequences')) {
            return;
        }

        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 32)->unique();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
