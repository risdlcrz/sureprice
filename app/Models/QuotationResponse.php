<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationResponse extends Model
{
    protected $fillable = [
        'quotation_id',
        'supplier_id',
        'total_amount',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'final_amount',
        'discount_reason',
        'payment_terms',
        'delivery_terms',
        'validity_period',
        'notes',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Discount type constants
    const DISCOUNT_TYPE_NONE = 'none';
    const DISCOUNT_TYPE_BULK = 'bulk';
    const DISCOUNT_TYPE_SEASONAL = 'seasonal';
    const DISCOUNT_TYPE_LOYALTY = 'loyalty';
    const DISCOUNT_TYPE_NEW_CUSTOMER = 'new_customer';
    const DISCOUNT_TYPE_PAYMENT_TERMS = 'payment_terms';
    const DISCOUNT_TYPE_DELIVERY_TERMS = 'delivery_terms';
    const DISCOUNT_TYPE_CUSTOM = 'custom';

    // Discount validation rules
    const DISCOUNT_RULES = [
        self::DISCOUNT_TYPE_BULK => [
            'max_percentage' => 25,
            'min_order_amount' => 50000,
            'description' => 'Bulk order discount for large quantities'
        ],
        self::DISCOUNT_TYPE_SEASONAL => [
            'max_percentage' => 15,
            'min_order_amount' => 0,
            'description' => 'Seasonal promotion discount'
        ],
        self::DISCOUNT_TYPE_LOYALTY => [
            'max_percentage' => 10,
            'min_order_amount' => 0,
            'description' => 'Loyalty discount for repeat customers'
        ],
        self::DISCOUNT_TYPE_NEW_CUSTOMER => [
            'max_percentage' => 20,
            'min_order_amount' => 0,
            'description' => 'New customer welcome discount'
        ],
        self::DISCOUNT_TYPE_PAYMENT_TERMS => [
            'max_percentage' => 5,
            'min_order_amount' => 0,
            'description' => 'Early payment discount'
        ],
        self::DISCOUNT_TYPE_DELIVERY_TERMS => [
            'max_percentage' => 8,
            'min_order_amount' => 0,
            'description' => 'Flexible delivery terms discount'
        ],
        self::DISCOUNT_TYPE_CUSTOM => [
            'max_percentage' => 30,
            'min_order_amount' => 0,
            'description' => 'Custom discount (requires approval)'
        ]
    ];

    // Relationships
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationResponseItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuotationResponseAttachment::class);
    }

    // Helper methods
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SUBMITTED]);
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SUBMITTED => 'badge-info',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger'
        ][$this->status] ?? 'badge-secondary';
    }

    // Discount calculation methods
    public function calculateDiscountAmount(): float
    {
        if ($this->discount_percentage > 0) {
            return ($this->total_amount * $this->discount_percentage) / 100;
        }
        return $this->discount_amount ?? 0;
    }

    public function calculateFinalAmount(): float
    {
        $discountAmount = $this->calculateDiscountAmount();
        return $this->total_amount - $discountAmount;
    }

    public function hasDiscount(): bool
    {
        return ($this->discount_percentage > 0) || ($this->discount_amount > 0);
    }

    public function getDiscountDisplayAttribute(): string
    {
        if ($this->discount_percentage > 0) {
            return $this->discount_percentage . '%';
        } elseif ($this->discount_amount > 0) {
            return '₱' . number_format($this->discount_amount, 2);
        }
        return 'No discount';
    }

    public function getSavingsDisplayAttribute(): string
    {
        if ($this->hasDiscount()) {
            $savings = $this->total_amount - $this->final_amount;
            return '₱' . number_format($savings, 2);
        }
        return '₱0.00';
    }

    // Discount type methods
    public function getDiscountTypeDisplayAttribute(): string
    {
        $types = [
            self::DISCOUNT_TYPE_NONE => 'No Discount',
            self::DISCOUNT_TYPE_BULK => 'Bulk Order Discount',
            self::DISCOUNT_TYPE_SEASONAL => 'Seasonal Promotion',
            self::DISCOUNT_TYPE_LOYALTY => 'Loyalty Discount',
            self::DISCOUNT_TYPE_NEW_CUSTOMER => 'New Customer Discount',
            self::DISCOUNT_TYPE_PAYMENT_TERMS => 'Early Payment Discount',
            self::DISCOUNT_TYPE_DELIVERY_TERMS => 'Flexible Delivery Discount',
            self::DISCOUNT_TYPE_CUSTOM => 'Custom Discount'
        ];

        return $types[$this->discount_type] ?? 'Unknown';
    }

    public function getDiscountTypeDescriptionAttribute(): string
    {
        return self::DISCOUNT_RULES[$this->discount_type]['description'] ?? '';
    }

    public function getDiscountTypeMaxPercentageAttribute(): float
    {
        return self::DISCOUNT_RULES[$this->discount_type]['max_percentage'] ?? 0;
    }

    public function getDiscountTypeMinOrderAmountAttribute(): float
    {
        return self::DISCOUNT_RULES[$this->discount_type]['min_order_amount'] ?? 0;
    }

    public function validateDiscount(): array
    {
        $errors = [];

        if (!$this->discount_type || $this->discount_type === self::DISCOUNT_TYPE_NONE) {
            return $errors; // No discount, no validation needed
        }

        $rules = self::DISCOUNT_RULES[$this->discount_type] ?? null;
        if (!$rules) {
            $errors[] = 'Invalid discount type selected.';
            return $errors;
        }

        // Check minimum order amount
        if ($this->total_amount < $rules['min_order_amount']) {
            $errors[] = "Minimum order amount of ₱" . number_format($rules['min_order_amount'], 2) . " required for " . $this->discount_type_display . ".";
        }

        // Check maximum percentage
        if ($this->discount_percentage > $rules['max_percentage']) {
            $errors[] = "Maximum discount percentage for " . $this->discount_type_display . " is " . $rules['max_percentage'] . "%.";
        }

        // Check if discount amount exceeds total
        if ($this->discount_amount > $this->total_amount) {
            $errors[] = "Discount amount cannot exceed the total order amount.";
        }

        // Special validation for bulk orders
        if ($this->discount_type === self::DISCOUNT_TYPE_BULK) {
            $totalQuantity = $this->items->sum('quantity');
            if ($totalQuantity < 100) { // Example: minimum 100 units for bulk discount
                $errors[] = "Bulk discount requires minimum 100 units total across all materials.";
            }
        }

        // Special validation for loyalty discount
        if ($this->discount_type === self::DISCOUNT_TYPE_LOYALTY) {
            $completedOrders = $this->supplier->purchaseOrders()
                ->where('status', 'completed')
                ->count();
            if ($completedOrders < 3) { // Example: minimum 3 completed orders
                $errors[] = "Loyalty discount requires minimum 3 completed orders with this supplier.";
            }
        }

        return $errors;
    }

    public function isDiscountValid(): bool
    {
        return empty($this->validateDiscount());
    }

    public function getDiscountBadgeClassAttribute(): string
    {
        $classes = [
            self::DISCOUNT_TYPE_NONE => 'badge-secondary',
            self::DISCOUNT_TYPE_BULK => 'badge-primary',
            self::DISCOUNT_TYPE_SEASONAL => 'badge-success',
            self::DISCOUNT_TYPE_LOYALTY => 'badge-warning',
            self::DISCOUNT_TYPE_NEW_CUSTOMER => 'badge-info',
            self::DISCOUNT_TYPE_PAYMENT_TERMS => 'badge-dark',
            self::DISCOUNT_TYPE_DELIVERY_TERMS => 'badge-light',
            self::DISCOUNT_TYPE_CUSTOM => 'badge-danger'
        ];

        return $classes[$this->discount_type] ?? 'badge-secondary';
    }

    // Static methods for discount types
    public static function getAvailableDiscountTypes(): array
    {
        return [
            self::DISCOUNT_TYPE_NONE => 'No Discount',
            self::DISCOUNT_TYPE_BULK => 'Bulk Order Discount',
            self::DISCOUNT_TYPE_SEASONAL => 'Seasonal Promotion',
            self::DISCOUNT_TYPE_LOYALTY => 'Loyalty Discount',
            self::DISCOUNT_TYPE_NEW_CUSTOMER => 'New Customer Discount',
            self::DISCOUNT_TYPE_PAYMENT_TERMS => 'Early Payment Discount',
            self::DISCOUNT_TYPE_DELIVERY_TERMS => 'Flexible Delivery Discount',
            self::DISCOUNT_TYPE_CUSTOM => 'Custom Discount'
        ];
    }

    public static function getDiscountRules(): array
    {
        return self::DISCOUNT_RULES;
    }
} 