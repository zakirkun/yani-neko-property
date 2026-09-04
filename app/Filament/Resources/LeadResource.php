<?php

namespace App\Filament\Resources;

use App\Enums\LeadStatus;
use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(255),
                Forms\Components\Select::make('status')->options([
                    LeadStatus::New->value => 'New', LeadStatus::Contacted->value => 'Contacted',
                    LeadStatus::Qualified->value => 'Qualified', LeadStatus::Closed->value => 'Closed',
                ])->default(LeadStatus::New->value)->required(),
            ])->columns(2),
            Forms\Components\Section::make('Enquiry')->schema([
                Forms\Components\Select::make('property_id')->relationship(
                    'property', 'title',
                    modifyQueryUsing: fn (Builder $query) => static::isAgentUser()
                        ? $query->where('agent_id', auth()->user()?->agent?->getKey() ?? 0)
                        : $query
                )->searchable()->preload(),
                Forms\Components\Select::make('agent_id')->relationship('agent', 'name')->searchable()->preload()
                    ->visible(fn (): bool => ! static::isAgentUser()),
                Forms\Components\Textarea::make('message')->rows(6)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable()->description(fn (Lead $record): string => $record->email),
            Tables\Columns\TextColumn::make('property.title')->searchable()->sortable()->limit(30),
            Tables\Columns\TextColumn::make('agent.name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('phone')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn (LeadStatus $state): string => ucfirst($state->value))
                ->color(fn (LeadStatus $state): string => match ($state) {
                    LeadStatus::New => 'danger', LeadStatus::Contacted => 'warning',
                    LeadStatus::Qualified => 'success', LeadStatus::Closed => 'gray',
                }),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                LeadStatus::New->value => 'New', LeadStatus::Contacted->value => 'Contacted',
                LeadStatus::Qualified->value => 'Qualified', LeadStatus::Closed->value => 'Closed',
            ]),
            Tables\Filters\SelectFilter::make('agent_id')->label('Agent')->relationship('agent', 'name')->visible(fn (): bool => ! static::isAgentUser()),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (static::isAgentUser()) {
            $query->where('agent_id', auth()->user()?->agent?->getKey() ?? 0);
        }

        return $query;
    }

    public static function isAgentUser(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('agent') && ! $user->hasAnyRole(['admin', 'super-admin']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view leads') ?? false;
    }

    public static function canCreate(): bool
    {
        return (auth()->user()?->can('create leads') ?? false)
            && (! static::isAgentUser() || auth()->user()?->agent !== null);
    }

    public static function canEdit(Model $record): bool
    {
        return (auth()->user()?->can('update leads') ?? false)
            && (! static::isAgentUser() || $record->agent_id === auth()->user()?->agent?->getKey());
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->can('delete leads') ?? false)
            && (! static::isAgentUser() || $record->agent_id === auth()->user()?->agent?->getKey());
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete leads') ?? false;
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListLeads::route('/'), 'create' => Pages\CreateLead::route('/create'), 'edit' => Pages\EditLead::route('/{record}/edit')];
    }
}
