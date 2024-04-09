<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string("uuid");
            $table->string("name");
            $table->string("phone")->nullable();
            $table->string("phone_2")->nullable()->comment("số điện thoại phụ");
            $table->string("email")->nullable();
            $table->string("address")->nullable();
            $table->integer("province_id")->nullable()->comment("Thành phố/Tỉnh");
            $table->integer("district_id")->nullable()->comment("Quận/Huyện");
            $table->integer("ward_id")->nullable()->comment("'Phường/Xã");
            $table->boolean("status")->default(1)->comment("Trạng thái hoạt động");
            $table->integer("user_id");
            $table->timestamps();

            $table->unique("phone");
            $table->unique("email");
        });

        Schema::create('branch_taking_addresses', function (Blueprint $table) {
            $table->id();
            $table->string("uuid");
            $table->integer("branch_id")->comment("Chi nhánh");
            $table->string("address");
            $table->string("phone")->nullable();
            $table->integer("province_id")->nullable()->comment("Thành phố/Tỉnh");
            $table->integer("district_id")->nullable()->comment("Quận/Huyện");
            $table->integer("ward_id")->nullable()->comment("'Phường/Xã");
            $table->integer("user_id");
            $table->timestamps();

            $table->unique("phone");
        });

        Schema::create('user_branches', function (Blueprint $table) {
            $table->id();
            $table->string("uuid")->nullable();
            $table->integer("user_id");
            $table->integer("branch_id");
            $table->boolean("active")->default(0)->comment("User đang ở chi nhánh nào thì sử dụng ở chi nhánh đó");
            $table->timestamps();

            $table->unique(['user_id', 'branch_id']); // đảm bảo không được trùng user_id và branch_id trong bảng
        });

        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->string("uuid")->nullable();
            $table->integer("product_id");
            $table->integer("branch_id");
            $table->integer("qty")->default(0)->comment("Số lượng hàng hoá ở trong chi nhánh đó");
            $table->timestamps();

            $table->unique(['product_id', 'branch_id']); // đảm bảo không được trùng product_id và branch_id trong bảng
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('branch_taking_addresses');
        Schema::dropIfExists('user_branches');
        Schema::dropIfExists('product_branches');
    }
};
