<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmenityResource\Pages;
use App\Models\Amenity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AmenityResource extends Resource
{
    protected static ?string $model = Amenity::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, Forms\Set $set) => $set('slug', Str::slug($state ?? ''))),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('properties_count')->counts('properties')->label('Properties')->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view amenities') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create amenities') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update amenities') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete amenities') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete amenities') ?? false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAmenities::route('/'), 'create' => Pages\CreateAmenity::route('/create'), 'edit' => Pages\EditAmenity::route('/{record}/edit')];
    }
}
