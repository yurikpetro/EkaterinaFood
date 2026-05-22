<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\Settings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('№')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Клиент')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('delivery_type')
                    ->label('Получение')
                    ->badge()
                    ->formatStateUsing(fn (DeliveryType $state): string => $state->getLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->color(fn (OrderStatus $state): string => $state->getColor()),
                TextColumn::make('total')
                    ->label('Сумма')
                    ->formatStateUsing(fn (int $state): string => number_format($state, 0, ',', ' ') . ' ₽')
                    ->sortable(),
                TextColumn::make('desired_date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('today')
                    ->label('Сегодня')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Filter::make('new')
                    ->label('Новые')
                    ->query(fn (Builder $query): Builder => $query->where('status', OrderStatus::New)),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Подтвердить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Подтвердить')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::New)
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Confirmed])),
                Action::make('done')
                    ->label('Готово')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->iconButton()
                    ->tooltip('Готово')
                    ->visible(fn (Order $record): bool => in_array($record->status, [OrderStatus::Confirmed, OrderStatus::InProgress], true))
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Done])),
                ActionGroup::make([
                    Action::make('copyWhatsApp')
                        ->label('Копировать для WhatsApp')
                        ->icon('heroicon-o-clipboard-document')
                        ->modalHeading('Текст для WhatsApp')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Закрыть')
                        ->form(fn (Order $record) => [
                            Textarea::make('text')
                                ->label('')
                                ->default(app(OrderService::class)->adminWhatsAppText($record))
                                ->rows(12)
                                ->extraAttributes(['onclick' => 'this.select()']),
                        ]),
                    Action::make('openWhatsApp')
                        ->label('Открыть WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->url(function (Order $record): string {
                            $service = app(OrderService::class);

                            return $service->whatsAppUrl(
                                Settings::get(Settings::WHATSAPP),
                                $service->adminWhatsAppText($record),
                            );
                        })
                        ->openUrlInNewTab(),
                    EditAction::make()->label('Изменить'),
                ])
                    ->iconButton()
                    ->tooltip('Действия'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Удалить выбранные'),
                ]),
            ])
            ->emptyStateHeading('Заказов пока нет')
            ->emptyStateDescription('Новые заказы с сайта появятся здесь автоматически.');
    }
}
