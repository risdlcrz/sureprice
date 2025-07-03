<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory;

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
        'is_wall_material',
        'coverage_rate',
        'waste_factor',
        'minimum_quantity',
        'bulk_pricing',
        'custom_category',
        'warranty_period',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'srp_price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_per_area' => 'boolean',
        'is_wall_material' => 'boolean',
        'coverage_rate' => 'float',
        'waste_factor' => 'decimal:2',
        'minimum_quantity' => 'integer',
        'bulk_pricing' => 'array',
        'warranty_period' => 'integer',
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
        return $this->belongsToMany(\App\Models\Supplier::class, 'material_supplier', 'material_id', 'supplier_id')
            ->withPivot(['price', 'is_preferred'])
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

    public function stockMovements(): HasMany
    {
        return $this->hasMany(\App\Models\StockMovement::class);
    }

    public function scopeTypes()
    {
        return $this->belongsToMany(ScopeType::class, 'scope_type_material');
    }

    public function priceHistories()
    {
        return $this->hasMany(MaterialPriceHistory::class);
    }

    /**
     * Get price history as [date => price] array, sorted by date asc
     */
    public function getPriceHistoryArray()
    {
        return $this->priceHistories()->orderBy('date')->pluck('price', 'date')->toArray();
    }

    public function stocks()
    {
        return $this->hasMany(\App\Models\Stock::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->code)) {
                $material->code = static::generateUniqueCode();
            }
        });

        // Sync to inventory on create
        static::created(function ($material) {
            if (!Inventory::where('material_id', $material->id)->exists()) {
                Inventory::create([
                    'material_id' => $material->id,
                    'quantity' => 0,
                    'unit' => $material->unit,
                    'location' => null,
                    'status' => 'active',
                    'minimum_threshold' => 0,
                ]);
            }
        });

        // Sync to inventory on update
        static::updated(function ($material) {
            $inventory = Inventory::where('material_id', $material->id)->first();
            if ($inventory) {
                // Update unit if changed
                if ($inventory->unit !== $material->unit) {
                    $inventory->unit = $material->unit;
                    $inventory->save();
                }
            } else {
                // If inventory record is missing, create it
                Inventory::create([
                    'material_id' => $material->id,
                    'quantity' => 0,
                    'unit' => $material->unit,
                    'location' => null,
                    'status' => 'active',
                    'minimum_threshold' => 0,
                ]);
            }
        });

        // Remove inventory on material delete
        static::deleting(function ($material) {
            Inventory::where('material_id', $material->id)->delete();
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