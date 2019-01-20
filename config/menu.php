<?php

return [
    [
        'icon' => 'fa fa-chart-pie',
        'crud' => 'home',
        'route' => 'home',
    ],
    [
        'icon' => 'fa fa-cog',
        'name' => 'configuration',
        'submenu' => [
            [
                'name' => 'permissions',
                'submenu' => [
                    [
                        'crud' => 'roles',
                    ],
                    [
                        'crud' => 'users',
                    ],
                ]
            ],
        ],
    ],
];
