<?php

declare(strict_types=1);

return [
    'default' => (int) env('PAGINATION_DEFAULT', 15),
    'max' => (int) env('PAGINATION_MAX', 100),
];