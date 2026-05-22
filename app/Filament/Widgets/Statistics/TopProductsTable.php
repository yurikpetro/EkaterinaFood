<?php

namespace App\Filament\Widgets\Statistics;

use App\Filament\Widgets\Statistics\Concerns\InteractsWithStatisticsFilters;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProductsTable extends TableWidget
{
    use InteractsWithStatisticsFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 5;

    protected static ?string $heading = 'Популярные блюда';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->statistics()->topProductsQuery($this->statisticsFilters()))
            ->columns([
                TextColumn::make('product_name')
                    ->label('Блюдо')
                    ->searchable(),
                TextColumn::make('total_quantity')
                    ->label('Кол-во')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('Сумма')
                    ->formatStateUsing(fn (int $state): string => self::formatMoney($state))
                    ->sortable(),
            ])
            ->defaultSort('total_quantity', 'desc')
            ->paginated([10, 15, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Нет продаж за период')
            ->emptyStateDescription('Измените фильтры или дождитесь заказов с сайта.');
    }
}
