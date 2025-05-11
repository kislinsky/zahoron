<?php

namespace App\Services\Parser;

use App\Models\Area;
use App\Models\City;
use App\Models\Edge;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ParserGeoService
{
    public static function index($request)
    {
        set_time_limit(300);
        $files = $request->file('files');
        $importedFiles = [];

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $fileName = $file->getClientOriginalName();
            $importedFiles[] = $fileName;

            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                continue;
            }

            $headers = array_shift($rows);
            $columns = array_flip($headers);

            foreach ($rows as $row) {

                // Получаем данные из строки
                $edgeName = trim($row[$columns['region'] ?? '']);
                $areaName = trim($row[$columns['municipality'] ?? '']);
                $cityName = trim($row[$columns['settlement'] ?? '']);
                $latitude = $row[$columns['latitude'] ?? null];
                $longitude = $row[$columns['longitude'] ?? null];
                $utcOffset = self::parseUtcOffset($row[$columns['utc_offset'] ?? null]);


                // Пропускаем если нет названия края
                if (empty($edgeName)) {
                    continue;
                }

                // Обрабатываем край (Edge)
                $edge = Edge::firstOrCreate(['title' => $edgeName]);

                // Обрабатываем округ (Area) если указан
                $area = null;
                if (!empty($areaName)) {
                    $area = Area::firstOrCreate([
                        'title' => $areaName,
                        'edge_id' => $edge->id
                    ]);
                }

                // Обрабатываем город (City) если указан
                if (!empty($cityName)) {
                    $cityData = [
                        'title' => $cityName,
                        'area_id' => $area->id ?? null,
                        'edge_id' => $edge->id,
                        'width' => is_numeric($latitude) ? (float)$latitude : null,
                        'longitude' => is_numeric($longitude) ? (float)$longitude : null,
                        'utc_offset' => $utcOffset
                    ];

                    City::updateOrCreate(
                        ['title' => $cityName],
                        array_filter($cityData, fn($value) => $value !== null)
                    );
                }
            }
        }

        $message = 'Географические данные успешно импортированы';
        if (!empty($importedFiles)) {
            $message .= ' (файлы: ' . implode(', ', $importedFiles) . ')';
        }

        return redirect()->back()->with('message_cart', $message);
    }

    /**
     * Преобразует UTC offset из строки в целое число
     * Примеры:
     * "UTC+3" → 3
     * "UTC-5" → -5
     * "UTC+10:30" → 10 (только целые часы)
     */
    private static function parseUtcOffset(?string $offset): ?int
    {
        if (empty($offset)) {
            return null;
        }

        if (preg_match('/^UTC([+-]\d+)/', $offset, $matches)) {
            return (int)$matches[1];
        }

        return null;
    }
}