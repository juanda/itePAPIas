<?php

$config = array(
    'as' => array(
        'connector' => 'SimpleWithForm',
        'id' => 'example-AS',
        'pkey_file' => dirname(__FILE__) . '/pkey.pem',
        'log_file' => '/tmp/as_log',
        'ttl' => 3600,
        'message_no_auth' => 'Incorrect user and/or password',
    ),
    'filters' => array(

    ),        
);

