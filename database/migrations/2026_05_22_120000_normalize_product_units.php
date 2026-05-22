<?php

use App\Enums\ProductUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('products')->orderBy('id')->lazy() as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'unit' => ProductUnit::fromLegacy((string) $product->unit)->value,
                ]);
        }
    }

    public function down(): void
    {
        // Не восстанавливаем произвольные подписи единиц.
    }
};
