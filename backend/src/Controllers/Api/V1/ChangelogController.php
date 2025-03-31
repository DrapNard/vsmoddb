<?php

namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Models\Changelog;

class ChangelogController extends Controller
{
    public function index()
    {
        $changelogs = Changelog::with(['mod', 'release'])
            ->latest()
            ->limit(100)
            ->get();
        
        return $this->json([
            'success' => true,
            'changelogs' => $this->formatChangelogs($changelogs)
        ]);
    }

    public function forAsset($assetId)
    {
        $changelogs = Changelog::with(['mod', 'release'])
            ->whereHas('mod', function($query) use ($assetId) {
                $query->where('id', $assetId);
            })
            ->latest()
            ->get();
        
        return $this->json([
            'success' => true,
            'changelogs' => $this->formatChangelogs($changelogs)
        ]);
    }

    private function formatChangelogs($changelogs)
    {
        return $changelogs->map(function($changelog) {
            return [
                'id' => $changelog->id,
                'version' => $changelog->release->version,
                'changes' => $changelog->changes,
                'created_at' => $changelog->created_at,
                'mod' => [
                    'id' => $changelog->mod->id,
                    'name' => $changelog->mod->name
                ],
                'release' => [
                    'id' => $changelog->release->id,
                    'version' => $changelog->release->version,
                    'game_version' => $changelog->release->game_version
                ]
            ];
        });
    }
}