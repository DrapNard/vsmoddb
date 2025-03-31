<?php

namespace App\Controllers\Api\V2;

use App\Core\Controller;
use App\Models\Changelog;

class ChangelogController extends Controller
{
    public function __construct()
    {
        // No authentication required for viewing changelogs
    }

    public function index()
    {
        $query = Changelog::query();

        // Filter by asset_id if provided
        if ($assetId = request()->get('asset_id')) {
            $query->where('asset_id', $assetId);
        }

        $changelogs = $query->with(['asset'])
            ->latest()
            ->paginate(20);

        return $this->json([
            'success' => true,
            'changelogs' => $this->formatChangelogs($changelogs->items()),
            'pagination' => [
                'total' => $changelogs->total(),
                'per_page' => $changelogs->perPage(),
                'current_page' => $changelogs->currentPage(),
                'last_page' => $changelogs->lastPage()
            ]
        ]);
    }

    private function formatChangelogs($changelogs)
    {
        return array_map(function($changelog) {
            $formatted = [
                'id' => $changelog->id,
                'asset_id' => $changelog->asset_id,
                'version' => $changelog->version,
                'changes' => $changelog->changes,
                'created_at' => $changelog->created_at
            ];

            // Add asset data if available
            if ($changelog->asset) {
                $formatted['asset'] = [
                    'id' => $changelog->asset->id,
                    'name' => $changelog->asset->name,
                    'type' => 'mod'
                ];
            }

            return $formatted;
        }, $changelogs);
    }
}