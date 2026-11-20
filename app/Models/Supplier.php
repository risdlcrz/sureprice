<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'registration_number',
        'status',
        'user_id',
        'company_id',
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material_supplier', 'supplier_id', 'material_id')
            ->withPivot(['price', 'lead_time', 'is_preferred', 'approval_status'])
            ->withTimestamps();
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class)
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusColorAttribute()
    {
        return \App\Support\Status::color($this->status);
    }

    public function evaluations()
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    public function metrics()
    {
        return $this->hasOne(SupplierMetrics::class);
    }

    /**
     * The supplier's company account (approval status, documents). Suppliers and companies are linked
     * through the shared user; `suppliers.company_id` is not populated.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function stocks()
    {
        return $this->hasMany(\App\Models\Stock::class);
    }
} 