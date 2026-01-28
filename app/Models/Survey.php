<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    /** @use HasFactory<\Database\Factories\SurveyFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consent_agreed' => 'boolean',
            'birthdate' => 'date',
            'age' => 'integer',
            'beneficiary_classification' => 'array',
            'demographic_classification' => 'array',
            'livelihood_types' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'altitude' => 'decimal:2',
            'accuracy' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'date_received' => 'date',
            'expense_food' => 'decimal:2',
            'expense_educational' => 'decimal:2',
            'expense_house_rental' => 'decimal:2',
            'expense_livelihood' => 'decimal:2',
            'expense_medical' => 'decimal:2',
            'expense_non_food_items' => 'decimal:2',
            'expense_utilities' => 'decimal:2',
            'expense_shelter_materials' => 'decimal:2',
            'expense_transportation' => 'decimal:2',
            'expense_others' => 'decimal:2',
            'total_utilization' => 'decimal:2',
            'unutilized_variance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Survey $survey): void {
            $survey->total_utilization = collect([
                $survey->expense_food,
                $survey->expense_educational,
                $survey->expense_house_rental,
                $survey->expense_livelihood,
                $survey->expense_medical,
                $survey->expense_non_food_items,
                $survey->expense_utilities,
                $survey->expense_shelter_materials,
                $survey->expense_transportation,
                $survey->expense_others,
            ])->sum();

            $survey->unutilized_variance = $survey->amount_received - $survey->total_utilization;
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * @return HasMany<SurveyUpload, $this>
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(SurveyUpload::class);
    }
}
