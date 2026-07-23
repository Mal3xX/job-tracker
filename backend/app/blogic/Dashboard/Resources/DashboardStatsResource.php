<?php

declare(strict_types=1);

namespace App\blogic\Dashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per le statistiche della dashboard.
 */
class DashboardStatsResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return $this->resource;
    }
}