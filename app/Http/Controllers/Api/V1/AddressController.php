<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AddressMunicipality;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = AddressMunicipality::query()
            ->select('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        return response()->json($provinces);
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate(['province' => ['required', 'string']]);

        $districts = AddressMunicipality::query()
            ->where('province', $request->query('province'))
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return response()->json($districts);
    }

    public function municipalities(Request $request): JsonResponse
    {
        $request->validate([
            'province' => ['required', 'string'],
            'district' => ['required', 'string'],
        ]);

        $municipalities = AddressMunicipality::query()
            ->where('province', $request->query('province'))
            ->where('district', $request->query('district'))
            ->select('municipality')
            ->distinct()
            ->orderBy('municipality')
            ->pluck('municipality');

        return response()->json($municipalities);
    }
}
