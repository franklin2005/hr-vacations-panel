<?php

namespace App\Filament\Employee\Resources\VacationRequests;

use App\Filament\Employee\Resources\VacationRequests\Pages\CreateVacationRequest;
use App\Filament\Employee\Resources\VacationRequests\Pages\EditVacationRequest;
use App\Filament\Employee\Resources\VacationRequests\Pages\ListVacationRequests;
use App\Filament\Employee\Resources\VacationRequests\Schemas\VacationRequestForm;
use App\Filament\Employee\Resources\VacationRequests\Tables\VacationRequestsTable;
use App\Models\VacationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class VacationRequestResource extends Resource
{
    protected static ?string $model = VacationRequest::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static UnitEnum|string|null $navigationGroup = 'My HR';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Vacation Requests';

    public static function form(Schema $schema): Schema
    {
        return VacationRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VacationRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVacationRequests::route('/'),
            'create' => CreateVacationRequest::route('/create'),
            'edit' => EditVacationRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (! $user?->employee_id) {
            return parent::getEloquentQuery()->whereRaw('0 = 1');
        }

        return parent::getEloquentQuery()->where('employee_id', $user->employee_id);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Auth::user()?->employee_id;
    }
}
