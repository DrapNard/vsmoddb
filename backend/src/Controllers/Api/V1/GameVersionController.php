<?php

namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Models\GameVersion;

class GameVersionController extends Controller
{
    public function index()
    {
        $gameVersions = GameVersion::orderBy('released_at', 'desc')->get();
        
        return $this->json([
            'success' => true,
            'gameversions' => $gameVersions->map(function($version) {
                return [
                    'id' => $version->id,
                    'version' => $version->version,
                    'released_at' => $version->released_at,
                    'is_stable' => $version->is_stable,
                    'mods_count' => $version->mods_count
                ];
            })
        ]);
    }
}