<?php

return array(
    'remote' => array(
        'name' => 'production',
        'migrations' => '/home/htdocs/testing/migrations/',
        'seeds' => '/home/htdocs/testing/seeds/'
    ),
    'export_path' => array(
        'migrations' => app_path().'/database/migrations/',
        'seeds' => app_path().'/database/seeds/'
    )
);
