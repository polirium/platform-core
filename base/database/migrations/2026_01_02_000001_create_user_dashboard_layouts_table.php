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
        Schema::create('user_dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('widget_id', 100);
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->default(4);
            $table->integer('height')->default(2);
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'widget_id']);
            $table->index(['user_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_layouts');
    }
};
