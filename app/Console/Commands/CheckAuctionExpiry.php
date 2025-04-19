<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BidsController;
use Illuminate\Support\Facades\Log;

class CheckAuctionExpiry extends Command
{
    protected $signature = 'auction:check-expiry';
    protected $description = 'Check and handle expired auctions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Starting auction expiry check.');
        $this->info('Cron job is running.');

        try {
            $bidsController = new BidsController();
            $bidsController->checkAuctionExpiry();

            Log::info('Auction expiry check completed successfully.');
            $this->info('Auction expiry check completed successfully.');
        } catch (\Exception $e) {
            Log::error('Error in auction expiry check: ' . $e->getMessage());
            $this->error('Error in auction expiry check. Check logs for more details.');
        }
    }
}
