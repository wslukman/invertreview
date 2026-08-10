<?php

namespace App\Helpers;

use App\Models\Church;
use Illuminate\Database\Eloquent\Collection;

class LocationHelper
{
    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 Latitude dari titik pertama
     * @param float $lon1 Longitude dari titik pertama
     * @param float $lat2 Latitude dari titik kedua
     * @param float $lon2 Longitude dari titik kedua
     * @return float distance dalam kilometer
     */
    public static function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // Radius bumi dalam km

        // Convert dari degree ke radian
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        // Haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }

    /**
     * Find nearby churches within radius
     * 
     * @param float $lat Latitude lokasi pencarian
     * @param float $lon Longitude lokasi pencarian
     * @param float $radiusKm Radius pencarian dalam km (default: 50)
     * @return Collection gereja-gereja yang ditemukan dengan property 'distance' ditambahkan
     */
    public static function findNearbyChurches(
        float $lat,
        float $lon,
        float $radiusKm = 50
    ): Collection {
        return Church::approved()
            ->get()
            ->map(function ($church) use ($lat, $lon) {
                $church->distance = self::calculateDistance(
                    $lat,
                    $lon,
                    $church->latitude,
                    $church->longitude
                );
                return $church;
            })
            ->filter(function ($church) use ($radiusKm) {
                return $church->distance <= $radiusKm;
            })
            ->sortBy('distance')
            ->values(); // Reset collection keys
    }

    /**
     * Get bounding box untuk search optimization (opsional)
     * 
     * @param float $lat Latitude center
     * @param float $lon Longitude center
     * @param float $radiusKm Radius dalam km
     * @return array Containing min/max lat/lon
     */
    public static function getBoundingBox(
        float $lat,
        float $lon,
        float $radiusKm = 50
    ): array {
        // Approximate conversion: 1 degree ≈ 111 km
        $latChange = $radiusKm / 111;
        $lonChange = $radiusKm / (111 * cos(deg2rad($lat)));

        return [
            'min_lat' => $lat - $latChange,
            'max_lat' => $lat + $latChange,
            'min_lon' => $lon - $lonChange,
            'max_lon' => $lon + $lonChange,
        ];
    }

    /**
     * Format jarak untuk display
     * 
     * @param float $km Jarak dalam km
     * @return string Formatted distance string (e.g., "2.5 km")
     */
    public static function formatDistance(float $km): string
    {
        if ($km < 1) {
            return round($km * 1000) . ' m';
        }
        return round($km, 2) . ' km';
    }
}
