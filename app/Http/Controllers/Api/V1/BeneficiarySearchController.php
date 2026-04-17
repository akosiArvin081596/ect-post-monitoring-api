<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiarySearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $beneficiaries = Survey::query()
            ->where('user_id', $request->user()->id)
            ->where('beneficiary_name', 'LIKE', "%{$query}%")
            ->orderByDesc('created_at')
            ->get()
            ->unique('beneficiary_name')
            ->take(10)
            ->map(fn (Survey $survey) => [
                'beneficiary_name' => $survey->beneficiary_name,
                'birthdate' => $survey->birthdate?->format('Y-m-d'),
                'age' => $survey->age,
                'sex' => $survey->sex,
                'beneficiary_classification' => $survey->beneficiary_classification,
                'household_id_no' => $survey->household_id_no,
                'demographic_classification' => $survey->demographic_classification,
                'ip_specify' => $survey->ip_specify,
                'highest_educational_attainment' => $survey->highest_educational_attainment,
                'educational_attainment_specify' => $survey->educational_attainment_specify,
            ])
            ->values();

        return response()->json($beneficiaries);
    }
}
