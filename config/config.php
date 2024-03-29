<?php

/*
  This file is part of itePAPIas.
  Foobar is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Foobar is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php

$config = array(
    'id' => 'example-AS',
    'prvkey_file' => __DIR__ . '/pkey.pem',
    'pubkey_file' => __DIR__ . '/pubkey.pem', //only needed for test action
    'log_file' => '/tmp/as_log',
    'ttl' => 3600,
    'message_no_auth' => 'Incorrect user and/or password',
    'debug' => true,
    
      'connector' => array(
      'name' => 'Simple',
      'config' => array(),
      ),
    
    /*
      'connector' => array(
      'name' => 'SimpleWithForm',
      'config' => array(),
      ), */

   /*
    'connector' => array(
        'name' => 'Symfonite',
        'config' => array(
            'dsn' => 'mysql:dbname=edae3;host=localhost',
            'dbuser' => 'root',
            'dbpass' => 'root',
        ),
    ),
    */
    /*
      'connector' => array(
      'name' => 'SQL',
      'config' => array(
      'dsn' => 'mysql:dbname=users;host=localhost',
      'dbuser' => 'root',
      'dbpass' => 'root',
      'sql' => 'SELECT username as uid, name, email FROM users WHERE username = :username AND password = :password',
      // Important: It must be an attributed named uid which identifies the user
      ),
      ),
     */
    /*
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
     */

    /*
      'filters' => array(
      '0' => array(
      'class_name' => 'AttributePrune',
      'config' => array(
      'attributes_to_prune' => array('att1', 'ePa'),
      ),
      ),
      '1' => array(
      'class_name' => 'AttributeReverse',
      'config' => array(),
      ),
      ),
     */

    'url_test' => 'http://localhost/itePAPIas/web/index.php',
);

