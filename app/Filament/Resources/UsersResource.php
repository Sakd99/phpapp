<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\Users;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class UsersResource extends Resource
{
    protected static ?string $model = Users::class;

    protected static ?string $slug = 'users';

    protected static ?string $pluralLabel = 'العملاء';
    protected static ?string $navigationGroup = 'إدارة المستخدمين';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'عميل';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات العميل الأساسية')
                ->description('معلومات الحساب والاتصال')
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم الكامل')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('الايميل')
                    ->email()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('تاريخ الانشاء')
                    ->disabled()
                    ->format('YYYY-MM-DD HH:mm:ss'),
                Forms\Components\TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->maxLength(255)
                    ->dehydrated(false), // تجاهل الحقل في حال كان فارغاً
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->maxLength(255),
                Forms\Components\TextInput::make('six')
                    ->label('الجنس')
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->label('العمر')
                    ->maxLength(255),
                ])->columns(2),

                Forms\Components\Section::make('حالة الحساب')
                ->description('إعدادات حالة الحساب والتحقق')
                ->schema([
                    Forms\Components\Toggle::make('is_verified')
                        ->label('حالة التحقق')
                        ->default(false)
                        ->onIcon('heroicon-s-check-circle')
                        ->offIcon('heroicon-s-x-circle'),
                ])->columns(1),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم الكامل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('الايميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('six')
                    ->label('الجنس')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('العمر')
                    ->searchable(),

                // إضافة حقل حالة التحقق الجديد
                Tables\Columns\BooleanColumn::make('is_verified')
                    ->label('حالة التحقق')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('حالة التحقق')
                    ->options([
                        '1' => 'محقق',
                        '0' => 'غير محقق',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'view' => Pages\ViewUsers::route('/{record}'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
