<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Products::create([
            'product_name' => 'Product 1',
            'product_description' => 'Description for Product 1',
            'product_price' => '100.00',
            'product_stock' => '50',
            'product_status' => 'active',
            'product_category' => 'Category 1', // إضافة قيمة للحقل product_category
            // يمكنك إضافة قيم لبقية الأعمدة حسب الحاجة
        ]);

        Products::create([
            'product_name' => 'Product 2',
            'product_description' => 'Description for Product 2',
            'product_price' => '150.00',
            'product_stock' => '30',
            'product_status' => 'active',
            'product_category' => 'Category 2', // إضافة قيمة للحقل product_category
            // يمكنك إضافة قيم لبقية الأعمدة حسب الحاجة
        ]);
    }
}
