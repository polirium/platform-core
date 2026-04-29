<?php

namespace Polirium\Core\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('poli:seed:sample', 'Generate sample data for testing')]
class SampleDataCommand extends Command
{
    protected $signature = 'poli:seed:sample
                            {--count=10 : Number of records to create per entity}
                            {--products : Only seed products}
                            {--customers : Only seed customers}
                            {--vendors : Only seed vendors}';

    protected $description = 'Generate sample data for testing';

    protected array $vietnameseNames = [
        'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Văn Cường', 'Phạm Thị Dung', 'Hoàng Văn Em',
        'Vũ Thị Phương', 'Đặng Văn Giang', 'Bùi Thị Hương', 'Đỗ Văn Khoa', 'Ngô Thị Lan',
        'Dương Văn Minh', 'Lý Thị Nga', 'Hồ Văn Phúc', 'Trương Thị Quỳnh', 'Đinh Văn Sơn',
        'Mai Thị Trang', 'Phan Văn Uy', 'Võ Thị Vân', 'Lâm Văn Xuân', 'Châu Thị Yến',
    ];

    protected array $productNames = [
        'Gạo ST25', 'Nước mắm Nam Ngư', 'Dầu ăn Neptune', 'Mì Hảo Hảo', 'Sữa Vinamilk',
        'Cà phê Trung Nguyên', 'Trà Lipton', 'Bia Tiger', 'Nước suối Aquafina', 'Coca Cola',
        'Bánh mì sandwich', 'Phở bò ăn liền', 'Xúc xích Vissan', 'Cá hồi fillet', 'Thịt heo xay',
        'Rau muống', 'Cà chua', 'Hành tây', 'Táo Mỹ', 'Cam sành',
        'Laptop Dell', 'iPhone 15', 'Samsung Galaxy', 'Tai nghe AirPods', 'Sạc dự phòng',
        'Áo thun nam', 'Quần jean nữ', 'Giày thể thao', 'Túi xách', 'Đồng hồ thông minh',
        'Nồi cơm điện', 'Máy xay sinh tố', 'Quạt điện', 'Tivi Samsung 55"', 'Tủ lạnh LG',
    ];

    protected array $companyNames = [
        'Công ty TNHH Thương mại ABC', 'Công ty CP Phân phối XYZ', 'Công ty TNHH Sản xuất Việt Nam',
        'Công ty CP Xuất nhập khẩu Toàn Cầu', 'Công ty TNHH Dịch vụ Thương mại Đông Á',
        'Công ty CP Công nghệ Số Việt', 'Công ty TNHH Thực phẩm Sạch', 'Công ty CP Đầu tư Nam Phương',
        'Công ty TNHH Phát triển Bắc Sơn', 'Công ty CP Logistics Nhanh',
    ];

    protected array $addresses = [
        '123 Nguyễn Huệ, Quận 1, TP.HCM',
        '456 Lê Lợi, Quận 5, TP.HCM',
        '789 Trần Hưng Đạo, Quận 1, TP.HCM',
        '321 Hai Bà Trưng, Quận 3, TP.HCM',
        '654 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
        '12 Hàng Bài, Hoàn Kiếm, Hà Nội',
        '45 Phố Huế, Hai Bà Trưng, Hà Nội',
        '78 Bà Triệu, Hoàn Kiếm, Hà Nội',
        '90 Trần Phú, Hải Châu, Đà Nẵng',
        '25 Lê Duẩn, Ninh Kiều, Cần Thơ',
    ];

    protected array $trademarkNames = [
        'Vinamilk', 'Unilever', 'P&G', 'Nestle', 'Acecook',
        'Samsung', 'Apple', 'Sony', 'LG', 'Panasonic',
        'Nike', 'Adidas', 'Zara', 'H&M', 'Uniqlo',
    ];

    protected array $shelveNames = [
        'Kệ A1', 'Kệ A2', 'Kệ A3', 'Kệ B1', 'Kệ B2',
        'Kệ C1', 'Kệ C2', 'Kệ D1', 'Kệ D2', 'Kệ E1',
        'Kho chính', 'Kho phụ', 'Tủ lạnh', 'Kho mát', 'Kho đông',
    ];

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $seedProducts = $this->option('products');
        $seedCustomers = $this->option('customers');
        $seedVendors = $this->option('vendors');

        // If no specific option, seed all
        $seedAll = ! $seedProducts && ! $seedCustomers && ! $seedVendors;

        $this->components->info("Starting sample data generation...\n");

        // Always seed base data first
        $this->seedCategories();
        $this->seedTrademarks();
        $this->seedShelves();
        $this->seedCustomerGroups();
        $this->seedVendorGroups();

        if ($seedAll || $seedProducts) {
            $this->seedProducts($count);
        }

        if ($seedAll || $seedCustomers) {
            $this->seedCustomers($count);
        }

        if ($seedAll || $seedVendors) {
            $this->seedVendors($count);
        }

        $this->components->info("\nSample data generation completed!");

        return self::SUCCESS;
    }

    protected function seedCategories(): void
    {
        $this->components->task('Creating categories', function () {
            $model = \Polirium\Modules\Product\Http\Model\Category::class;

            if (! class_exists($model)) {
                return false;
            }

            $categories = [
                'Thực phẩm' => ['Thịt', 'Cá', 'Rau củ', 'Trái cây', 'Đồ khô'],
                'Đồ uống' => ['Nước ngọt', 'Nước suối', 'Bia rượu', 'Sữa các loại'],
                'Điện tử' => ['Điện thoại', 'Laptop', 'Phụ kiện điện tử', 'Gia dụng điện'],
                'Thời trang' => ['Áo', 'Quần', 'Giày dép', 'Túi xách', 'Đồng hồ'],
                'Gia dụng' => ['Nồi chảo', 'Dao kéo', 'Hộp đựng', 'Dụng cụ nhà bếp'],
            ];

            foreach ($categories as $parentName => $children) {
                $parent = $model::firstOrCreate(
                    ['name' => $parentName, 'parent_id' => null],
                    [
                        'uuid' => Str::uuid(),
                        'name' => $parentName,
                        'parent_id' => null,
                        'user_id' => 1,
                    ]
                );

                foreach ($children as $childName) {
                    $model::firstOrCreate(
                        ['name' => $childName, 'parent_id' => $parent->id],
                        [
                            'uuid' => Str::uuid(),
                            'name' => $childName,
                            'parent_id' => $parent->id,
                            'user_id' => 1,
                        ]
                    );
                }
            }

            return true;
        });
    }

    protected function seedTrademarks(): void
    {
        $this->components->task('Creating trademarks', function () {
            $model = \Polirium\Modules\Product\Http\Model\Trademark::class;

            if (! class_exists($model)) {
                return false;
            }

            foreach ($this->trademarkNames as $name) {
                $model::firstOrCreate(
                    ['name' => $name],
                    [
                        'uuid' => Str::uuid(),
                        'name' => $name,
                        'user_id' => 1,
                    ]
                );
            }

            return true;
        });
    }

    protected function seedShelves(): void
    {
        $this->components->task('Creating shelves', function () {
            $model = \Polirium\Modules\Product\Http\Model\Shelve::class;

            if (! class_exists($model)) {
                return false;
            }

            foreach ($this->shelveNames as $name) {
                $model::firstOrCreate(
                    ['name' => $name],
                    [
                        'uuid' => Str::uuid(),
                        'name' => $name,
                        'user_id' => 1,
                    ]
                );
            }

            return true;
        });
    }

    protected function seedCustomerGroups(): void
    {
        $this->components->task('Creating customer groups', function () {
            $model = \Polirium\Modules\Customer\Http\Model\CustomerGroup::class;

            if (! class_exists($model)) {
                return false;
            }

            $groups = [
                'VIP' => 'Khách hàng VIP, giảm giá 10%',
                'Thường' => 'Khách hàng thông thường',
                'Mới' => 'Khách hàng mới',
                'Đại lý' => 'Đại lý phân phối, giảm giá 15%',
            ];

            foreach ($groups as $name => $description) {
                $model::firstOrCreate(
                    ['name' => $name],
                    [
                        'uuid' => Str::uuid(),
                        'name' => $name,
                        'description' => $description,
                        'user_id' => 1,
                    ]
                );
            }

            return true;
        });
    }

    protected function seedVendorGroups(): void
    {
        $this->components->task('Creating vendor groups', function () {
            $model = \Polirium\Modules\Vendor\Http\Model\VendorGroup::class;

            if (! class_exists($model)) {
                return false;
            }

            $groups = ['Trong nước', 'Nhập khẩu', 'Ủy thác', 'Đối tác chiến lược'];

            foreach ($groups as $name) {
                $model::firstOrCreate(
                    ['name' => $name],
                    [
                        'uuid' => Str::uuid(),
                        'name' => $name,
                        'user_created_id' => 1,
                    ]
                );
            }

            return true;
        });
    }

    protected function seedProducts(int $count): void
    {
        $this->components->task("Creating {$count} products", function () use ($count) {
            $model = \Polirium\Modules\Product\Http\Model\Product::class;
            $categoryModel = \Polirium\Modules\Product\Http\Model\Category::class;
            $trademarkModel = \Polirium\Modules\Product\Http\Model\Trademark::class;
            $shelveModel = \Polirium\Modules\Product\Http\Model\Shelve::class;
            $branchModel = \Polirium\Core\Base\Http\Models\Branch\Branch::class;

            if (! class_exists($model)) {
                return false;
            }

            // Get IDs for relations
            $categoryIds = class_exists($categoryModel)
                ? $categoryModel::whereNotNull('parent_id')->pluck('id')->toArray()
                : [];
            $trademarkIds = class_exists($trademarkModel)
                ? $trademarkModel::pluck('id')->toArray()
                : [];
            $shelveIds = class_exists($shelveModel)
                ? $shelveModel::pluck('id')->toArray()
                : [];
            $branchIds = class_exists($branchModel)
                ? $branchModel::pluck('id')->toArray()
                : [1];

            $units = ['cái', 'kg', 'hộp', 'chai', 'gói', 'thùng', 'lon', 'bịch'];
            $types = ['product', 'service', 'combo'];

            for ($i = 0; $i < $count; $i++) {
                $name = $this->productNames[array_rand($this->productNames)] . ' ' . Str::random(4);
                $cost = rand(10000, 5000000);
                $price = $cost + rand(5000, 500000);
                $qty = rand(10, 1000);
                $type = $types[array_rand($types)];

                $product = $model::create([
                    'uuid' => Str::uuid(),
                    'name' => $name,
                    'code' => 'SP-' . strtoupper(Str::random(6)),
                    'category_id' => ! empty($categoryIds) ? $categoryIds[array_rand($categoryIds)] : null,
                    'trademark_id' => ! empty($trademarkIds) ? $trademarkIds[array_rand($trademarkIds)] : null,
                    'shelve_id' => ($type === 'product' && ! empty($shelveIds)) ? $shelveIds[array_rand($shelveIds)] : null,
                    'cost' => $type !== 'service' ? $cost : 0,
                    'price' => $price,
                    'qty' => $type !== 'service' ? $qty : 0,
                    'weight' => $type === 'product' ? rand(100, 10000) : 0,
                    'weight_type' => 'gram',
                    'unit' => $units[array_rand($units)],
                    'min_quantity' => rand(5, 20),
                    'max_quantity' => rand(500, 2000),
                    'type' => $type,
                    'allows_sale' => true,
                    'description' => 'Mô tả sản phẩm ' . $name,
                    'note' => rand(0, 1) ? 'Ghi chú cho sản phẩm' : null,
                    'user_id' => 1,
                ]);

                // Sync product with branches
                if (! empty($branchIds) && method_exists($product, 'branches')) {
                    $branchData = [];
                    foreach ($branchIds as $branchId) {
                        $branchData[$branchId] = ['qty' => $qty];
                    }
                    $product->branches()->sync($branchData);
                }
            }

            return true;
        });
    }

    protected function seedCustomers(int $count): void
    {
        $this->components->task("Creating {$count} customers", function () use ($count) {
            $model = \Polirium\Modules\Customer\Http\Model\Customer::class;
            $groupModel = \Polirium\Modules\Customer\Http\Model\CustomerGroup::class;

            if (! class_exists($model)) {
                return false;
            }

            $phonePrefixes = ['090', '091', '093', '094', '096', '097', '098', '032', '033', '034', '035', '036', '037', '038', '039'];
            $groupIds = class_exists($groupModel)
                ? $groupModel::pluck('id')->toArray()
                : [];

            for ($i = 0; $i < $count; $i++) {
                $name = $this->vietnameseNames[array_rand($this->vietnameseNames)];
                $phonePrefix = $phonePrefixes[array_rand($phonePrefixes)];
                $isCompany = rand(0, 3) === 0; // 25% chance of being a company

                $customer = $model::create([
                    'uuid' => Str::uuid(),
                    'code' => 'KH-' . strtoupper(Str::random(6)),
                    'name' => $name,
                    'phone' => $phonePrefix . rand(1000000, 9999999),
                    'phone2' => rand(0, 1) ? $phonePrefix . rand(1000000, 9999999) : null,
                    'sex' => rand(0, 1),
                    'birthday' => now()->subYears(rand(18, 60))->subDays(rand(0, 365))->format('Y-m-d'),
                    'address' => $this->addresses[array_rand($this->addresses)],
                    'type' => $isCompany ? 1 : 0, // 0 = personal, 1 = company
                    'company' => $isCompany ? $this->companyNames[array_rand($this->companyNames)] : null,
                    'vat' => $isCompany ? (string) rand(1000000000, 9999999999) : null,
                    'email' => Str::slug($name, '.') . rand(1, 999) . '@gmail.com',
                    'facebook' => rand(0, 1) ? 'facebook.com/' . Str::slug($name) : null,
                    'note' => rand(0, 1) ? 'Ghi chú khách hàng' : null,
                    'user_id' => 1,
                    'branch_id' => 1,
                ]);

                // Sync customer with groups
                if (! empty($groupIds) && method_exists($customer, 'customerGroups')) {
                    $randomGroupIds = array_rand(array_flip($groupIds), rand(1, min(2, count($groupIds))));
                    $customer->customerGroups()->sync((array) $randomGroupIds);
                }
            }

            return true;
        });
    }

    protected function seedVendors(int $count): void
    {
        $this->components->task("Creating {$count} vendors", function () use ($count) {
            $model = \Polirium\Modules\Vendor\Http\Model\Vendor::class;
            $groupModel = \Polirium\Modules\Vendor\Http\Model\VendorGroup::class;

            if (! class_exists($model)) {
                return false;
            }

            $phonePrefixes = ['028', '024', '0236', '0225'];
            $groupIds = class_exists($groupModel)
                ? $groupModel::pluck('id')->toArray()
                : [];

            for ($i = 0; $i < $count; $i++) {
                $company = $this->companyNames[array_rand($this->companyNames)] . ' ' . Str::random(3);
                $phonePrefix = $phonePrefixes[array_rand($phonePrefixes)];
                $contactName = $this->vietnameseNames[array_rand($this->vietnameseNames)];

                $vendor = $model::create([
                    'uuid' => Str::uuid(),
                    'code' => 'NCC-' . strtoupper(Str::random(6)),
                    'name' => $contactName,
                    'company' => $company,
                    'phone' => $phonePrefix . rand(1000000, 9999999),
                    'address' => $this->addresses[array_rand($this->addresses)],
                    'email' => 'contact@' . Str::slug(Str::limit($company, 20), '') . '.vn',
                    'vat' => (string) rand(1000000000, 9999999999),
                    'status' => 'active',
                    'total' => 0,
                    'debt' => 0,
                    'total_purchase' => 0,
                    'note' => rand(0, 1) ? 'Ghi chú nhà cung cấp' : null,
                    'user_created_id' => 1,
                    'branch_id' => 1,
                ]);

                // Sync vendor with groups
                if (! empty($groupIds) && method_exists($vendor, 'group')) {
                    $randomGroupIds = array_rand(array_flip($groupIds), rand(1, min(2, count($groupIds))));
                    $vendor->group()->sync((array) $randomGroupIds);
                }
            }

            return true;
        });
    }
}
