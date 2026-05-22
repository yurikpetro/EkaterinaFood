<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductUnit;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->label('Цена, ₽')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('₽')
                            ->helperText('Для весовых блюд — цена за 1 кг'),
                        Select::make('unit')
                            ->label('Единица')
                            ->options(ProductUnit::class)
                            ->default(ProductUnit::Portion)
                            ->required()
                            ->live()
                            ->native(false),
                        TextInput::make('min_quantity')
                            ->label(function (Get $get): string {
                                $unit = $get('unit');
                                $enum = $unit instanceof ProductUnit ? $unit : ProductUnit::tryFrom((string) $unit);

                                return $enum?->minQuantityFieldLabel() ?? 'Мин. количество';
                            })
                            ->helperText(function (Get $get): ?string {
                                $unit = $get('unit');
                                $enum = $unit instanceof ProductUnit ? $unit : ProductUnit::tryFrom((string) $unit);

                                return $enum?->minQuantityHelper();
                            })
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->default(0),
                        FileUpload::make('image_path')
                            ->label('Фото')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->maxParallelUploads(1)
                            ->fetchFileInformation(false)
                            ->downloadable(false)
                            ->openable(false)
                            ->deletable(true)
                            ->reorderable(false)
                            ->imagePreviewHeight('160')
                            ->helperText('JPG, PNG или WebP, не больше 2 МБ. После выбора файла дождитесь галочки, затем нажмите «Сохранить».')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('В продаже')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
