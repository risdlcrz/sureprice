<?php

namespace App\Support;

/**
 * Single source of truth for status → Bootstrap color + human label.
 * Used by <x-status-badge> and every model's status_color accessor.
 */
class Status
{
    public const COLORS = [
        // neutral / not started
        'draft' => 'secondary', 'cancelled' => 'secondary', 'expired' => 'secondary',
        'inactive' => 'secondary', 'on_hold' => 'secondary', 'closed' => 'secondary',
        // waiting on someone
        'pending' => 'warning', 'pending_admin_approval' => 'warning', 'responded' => 'warning',
        'suspended' => 'warning', 'partially_delivered' => 'warning', 'partially_paid' => 'warning',
        'unpaid' => 'warning',
        // in motion
        'pending_supplier_approval' => 'info', 'for_verification' => 'info', 'in_progress' => 'info',
        'in_review' => 'info', 'reviewed' => 'info', 'sent' => 'info', 'sent_to_suppliers' => 'info',
        'shipped' => 'info', 'processing' => 'info', 'ordered' => 'info',
        'active' => 'primary', 'renewed' => 'primary',
        // done / good
        'approved' => 'success', 'completed' => 'success', 'verified' => 'success', 'paid' => 'success',
        'fully_paid' => 'success', 'delivered' => 'success', 'awarded' => 'success', 'accepted' => 'success',
        'received' => 'success', 'resolved' => 'success',
        // bad
        'rejected' => 'danger', 'overdue' => 'danger', 'delayed' => 'danger', 'declined' => 'danger',
        'failed' => 'danger', 'terminated' => 'dark',
    ];

    public const PRIORITY = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger', 'urgent' => 'dark'];

    public static function key(?string $status): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim((string) $status)));
    }

    public static function color(?string $status, string $kind = 'status'): string
    {
        return ($kind === 'priority' ? self::PRIORITY : self::COLORS)[self::key($status)] ?? 'secondary';
    }

    public static function label(?string $status): string
    {
        return $status === null || $status === '' ? 'N/A' : ucwords(str_replace(['_', '-'], ' ', strtolower($status)));
    }
}
