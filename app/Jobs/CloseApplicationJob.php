<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $application;

    /**
     * Create a new job instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->application->changeStatus(4);
    }
}
