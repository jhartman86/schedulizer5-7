<?php

return array(
    'timezone' => 'America/New_York',
    'providers' => array(
        'core_session'  => '\Application\Src\Session\SessionServiceProvider',
        'core_database' => '\Application\Src\Database\DatabaseServiceProvider'
    )
);