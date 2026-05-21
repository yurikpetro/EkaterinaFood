<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Services\OrderService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заказ')
                    ->schema([
                        TextInput::make('number')
                            ->label('Номер')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => app(OrderService::class)->generateNumber()),
                        Select::make('status')
                            ->label('Статус')
                            ->options(OrderStatus::class)
                            ->required()
                            ->native(false),
                        TextInput::make('total')
                            ->label('Сумма, ₽')
                            ->numeric()
                            ->suffix('₽')
                            ->required(),
                    ])
                    ->columns(3),
                Section::make('Клиент')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Имя')
                            ->required(),
                        TextInput::make('customer_phone')
                            ->label('Телефон')
                            ->tel()
                            ->required(),
                        Select::make('delivery_type')
                            ->label('Способ получения')
                            ->options(DeliveryType::class)
                            ->required()
                            ->native(false),
                        TextInput::make('address')
                            ->label('Адрес доставки')
                            ->columnSpanFull(),
                        DatePicker::make('desired_date')
                            ->label('Желаемая дата')
                            ->native(false),
                        TextInput::make('desired_time')
                            ->label('Желаемое время'),
                        Textarea::make('comment')
                            ->label('Комментарий')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Состав заказа')
                    ->schema([
                        Placeholder::make('items_preview')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                if (! $record || ! $record->items()->exists()) {
                                    return new HtmlString('<p class="text-sm text-gray-500">Позиции появятся после сохранения заказа с сайта или добавьте вручную через сумму.</p>');
                                }

                                $rows = $record->items->map(function ($item) {
                                    $price = number_format($item->unit_price, 0, ',', ' ');
                                    $subtotal = number_format($item->subtotal, 0, ',', ' ');

                                    return "<tr class=\"border-b\"><td class=\"py-2 pr-4\">{$item->product_name}</td><td class=\"py-2 pr-4\">{$item->quantity} {$item->unit}</td><td class=\"py-2 pr-4\">{$price} ₽</td><td class=\"py-2 font-medium\">{$subtotal} ₽</td></tr>";
                                })->implode('');

                                return new HtmlString(
                                    '<table class="w-full text-sm"><thead><tr class="text-left text-gray-500"><th class="pb-2">Блюдо</th><th class="pb-2">Кол-во</th><th class="pb-2">Цена</th><th class="pb-2">Сумма</th></tr></thead><tbody>' . $rows . '</tbody></table>'
                                );
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
