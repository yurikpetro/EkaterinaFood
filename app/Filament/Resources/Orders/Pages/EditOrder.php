<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Services\OrderService;
use App\Support\Settings;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $order = $this->getRecord();
        $service = app(OrderService::class);

        return [
            Action::make('confirm')
                ->label('Подтвердить')
                ->color('success')
                ->visible($order->status === OrderStatus::New)
                ->action(fn () => $this->record->update(['status' => OrderStatus::Confirmed])),
            Action::make('inProgress')
                ->label('Готовится')
                ->color('primary')
                ->visible($order->status === OrderStatus::Confirmed)
                ->action(fn () => $this->record->update(['status' => OrderStatus::InProgress])),
            Action::make('done')
                ->label('Выполнен')
                ->color('success')
                ->visible(in_array($order->status, [OrderStatus::Confirmed, OrderStatus::InProgress], true))
                ->action(fn () => $this->record->update(['status' => OrderStatus::Done])),
            Action::make('copyWhatsApp')
                ->label('Копировать для WhatsApp')
                ->icon('heroicon-o-clipboard-document')
                ->modalHeading('Текст заказа')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Закрыть')
                ->form([
                    Textarea::make('text')
                        ->default($service->adminWhatsAppText($order))
                        ->rows(12)
                        ->extraAttributes(['onclick' => 'this.select()']),
                ]),
            Action::make('whatsapp')
                ->label('Открыть WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->url($service->whatsAppUrl(Settings::get(Settings::WHATSAPP), $service->adminWhatsAppText($order)))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
