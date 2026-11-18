<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Traits;

use Illuminate\Http\Request;

trait ResolvesPerPage
{
    protected function resolvePerPage(Request $request): int
    {
        $default = (int) config('pagination.default', 15);
        $max = (int) config('pagination.max', 100);
        $raw = $request->query('per_page');

        if (!is_scalar($raw) || !is_numeric($raw)) {
            return $default;
        }

        $value = (int) $raw;

        return $value < 1 ? $default : min($value, $max);
    }
}
