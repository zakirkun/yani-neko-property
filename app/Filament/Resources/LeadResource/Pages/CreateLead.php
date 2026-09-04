<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (LeadResource::isAgentUser()) {
            $data['agent_id'] = auth()->user()?->agent?->getKey();
        }

        return $data;
    }
}
