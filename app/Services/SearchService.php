<?php

namespace App\Services;

use App\Models\Product;

class SearchService
{
    /**
     * Perform a search based on given parameters.
     *
     * @param array $params
     * @return array
     */
    public function search(array $params)
    {
        $query = Product::where('status', 1);

        // Search by destination (maps to product name or description as a basic implementation)
        if (!empty($params['destination'])) {
            $destination = $params['destination'];
            $query->where(function ($q) use ($destination) {
                $q->where('name', 'LIKE', "%{$destination}%")
                  ->orWhere('description', 'LIKE', "%{$destination}%");
            });
        }

        // Additional filters could be added here (e.g. days, date, guests)
        // Since we may not have these fields on Product model, we just keep it simple for now.

        $products = $query->latest()->paginate(10);

        return [
            'products' => $products
        ];
    }
}
