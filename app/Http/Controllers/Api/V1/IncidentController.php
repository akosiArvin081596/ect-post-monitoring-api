<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IncidentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $activeOnly = $request->boolean('active_only', true);

        $query = Incident::query();
        if ($activeOnly) {
            $query->where('is_active', true);
        }

        $incidents = $query
            ->orderByDesc('starts_at')
            ->orderByDesc('created_at')
            ->get();

        return IncidentResource::collection($incidents);
    }
}
