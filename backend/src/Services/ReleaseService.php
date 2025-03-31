<?php
namespace App\Services;

use App\Core\Service;

class ReleaseService extends Service
{
    private $assetService;
    private $tagService;

    public function __construct()
    {
        parent::__construct();
        $this->assetService = new AssetService();
        $this->tagService = new TagService();
    }

    /**
     * Create a new release
     *
     * @param array $data
     * @param int $modId
     * @return int|false
     */
    public function create($data, $modId)
    {
        try {
            $this->beginTransaction();

            $sql = "INSERT INTO releases (mod_id, version, title, description, status, created_at) "
                 . "VALUES (?, ?, ?, ?, ?, NOW())";
            $this->query($sql, [
                $modId,
                $data['version'],
                $data['title'],
                $data['description'],
                $data['status'] ?? 'draft'
            ]);
            $releaseId = $this->lastInsertId();

            // Handle file uploads if present
            if (isset($data['files']) && is_array($data['files'])) {
                foreach ($data['files'] as $file) {
                    $assetId = $this->assetService->create([
                        'title' => $file['name'],
                        'description' => 'Release file for ' . $data['version'],
                        'type' => 'release_file'
                    ], $data['user_id']);

                    if ($assetId) {
                        $sql = "INSERT INTO release_files (release_id, asset_id, file_type) VALUES (?, ?, ?)";
                        $this->query($sql, [$releaseId, $assetId, $file['type']]);
                    }
                }
            }

            // Handle version tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagId) {
                    $this->tagService->addToRelease($releaseId, $tagId);
                }
            }

            $this->commit();
            return $releaseId;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Update a release
     *
     * @param int $releaseId
     * @param array $data
     * @return bool
     */
    public function update($releaseId, $data)
    {
        try {
            $this->beginTransaction();

            $allowedFields = ['version', 'title', 'description', 'status'];
            $updates = [];
            $params = [];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updates[] = "{$field} = ?";
                    $params[] = $value;
                }
            }

            if (!empty($updates)) {
                $params[] = $releaseId;
                $sql = "UPDATE releases SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = ?";
                $this->query($sql, $params);
            }

            // Update version tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $sql = "DELETE FROM release_tags WHERE release_id = ?";
                $this->query($sql, [$releaseId]);

                foreach ($data['tags'] as $tagId) {
                    $this->tagService->addToRelease($releaseId, $tagId);
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
     * Get release by ID
     *
     * @param int $releaseId
     * @return array|null
     */
    public function getById($releaseId)
    {
        $sql = "SELECT r.*, m.title as mod_title "
             . "FROM releases r "
             . "JOIN mods m ON r.mod_id = m.id "
             . "WHERE r.id = ?";
        $result = $this->query($sql, [$releaseId]);
        
        if ($result) {
            $release = $result[0];
            $release['tags'] = $this->tagService->getForRelease($releaseId);
            $release['files'] = $this->getFiles($releaseId);
            return $release;
        }

        return null;
    }

    /**
     * Get files for a release
     *
     * @param int $releaseId
     * @return array
     */
    public function getFiles($releaseId)
    {
        $sql = "SELECT rf.*, a.title, a.description "
             . "FROM release_files rf "
             . "JOIN assets a ON rf.asset_id = a.id "
             . "WHERE rf.release_id = ?";
        return $this->query($sql, [$releaseId]);
    }

    /**
     * Delete a release
     *
     * @param int $releaseId
     * @return bool
     */
    public function delete($releaseId)
    {
        try {
            $this->beginTransaction();

            // Delete release files
            $files = $this->getFiles($releaseId);
            foreach ($files as $file) {
                $this->assetService->delete($file['asset_id']);
            }

            // Delete release tags
            $sql = "DELETE FROM release_tags WHERE release_id = ?";
            $this->query($sql, [$releaseId]);

            // Delete release
            $sql = "DELETE FROM releases WHERE id = ?";
            $this->query($sql, [$releaseId]);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }
}