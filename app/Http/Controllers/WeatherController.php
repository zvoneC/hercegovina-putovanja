<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(City $city)
    {
        try {
            $weather = Cache::remember('weather-'.$city->id.'-'.$city->updated_at->timestamp, 1200, function () use ($city) {
                $data = Http::timeout(6)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $city->latitude, 'longitude' => $city->longitude,
                    'current' => 'temperature_2m,weather_code,wind_speed_10m', 'timezone' => 'Europe/Sarajevo',
                ])->throw()->json('current');
                if (! isset($data['temperature_2m'], $data['weather_code'], $data['wind_speed_10m'])) {
                    throw new \RuntimeException('Nedostaju vremenski podaci.');
                }
                $code = $data['weather_code'];
                $description = match (true) {
                    $code === 0 => 'Vedro', $code <= 3 => 'Djelomično oblačno', $code <= 48 => 'Magla',
                    $code <= 67 => 'Kiša', $code <= 77 => 'Snijeg', $code <= 82 => 'Pljuskovi', $code <= 86 => 'Snježni pljuskovi', default => 'Grmljavina',
                };

                return ['temperature' => $data['temperature_2m'], 'description' => $description, 'wind' => $data['wind_speed_10m']];
            });

            return response()->json($weather);
        } catch (\Throwable $error) {
            return response()->json(['message' => 'Vremenski podaci trenutačno nisu dostupni.'], 503);
        }
    }
}
