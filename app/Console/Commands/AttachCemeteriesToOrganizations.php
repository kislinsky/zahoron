<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\City;
use App\Models\Cemetery;

class AttachCemeteriesToOrganizations extends Command
{
    protected $signature = 'organizations:attach-cemeteries';
    protected $description = 'Attach all cemeteries from organization\'s city';

    public function handle()
    {
        $organizations = Organization::with('city')->get();

        foreach ($organizations as $organization) {
            if ($organization->city) {
                $cemeteries = Cemetery::where('area_id', $organization->city->area_id)
                    ->pluck('id')
                    ->toArray();

                if (!empty($cemeteries)) {
                    $organization->cemetery_ids = implode(',', $cemeteries) . ',';
                    $organization->save();
                }
            }
        }

        $this->info('Successfully attached city cemeteries to organizations.');
    }
}