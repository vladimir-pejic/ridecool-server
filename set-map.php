<?php

require 'loader.php';

try {
    $indexParams['index']  = 'places'; // the name of the index

    $myTypeMapping = [
        '_source' => [
            'enabled' => true
        ],
        'properties' => [
            'from_coords' => [
                'type' => 'geo_point'
            ],
            'to_coords' => [
                'type' => 'geo_point'
            ],
            'current_coords' => [
                'type' => 'geo_point'
            ],
            'from_bounds.top_left.coords' => [
                'type' => 'geo_point'
            ],
            'from_bounds.bottom_right.coords' => [
                'type' => 'geo_point'
            ],
            'to_bounds.top_left.coords' => [
                'type' => 'geo_point'
            ],
            'to_bounds.bottom_right.coords' => [
                'type' => 'geo_point'
            ]
        ]
    ];
} catch (\Exception $e) {
    echo 'err: ' . $e->getMessage();
}