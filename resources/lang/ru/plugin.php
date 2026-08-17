<?php

declare(strict_types=1);

return [
    'title' => 'Монитор хранилища',
    'labels' => [
        'used' => 'Использовано',
        'total' => 'Всего',
        'free' => 'Свободно',
    ],
    'errors' => [
        'disk_not_found' => 'Диск [:name] не найден.',
        'root_not_found' => 'Корневой путь не найден для диска [:name].',
        'invalid_path' => 'Путь [:path] не является допустимой директорией.',
    ],
];
