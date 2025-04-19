<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'الفئة';
    protected static ?string $pluralModelLabel = 'الفئات';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الفئة')
                ->description('أدخل معلومات الفئة الأساسية')
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الفئة')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('صورة الفئة')
                    ->image()
                    ->directory('categories')
                    ->maxSize(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('percentage') // حقل النسبة المئوية المعدل
                ->label('النسبة المئوية')
                    ->numeric()
                    ->required()
                    ->rules(['min:0', 'max:100'])
                    ->default(0),
                Forms\Components\TextInput::make('id')
                    ->label('معرف الفئة')
                    ->disabled()
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditCategory),
                ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('معرف الفئة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover']),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفئة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('percentage') // عرض النسبة المئوية في الجدول
                ->label('النسبة المئوية')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('subCategories.count')
                    ->label('عدد الفئات الفرعية')
                    ->counts('subCategories'),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubCategoriesRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
