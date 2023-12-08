<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{


    /**
     * @Route("/api/vehicles", name="api_vehicles")
     */
    public function index()
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/src/Entity/vehicles.json';
        $data = json_decode(file_get_contents($filePath), true);

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/vehicles/sort/{sort?}", name="app_vehicles_sort")
     */
    public function sort(Request $request, ?string $sort = null): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/src/Entity/vehicles.json';
        $data = json_decode(file_get_contents($filePath), true);


        if ($sort === null) {
            return new JsonResponse(['error' => 'Sort parameter is missing'], JsonResponse::HTTP_BAD_REQUEST);
        }

        switch ($sort) {
            case 'price':
                usort($data, function ($a, $b) {
                    return $a['official_price'] - $b['official_price'];
                });
                break;
            case 'gross_price':
                usort($data, function ($a, $b) {
                    return $b['gross_price'] - $a['gross_price'];
                });
                break;
            case 'year':
                usort($data, function ($a, $b) {
                    return $b['year'] - $a['year'];
                });
                break;
            case 'brand':
                usort($data, function ($a, $b) {
                    return strcmp($a['brand'], $b['brand']);
                });
                break;
            case 'essence':
                foreach ($data as $vehicle) {
                    if (strtolower($vehicle['fuel_type']) == 'essence') {
                        $data[] = $vehicle;
                    }
                }
                break;
            case 'diesel':
                foreach ($data as $vehicle) {
                    if (strtolower($vehicle['fuel_type']) == 'diesel') {
                        $data[] = $vehicle;
                    }
                }
                break;

            default:
                return new JsonResponse(['error' => 'Invalid sort parameter'], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        if (empty($data)) {
            return new JsonResponse(['error' => 'No vehicles match the specified criteria'], JsonResponse::HTTP_NOT_FOUND);
        }
        return new JsonResponse($data);
    }



    /**
     * @Route("/api/vehicles/filter/{filter?}", name="api_vehicles_filter")
     */
    public function filterBySelectedValues(Request $request, ?string $filter = null): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/src/Entity/vehicles.json';
        $data = json_decode(file_get_contents($filePath), true);

        $selectedValues = explode(',', $filter);
        $filteredData = [];
        foreach ($data as $vehicle) {
            $isZeroKm = in_array('zeroKm', $selectedValues) && $vehicle['kilometer'] == 0;
            $isHybride = in_array('hybride', $selectedValues) && strtolower($vehicle['deliver']) == 'ready';
            
            if ($isZeroKm || $isHybride) {
                $filteredData[] = $vehicle;
            }
        }
        if ($filteredData) {
            $data = [];
            $data = $filteredData;
        }
        if (in_array('Promotions', $selectedValues)) {
            usort($data, function ($a, $b) {
                return $b['gross_price'] - $a['gross_price'];
            });
        }
    
        if (empty($data)) {
            return new JsonResponse(['error' => 'No vehicles match the specified criteria'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        return new JsonResponse($data);
    }


}
