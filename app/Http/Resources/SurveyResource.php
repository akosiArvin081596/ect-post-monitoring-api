<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_uuid' => $this->client_uuid,
            'incident_id' => $this->incident_id,
            'consent_agreed' => $this->consent_agreed,
            'beneficiary_name' => $this->beneficiary_name,
            'respondent_name' => $this->respondent_name,
            'relationship_to_beneficiary' => $this->relationship_to_beneficiary,
            'relationship_specify' => $this->relationship_specify,
            'birthdate' => $this->birthdate?->toDateString(),
            'age' => $this->age,
            'beneficiary_classification' => $this->beneficiary_classification,
            'household_id_no' => $this->household_id_no,
            'sex' => $this->sex,
            'demographic_classification' => $this->demographic_classification,
            'ip_specify' => $this->ip_specify,
            'highest_educational_attainment' => $this->highest_educational_attainment,
            'educational_attainment_specify' => $this->educational_attainment_specify,
            'province' => $this->province,
            'district' => $this->district,
            'municipality' => $this->municipality,
            'barangay' => $this->barangay,
            'sitio_purok_street' => $this->sitio_purok_street,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude,
            'accuracy' => $this->accuracy,
            'utilization_type' => $this->utilization_type,
            'amount_received' => $this->amount_received,
            'date_received' => $this->date_received?->toDateString(),
            'expense_food' => $this->expense_food,
            'expense_educational' => $this->expense_educational,
            'expense_house_rental' => $this->expense_house_rental,
            'livelihood_types' => $this->livelihood_types,
            'livelihood_specify' => $this->livelihood_specify,
            'expense_livelihood' => $this->expense_livelihood,
            'expense_medical' => $this->expense_medical,
            'expense_non_food_items' => $this->expense_non_food_items,
            'expense_utilities' => $this->expense_utilities,
            'expense_shelter_materials' => $this->expense_shelter_materials,
            'expense_transportation' => $this->expense_transportation,
            'expense_others_specify' => $this->expense_others_specify,
            'expense_others' => $this->expense_others,
            'total_utilization' => $this->total_utilization,
            'unutilized_variance' => $this->unutilized_variance,
            'reason_not_fully_utilized' => $this->reason_not_fully_utilized,
            'interviewed_by' => $this->interviewed_by,
            'position' => $this->position,
            'survey_modality' => $this->survey_modality,
            'modality_specify' => $this->modality_specify,
            'uploads' => SurveyUploadResource::collection($this->whenLoaded('uploads')),
            'incident' => new IncidentResource($this->whenLoaded('incident')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
