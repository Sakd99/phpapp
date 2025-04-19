<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms\Components\Placeholder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $slug = 'banners';

    protected static ?string $pluralLabel = 'البانر الاعلاني';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'banner_title';
    protected static ?string $modelLabel = 'بانر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات البانر')
                ->description('أدخل معلومات البانر الأساسية')
                ->schema([
                Forms\Components\FileUpload::make('banner_image')
                    ->label('صورة البانر')
                    ->required(),
                Forms\Components\TextInput::make('banner_title')
                    ->label('عنوان البانر')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('banner_address')
                    ->label('رابط البانر')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('banner_description')
                    ->label('وصف البانر')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('banner_date')
                    ->label('تاريخ البانر')
                    ->required()
                    ->maxLength(255),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('banner_image')
                    ->label('صورة البانر')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover']),
                Tables\Columns\TextColumn::make('banner_title')
                    ->label('عنوان البانر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('banner_address')
                    ->label('عنوان البانر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('banner_description')
                    ->label('وصف البانر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('banner_date')
                    ->label('تاريخ البانر')
                    ->searchable()
                    ->sortable(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'view' => Pages\ViewBanner::route('/{record}'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
