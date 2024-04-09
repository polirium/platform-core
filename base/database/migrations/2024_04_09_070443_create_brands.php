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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string("uuid");
            $table->string("name");
            $table->string("status")->nullable();
            $table->string("note")->nullable();
            $table->integer("user_id");
            $table->timestamps();

            $table->index("user_id");
        });

        Schema::create('brands_branches', function (Blueprint $table) {
            $table->id();
            $table->integer("brand_id")->comment("Thương hiệu");
            $table->integer("branch_id")->comment("Chi nhánh");
            $table->timestamps();

            $table->unique(["brand_id", "branch_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
