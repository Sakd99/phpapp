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
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_image')
                    ->label('صورة البانر'),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
