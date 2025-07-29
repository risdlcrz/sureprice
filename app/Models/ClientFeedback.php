<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientFeedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'client_id',
        'user_id',
        'overall_rating',
        'communication_rating',
        'quality_rating',
        'timeliness_rating',
        'professionalism_rating',
        'value_rating',
        'comments',
        'recommendation_likelihood',
        'submitted_at',
        'is_anonymous',
        'status',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'communication_rating' => 'integer',
        'quality_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'value_rating' => 'integer',
        'recommendation_likelihood' => 'integer',
        'submitted_at' => 'datetime',
        'is_anonymous' => 'boolean',
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function client()
    {
        return $this->belongsTo(Party::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('submitted_at');
    }

    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    public function scopeNamed($query)
    {
        return $query->where('is_anonymous', false);
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        $ratings = [
            $this->communication_rating,
            $this->quality_rating,
            $this->timeliness_rating,
            $this->professionalism_rating,
            $this->value_rating
        ];

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getRatingTextAttribute()
    {
        $rating = $this->overall_rating;
        
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 4.0) return 'Very Good';
        if ($rating >= 3.5) return 'Good';
        if ($rating >= 3.0) return 'Satisfactory';
        if ($rating >= 2.5) return 'Fair';
        return 'Poor';
    }

    public function getRecommendationTextAttribute()
    {
        $likelihood = $this->recommendation_likelihood;
        
        if ($likelihood >= 9) return 'Definitely';
        if ($likelihood >= 7) return 'Very Likely';
        if ($likelihood >= 5) return 'Likely';
        if ($likelihood >= 3) return 'Maybe';
        return 'Unlikely';
    }

    // Methods
    public function isSubmitted()
    {
        return !is_null($this->submitted_at);
    }

    public function canBeSubmitted()
    {
        return !$this->isSubmitted() && $this->contract && $this->contract->isPaymentComplete();
    }

    public function submit()
    {
        $this->update([
            'submitted_at' => now(),
            'status' => 'submitted'
        ]);
    }
} 