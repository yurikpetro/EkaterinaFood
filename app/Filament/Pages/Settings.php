<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Settings as AppSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Настройки сайта';

    protected static ?string $title = 'Настройки сайта';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'settings';

    /** @var array<string, string|null> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->data = array_merge(
            AppSettings::defaults(),
            Setting::allKeyed(),
        );
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контакты')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Телефон для сайта')
                            ->tel(),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp (только цифры, с 7)')
                            ->helperText('Например: 79001234567'),
                        TextInput::make('telegram')
                            ->label('Ссылка Telegram')
                            ->url()
                            ->placeholder('https://t.me/username'),
                        TextInput::make('pickup_address')
                            ->label('Адрес самовывоза')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Тексты на главной')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Заголовок')
                            ->columnSpanFull(),
                        TextInput::make('hero_subtitle')
                            ->label('Подзаголовок')
                            ->columnSpanFull(),
                        Textarea::make('about_text')
                            ->label('О нас')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        foreach ($this->data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Настройки сохранены')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Сохранить')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }
}
