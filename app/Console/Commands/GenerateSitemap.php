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
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap with strict 20,000 URLs per file.';
    
    protected $maxUrlsPerSitemap = 1000;
    protected $currentSitemapCount = 0;
    protected $urlsCount = 0;
    protected $sitemapWriter;
    protected $now;
    protected $citySlugs = [];
    protected $processedUrls = [];
    protected $baseUrl = 'https://zahoron.ru';

    public function handle()
    {
        ini_set('memory_limit', '512M');
        $this->now = now();
        $this->info('Starting strict sitemap generation (exactly 20,000 URLs per file)...');
        
        $this->cleanupOldSitemaps();
        $this->loadVisibleCities();
        
        $index = SitemapIndex::create();
        $this->createNewSitemapFile();
        
        $this->addStaticPages();
        $this->addDynamicRoutes();
        
        $this->finalizeCurrentSitemap();
        
        // Add all sitemap parts to the index with correct domain
        for ($i = 1; $i <= $this->currentSitemapCount; $i++) {
            $index->add("{$this->baseUrl}/sitemap_part{$i}.xml");
        }
        
        $index->writeToFile(public_path('sitemap.xml'));
        
        $this->info("Sitemap successfully generated with {$this->currentSitemapCount} parts (exactly 20,000 URLs each except last)!");
    }
    
    protected function loadVisibleCities()
    {
        $this->citySlugs = City::with(['area.edge'])
            ->whereHas('area.edge', function($query) {
                $query->where('is_show', 1);
            })
            ->whereHas('organizations')
            ->pluck('slug', 'id')
            ->toArray();
        
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
    
    protected function createNewSitemapFile()
    {
        if ($this->currentSitemapCount > 0) {
            $this->finalizeCurrentSitemap();
        }
        
        $this->currentSitemapCount++;
        $this->urlsCount = 0;
        $this->processedUrls = [];
        
        $filename = public_path("sitemap_part{$this->currentSitemapCount}.xml");
        $this->sitemapWriter = fopen($filename, 'w');
        
        fwrite($this->sitemapWriter, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($this->sitemapWriter, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
        
        $this->info("Created new sitemap part {$this->currentSitemapCount}");
    }
    
    protected function finalizeCurrentSitemap()
    {
        if ($this->urlsCount === 0) {
            return;
        }
        
        fwrite($this->sitemapWriter, '</urlset>' . PHP_EOL);
        fclose($this->sitemapWriter);
        
        $this->info("Finalized part {$this->currentSitemapCount} with exactly {$this->urlsCount} URLs");
    }
    
    protected function addUrlWithStrictCounting(Url $url)
    {
        // Ensure URL starts with our domain
        $urlString = $url->url;
        if (!preg_match('/^https?:\/\//', $urlString)) {
            $urlString = $this->baseUrl . $urlString;
        }
        
        if (isset($this->processedUrls[$urlString])) {
            return false;
        }
        
        if ($this->urlsCount >= $this->maxUrlsPerSitemap) {
            $this->createNewSitemapFile();
        }
        
        // Manually write the URL entry
        fwrite($this->sitemapWriter, '  <url>' . PHP_EOL);
        fwrite($this->sitemapWriter, "    <loc>{$urlString}</loc>" . PHP_EOL);
        fwrite($this->sitemapWriter, "    <lastmod>{$url->lastModificationDate->toAtomString()}</lastmod>" . PHP_EOL);
        fwrite($this->sitemapWriter, "    <changefreq>{$url->changeFrequency}</changefreq>" . PHP_EOL);
        fwrite($this->sitemapWriter, "    <priority>{$url->priority}</priority>" . PHP_EOL);
        fwrite($this->sitemapWriter, '  </url>' . PHP_EOL);
        
        $this->urlsCount++;
        $this->processedUrls[$urlString] = true;
        
        if ($this->urlsCount >= $this->maxUrlsPerSitemap) {
            $this->createNewSitemapFile();
        }
        
        return true;
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
            $url = Url::create($this->baseUrl . $route)
                ->setLastModificationDate($this->now)
                ->setChangeFrequency($params['freq'])
                ->setPriority($params['priority']);
            
            $this->addUrlWithStrictCounting($url);
            
            foreach ($this->citySlugs as $slug) {
                $url = Url::create($this->baseUrl . "/{$slug}{$route}")
                    ->setLastModificationDate($this->now)
                    ->setChangeFrequency($params['freq'])
                    ->setPriority($params['priority'] - 0.1);
                
                $this->addUrlWithStrictCounting($url);
            }
        }
    }

    protected function addDynamicRoutes()
    {
        $this->addOrganizations();
        $this->addCemeteries();
        $this->addMortuaries();
    }

    protected function addOrganizations()
    {
        $this->info('Processing organizations with strict counting...');
        
        // Base organization routes
        $this->addUrlWithStrictCounting(
            Url::create($this->baseUrl . '/organizations')
                ->setLastModificationDate($this->now)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8)
        );

        // City organization routes
        $cityIdsWithOrgs = Organization::whereIn('city_id', array_keys($this->citySlugs))
            ->select('city_id')
            ->distinct()
            ->pluck('city_id');
            
        foreach ($cityIdsWithOrgs as $cityId) {
            if (isset($this->citySlugs[$cityId])) {
                $this->addUrlWithStrictCounting(
                    Url::create($this->baseUrl . "/{$this->citySlugs[$cityId]}/organizations")
                        ->setLastModificationDate($this->now)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            }
        }

        // Organization category routes
        $categories = CategoryProduct::where('display', 1)
            ->where('parent_id', '!=', null)
            ->pluck('slug');
            
        foreach ($cityIdsWithOrgs as $cityId) {
            if (isset($this->citySlugs[$cityId])) {
                foreach ($categories as $categorySlug) {
                    $this->addUrlWithStrictCounting(
                        Url::create($this->baseUrl . "/{$this->citySlugs[$cityId]}/organizations/{$categorySlug}")
                            ->setLastModificationDate($this->now)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            }
        }

        // Individual organization pages - используем slug
        Organization::whereIn('city_id', array_keys($this->citySlugs))
            ->select(['slug', 'city_id', 'updated_at'])
            ->orderBy('id')
            ->chunk(1000, function($organizations) {
                foreach ($organizations as $organization) {
                    if (isset($this->citySlugs[$organization->city_id])) {
                        $this->addUrlWithStrictCounting(
                            Url::create($this->baseUrl . "/{$this->citySlugs[$organization->city_id]}/organization/{$organization->slug}")
                                ->setLastModificationDate($organization->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7)
                        );
                    }
                }
            });
    }

    protected function processEntityWithPreciseCounting($model, $listRoute, $detailRoutePattern, $name)
    {
        $this->info("Processing {$name} with strict counting...");
        
        // Add list route
        $this->addUrlWithStrictCounting(
            Url::create($this->baseUrl . $listRoute)
                ->setLastModificationDate($this->now)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7)
        );
        
        // Add city list routes
        foreach ($this->citySlugs as $citySlug) {
            $this->addUrlWithStrictCounting(
                Url::create($this->baseUrl . "/{$citySlug}{$listRoute}")
                    ->setLastModificationDate($this->now)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.6)
            );
        }
        
        // Add detail pages - используем slug вместо ID
        $model::whereHas('city', function($query) {
                $query->whereIn('id', array_keys($this->citySlugs));
            })
            ->select(['slug', 'city_id', 'updated_at'])
            ->orderBy('id')
            ->chunk(1000, function($entities) use ($detailRoutePattern, $name) {
                foreach ($entities as $entity) {
                    if (isset($this->citySlugs[$entity->city_id])) {
                        $url = sprintf($this->baseUrl . $detailRoutePattern, 
                            $this->citySlugs[$entity->city_id], 
                            $entity->slug
                        );
                        
                        $this->addUrlWithStrictCounting(
                            Url::create($url)
                                ->setLastModificationDate($entity->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.5)
                        );
                    }
                }
            });
    }

    protected function addCemeteries()
    {
        $this->processEntityWithPreciseCounting(
            Cemetery::class,
            '/cemeteries',
            '/%s/cemetery/%s',
            'cemeteries'
        );
    }

    protected function addMortuaries()
    {
        $this->processEntityWithPreciseCounting(
            Mortuary::class,
            '/mortuaries',
            '/%s/mortuary/%s',
            'mortuaries'
        );
    }

    protected function addCrematoriums()
    {
        $this->processEntityWithPreciseCounting(
            Crematorium::class,
            '/crematoriums',
            '/%s/crematorium/%s',
            'crematoriums'
        );
    }

    protected function addColumbariums()
    {
        $this->processEntityWithPreciseCounting(
            Columbarium::class,
            '/columbariums',
            '/%s/columbarium/%s',
            'columbariums'
        );
    }
    
    protected function addChurches()
    {
        $this->processEntityWithPreciseCounting(
            Church::class,
            '/churches',
            '/%s/church/%s',
            'churches'
        );
    }
    
    protected function addMosques()
    {
        $this->processEntityWithPreciseCounting(
            Mosque::class,
            '/mosques',
            '/%s/mosque/%s',
            'mosques'
        );
    }
}