<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Get unread notification count for the current user
     */
    public static function getUnreadCount($userId = null)
    {
        try {
            if (!$userId) {
                $userId = Auth::id();
            }

            if (!$userId) {
                return 0;
            }

            return Notification::where(function($query) use ($userId) {
                    $query->where('notifiable_id', $userId)
                          ->orWhere('user_id', $userId);
                })
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting notification count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get unread notification count for a specific user type/role
     */
    public static function getUnreadCountByRole($role, $userId = null)
    {
        try {
            if (!$userId) {
                $userId = Auth::id();
            }

            if (!$userId) {
                return 0;
            }

            return Notification::where(function($query) use ($userId) {
                    $query->where('notifiable_id', $userId)
                          ->orWhere('user_id', $userId);
                })
                ->where(function($query) use ($role) {
                    $query->where('for_role', $role)
                          ->orWhereNull('for_role');
                })
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting notification count by role: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get notifications for the current user
     */
    public static function getUserNotifications($userId = null, $limit = 50)
    {
        try {
            if (!$userId) {
                $userId = Auth::id();
            }

            if (!$userId) {
                return collect();
            }

            return Notification::where(function($query) use ($userId) {
                    $query->where('notifiable_id', $userId)
                          ->orWhere('user_id', $userId);
                })
                ->latest()
                ->take($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting user notifications: ' . $e->getMessage());
            return collect();
        }
    }
} 