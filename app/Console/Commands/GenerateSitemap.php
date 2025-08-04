<?php

namespace App\Console\Commands;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\Crematorium;
use App\Models\Mortuary;
use App\Models\Organization;
use App\Models\Church;
use App\Models\Mosque;
use App\Models\CategoryProduct;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';
    
    protected $maxUrlsPerSitemap = 40000;
    protected $currentSitemapCount = 0;
    protected $urlsCount = 0;
    protected $sitemap;
    protected $now;
    protected $citySlugs = [];
    protected $processedUrls = [];

    public function handle()
    {
        ini_set('memory_limit', '512M');
        $this->now = now();
        $this->info('Starting sitemap generation...');
        
        $this->cleanupOldSitemaps();
        $this->loadVisibleCities();
        
        $index = SitemapIndex::create();
        $this->newSitemap();
        
        $this->addStaticPages();
        $this->addDynamicRoutes();
        
        $this->writeCurrentSitemap();
        $index->add("sitemap{$this->currentSitemapCount}.xml");
        $index->writeToFile(public_path('sitemap.xml'));
        
        $this->info("Sitemap successfully generated with {$this->currentSitemapCount} parts!");
    }
    
    protected function loadVisibleCities()
    {
        $this->citySlugs = City::with(['area.edge'])
            ->whereHas('area.edge', function($query) {
                $query->where('is_show', 1);
            })
            ->whereHas('organizations')
            ->pluck('slug', 'id')
            ->all();
        
        $this->info('Loaded ' . count($this->citySlugs) . ' visible cities');
    }
    
    protected function cleanupOldSitemaps()
    {
        $files = File::glob(public_path('sitemap*.xml'));
        foreach ($files as $file) {
            File::delete($file);
        }
        $this->info('Old sitemap files deleted.');
    }
    
    protected function newSitemap()
    {
        if ($this->currentSitemapCount > 0) {
            $this->writeCurrentSitemap();
        }
        
        $this->currentSitemapCount++;
        $this->sitemap = Sitemap::create();
        $this->urlsCount = 0;
        $this->processedUrls = [];
        $this->info("Starting new sitemap part {$this->currentSitemapCount}");
    }
    
    protected function writeCurrentSitemap()
    {
        if ($this->urlsCount === 0) {
            return;
        }
        
        $filename = "sitemap{$this->currentSitemapCount}.xml";
        $this->sitemap->writeToFile(public_path($filename));
        $this->info("Generated part {$this->currentSitemapCount} with {$this->urlsCount} URLs");
    }
    
    protected function addUrl(Url $url)
    {
        $urlString = $url->url;
        
        if (isset($this->processedUrls[$urlString])) {
            return;
        }
        
        if ($this->urlsCount >= $this->maxUrlsPerSitemap) {
            $this->newSitemap();
        }
        
        $this->sitemap->add($url);
        $this->urlsCount++;
        $this->processedUrls[$urlString] = true;
    }

    protected function addStaticPages()
    {
        $staticRoutes = [
            '/' => ['priority' => 1.0, 'freq' => Url::CHANGE_FREQUENCY_YEARLY],
            '/speczialist' => ['priority' => 0.8, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            '/kontakty' => ['priority' => 0.8, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            '/terms-of-use' => ['priority' => 0.7, 'freq' => Url::CHANGE_FREQUENCY_YEARLY],
            '/our-works' => ['priority' => 0.8, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($staticRoutes as $route => $params) {
            $this->addUrl(Url::create($route)
                ->setLastModificationDate($this->now)
                ->setChangeFrequency($params['freq'])
                ->setPriority($params['priority']));
            
            foreach ($this->citySlugs as $slug) {
                $this->addUrl(Url::create("/{$slug}{$route}")
                    ->setLastModificationDate($this->now)
                    ->setChangeFrequency($params['freq'])
                    ->setPriority($params['priority'] - 0.1));
            }
        }
    }

    protected function addDynamicRoutes()
    {
        $this->addOrganizations();
        $this->addCemeteries();
        $this->addMortuaries();
        $this->addCrematoriums();
        $this->addColumbariums();
        $this->addChurches();
        $this->addMosques();
    }

    protected function addOrganizations()
    {
        $this->info('Processing organizations...');
        
        // Base organization routes
        $this->addUrl(Url::create('/organizations')
            ->setLastModificationDate($this->now)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // City organization routes
        $cityIdsWithOrgs = Organization::whereIn('city_id', array_keys($this->citySlugs))
            ->select('city_id')
            ->distinct()
            ->pluck('city_id');
            
        foreach ($cityIdsWithOrgs as $cityId) {
            if (isset($this->citySlugs[$cityId])) {
                $this->addUrl(Url::create("/{$this->citySlugs[$cityId]}/organizations")
                    ->setLastModificationDate($this->now)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8));
            }
        }

        // Organization category routes
        $categories = CategoryProduct::where('display', 1)->where('parent_id', '!=', null)->pluck('slug');
        foreach ($cityIdsWithOrgs as $cityId) {
            if (isset($this->citySlugs[$cityId])) {
                foreach ($categories as $categorySlug) {
                    $this->addUrl(Url::create("/{$this->citySlugs[$cityId]}/organizations/{$categorySlug}")
                        ->setLastModificationDate($this->now)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7));
                }
            }
        }

        // Individual organization pages
        Organization::whereIn('city_id', array_keys($this->citySlugs))
            ->select(['slug', 'city_id', 'updated_at'])
            ->orderBy('id')
            ->chunk(5000, function($organizations) {
                foreach ($organizations as $organization) {
                    if (isset($this->citySlugs[$organization->city_id])) {
                        $this->addUrl(Url::create("/{$this->citySlugs[$organization->city_id]}/organization/{$organization->slug}")
                            ->setLastModificationDate($organization->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7));
                    }
                }
            });
    }

    protected function addCemeteries()
    {
        $this->processEntity(
            Cemetery::class,
            '/cemeteries',
            '/%s/cemetery/%d',
            'cemeteries'
        );
    }

    protected function addMortuaries()
    {
        $this->processEntity(
            Mortuary::class,
            '/mortuaries',
            '/%s/mortuary/%d',
            'mortuaries'
        );
    }

    protected function addCrematoriums()
    {
        $this->processEntity(
            Crematorium::class,
            '/crematoriums',
            '/%s/crematorium/%d',
            'crematoriums'
        );
    }

    protected function addColumbariums()
    {
        $this->processEntity(
            Columbarium::class,
            '/columbariums',
            '/%s/columbarium/%d',
            'columbariums'
        );
    }
    
    protected function addChurches()
    {
        $this->processEntity(
            Church::class,
            '/churches',
            '/%s/church/%d',
            'churches'
        );
    }
    
    protected function addMosques()
    {
        $this->processEntity(
            Mosque::class,
            '/mosques',
            '/%s/mosque/%d',
            'mosques'
        );
    }

    protected function processEntity($model, $baseUrl, $itemUrlTemplate, $name)
    {
        $this->info("Processing {$name}...");
        
        // Base route
        $this->addUrl(Url::create($baseUrl)
            ->setLastModificationDate($this->now)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // City routes
        $cityIds = $model::whereIn('city_id', array_keys($this->citySlugs))
            ->select('city_id')
            ->distinct()
            ->pluck('city_id');
            
        foreach ($cityIds as $cityId) {
            if (isset($this->citySlugs[$cityId])) {
                $this->addUrl(Url::create("/{$this->citySlugs[$cityId]}{$baseUrl}")
                    ->setLastModificationDate($this->now)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8));
            }
        }

        // Individual entity pages
        $model::whereIn('city_id', array_keys($this->citySlugs))
            ->select(['id', 'city_id', 'updated_at'])
            ->orderBy('id')
            ->chunk(5000, function($items) use ($itemUrlTemplate, $name) {
                foreach ($items as $item) {
                    if (isset($this->citySlugs[$item->city_id])) {
                        $this->addUrl(Url::create(sprintf($itemUrlTemplate, $this->citySlugs[$item->city_id], $item->id))
                            ->setLastModificationDate($item->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7));
                    }
                }
            });
    }
}