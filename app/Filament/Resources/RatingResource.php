<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'التقييمات';
    protected static ?string $navigationGroup = 'إدارة المبيعات';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $modelLabel = 'تقييم';
    protected static ?string $pluralModelLabel = 'التقييمات';


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات التقييم')
                ->description('معلومات التقييم والمراجعة')
                ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->label('معرف الطلب')
                    ->disabled(),
                Forms\Components\TextInput::make('buyer_id')
                    ->label('معرف المشتري')
                    ->disabled(),
                Forms\Components\TextInput::make('seller_id')
                    ->label('معرف البائع')
                    ->disabled(),
                Forms\Components\TextInput::make('rating')
                    ->label('التقييم')
                    ->numeric()
                    ->required()
                    ->rules(['min:1', 'max:5']), // استخدام قواعد التحقق لضبط القيم الدنيا والعليا
                Forms\Components\Textarea::make('review')
                    ->label('المراجعة')
                    ->rows(5),
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
                    ->label('معرف التقييم')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('معرف الطلب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_id')
                    ->label('معرف المشتري')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller_id')
                    ->label('معرف البائع')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('التقييم')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('review')
                    ->label('المراجعة')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('التقييم')
                    ->options([
                        '1' => 'نجمة واحدة ★',
                        '2' => 'نجمتان ★★',
                        '3' => 'ثلاث نجوم ★★★',
                        '4' => 'أربع نجوم ★★★★',
                        '5' => 'خمس نجوم ★★★★★',
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
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'view' => Pages\ViewRating::route('/{record}'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
