<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePriorityOrganization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $organization;

    public function __construct($organization)
    {
        $this->organization = $organization;
    }

   public function handle(): void
    {
        $this->organization->update([
            'priority'=>0,
            'rotation_order'=>0,
        ]);
    }
}