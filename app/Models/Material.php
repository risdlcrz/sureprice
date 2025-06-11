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
        'code',
        'description',
        'unit',
        'category_id',
        'base_price',
        'srp_price',
        'specifications',
        'minimum_stock',
        'current_stock',
        'is_per_area',
        'coverage_rate',
        'waste_factor',
        'minimum_quantity',
        'bulk_pricing'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'srp_price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_per_area' => 'boolean',
        'coverage_rate' => 'decimal:2',
        'waste_factor' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'bulk_pricing' => 'array'
    ];

    protected $with = ['category'];

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

    public function images(): HasMany
    {
        return $this->hasMany(MaterialImage::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function scopeTypes()
    {
        return $this->belongsToMany(ScopeType::class, 'scope_type_material');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->code)) {
                $material->code = static::generateUniqueCode();
            }
        });
    }

    protected static function generateUniqueCode()
    {
        $prefix = 'MAT';
        $year = date('y');
        $lastMaterial = static::where('code', 'like', "{$prefix}{$year}%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastMaterial) {
            $lastNumber = (int) substr($lastMaterial->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("%s%s%04d", $prefix, $year, $newNumber);
    }
} 