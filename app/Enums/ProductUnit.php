<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductUnit: string implements HasLabel
{
    case Piece = 'piece';
    case Portion = 'portion';
    case Plate = 'plate';
    case Kg = 'kg';
    case Gram = 'g';

    public function getLabel(): string
    {
        return match ($this) {
            self::Piece => 'шт',
            self::Portion => 'порция',
            self::Plate => 'тарелка',
            self::Kg => 'кг',
            self::Gram => 'г',
        };
    }

    public function isWeighted(): bool
    {
        return in_array($this, [self::Kg, self::Gram], true);
    }

    public function priceUnitLabel(): string
    {
        return match ($this) {
            self::Kg, self::Gram => 'кг',
            default => $this->getLabel(),
        };
    }

    public function amountInputLabel(): string
    {
        return $this->isWeighted() ? 'Вес' : 'Кол-во';
    }

    public function minQuantityFieldLabel(): string
    {
        return match ($this) {
            self::Kg => 'Минимальный вес (× 0,1 кг)',
            self::Gram => 'Минимальный вес (граммы)',
            default => 'Мин. количество',
        };
    }

    public function minQuantityHelper(): string
    {
        return match ($this) {
            self::Kg => 'Например: 5 = 0,5 кг, 10 = 1 кг. Шаг на сайте — 0,1 кг.',
            self::Gram => 'Например: 300 = 300 г. Шаг на сайте — 100 г. Цена указывается за 1 кг.',
            default => 'Сколько штук (порций) минимум можно заказать.',
        };
    }

    public function stepAmount(): int
    {
        return match ($this) {
            self::Kg => 1,
            self::Gram => 100,
            default => 1,
        };
    }

    public function calculateSubtotal(int $unitPrice, int $quantity): int
    {
        return match ($this) {
            self::Kg => (int) round($unitPrice * $quantity / 10),
            self::Gram => (int) round($unitPrice * $quantity / 1000),
            default => $unitPrice * $quantity,
        };
    }

    public function formatAmount(int $quantity): string
    {
        return match ($this) {
            self::Kg => self::formatDecimalKg($quantity / 10) . ' кг',
            self::Gram => number_format($quantity, 0, ',', ' ') . ' г',
            default => $quantity . ' ' . $this->getLabel(),
        };
    }

    public static function fromLegacy(string $unit): self
    {
        $normalized = mb_strtolower(trim($unit));

        return match ($normalized) {
            'шт', 'piece' => self::Piece,
            'порция', 'portion' => self::Portion,
            'тарелка', 'plate' => self::Plate,
            'кг', 'kg' => self::Kg,
            'г', 'гр', 'гм', 'грамм', 'gram', 'g' => self::Gram,
            default => self::Portion,
        };
    }

    private static function formatDecimalKg(float $kg): string
    {
        $formatted = number_format($kg, 1, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',');
    }
}
