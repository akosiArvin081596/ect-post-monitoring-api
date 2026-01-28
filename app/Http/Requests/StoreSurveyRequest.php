<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_uuid' => ['required', 'string', 'uuid', 'max:36'],
            'incident_id' => ['required', 'integer', 'exists:incidents,id'],
            'consent_agreed' => ['required', 'boolean'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'respondent_name' => ['required', 'string', 'max:255'],
            'relationship_to_beneficiary' => ['required', 'string', 'max:255'],
            'relationship_specify' => ['nullable', 'required_if:relationship_to_beneficiary,Others', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:0', 'max:150'],
            'beneficiary_classification' => ['required', 'array', 'min:1'],
            'beneficiary_classification.*' => ['string'],
            'household_id_no' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'demographic_classification' => ['required', 'array', 'min:1'],
            'demographic_classification.*' => ['string'],
            'ip_specify' => ['nullable', 'string', 'max:255'],
            'highest_educational_attainment' => ['required', 'string', 'max:255'],
            'educational_attainment_specify' => ['nullable', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'sitio_purok_street' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'altitude' => ['nullable', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'utilization_type' => ['required', 'string', 'in:Relief/Response,Recovery/Rehabilitation'],
            'amount_received' => ['required', 'numeric', 'min:0'],
            'date_received' => ['required', 'date'],
            'expense_food' => ['required', 'numeric', 'min:0'],
            'expense_educational' => ['required', 'numeric', 'min:0'],
            'expense_house_rental' => ['required', 'numeric', 'min:0'],
            'livelihood_types' => ['nullable', 'array'],
            'livelihood_types.*' => ['string'],
            'livelihood_specify' => ['nullable', 'string', 'max:255'],
            'expense_livelihood' => ['required', 'numeric', 'min:0'],
            'expense_medical' => ['required', 'numeric', 'min:0'],
            'expense_non_food_items' => ['required', 'numeric', 'min:0'],
            'expense_utilities' => ['required', 'numeric', 'min:0'],
            'expense_shelter_materials' => ['required', 'numeric', 'min:0'],
            'expense_transportation' => ['required', 'numeric', 'min:0'],
            'expense_others_specify' => ['nullable', 'string', 'max:255'],
            'expense_others' => ['required', 'numeric', 'min:0'],
            'reason_not_fully_utilized' => ['nullable', 'string'],
            'interviewed_by' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'survey_modality' => ['required', 'string', 'max:255'],
            'modality_specify' => ['nullable', 'string', 'max:255'],
        ];
    }
}
