<?php

return [
    /**
     * The main language of the application, the lines of this language will be
     * filled by the exact key values by default.
     */
    'base_language' => 'en',

    /**
     * PHP function we'll be looking for in your project files to collect
     * un-translated keys.
     */
    'localization_methods' => ['__'],

    /**
     * Configurations for the route group that serves the Langman Controller.
     */
    'route_group_config' => [
        'middleware' => ['web'],
        'namespace' => 'Themsaid\LangmanGUI'
    ]
];
