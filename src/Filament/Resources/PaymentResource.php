<?php

namespace Lipe\Payment\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Lipe\Payment\Models\Order;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Платежи';

    protected static ?string $modelLabel = 'Платеж';

    protected static ?string $pluralModelLabel = 'Платежи';

    protected static ?string $navigationGroup = 'Платежи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('payment_id')
                            ->label('ID платежа')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Сумма')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Select::make('payment_gateway')
                            ->label('Платежная система')
                            ->options([
                                'click' => 'Click',
                                'payme' => 'Payme',
                                'ipak_yuli' => 'Ipak Yuli',
                            ])
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'pending' => 'Ожидает оплаты',
                                'paid' => 'Оплачен',
                                'cancelled' => 'Отменен',
                                'failed' => 'Ошибка',
                            ])
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_url')
                            ->label('URL оплаты')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Сумма')
                    ->money('UZS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_gateway')
                    ->label('Платежная система')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'click' => 'Click',
                        'payme' => 'Payme',
                        'ipak_yuli' => 'Ipak Yuli',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'cancelled' => 'Отменен',
                        'failed' => 'Ошибка',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_gateway')
                    ->label('Платежная система')
                    ->options([
                        'click' => 'Click',
                        'payme' => 'Payme',
                        'ipak_yuli' => 'Ipak Yuli',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('От'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
} 