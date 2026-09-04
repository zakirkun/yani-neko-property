<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function __invoke(): View
    {
        $agents = Agent::query()->where('is_active', true)
            ->withCount(['properties' => fn ($query) => $query->published()])
            ->orderBy('name')->paginate(12);

        return view('agents.index', compact('agents'));
    }
}
