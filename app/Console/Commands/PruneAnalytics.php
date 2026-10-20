<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('analytics:prune {--days= : Keep this many days of data (default from config/analytics.php)}')]
#[Description('Delete visitor analytics data older than the retention period')]
class PruneAnalytics extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('analytics.keep_days'));
        $cutoff = now()->subDays($days);

        // Page views are removed with their visit (cascade), and visitors with all their visits.
        $visits = Visit::where('last_activity_at', '<', $cutoff)->delete();
        $visitors = Visitor::where('last_seen_at', '<', $cutoff)->delete();

        $this->info("Removed {$visits} visits and {$visitors} visitors older than {$days} days.");

        return self::SUCCESS;
    }
}
