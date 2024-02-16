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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->string('username', 100)->unique()->after('uuid');
            $table->string('first_name', 100)->after('username');
            $table->string('last_name', 100)->after('first_name');
            $table->boolean('super_admin')->default(0)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('username');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            $table->dropColumn('super_admin');
        });
    }
};
