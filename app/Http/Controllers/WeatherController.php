<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $apiKey = env('OPENWEATHERMAP_API_KEY');
        
        $cityName = 'Kuressaare';
        
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$apiKey&units=metric";

        $cacheKey = 'weather_data_kuressaare';

        if (Cache::has($cacheKey)) {
            $weatherData = Cache::get($cacheKey);
        } else {
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $weatherData = $response->json();
                Cache::put($cacheKey, $weatherData, now()->addHour());
            } else {
                Log::error('Failed to fetch weather data from the API. Response: ' . $response->body());
                $errorDetails = [
                    'message' => 'Failed to fetch weather data.',
                    'details' => $response->json() ?? 'No additional information available.'
                ];
                return view('error', compact('errorDetails'));
            }
        }

        return view('weather', compact('weatherData'));
    }
}