<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyTypeResource\Pages;
use App\Models\PropertyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyTypeResource extends Resource
{
    protected static ?string $model = PropertyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, Forms\Set $set) => $set('slug', Str::slug($state ?? ''))),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->rows(5)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('properties_count')->counts('properties')->label('Properties')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view property types') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create property types') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update property types') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->can('delete property types') ?? false)
            && ! $record->properties()->exists();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPropertyTypes::route('/'), 'create' => Pages\CreatePropertyType::route('/create'), 'edit' => Pages\EditPropertyType::route('/{record}/edit')];
    }
}
