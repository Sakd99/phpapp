<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidsResource\Pages;
use App\Models\Bids;
use App\Models\Category;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BidsResource extends Resource
{
    protected static ?string $model = Bids::class;

    protected static ?string $slug = 'bids';

    protected static ?string $pluralLabel = 'المزايدات';
    protected static ?string $navigationGroup = 'إدارة المزايدات';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $recordTitleAttribute = 'product_name';
    protected static ?string $modelLabel = 'مزايدة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معرض صور المنتج')
                ->description('صور المنتج المعروض للمزايدة')
                ->schema([
                    Forms\Components\ViewField::make('product_images')
                        ->label('') // بدون عنوان
                        ->view('filament.resources.bids-resource.product-images'),
                ])
                ->collapsible()
                ->visible(fn ($livewire) => $livewire instanceof Pages\ViewBids),
                Forms\Components\Section::make('معلومات المنتج')
                ->description('معلومات المنتج المعروض للمزايدة')
                ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->label('اسم المنتج')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('product_description')
                    ->label('وصف المنتج')
                    ->required(),
                Forms\Components\TextInput::make('initial_price')
                    ->label('السعر الأولي')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('current_price')
                    ->label('السعر الحالي')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('shipping_option')
                    ->label('خيار التوصيل')
                    ->options([
                        'seller' => 'التوصيل على البائع',
                        'buyer' => 'التوصيل على المشتري',
                    ])
                    ->default('buyer')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('وقت انتهاء المزايدة')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('حالة المزايدة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'accepted' => 'مقبول',
                        'sold' => 'مباع',
                        'rejected' => 'مرفوض',
                    ])
                    ->required(),
                Forms\Components\Select::make('product_condition')
                    ->label('حالة المنتج')
                    ->options([
                        'new' => 'جديد',
                        'used' => 'مستعمل',
                    ])
                    ->default('new')
                    ->required(),
                ])->columns(2),

                Forms\Components\Section::make('صور المنتج')
                ->description('صور المنتج المعروض للمزايدة')
                ->schema([
                Forms\Components\FileUpload::make('product_image1')
                    ->label('صورة المنتج الأولى')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('bids')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('600')
                    ->imageResizeTargetHeight('600')
                    ->required(),
                Forms\Components\FileUpload::make('product_image2')
                    ->label('صورة المنتج الثانية')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('bids')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('600')
                    ->imageResizeTargetHeight('600')
                    ->required(),
                Forms\Components\FileUpload::make('product_image3')
                    ->label('صورة المنتج الثالثة')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('bids')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('600')
                    ->imageResizeTargetHeight('600')
                    ->required(),
                ])->columns(3),

                Forms\Components\Section::make('معلومات التصنيف والشحن')
                ->description('معلومات التصنيف والشحن للمنتج')
                ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('الفئة')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('shipping_address')
                    ->label('عنوان الشحن')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_origin')
                    ->label('منشأ المنتج (اسم الدولة)')
                    ->placeholder('مثال: العراق، الصين، اليابان، إلخ')
                    ->default('العراق')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Placeholder::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->content(fn(?Bids $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                Forms\Components\Placeholder::make('updated_at')
                    ->label('آخر تعديل')
                    ->content(fn(?Bids $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ])->columns(2),

                Forms\Components\Section::make('خصائص المنتج')
                ->description('اختر الخصائص المناسبة للمنتج')
                ->schema(function () {
                    // جلب جميع الخصائص النشطة
                    $properties = Property::where('is_active', true)->get();

                    // إنشاء حقول ديناميكية لكل خاصية
                    $fields = [];

                    foreach ($properties as $property) {
                        // إنشاء حقل مناسب بناءً على نوع الخاصية
                        $field = null;

                        switch ($property->type) {
                            case 'text':
                                $field = Forms\Components\TextInput::make('property_' . $property->id)
                                    ->label($property->name)
                                    ->required($property->is_required)
                                    ->maxLength(255);
                                break;

                            case 'select':
                                $field = Forms\Components\Select::make('property_' . $property->id)
                                    ->label($property->name)
                                    ->options(collect($property->options)->mapWithKeys(fn ($option) => [$option => $option]))
                                    ->required($property->is_required);
                                break;

                            case 'multiselect':
                                $field = Forms\Components\Select::make('property_' . $property->id)
                                    ->label($property->name)
                                    ->multiple()
                                    ->options(collect($property->options)->mapWithKeys(fn ($option) => [$option => $option]))
                                    ->required($property->is_required);
                                break;

                            case 'number':
                                $field = Forms\Components\TextInput::make('property_' . $property->id)
                                    ->label($property->name)
                                    ->numeric()
                                    ->required($property->is_required);
                                break;

                            case 'boolean':
                                $field = Forms\Components\Toggle::make('property_' . $property->id)
                                    ->label($property->name)
                                    ->required($property->is_required);
                                break;
                        }

                        if ($field) {
                            $fields[] = $field;
                        }
                    }

                    // إذا لم تكن هناك خصائص، أضف رسالة توضيحية
                    if (empty($fields)) {
                        $fields[] = Forms\Components\Placeholder::make('no_properties')
                            ->label('لا توجد خصائص')
                            ->content('لم يتم إنشاء أي خصائص بعد. يمكنك إنشاء خصائص من قسم الخصائص في لوحة التحكم.');
                    }

                    return $fields;
                })
                ->columns(2)
                ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('ايدي المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('اسم الفئة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('اسم المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_price')
                    ->label('السعر الأولي')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('السعر الحالي')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('حالة المزايدة')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'sold' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => 'قيد الانتظار',
                            'accepted' => 'مقبول',
                            'sold' => 'مباع',
                            'rejected' => 'مرفوض',
                            default => $state,
                        };
                    }),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('وقت انتهاء المزايدة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_option')
                    ->label('خيار التوصيل')
                    ->getStateUsing(function ($record) {
                        return $record->shipping_option === 'seller' ? 'التوصيل على البائع' : 'التوصيل على المشتري';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_image1')
                    ->label('صورة المنتج')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($state)) return 'لا توجد صورة';

                        // تعديل مسار الصورة ليعمل بشكل صحيح على سي بانل
                        // استخدام المسار المباشر مع إضافة "storage/app/public"
                        $imageUrl = asset('storage/app/public/' . $state);

                        return '<img src="' . $imageUrl . '" alt="صورة المنتج" class="w-20 h-20 object-cover rounded-lg">';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('shipping_address')
                    ->label('عنوان الشحن')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('properties_count')
                    ->label('عدد الخصائص')
                    ->counts('properties')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة المزايدة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'accepted' => 'مقبول',
                        'sold' => 'مباع',
                        'rejected' => 'مرفوض',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBids::route('/create'),
            'view' => Pages\ViewBids::route('/{record}'),
            'edit' => Pages\EditBids::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::id();

        // استخراج بيانات الخصائص من النموذج
        $propertyData = [];
        $properties = Property::where('is_active', true)->get();

        foreach ($properties as $property) {
            $key = 'property_' . $property->id;

            if (isset($data[$key])) {
                $propertyData[$property->id] = $data[$key];
                // إزالة البيانات من مصفوفة البيانات الأصلية
                unset($data[$key]);
            }
        }

        // تخزين بيانات الخصائص في حقل JSON
        $data['properties_data'] = json_encode($propertyData);

        return $data;
    }
}
