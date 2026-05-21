<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.account-widget';

    public static function canView(): bool
    {
        return Filament::auth()->check();
    }

    public function getUserName(): string
    {
        return Filament::auth()->user()?->name ?? '';
    }
}
