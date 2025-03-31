<?php

namespace App\Services;

use App\Models\Mod;
use App\Models\Download;
use App\Models\Comment;

class TrendingService
{
    private $db;
    private $modModel;
    private $downloadModel;
    private $commentModel;

    public function __construct($db, Mod $modModel, Download $downloadModel, Comment $commentModel)
    {
        $this->db = $db;
        $this->modModel = $modModel;
        $this->downloadModel = $downloadModel;
        $this->commentModel = $commentModel;
    }

    /**
     * Update trending points for all mods based on recent downloads and comments
     * Calculates points using: downloads + (comments * 5) in last 72 hours
     */
    public function updateTrendingPoints(): void
    {
        $mods = $this->modModel->all();
        $cutoffTime = date('Y-m-d H:i:s', strtotime('-72 hours'));

        foreach ($mods as $mod) {
            $downloads = $this->downloadModel->newQuery()
                ->where('file.assetid', $mod->assetid)
                ->where('downloadip.date', '>', $cutoffTime)
                ->count();

            $comments = $this->commentModel->newQuery()
                ->where('assetid', $mod->assetid)
                ->where('created', '>', $cutoffTime)
                ->count();

            $trendingPoints = $downloads + (5 * $comments);
            
            $mod->trendingpoints = $trendingPoints;
            $mod->save($this->db);
        }
    }
}