<?php

class VehicleService
{
    private array $vehicles;

    public function __construct(string $filePath)
    {
        $data = json_decode(file_get_contents($filePath), true);
        $this->vehicles = array_map(function ($vehicleData) {
            $vehicle = new Vehicle();
            foreach ($vehicleData as $key => $value) {
                $vehicle->$key = $value;
            }
            return $vehicle;
        }, $data);
    }

    public function getVehicles(): array
    {
        return $this->vehicles;
    }

    public function sortVehicles(string $sort): array
    {
        $data = $this->vehicles;

        switch ($sort) {
            case 'price':
                usort($data, function ($a, $b) {
                    return $a->official_price - $b->official_price;
                });
                break;
            case 'gross_price':
                usort($data, function ($a, $b) {
                    return $b->gross_price - $a->gross_price;
                });
                break;
            case 'year':
                usort($data, function ($a, $b) {
                    return $b->year - $a->year;
                });
                break;
            case 'essence':
                $data = array_filter($data, function ($vehicle) {
                    return $vehicle->fuel_type === 'essence';
                });
                break;
            case 'diesel':
                $data = array_filter($data, function ($vehicle) {
                    return $vehicle->fuel_type === 'diesel';
                });
                break;
        }

        return $data;
    }

}
