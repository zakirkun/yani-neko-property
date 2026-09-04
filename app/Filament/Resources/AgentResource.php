<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Models\Agent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Agent profile')->schema([
                Forms\Components\FileUpload::make('avatar')->image()->avatar()->directory('agents')->disk('public')->columnSpanFull(),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->searchable()->preload()->label('User account'),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(255),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\Textarea::make('bio')->rows(5)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('avatar')->disk('public')->circular(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('phone')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('properties_count')->counts('properties')->label('Properties')->sortable(),
            Tables\Columns\TextColumn::make('leads_count')->counts('leads')->label('Leads')->sortable()->toggleable(),
            Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view agents') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create agents') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update agents') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete agents') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete agents') ?? false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAgents::route('/'), 'create' => Pages\CreateAgent::route('/create'), 'edit' => Pages\EditAgent::route('/{record}/edit')];
    }
}
