<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

class RereadCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reread-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cities=City::all();
        foreach($cities as $city){
            if(isset(getCoordinatesCity($city->title)['results'][0]['geometry'])){
                $coordinates=getCoordinatesCity($city->title)['results'][0]['geometry'];
                $city->update([
                    'width'=>$coordinates['lat'],
                    'longitude'=>$coordinates['lng'],
                    ]);    
            }
            
        }
    }
}
 