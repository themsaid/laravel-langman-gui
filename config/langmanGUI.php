<?php

return [
    'base_language' => 'en',

    'routeGroupConfig' => [
        'middleware' => ['web', 'auth', 'auth.foodics_admin']
    ]
];
