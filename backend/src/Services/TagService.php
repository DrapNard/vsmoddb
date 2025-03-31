<?php
namespace App\Services;

use App\Core\Service;

class TagService extends Service
{
    /**
     * Create a new tag
     *
     * @param string $name
     * @param string $description
     * @param string $type
     * @return bool
     */
    public function create($name, $description = '', $type = 'general')
    {
        $sql = "INSERT INTO tags (name, description, type, created_at) VALUES (?, ?, ?, NOW())";
        return $this->query($sql, [$name, $description, $type]);
    }

    /**
     * Get tag by ID
     *
     * @param int $tagId
     * @return array|null
     */
    public function getById($tagId)
    {
        $sql = "SELECT * FROM tags WHERE id = ?";
        $result = $this->query($sql, [$tagId]);
        return $result[0] ?? null;
    }

    /**
     * Get tag by name
     *
     * @param string $name
     * @return array|null
     */
    public function getByName($name)
    {
        $sql = "SELECT * FROM tags WHERE name = ?";
        $result = $this->query($sql, [$name]);
        return $result[0] ?? null;
    }

    /**
     * Update tag
     *
     * @param int $tagId
     * @param array $data
     * @return bool
     */
    public function update($tagId, $data)
    {
        $allowedFields = ['name', 'description', 'type'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $tagId;
        $sql = "UPDATE tags SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = ?";
        return $this->query($sql, $params);
    }

    /**
     * Delete tag
     *
     * @param int $tagId
     * @return bool
     */
    public function delete($tagId)
    {
        $sql = "DELETE FROM tags WHERE id = ?";
        return $this->query($sql, [$tagId]);
    }

    /**
     * Get all tags
     *
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($type = null, $limit = 100, $offset = 0)
    {
        $sql = "SELECT * FROM tags";
        $params = [];

        if ($type) {
            $sql .= " WHERE type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->query($sql, $params);
    }

    /**
     * Search tags
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function search($query, $limit = 10)
    {
        $sql = "SELECT * FROM tags WHERE name LIKE ? ORDER BY name ASC LIMIT ?";
        return $this->query($sql, ["%{$query}%", $limit]);
    }

    /**
     * Get tags for an asset
     *
     * @param int $assetId
     * @return array
     */
    public function getForAsset($assetId)
    {
        $sql = "SELECT t.* FROM tags t 
                JOIN asset_tags at ON t.id = at.tag_id 
                WHERE at.asset_id = ? 
                ORDER BY t.name ASC";
        return $this->query($sql, [$assetId]);
    }

    /**
     * Add tag to asset
     *
     * @param int $assetId
     * @param int $tagId
     * @return bool
     */
    public function addToAsset($assetId, $tagId)
    {
        $sql = "INSERT IGNORE INTO asset_tags (asset_id, tag_id) VALUES (?, ?)";
        return $this->query($sql, [$assetId, $tagId]);
    }

    /**
     * Remove tag from asset
     *
     * @param int $assetId
     * @param int $tagId
     * @return bool
     */
    public function removeFromAsset($assetId, $tagId)
    {
        $sql = "DELETE FROM asset_tags WHERE asset_id = ? AND tag_id = ?";
        return $this->query($sql, [$assetId, $tagId]);
    }
}