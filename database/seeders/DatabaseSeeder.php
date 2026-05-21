<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'katya@ekaterinafood.local'],
            [
                'name' => 'Тётя Катя',
                'password' => Hash::make('password'),
            ],
        );

        foreach (Settings::defaults() as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Product::query()->delete();

        $appetizers = Category::query()->updateOrCreate(
            ['name' => 'Закуски и к столу'],
            ['sort_order' => 1, 'is_active' => true],
        );

        $party = Category::query()->updateOrCreate(
            ['name' => 'Праздники'],
            ['sort_order' => 2, 'is_active' => true],
        );

        $home = Category::query()->updateOrCreate(
            ['name' => 'Домашняя еда'],
            ['sort_order' => 3, 'is_active' => true],
        );

        $grill = Category::query()->updateOrCreate(
            ['name' => 'Мангальное ассорти'],
            ['sort_order' => 4, 'is_active' => true],
        );

        Category::query()
            ->whereNotIn('id', [$appetizers->id, $party->id, $home->id, $grill->id])
            ->update(['is_active' => false]);

        $this->seedProducts($appetizers->id, [
            [
                'name' => 'Жульен в тарталетках',
                'description' => 'Закуска в тарталетках',
                'price' => 100,
                'unit' => 'шт',
            ],
            [
                'name' => 'Баклажанные рулетики с начинкой',
                'description' => 'Сливочный творожный сыр, чуть-чуть чеснока и орехи. 15 шт в порции — ориентир 1–2 шт на человека',
                'price' => 600,
                'unit' => 'порция',
                'min_quantity' => 1,
            ],
            [
                'name' => 'Овощная нарезка',
                'description' => 'Тарелка Ø 25 см: помидоры черри разных цветов, огурцы, болгарский перец (разноцветный, мясистый)',
                'price' => 600,
                'unit' => 'тарелка',
            ],
        ]);

        $this->seedProducts($party->id, [
            [
                'name' => 'Пицца большая',
                'description' => '32 см',
                'price' => 800,
                'unit' => 'шт',
            ],
            [
                'name' => 'Пирог',
                'description' => 'Диаметр 32 см (как пицца)',
                'price' => 800,
                'unit' => 'шт',
            ],
        ]);

        $this->seedProducts($home->id, [
            [
                'name' => 'Курица по-французски',
                'description' => 'Большая сытная порция (размером с телефон): грибы, помидор, сыр',
                'price' => 300,
                'unit' => 'порция',
            ],
        ]);

        $this->seedProducts($grill->id, [
            [
                'name' => 'Мякоть шеи маленькими кусочками',
                'description' => 'Мангальное ассорти на третий день',
                'price' => 3500,
                'unit' => 'кг',
            ],
            [
                'name' => 'Аджапсандал',
                'description' => 'Мангальное ассорти на третий день',
                'price' => 1400,
                'unit' => 'кг',
            ],
            [
                'name' => 'Картошка с салом',
                'description' => 'Мангальное ассорти на третий день',
                'price' => 1000,
                'unit' => 'кг',
            ],
        ]);
    }

    private function seedProducts(int $categoryId, array $products): void
    {
        foreach ($products as $index => $data) {
            Product::query()->create([
                'category_id' => $categoryId,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'unit' => $data['unit'],
                'min_quantity' => $data['min_quantity'] ?? 1,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
