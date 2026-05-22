<?php

namespace App\Filament\Pages;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Filament\Widgets\Statistics\DeliveryTypeChart;
use App\Filament\Widgets\Statistics\OrdersByStatusChart;
use App\Filament\Widgets\Statistics\OrdersTrendChart;
use App\Filament\Widgets\Statistics\StatisticsOverview;
use App\Filament\Widgets\Statistics\TopProductsTable;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Statistics extends Page
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Статистика';

    protected static ?string $title = 'Статистика';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'statistics';

    public function mount(): void
    {
        $this->mountHasFilters();

        if (blank($this->filters)) {
            $this->filters = [
                'period' => '30days',
                'status' => [],
                'delivery_type' => null,
            ];
            $this->getFiltersForm()->fill($this->filters);
        }
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Фильтры')
                    ->description('Период и условия применяются ко всем блокам ниже')
                    ->schema([
                        Select::make('period')
                            ->label('Период')
                            ->options([
                                'today' => 'Сегодня',
                                '7days' => '7 дней',
                                '30days' => '30 дней',
                                'month' => 'Этот месяц',
                                'prev_month' => 'Прошлый месяц',
                                'custom' => 'Свой период',
                            ])
                            ->default('30days')
                            ->live()
                            ->native(false),
                        DatePicker::make('from')
                            ->label('С')
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('period') === 'custom'),
                        DatePicker::make('to')
                            ->label('По')
                            ->native(false)
                            ->maxDate(now())
                            ->visible(fn (Get $get): bool => $get('period') === 'custom'),
                        Select::make('status')
                            ->label('Статус')
                            ->options(OrderStatus::class)
                            ->multiple()
                            ->placeholder('Все')
                            ->native(false),
                        Select::make('delivery_type')
                            ->label('Получение')
                            ->options(DeliveryType::class)
                            ->placeholder('Все')
                            ->native(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 5,
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            StatisticsOverview::class,
            OrdersTrendChart::class,
            OrdersByStatusChart::class,
            DeliveryTypeChart::class,
            TopProductsTable::class,
        ];
    }

    /**
     * @return int|array<string, ?int>
     */
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFiltersFormContentComponent(),
                Grid::make($this->getColumns())
                    ->schema(fn (): array => $this->getWidgetsSchemaComponents($this->getWidgets())),
            ]);
    }

    public function getFiltersFormContentComponent(): EmbeddedSchema
    {
        return EmbeddedSchema::make('filtersForm');
    }
}
