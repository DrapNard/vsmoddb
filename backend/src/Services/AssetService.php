<?php
namespace App\Services;

use App\Core\Service;

class AssetService extends Service
{
    private $userService;
    private $tagService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
        $this->tagService = new TagService();
    }

    /**
     * Create a new asset
     *
     * @param array $data
     * @param int $userId
     * @return int|false
     */
    public function create($data, $userId)
    {
        try {
            $this->beginTransaction();

            $sql = "INSERT INTO assets (user_id, title, description, type, created_at) VALUES (?, ?, ?, ?, NOW())";
            $this->query($sql, [$userId, $data['title'], $data['description'], $data['type']]);
            $assetId = $this->lastInsertId();

            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagId) {
                    $this->tagService->addToAsset($assetId, $tagId);
                }
            }

            $this->commit();
            return $assetId;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Update an asset
     *
     * @param int $assetId
     * @param array $data
     * @return bool
     */
    public function update($assetId, $data)
    {
        try {
            $this->beginTransaction();

            $allowedFields = ['title', 'description', 'status'];
            $updates = [];
            $params = [];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updates[] = "{$field} = ?";
                    $params[] = $value;
                }
            }

            if (!empty($updates)) {
                $params[] = $assetId;
                $sql = "UPDATE assets SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = ?";
                $this->query($sql, $params);
            }

            if (isset($data['tags']) && is_array($data['tags'])) {
                $sql = "DELETE FROM asset_tags WHERE asset_id = ?";
                $this->query($sql, [$assetId]);

                foreach ($data['tags'] as $tagId) {
                    $this->tagService->addToAsset($assetId, $tagId);
                }
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Get asset by ID
     *
     * @param int $assetId
     * @return array|null
     */
    public function getById($assetId)
    {
        $sql = "SELECT a.*, u.username as author_name 
                FROM assets a 
                JOIN users u ON a.user_id = u.id 
                WHERE a.id = ?";
        $result = $this->query($sql, [$assetId]);
        
        if ($result) {
            $asset = $result[0];
            $asset['tags'] = $this->tagService->getForAsset($assetId);
            return $asset;
        }

        return null;
    }

    /**
     * Get assets list
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList($filters = [], $limit = 20, $offset = 0)
    {
        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "a.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['user_id'])) {
            $where[] = "a.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT a.*, u.username as author_name 
                FROM assets a 
                JOIN users u ON a.user_id = u.id 
                {$whereClause} 
                ORDER BY a.created_at DESC 
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        return $this->query($sql, $params);
    }

    /**
     * Search assets
     *
     * @param string $query
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function search($query, $filters = [], $limit = 20)
    {
        $where = ["(a.title LIKE ? OR a.description LIKE ?)"];
        $params = ["%{$query}%", "%{$query}%"];

        if (!empty($filters['type'])) {
            $where[] = "a.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $sql = "SELECT a.*, u.username as author_name 
                FROM assets a 
                JOIN users u ON a.user_id = u.id 
                {$whereClause} 
                ORDER BY a.created_at DESC 
                LIMIT ?";

        $params[] = $limit;

        return $this->query($sql, $params);
    }

    /**
     * Delete an asset
     *
     * @param int $assetId
     * @return bool
     */
    public function delete($assetId)
    {
        try {
            $this->beginTransaction();

            // Delete asset tags
            $sql = "DELETE FROM asset_tags WHERE asset_id = ?";
            $this->query($sql, [$assetId]);

            // Delete asset
            $sql = "DELETE FROM assets WHERE id = ?";
            $this->query($sql, [$assetId]);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }
}