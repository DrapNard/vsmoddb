<?php
namespace App\Services;

use App\Core\Service;

class NotificationService extends Service
{
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    /**
     * Create a new notification
     *
     * @param int $userId
     * @param string $type
     * @param array $data
     * @return bool
     */
    public function create($userId, $type, $data = [])
    {
        $sql = "INSERT INTO notifications (user_id, type, data, created_at) VALUES (?, ?, ?, NOW())";
        return $this->query($sql, [$userId, $type, json_encode($data)]);
    }

    /**
     * Get notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getForUser($userId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->query($sql, [$userId, $limit, $offset]);
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead($notificationId, $userId)
    {
        $sql = "UPDATE notifications SET read_at = NOW() WHERE id = ? AND user_id = ?";
        return $this->query($sql, [$notificationId, $userId]);
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId)
    {
        $sql = "UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL";
        return $this->query($sql, [$userId]);
    }

    /**
     * Get unread notifications count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND read_at IS NULL";
        $result = $this->query($sql, [$userId]);
        return $result[0]['count'] ?? 0;
    }

    /**
     * Delete old notifications
     *
     * @param int $daysOld
     * @return bool
     */
    public function deleteOldNotifications($daysOld = 30)
    {
        $sql = "DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        return $this->query($sql, [$daysOld]);
    }
}