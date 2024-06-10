<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Models\Orders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $slug = 'orders';

    protected static ?string $pluralLabel = 'الطلبات';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ايدي الطلب')
                    ->disabled(),
                Forms\Components\TextInput::make('order_number')
                    ->label('رقم الطلب')
                    ->disabled(),
                Forms\Components\TextInput::make('order_status')
                    ->label('حالة الطلب')
                    ->required(),
                Forms\Components\Textarea::make('order_note')
                    ->label('ملاحظات الطلب'),
                Forms\Components\TextInput::make('buyer_name')
                    ->label('اسم العميل')
                    ->required(),
                Forms\Components\TextInput::make('buyer_email')
                    ->label('بريد العميل')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('buyer_phone')
                    ->label('هاتف العميل')
                    ->required(),
                Forms\Components\TextInput::make('buyer_address')
                    ->label('عنوان العميل')
                    ->required(),
                Forms\Components\TextInput::make('buyer_city')
                    ->label('مدينة العميل')
                    ->required(),
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Forms\Components\TextInput::make('product_id')
                            ->label('معرف المنتج')
                            ->disabled(),
                        Forms\Components\TextInput::make('product_name')
                            ->label('اسم المنتج')
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('السعر')
                            ->disabled(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ايدي الطلب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('اسم العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_email')
                    ->label('بريد العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_phone')
                    ->label('هاتف العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_address')
                    ->label('عنوان العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_city')
                    ->label('مدينة العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
