<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class SendProjectAlertsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function handle(): void
    {
        Artisan::call('projects:send-alerts');
        Artisan::call('experts:check-contracts');
    }
}
