<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidsResource\Pages;
use App\Models\Bids;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class BidsResource extends Resource
{
    protected static ?string $model = Bids::class;

    protected static ?string $slug = 'bids';

    protected static ?string $pluralLabel = 'المزايدات';

    protected static ?string $title = 'الرئيسية';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->label('معرف المستخدم')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('product_id')
                    ->label('معرف المنتج')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bid_amount')
                    ->label('سعر المزايدة')
                    ->required()
                    ->numeric(),
                Forms\Components\Placeholder::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->content(fn(?Bids $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                Forms\Components\Placeholder::make('updated_at')
                    ->label('آخر تعديل')
                    ->content(fn(?Bids $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('معرف المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_id')
                    ->label('معرف المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid_amount')
                    ->label('سعر المزايدة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_name')
                    ->label('اسم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_price')
                    ->label('السعر الأصلي')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_description')
                    ->label('وصف المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_category')
                    ->label('تصنيف المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_stock')
                    ->label('المخزون')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_status')
                    ->label('حالة المنتج')
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
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBids::route('/create'),
            'edit' => Pages\EditBids::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
