<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCategoryResource\Pages;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'القوائم الفرعية';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'قائمة فرعية';
    protected static ?string $pluralModelLabel = 'القوائم الفرعية';


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات القائمة الفرعية')
                ->description('أدخل معلومات القائمة الفرعية والفئات المرتبطة بها')
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الفئة الفرعية')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('صورة الفئة الفرعية')
                    ->directory('subcategories')
                    ->image()
                    ->maxSize(2048),
                Forms\Components\Select::make('category_id')
                    ->label('الفئة الرئيسية')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->label('الفئة الفرعية الرئيسية')
                    ->options(SubCategory::all()->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفئة الفرعية')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover']),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('الفئة الرئيسية'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('الفئة الفرعية الرئيسية'),
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
                    ->label('الفئة الرئيسية')
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
            'index' => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'view' => Pages\ViewSubCategory::route('/{record}'),
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
