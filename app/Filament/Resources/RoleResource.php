<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Role')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\Hidden::make('guard_name')->default('web'),
            ]),
            Forms\Components\Section::make('Permissions')->description('Select the actions users with this role may perform.')->schema([
                Forms\Components\CheckboxList::make('permissions')->relationship('permissions', 'name')
                    ->searchable()->bulkToggleable()->columns(3)->gridDirection('row'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable()->badge(),
            Tables\Columns\TextColumn::make('permissions_count')->counts('permissions')->label('Permissions')->sortable(),
            Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Users')->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view roles') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create roles') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update roles') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete roles') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete roles') ?? false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListRoles::route('/'), 'create' => Pages\CreateRole::route('/create'), 'edit' => Pages\EditRole::route('/{record}/edit')];
    }
}
