<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'الخصائص';
    protected static ?string $navigationGroup = 'إدارة المزايدات';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الخاصية')
                    ->description('إدخال معلومات الخاصية التي سيتم استخدامها في المزايدات')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الخاصية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('نوع الخاصية')
                            ->options([
                                'text' => 'نص',
                                'select' => 'قائمة منسدلة',
                                'multiselect' => 'قائمة متعددة الاختيارات',
                                'number' => 'رقم',
                                'boolean' => 'نعم/لا',
                            ])
                            ->default('text')
                            ->required(),
                        Forms\Components\TagsInput::make('options')
                            ->label('الخيارات')
                            ->helperText('أدخل الخيارات المتاحة للخاصية (مطلوب للقوائم المنسدلة)')
                            ->placeholder('أدخل الخيار واضغط Enter')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'multiselect'])),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('إعدادات الخاصية')
                    ->schema([
                        Forms\Components\Toggle::make('is_required')
                            ->label('مطلوبة')
                            ->helperText('هل هذه الخاصية مطلوبة عند إنشاء مزايدة جديدة')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشطة')
                            ->helperText('هل هذه الخاصية نشطة ويمكن استخدامها')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخاصية')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الخاصية')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'text' => 'نص',
                        'select' => 'قائمة منسدلة',
                        'multiselect' => 'قائمة متعددة',
                        'number' => 'رقم',
                        'boolean' => 'نعم/لا',
                        default => $state,
                    })
                    ->colors([
                        'primary' => fn (string $state): bool => $state === 'text',
                        'success' => fn (string $state): bool => $state === 'select',
                        'warning' => fn (string $state): bool => $state === 'multiselect',
                        'danger' => fn (string $state): bool => $state === 'number',
                        'gray' => fn (string $state): bool => $state === 'boolean',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('options')
                    ->label('الخيارات')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? implode(', ', $state) : '-')
                    ->visible(fn ($record) => $record && in_array($record->type, ['select', 'multiselect'])),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('مطلوبة')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
