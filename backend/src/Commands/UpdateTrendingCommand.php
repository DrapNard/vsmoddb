<?php

namespace App\Commands;

use App\Services\TrendingService;

class UpdateTrendingCommand
{
    private $trendingService;

    public function __construct(TrendingService $trendingService)
    {
        $this->trendingService = $trendingService;
    }

    /**
     * Execute the console command to update trending points
     */
    public function handle(): void
    {
        try {
            $this->trendingService->updateTrendingPoints();
            echo "Successfully updated trending points for all mods.\n";
        } catch (\Exception $e) {
            echo "Error updating trending points: " . $e->getMessage() . "\n";
        }
    }
}