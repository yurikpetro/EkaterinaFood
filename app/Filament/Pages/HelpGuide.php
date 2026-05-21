<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class HelpGuide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = 'Как принять заказ';

    protected static ?string $title = 'Как принять заказ';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.help-guide';
}
