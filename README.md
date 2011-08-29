PAPI AUTHENTICATION SERVER
==========================

This software provides an easy-to-deploy PAPI authentication server.

## Installation

* Get the code. The easier and quicker way to do that is by downloading from:
<http://ntic.educacion.es/desarrollo/itePAPIas/itePAPIas.Beta-1.tgz>.

But if you can prefer a versioned and up to date copy, you should use a git client:

        git clone git@github.com:juandalibaba/itePAPIas.git
        cd itePAPIas
        git submodule init
        git submodule update

* Configure the AS by editing the config.php file (see the next section).

* That's all, now you can access the AS through the following URL:
	
        http(s)://yourdomain/path_to_the_web_directory/index.php/signin

In order to test the Authentication Server (AS) you need a web application
(service provider) which perform a identification request on the AS. If you
haven't yet such service provider, you can perform the test through the
following URL:

        http(s)://yourdomain/path_to_the_web_directory/index.php/test

You can try with the username "anselmo" and password "pruebas".

Important! You must make writable to the web server the 'src/phpPoA-2.3/log'
directory in order to run succesfully the test.

## Configuration

The configuration is made by using a single PHP hierarchical associative array
located in config/config.php file.

	<?php

	$config = array(
            'id' => 'example-AS',
            'pkey_file' => dirname(__FILE__) . '/pkey.pem',
            'log_file' => '/tmp/as_log',
            'ttl' => 3600,
            'message_no_auth' => 'Incorrect user and/or password',
            'debug' => 'true',
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

            'url_test' => 'http://localhost/itePAPIas/web/index.php/signin',
);

### Meaning of the configuration parameters

* `$config['id']`: The identification name of the AS

* `$config['pkey_file']` : The path to the file with the private key used to sign
the assertions builded by this AS.

* `$config['log_file']` : The path to the log file

* `$config['ttl']` : Session cookie Time To Live

* `$config['message_no_auth']` : Message to show when the authentication
process is not correct.

* `$config['debug']` : User to activate/deactivate the debug functionality. When
it is active all the catched exception are show with verbosity in order to help
the debugging process. Else, just an error 500 is shown.

* `$config['connector']` : associative array with the connectors configuration
data. It must include two elements:
   $config['connector']['name'] witch is the connector name, and
   $config['connector']['config'] is an associative array with the connector
configuration data. Each connector defines its configuration array.

* `$config['filters']` : associative arrar with the chain of filter to be applied
on the attributes returned by the connector.

* `$config['url_test']` : The test action url.


### Notes

It's highly recommended to set the 'web' directory as the document root
of your web server.

## Connectors

### Available connectors

You can find in a commented section of the config file a configuration example
for every connector.

#### LDAP Connector

This connector retrieves the user attributes from a LDAP service.

#### Simple Connector

This is an very simple connector which takes the user attributes directly from
an array. It is intended to show the connector structure and to help to develop
your own connectors.

#### SimpleWithForm Connector

As the Simple Connector, this one takes the user attributes directly from
an array, but it also needs an identification number (DNI), besides the usual
username and password credentials, to perform the authentication. This connector
is intended to show how to develop connectors which use credentials beyond the
usual username and password. Here you can see how to include a form with the
data you need to perform the authentication process.

#### SQL Connector

A connector to retrieve the user attribute from an PDO compatible database by
means of a simple SQL query.

#### Symfonite Connector

A connector to retrive the user attribute from a [symfonite][1] system


### How to add and implement new Connectors

The connector is a service used by the PAPI AS framework in order to retrieve the
user's attributes from the information system where his data are stored (a
database, a LDAP directory service, a file, etc). The connector must be imple-
mented as a class with several required public method (an interface). We are
going to describe how to develop a connector to access your information service.

All the connectors reside in the src/TeyDe/Papi/Connectors directory. You must
create a directory named {connector_name} under such ubication. For example, to
create a connector named "Simple"[2], we create the directory:

        'src/Papi/Connectors/Simple'.

A class named 'Connector' belonging to the namespace

        TeyDe\Papi\Connectors\{connector_name}.

will provide the interface needed to access the information system where the
attributes are stored and will return these attributes to the AS. This class must
reside into a file named 'Connector.php'. The interface is composed by three
public methods:

        public function __construct($data, $config=null){}

        public function isAuthenticated(){}

        public function getAttributes(){}

The '__construct($data, $config=null)' method is going to initialize the object
with the data collected by the login form. If you are going to use the default
form, then the $data argument is a two element associative array wich keys are
'username' and 'password', and which values are what you can imagine. If needed,
the login form and the data collected by him can be redefined. How to do this will
be later explained.

if the connector needs some configuration parameters, you should define them
in the '$config['connector]['config']' array of the config.php file. For example:

        $config = array(
            ...
            'connector' => array(
                'name'  => 'Symfonite',
                'config'=> array(
                    'dsn'    => 'mysql:dbname=edae3;host=localhost',
                    'dbuser' => 'root',
                    'dbpass' => 'root',
                ),
            ),
            ...

The $config parameter of the __construct method is set with the
'$config['connector]['config']' value.


The 'isAuthenticated()' method must return true if the data retrieved from the
login form corresponds to a authenticated user in the information systemm and
false otherwise.

The 'getAttributes()' method must returned a normalized array of attributes
associated to the user. Such array must be an associative array
which keys are the names of the attributes and wich values can be scalars with
the attributes values or arrays of values when the attribute is multi-valued.
For example:

        $attributes = array(
            'attr1' => val1,
            'attr2' => array(val21,val22,val23),
            );


And that's all folks!. Now you can use the your new connector by setting the
rigth params in the configuration file by setting the '$config['connector']'
array.

You should take a look at the existing connectors in order to get a better
insight into the connector development.

## Filters

### Available filters

### How to add and implement new Filters

Once the user attributes have been retrieved thanks to the connector, the PAPI
framework can filter them before the PAPI assertion is built and sent.

#### How to use the filters

You can use the available filters which reside in the

         src/TeyDe/Papi/Filters

directory.

In order to use these filters you must add them to the filters section of the
$config array in the config/congig.php file:

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

You can add as many filters as you want to the sequence. The documentation
of each filter must explain the correct values of its parameters.

#### How to create new filters

Create a new filter is as easy as create a new class wich defines a public
static method called 'execute($attributes, $configuration)'. Such method must
get as the first argument the attributes array and as the second an array with
the configuration defined in the $config array. The method must return an array
with the filtered attributes.

Take a look at the classes defined in the 'lib/filters' directory to see how
simple is a filter implementation.

# Copyright and License

Copyright 2011 Juan David Rodríguez García (juandalibaba@gmail.com).

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

## Third parties licenses

The current software uses the following third parties sofware as
libraries:

silex (http://silex-project.org)                      -------> MIT License
Symfony/Component/Validator (http://symfony.com)      -------> MIT License
phpPoA-2.3 (https://forja.rediris.es/projects/phppoa) -------> GNU License


# TO-DO

* Improve the configuration system
* Improve de logger system
* the getLDAPException method of TeyDe\Papi\Connectors\LDAP\LDAP class is not
implemented (neither in the original simpleSAMLphp class)


[1]: http://ntic.educacion.es/desarrollo/symfonite
[2]: This connector is included as example with the software
