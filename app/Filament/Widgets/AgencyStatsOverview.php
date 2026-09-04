<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Enums\PropertyStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgencyStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $propertyQuery = Property::query();
        $leadQuery = Lead::query();
        $user = auth()->user();

        if ($user?->hasRole('agent') && ! $user->hasAnyRole(['admin', 'super-admin'])) {
            $agentId = $user->agent?->getKey() ?? 0;
            $propertyQuery->where('agent_id', $agentId);
            $leadQuery->where('agent_id', $agentId);
        }

        return [
            Stat::make('Properties', (clone $propertyQuery)->count())->icon('heroicon-o-home-modern')->color('primary'),
            Stat::make('Published', (clone $propertyQuery)->where('status', PropertyStatus::Published->value)->count())->icon('heroicon-o-eye')->color('success'),
            Stat::make('New leads', $leadQuery->where('status', LeadStatus::New->value)->count())->icon('heroicon-o-chat-bubble-left-right')->color('warning'),
            Stat::make('Active agents', Agent::query()->where('is_active', true)->count())->icon('heroicon-o-identification')->color('info'),
        ];
    }
}
