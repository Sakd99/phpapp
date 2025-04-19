<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Models\Orders;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $slug = 'orders';

    protected static ?string $pluralLabel = 'الطلبات';
    protected static ?string $navigationGroup = 'إدارة المبيعات';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $recordTitleAttribute = 'order_number';
    protected static ?string $modelLabel = 'طلب';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الطلب الأساسية')
                ->description('معلومات الطلب وحالته')
                ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ايدي الطلب')
                    ->disabled(),
                Forms\Components\TextInput::make('order_number')
                    ->label('رقم الطلب')
                    ->disabled(),
                Forms\Components\Select::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'Pending' => 'قيد الانتظار',
                        'Completed' => 'مكتمل',
                        'Canceled' => 'ملغى',
                        'InProgress' => 'قيد التوصيل',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('order_note')
                    ->label('ملاحظات الطلب'),
                ])->columns(2),

                Forms\Components\Section::make('معلومات العميل')
                ->description('بيانات المشتري')
                ->schema([

                // معلومات المشتري
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
                ])->columns(2),

                Forms\Components\Section::make('معلومات البائع')
                ->description('بيانات البائع')
                ->schema([

                // معلومات البائع
                Forms\Components\TextInput::make('seller.name')
                    ->label('اسم البائع')
                    ->disabled(),
                Forms\Components\TextInput::make('seller.email')
                    ->label('بريد البائع')
                    ->disabled(),
                Forms\Components\TextInput::make('seller.phone')
                    ->label('هاتف البائع')
                    ->disabled(),
                ])->columns(3),

                Forms\Components\Section::make('معلومات المنتج والمزايدة')
                ->description('بيانات المنتج والمزايدة')
                ->schema([

                // معلومات المنتج والمزايدة
                Forms\Components\TextInput::make('bid.product_name')
                    ->label('اسم المنتج')
                    ->disabled(),
                Forms\Components\TextInput::make('bid.initial_price')
                    ->label('السعر الأولي')
                    ->disabled(),
                Forms\Components\TextInput::make('bid.current_price')
                    ->label('السعر الحالي')
                    ->disabled(),
                Forms\Components\TextInput::make('bid.end_time')
                    ->label('وقت انتهاء المزايدة')
                    ->disabled(),
                ])->columns(2),

                // عناصر الطلب
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
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ايدي الطلب')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->color(function ($state) {
                        return match ($state) {
                            'Pending' => 'warning',
                            'Completed' => 'success',
                            'Canceled' => 'danger',
                            'InProgress' => 'info',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'Pending' => 'قيد الانتظار',
                            'Completed' => 'مكتمل',
                            'Canceled' => 'ملغى',
                            'InProgress' => 'قيد التوصيل',
                            default => $state,
                        };
                    }),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('اسم العميل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_email')
                    ->label('بريد العميل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_phone')
                    ->label('هاتف العميل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_address')
                    ->label('عنوان العميل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_city')
                    ->label('مدينة العميل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                // معلومات البائع
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('اسم البائع')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.email')
                    ->label('بريد البائع')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.phone')
                    ->label('هاتف البائع')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                // معلومات المنتج والمزايدة
                Tables\Columns\TextColumn::make('bid.product_name')
                    ->label('اسم المنتج')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid.initial_price')
                    ->label('السعر الأولي')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid.current_price')
                    ->label('السعر الحالي')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid.end_time')
                    ->label('وقت انتهاء المزايدة')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'Pending' => 'قيد الانتظار',
                        'Completed' => 'مكتمل',
                        'Canceled' => 'ملغى',
                        'InProgress' => 'قيد التوصيل',
                    ]),

                // الفلترة الزمنية الجديدة
                SelectFilter::make('date_filter')
                    ->label('فلترة حسب الفترة الزمنية')
                    ->options([
                        'today' => 'اليوم',
                        'week' => 'هذا الأسبوع',
                        'month' => 'هذا الشهر',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'today') {
                            $query->whereDate('created_at', now()->toDateString());
                        } elseif ($state === 'week') {
                            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        } elseif ($state === 'month') {
                            $query->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('print_pdf')
                        ->label('طباعة PDF')
                        ->url(fn ($record) => url('orders/' . $record->id . '/export-pdf'))
                        ->icon('heroicon-o-printer')
                        ->openUrlInNewTab(),
                    Action::make('change_status')
                    ->label('تغيير الحالة')
                    ->action(function ($record, $data) {
                        // تحديث حالة الطلب
                        $record->update(['order_status' => $data['order_status']]);

                        // إرسال الإشعار
                        $message = "تم تحديث حالة طلبك إلى: {$record->order_status}";
                        $record->notify(new \App\Notifications\OrderStatusNotification($record, $message));

                        // إظهار إشعار النجاح
                        \Filament\Notifications\Notification::make()
                            ->title('تم تحديث الحالة')
                            ->body('تم تحديث حالة الطلب وإرسال الإشعار بنجاح.')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Select::make('order_status')
                            ->label('حالة الطلب')
                            ->options([
                                'Pending' => 'قيد الانتظار',
                                'Completed' => 'مكتمل',
                                'Canceled' => 'ملغى',
                                'InProgress' => 'قيد التوصيل',
                            ])
                            ->required(),
                    ])
                    ->icon('heroicon-o-pencil'), // استخدام أيقونة موجودة
                ])
                ->tooltip('الإجراءات')
                ->icon('heroicon-m-ellipsis-vertical')
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
            'view' => Pages\ViewOrders::route('/{record}'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
