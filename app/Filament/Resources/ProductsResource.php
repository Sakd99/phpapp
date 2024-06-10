<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $slug = 'products';

    protected static ?string $pluralLabel = 'المنتجات';

    protected static ?string $title = 'الرئيسية';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->label('اسم المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_description')
                    ->label('وصف المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_price')
                    ->label('سعر المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('product_image1')
                    ->label('صورة المنتج الاولى')
                    ->columnSpan(2)
                    ->visibility('private')
                    ->image(),
                Forms\Components\FileUpload::make('product_image2')
                    ->label('صورة المنتج الثانية')
                    ->columnSpan(2)
                    ->visibility('private')
                    ->image(),
                Forms\Components\FileUpload::make('product_image3')
                    ->label('صورة المنتج الثالثة')
                    ->columnSpan(2)
                    ->visibility('private')
                    ->image(),
                Forms\Components\TextInput::make('prodeuct_discount')
                    ->label('خصم المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_category')
                    ->label('تصنيف المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_stock')
                    ->label('المخزون')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_status')
                    ->label('حالة المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_rating')
                    ->label('تقييم المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_review')
                    ->label('مراجعة المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('prodeuct_color')
                    ->label('لون المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_size')
                    ->label('حجم المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_weight')
                    ->label('وزن المنتج')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_dimension')
                    ->label('الابعاد')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('اسم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_description')
                    ->label('وصف المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_price')
                    ->label('سعر المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('product_image1')
                    ->label('صورة المنتج الاولى'),
                Tables\Columns\ImageColumn::make('product_image2')
                    ->label('صورة المنتج الثانية'),
                Tables\Columns\ImageColumn::make('product_image3')
                    ->label('صورة المنتج الثالثة'),
                Tables\Columns\TextColumn::make('prodeuct_discount')
                    ->label('خصم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_category')
                    ->label('تصنيف المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_stock')
                    ->label('المخزون')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_status')
                    ->label('حالة المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_rating')
                    ->label('تقييم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_review')
                    ->label('مراجعة المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prodeuct_color')
                    ->label('لون المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_size')
                    ->label('حجم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_weight')
                    ->label('وزن المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_dimension')
                    ->label('الابعاد')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
