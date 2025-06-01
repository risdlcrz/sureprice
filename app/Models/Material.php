<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unit',
        'category_id',
        'minimum_stock',
        'current_stock'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inquiries()
    {
        return $this->belongsToMany(Inquiry::class)
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class)
            ->withPivot(['quantity', 'specifications'])
            ->withTimestamps();
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot(['price', 'lead_time'])
            ->withTimestamps();
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }
} 