<?php

$config = array(
    'id' => 'example-AS',
    'pkey_file' => dirname(__FILE__) . '/pkey.pem',
    'log_file' => '/tmp/as_log',
    'ttl' => 3600,
    'message_no_auth' => 'Incorrect user and/or password',
    /*
      'connector' => array(
        'name' => 'Simple',
        'config' => array(),
      ),
     */
    /*
     'connector' => array(
        'name' => 'SimpleWithForm',
        'config' => array(),
    ),
     */
    'connector' => array(
        'name'  => 'Symfonite',
        'config'=> array(
            'dsn'    => 'mysql:dbname=edae3;host=localhost',
            'dbuser' => 'root',
            'dbpass' => 'root',            
        ),
    ),
    'filters' => array(
    ),
);

