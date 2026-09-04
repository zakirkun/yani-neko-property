<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (PropertyResource::isAgentUser()) {
            $data['agent_id'] = auth()->user()?->agent?->getKey();
        }

        return $data;
    }
}
