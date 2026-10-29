<?php

return [
    'node_binary' => env('BROWSERSHOT_NODE_BINARY', env('NODE_BINARY')),
    'npm_binary' => env('BROWSERSHOT_NPM_BINARY', env('NPM_BINARY')),
    'chrome_path' => env('BROWSERSHOT_CHROME_PATH', env('CHROME_PATH')),
    'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH', env('NODE_MODULES_PATH', base_path('node_modules'))),
    'timeout' => env('BROWSERSHOT_TIMEOUT'),

    'paths' => [
        'plans' => env('PDF_PLANS_DIRECTORY') ?: public_path('plans'),
        'catalogs' => env('PDF_CATALOGS_DIRECTORY') ?: public_path('catalogs'),
    ],
];
