<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Create a notification
     *
     * @param int|null $userId User to notify (null for admin/HR notifications)
     * @param string $type Notification type (e.g., 'obrequest', 'docrequest', 'pending_change')
     * @param string $message Notification message
     * @param int|null $relatedId Related record ID
     * @param string|null $relatedType Related record type (e.g., 'pending_change', 'official_business')
     * @return Notification
     */
    public function create(
        ?int $userId,
        string $type,
        string $notif,
        ?int $relatedId = null,
        ?string $relatedType = null
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'notif' => $notif,
            'read' => 0,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
        ]);
    }

    /**
     * Create multiple notifications at once
     *
     * @param array $notifications Array of notification data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createBulk(array $notifications)
    {
        $created = collect();
        
        foreach ($notifications as $notification) {
            $created->push($this->create(
                $notification['user_id'] ?? null,
                $notification['type'],
                $notification['notif'],
                $notification['related_id'] ?? null,
                $notification['related_type'] ?? null
            ));
        }
        
        return $created;
    }

    /**
     * Get unread notifications
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param array $notif Filter by notification notif
     * @param int|null $limit Limit results
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnread(?int $userId = null, array $notif = [], ?int $limit = null)
    {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if (!empty($notif)) {
            $query->whereIn('notif', $notif);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->latest()->get();
    }

    /**
     * Get notification counts grouped by notif
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param array $notifs Filter by notification notifs
     * @return \Illuminate\Support\Collection
     */
    public function getCountsByType(?int $userId = null, array $notifs = [])
    {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if (!empty($notifs)) {
            $query->whereIn('notif', $notifs);
        }

        return $query->selectRaw('notif, COUNT(*) as count')
            ->groupBy('notif')
            ->get()
            ->pluck('count', 'notif');
    }

    /**
     * Get per notif unread count
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param array $notif Filter by notification notif
     * @return int
     */
    public function getUnreadCount(?int $userId = null, string $notif): int
    {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if ($notif) {
            $query->where('notif', $notif);
        }

        return $query->count();
    }

    /**
     * Get total unread count
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param int|null $type
     * @param int|null $notif
     * @return int
     */
    public function getTotalUnreadCount(?int $userId = null, ?string $type = null, ?string $notif = null): int
    {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($notif) {
            $query->where('notif', $notif);
        }

        return $query->count();
    }

    /**
     * Mark notifications as read
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param string|null $notif Specific notif or null for all
     * @param int|null $relatedId Specific related record ID
     * @return int Number of notifications marked as read
     */
    public function markAsRead(
        ?int $userId = null,
        ?string $notif = null,
        ?int $relatedId = null,
        ?int $notifId = null,
        ?string $type = null,
    ): int {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if ($notif) {
            $query->where('notif', $notif);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($relatedId) {
            $query->where('related_id', $relatedId);
        }

        if ($notifId) {
            $query->where('id', $notifId);
        }

        return $query->update(['read' => 1]);
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int|null $userId User ID (null for admin/HR notifications)
     * @param array $notif Optional: Filter by notif
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(?int $userId = null, array $notif = []): int
    {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if (!empty($notif)) {
            $query->whereIn('notif', $notif);
        }

        return $query->update(['read' => 1]);
    }

    /**
     * Delete old read notifications
     *
     * @param int $daysOld Delete notifications older than X days
     * @return int Number of notifications deleted
     */
    public function cleanupOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('read', 1)
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get latest notification by notif
     *
     * @param string $notif
     * @param int|null $userId
     * @return Notification|null
     */
    public function getLatestByType(string $notif, ?int $userId = null): ?Notification
    {
        $query = Notification::where('read', 0)
            ->where('notif', $notif);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        return $query->latest()->first();
    }

    /**
     * Check if unread notifications exist
     *
     * @param int|null $userId
     * @param string|null $notif
     * @param int|null $relatedId
     * @return bool
     */
    public function hasUnread(
        ?int $userId = null,
        ?string $notif = null,
        ?int $relatedId = null,
    ): bool {
        $query = Notification::where('read', 0);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        if ($notif) {
            $query->where('notif', $notif);
        }

        if ($relatedId) {
            $query->where('related_id', $relatedId);
        }

        return $query->exists();
    }
}