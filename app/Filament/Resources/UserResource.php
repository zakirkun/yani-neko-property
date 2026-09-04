<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')->password()->revealable()->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Forms\Components\CheckboxList::make('roles')->relationship(
                    'roles',
                    'name',
                    modifyQueryUsing: fn (Builder $query) => auth()->user()?->hasRole('super-admin')
                        ? $query
                        : $query->where('name', '!=', 'super-admin')
                )->columns(2)->searchable()->bulkToggleable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('roles.name')->label('Roles')->badge()->separator(','),
            Tables\Columns\TextColumn::make('agent.name')->label('Agent profile')->toggleable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            Tables\Filters\SelectFilter::make('roles')->relationship('roles', 'name')->multiple()->preload(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view users') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create users') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return (auth()->user()?->can('update users') ?? false)
            && (! $record->hasRole('super-admin') || auth()->user()?->hasRole('super-admin'));
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->can('delete users') ?? false)
            && ! $record->hasRole('super-admin');
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListUsers::route('/'), 'create' => Pages\CreateUser::route('/create'), 'edit' => Pages\EditUser::route('/{record}/edit')];
    }
}
