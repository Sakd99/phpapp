<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeBannerResource\Pages;
use App\Models\HomeBanner;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class HomeBannerResource extends Resource
{
    protected static ?string $model = HomeBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'بنر هوم موبايل';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $modelLabel = 'بنر هوم';
    protected static ?string $pluralModelLabel = 'بنرات هوم';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات البنر')
                ->description('أدخل معلومات البنر والفئات المرتبطة به')
                ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('الفئة')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('subcategory_id')
                    ->label('الفئة الفرعية')
                    ->options(SubCategory::whereNull('parent_id')->pluck('name', 'id')) // الفئات الفرعية الرئيسية فقط
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('subsubcategory_id')
                    ->label('الفئة الفرعية الفرعية')
                    ->options(SubCategory::whereNotNull('parent_id')->pluck('name', 'id')) // الفئات الفرعية الفرعية فقط
                    ->searchable()
                    ->nullable(),

                Forms\Components\FileUpload::make('image')
                    ->label('صورة')
                    ->disk('public') // استخدام القرص 'public'
                    ->image()
                    ->maxSize(2048),



                Forms\Components\TextInput::make('priority')
                    ->label('الأولوية')
                    ->numeric()
                    ->default(0)
                    ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->striped()
            ->defaultSort('priority', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('الفئة'),
                Tables\Columns\TextColumn::make('subCategory.name')
                    ->label('الفئة الفرعية'),
                Tables\Columns\TextColumn::make('subSubCategory.name')
                    ->label('الفئة الفرعية الفرعية'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover']),
                Tables\Columns\TextColumn::make('priority')
                    ->label('الأولوية')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('الفئة')
                    ->relationship('category', 'name'),
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
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->tooltip('الإجراءات')
                ->icon('heroicon-m-ellipsis-vertical')
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeBanners::route('/'),
            'create' => Pages\CreateHomeBanner::route('/create'),
            'view' => Pages\ViewHomeBanner::route('/{record}'),
            'edit' => Pages\EditHomeBanner::route('/{record}/edit'),
        ];
    }
}
