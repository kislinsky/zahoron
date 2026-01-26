<?php

namespace App\Jobs;

use App\Models\ImageProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $productId;
    public string $imageUrl;

    public function __construct(string $productId, string $imageUrl)
    {
        $this->productId = $productId;
        $this->imageUrl = $imageUrl;
    }

    public function handle(): void
    {
        try {
            $imageContent = file_get_contents($this->imageUrl);

            if ($imageContent !== false) {
                $imageName = 'product_' . $this->productId . '_' . time() . '.jpg';
                $imagePath = 'uploads_product/' . $imageName;

                Storage::disk('public')->put($imagePath, $imageContent);

                ImageProduct::create([
                    'product_id' => $this->productId,
                    'title'      => $imagePath,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Не удалось скачать фото для товара {$this->productId}: " . $e->getMessage());
        }
    }
}
