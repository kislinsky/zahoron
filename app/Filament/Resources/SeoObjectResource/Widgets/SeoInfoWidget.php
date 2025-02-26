<?php

namespace App\Filament\Resources\SeoObjectResource\Widgets;

use Filament\Widgets\Widget;

class SeoInfoWidget extends Widget
{
    protected static string $view = 'filament.resources.seo-object-resource.widgets.seo-info-widget';

    protected int | string | array $columnSpan = 'full'; // Виджет будет занимать всю ширину

    public function getHeading(): string
    {
        return 'Плэйсхолдеры seo';
    }

    
}
