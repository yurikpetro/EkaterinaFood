<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                            ->suffix('₽'),
                        TextInput::make('unit')
                            ->label('Единица')
                            ->required()
                            ->default('порция')
                            ->maxLength(50),
                        TextInput::make('min_quantity')
                            ->label('Мин. количество')
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
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public'),
                        Toggle::make('is_active')
                            ->label('В продаже')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
