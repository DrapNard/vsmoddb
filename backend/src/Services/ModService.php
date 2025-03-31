<?php
namespace App\Services;

use App\Models\Mod;
use App\Models\User;
use App\Core\Service;

class ModService extends Service
{
    private $modModel;

    public function __construct()
    {
        parent::__construct();
        $this->modModel = new Mod();
    }

    /**
     * Get all mods with their associated users
     *
     * @return array
     */
    public function getAllMods()
    {
        return $this->modModel->getAllWithUsers();
    }

    /**
     * Get a specific mod with its user
     *
     * @param int $id
     * @return array|null
     */
    public function getMod($id)
    {
        return $this->modModel->getWithUser($id);
    }

    /**
     * Create a new mod
     *
     * @param array $data
     * @return int
     */
    public function createMod(array $data)
    {
        // Start transaction
        $this->beginTransaction();

        try {
            // Create asset record first
            $assetId = $this->query(
                "INSERT INTO asset (created) VALUES (NOW())"
            )->lastInsertId();

            // Add asset ID to mod data
            $data['assetid'] = $assetId;
            $data['created'] = date('Y-m-d H:i:s');

            // Create mod record
            $modId = $this->modModel->create($data);

            $this->commit();
            return $modId;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Update a mod
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateMod($id, array $data)
    {
        return $this->modModel->update($id, $data);
    }

    /**
     * Delete a mod
     *
     * @param int $id
     * @return bool
     */
    public function deleteMod($id)
    {
        $this->beginTransaction();

        try {
            // Get the asset ID first
            $mod = $this->modModel->find($id);
            if (!$mod) {
                throw new \Exception('Mod not found');
            }

            // Delete mod record
            $this->modModel->delete($id);

            // Delete associated asset record
            $this->query(
                "DELETE FROM asset WHERE assetid = ?",
                [$mod['assetid']]
            );

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}