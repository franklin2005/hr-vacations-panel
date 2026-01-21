<?php

namespace App\Filament\Employee\Resources\VacationRequests\Tables;

use App\Models\VacationRequest;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VacationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('start_date')
                    ->label('Start')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('requested_days')
                    ->label('Days')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label('Reviewed by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reviewed_at')
                    ->label('Reviewed at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('year')
                    ->label('Year')
                    ->options(function () {
                        $employeeId = auth()->user()?->employee_id;

                        if (! $employeeId) {
                            return [];
                        }

                        return VacationRequest::query()
                            ->where('employee_id', $employeeId)
                            ->whereNotNull('year')
                            ->orderByDesc('year')
                            ->distinct()
                            ->pluck('year', 'year')
                            ->toArray();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('cancel')
                    ->label('Cancel')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'rejected',
                            'reason' => 'Cancelled by employee',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->defaultSort('start_date', 'desc');
    }
}
