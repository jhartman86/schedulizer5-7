<?php

return array(
    'default-connection' => 'concrete',
    'connections' => array(
        'concrete' => array(
            'driver'    => 'c5_pdo_mysql',
            'server'    => $_SERVER['DATABASE1_HOST'],
            'database'  => $_SERVER['DATABASE1_NAME'],
            'username'  => $_SERVER['DATABASE1_USER'],
            'password'  => $_SERVER['DATABASE1_PASS'],
            'charset'   => 'utf8'
        )
    )
);
