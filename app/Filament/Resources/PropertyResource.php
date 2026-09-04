<?php

namespace App\Filament\Resources;

use App\Enums\PropertyPurpose;
use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Property details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('property_type_id')->relationship('propertyType', 'name')->searchable()->preload()->required(),
                Forms\Components\Select::make('agent_id')->relationship('agent', 'name')->searchable()->preload()
                    ->visible(fn (): bool => ! static::isAgentUser()),
                Forms\Components\Select::make('purpose')->options([
                    PropertyPurpose::Sale->value => 'Sale', PropertyPurpose::Rent->value => 'Rent',
                ])->required(),
                Forms\Components\TextInput::make('price')->numeric()->prefix('$')->minValue(0)->required(),
                Forms\Components\Textarea::make('description')->rows(6)->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Publication')->schema([
                Forms\Components\Select::make('status')->options([
                    PropertyStatus::Draft->value => 'Draft', PropertyStatus::Published->value => 'Published',
                    PropertyStatus::Sold->value => 'Sold', PropertyStatus::Rented->value => 'Rented',
                ])->default(PropertyStatus::Draft->value)->required(),
                Forms\Components\DateTimePicker::make('published_at')->seconds(false)
                    ->required(fn (Forms\Get $get): bool => $get('status') === PropertyStatus::Published->value),
                Forms\Components\Toggle::make('is_featured')->label('Featured property')->default(false),
            ])->columns(3),
            Forms\Components\Section::make('Location')->schema([
                Forms\Components\TextInput::make('address')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('city')->required()->maxLength(255),
                Forms\Components\TextInput::make('state')->maxLength(255),
                Forms\Components\TextInput::make('postcode')->maxLength(20),
                Forms\Components\TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90),
                Forms\Components\TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180),
            ])->columns(3)->collapsed(),
            Forms\Components\Section::make('Features and amenities')->schema([
                Forms\Components\TextInput::make('bedrooms')->numeric()->integer()->minValue(0)->default(0)->required(),
                Forms\Components\TextInput::make('bathrooms')->numeric()->integer()->minValue(0)->default(0)->required(),
                Forms\Components\TextInput::make('area')->numeric()->minValue(0)->suffix('sq ft'),
                Forms\Components\Select::make('amenities')->relationship('amenities', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
            ])->columns(3),
            Forms\Components\Section::make('Images')->schema([
                Forms\Components\Repeater::make('images')->relationship()->orderColumn('sort_order')->reorderable()->schema([
                    Forms\Components\FileUpload::make('path')->image()->directory('properties')->disk('public')->required()->columnSpanFull(),
                    Forms\Components\TextInput::make('alt_text')->label('Alternative text')->maxLength(255),
                    Forms\Components\Toggle::make('is_primary')->label('Primary image'),
                ])->columns(2)->addActionLabel('Add image')->defaultItems(0)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('images.path')->label('Image')->disk('public')->circular()->limit(1),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn (Property $record): string => $record->city),
            Tables\Columns\TextColumn::make('propertyType.name')->label('Type')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('agent.name')->sortable()->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('purpose')->badge()->formatStateUsing(fn (PropertyPurpose $state): string => ucfirst($state->value)),
            Tables\Columns\TextColumn::make('price')->money('USD')->sortable(),
            Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn (PropertyStatus $state): string => ucfirst($state->value))
                ->color(fn (PropertyStatus $state): string => match ($state) {
                    PropertyStatus::Published => 'success', PropertyStatus::Sold, PropertyStatus::Rented => 'info', default => 'gray',
                }),
            Tables\Columns\IconColumn::make('is_featured')->boolean()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                PropertyStatus::Draft->value => 'Draft', PropertyStatus::Published->value => 'Published',
                PropertyStatus::Sold->value => 'Sold', PropertyStatus::Rented->value => 'Rented',
            ]),
            Tables\Filters\SelectFilter::make('purpose')->options([
                PropertyPurpose::Sale->value => 'Sale', PropertyPurpose::Rent->value => 'Rent',
            ]),
            Tables\Filters\SelectFilter::make('property_type_id')->label('Property type')->relationship('propertyType', 'name'),
            Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('images');
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
        return auth()->user()?->can('view properties') ?? false;
    }

    public static function canCreate(): bool
    {
        return (auth()->user()?->can('create properties') ?? false)
            && (! static::isAgentUser() || auth()->user()?->agent !== null);
    }

    public static function canEdit(Model $record): bool
    {
        return (auth()->user()?->can('update properties') ?? false)
            && (! static::isAgentUser() || $record->agent_id === auth()->user()?->agent?->getKey());
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->can('delete properties') ?? false)
            && (! static::isAgentUser() || $record->agent_id === auth()->user()?->agent?->getKey());
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete properties') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
