<?php

$ephemeralStashCacheDriver = array(
    'class' => '\Stash\Driver\Ephemeral',
    'options' => array()
);

$redisStashCacheDriver = array(
    'class' => '\Stash\Driver\Redis',
    'options' => array(
        'servers' => array(
            array(
                'server' => $_SERVER['CACHE1_HOST'],
                'port'   => $_SERVER['CACHE1_PORT']
            )
        )
    )
);

return array(
    'seo' => array(
        'url_rewriting' => true,
        'url_rewriting_all' => true
    ),
    'sitemap_xml' => array(
        'file' => 'application/files/sitemap.xml',
        'frequency' => 'weekly',
        'priority' => 0.5,
        'base_url' => BASE_URL
    ),
    'permissions' => array(
        'model' => 'advanced'
    ),
    'marketplace' => array(
        'enabled' => false
    ),
    'external' => array(
        'intelligent_search_help' => false,
        'news_overlay' => false,
        'news' => false
    ),
    'misc'  => array(
        'seen_introduction' => true
    ),
    'debug' => array(
        'detail' => 'debug'
    ),
    'cache' => array(
        'pages' => true,
        'levels' => array(
            'expensive' => array(
                'drivers' => array(
                    $ephemeralStashCacheDriver,
                    (defined('EPHEMERAL_ONLY_DURING_INSTALL') ? $ephemeralStashCacheDriver : $redisStashCacheDriver)
                )
            ),
            'object' => array(
                'drivers' => array(
                    $ephemeralStashCacheDriver
                )
            )
        )
    )
);
