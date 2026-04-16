<?php

declare(strict_types=1);

return [
    'title' => 'Storage Monitor',
    'labels' => [
        'used' => 'Used',
        'total' => 'Total',
        'free' => 'Free',
    ],
    'errors' => [
        'disk_not_found' => 'Disk [:name] not found.',
        'root_not_found' => 'Root path not found in disk [:name].',
        'invalid_path' => 'The path [:path] is not a valid directory.',
    ],
];
