<?php

$config = array(
    'id' => 'example-AS',
    'pkey_file' => __DIR__ . '/pkey.pem',
    'log_file' => '/tmp/as_log',
    'ttl' => 3600,
    'message_no_auth' => 'Incorrect user and/or password',
    /*'connector' => array(
        'name' => 'Simple',
        'config' => array(),
    ),*/
    /*
      'connector' => array(
      'name' => 'SimpleWithForm',
      'config' => array(),
      ),*/
     
    /* 'connector' => array(
      'name'  => 'Symfonite',
      'config'=> array(
        'dsn'    => 'mysql:dbname=edae3;host=localhost',
        'dbuser' => 'root',
        'dbpass' => 'root',
      ),
      ), */
    'connector' => array(
        'name' => 'LDAP',
        'config' => array(
            // The hostname of the LDAP server.
            'hostname' => '10.200.16.75',
            // Whether SSL/TLS should be used when contacting the LDAP server.
            'enable_tls' => FALSE,
            // Whether debug output from the LDAP library should be enabled.
            // Default is FALSE.
            'debug' => FALSE,
            // The timeout for accessing the LDAP server, in seconds.
            // The default is 0, which means no timeout.
            'timeout' => 0,
            // Which attributes should be retrieved from the LDAP server.
            // This can be an array of attribute names, or NULL, in which case
            // all attributes are fetched.
            'attributes' => NULL,
            // The pattern which should be used to create the users DN given the username.
            // %username% in this pattern will be replaced with the users username.
            //
            // This option is not used if the search.enable option is set to TRUE.
            'dnpattern' => 'uid=%username%,ou=internal,o=ITE,ou=people,dc=ite,dc=es',
            // As an alternative to specifying a pattern for the users DN, it is possible to
            // search for the username in a set of attributes. This is enabled by this option.
            'search.enable' => FALSE,
            // The DN which will be used as a base for the search.
            // This can be a single string, in which case only that DN is searched, or an
            // array of strings, in which case they will be searched in the order given.
            'search.base' => 'ou=people,dc=example,dc=org',
            // The attribute(s) the username should match against.
            //
            // This is an array with one or more attribute names. Any of the attributes in
            // the array may match the value the username.
            'search.attributes' => array('uid', 'mail'),
            // The username & password the simpleSAMLphp should bind to before searching. If
            // this is left as NULL, no bind will be performed before searching.
            'search.username' => NULL,
            'search.password' => NULL,
            // If the directory uses privilege separation,
            // the authenticated user may not be able to retrieve
            // all required attribures, a privileged entity is required
            // to get them. This is enabled with this option.
            'priv.read' => FALSE,
            // The DN & password the simpleSAMLphp should bind to before
            // retrieving attributes. These options are required if
            // 'priv.read' is set to TRUE.
            'priv.username' => NULL,
            'priv.password' => NULL,
        )
    ),
    'filters' => array(
    ),
    'url_test' => 'http://localhost/itePAPIas/web/index.php/signin',
);

