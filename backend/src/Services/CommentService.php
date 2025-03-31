<?php
namespace App\Services;

use App\Core\Service;

class CommentService extends Service
{
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    /**
     * Create a new comment
     *
     * @param array $data
     * @param int $userId
     * @return int|false
     */
    public function create($data, $userId)
    {
        try {
            $this->beginTransaction();

            $sql = "INSERT INTO comments (user_id, target_type, target_id, content, created_at) "
                 . "VALUES (?, ?, ?, ?, NOW())";
            $this->query($sql, [
                $userId,
                $data['target_type'], // 'mod' or 'release'
                $data['target_id'],
                $data['content']
            ]);
            $commentId = $this->lastInsertId();

            // If this is a reply to another comment
            if (isset($data['parent_id'])) {
                $sql = "UPDATE comments SET parent_id = ? WHERE id = ?";
                $this->query($sql, [$data['parent_id'], $commentId]);
            }

            $this->commit();
            return $commentId;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Update a comment
     *
     * @param int $commentId
     * @param array $data
     * @param int $userId
     * @return bool
     */
    public function update($commentId, $data, $userId)
    {
        try {
            // Verify the user owns this comment
            $comment = $this->getById($commentId);
            if (!$comment || $comment['user_id'] !== $userId) {
                return false;
            }

            $sql = "UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ?";
            $this->query($sql, [$data['content'], $commentId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get comment by ID
     *
     * @param int $commentId
     * @return array|null
     */
    public function getById($commentId)
    {
        $sql = "SELECT c.*, u.username as author_name "
             . "FROM comments c "
             . "JOIN users u ON c.user_id = u.id "
             . "WHERE c.id = ?";
        $result = $this->query($sql, [$commentId]);
        
        return $result ? $result[0] : null;
    }

    /**
     * Get comments for a target (mod or release)
     *
     * @param string $targetType
     * @param int $targetId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getForTarget($targetType, $targetId, $limit = 20, $offset = 0)
    {
        $sql = "SELECT c.*, u.username as author_name "
             . "FROM comments c "
             . "JOIN users u ON c.user_id = u.id "
             . "WHERE c.target_type = ? AND c.target_id = ? "
             . "ORDER BY c.created_at DESC "
             . "LIMIT ? OFFSET ?";

        return $this->query($sql, [$targetType, $targetId, $limit, $offset]);
    }

    /**
     * Get replies to a comment
     *
     * @param int $commentId
     * @return array
     */
    public function getReplies($commentId)
    {
        $sql = "SELECT c.*, u.username as author_name "
             . "FROM comments c "
             . "JOIN users u ON c.user_id = u.id "
             . "WHERE c.parent_id = ? "
             . "ORDER BY c.created_at ASC";

        return $this->query($sql, [$commentId]);
    }

    /**
     * Delete a comment
     *
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function delete($commentId, $userId)
    {
        try {
            // Verify the user owns this comment
            $comment = $this->getById($commentId);
            if (!$comment || $comment['user_id'] !== $userId) {
                return false;
            }

            $this->beginTransaction();

            // Delete replies first
            $sql = "DELETE FROM comments WHERE parent_id = ?";
            $this->query($sql, [$commentId]);

            // Delete the comment
            $sql = "DELETE FROM comments WHERE id = ?";
            $this->query($sql, [$commentId]);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }
}