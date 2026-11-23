<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory, Concerns\HasStatusFilters;

    /** List-page status groups: key => [label, statuses, tone, icon] (see HasStatusFilters). */
    public const STATUS_FILTERS = [
        'pending' => ['Pending', ['pending', 'pending_admin_approval'], 'warning', 'fa-hourglass-half'],
        'in_progress' => ['In progress', ['approved', 'partial', 'requested'], 'info', 'fa-dolly'],
        'completed' => ['Completed', ['completed', 'fulfilled'], 'success', 'fa-check-circle'],
        'cancelled' => ['Cancelled / rejected', ['cancelled', 'rejected'], 'danger', 'fa-times-circle'],
    ];

    protected $fillable = [
        'contract_id',
        'requested_by',
        'notes',
        'quotation_request_id',
        'status',
    ];

    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    /** Same as requestedBy(); material_requests has no user_id column. */
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\MaterialRequestItem::class);
    }

    public function contract()
    {
        return $this->belongsTo(\App\Models\Contract::class);
    }
} 