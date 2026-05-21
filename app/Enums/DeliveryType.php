<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DeliveryType: string implements HasLabel
{
    case Delivery = 'delivery';
    case Pickup = 'pickup';

    public function getLabel(): string
    {
        return match ($this) {
            self::Delivery => 'Доставка',
            self::Pickup => 'Самовывоз',
        };
    }
}
