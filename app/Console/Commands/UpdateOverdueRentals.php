<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;

class UpdateOverdueRentals extends Command
{
    protected $signature = 'rentals:update-overdue';
    protected $description = 'Updates the status of active rentals that are past their due date';

    public function handle()
    {
        $count = Rental::where('status', 'Active')
            ->where('due_at', '<', now())
            ->update(['status' => 'Overdue']);

        $this->info("Successfully updated {$count} rentals to Overdue.");
    }
}
