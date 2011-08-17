PAPI AUTHENTICATION SERVER
==========================

This software provides an easy-to-deploy PAPI authentication server.

## Installation

* Get the code. The easier and quicker way to do that is by downloading from:
<http://ntic.educacion.es/desarrollo/itePAPIas/itePAPIas.Beta-1.tgz>.

But if you can prefer a versioned copy, you can use a git client:

	git clone git@github.com:juandalibaba/itePAPIas.git
        git submodule init
        git submodule upload


* Configure the AS by editing the config.php  file. 

* That's all, now you can access the AS through the following URL:
	
	http(s)://yourdomain/path_to_the_web_directory/index.php/signin

## Configuration

The configuration is made by using a single PHP hierarchical associative array.

	<?php

	$config = array(
            'id' => 'example-AS',
            'pkey_file' => dirname(__FILE__) . '/pkey.pem',
            'log_file' => '/tmp/as_log',
            'ttl' => 3600,
            'message_no_auth' => 'Incorrect user and/or password',
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

### Meaning of the configuration parameters

* `$config['id']`: The identification name of the AS

* `$config['pkey_file']` : The path to the file with the private key 
which identify this AS.

* `$config['log_file']` : The path to the log file

* `$config['ttl']` : Session cookie Time To Live

* `$config['message_no_auth']` : Message to show when the authentication
process is not correct.

* `$config['connector']` : associative array with the connectors configuration
data. It must include two elements:
   $config['connector']['name'] witch is the connector name, and
   $config['connector']['config'] is an associative array with the connector
configuration data.


### Notes

It's highly recommended to set the 'web' directory as the document root
of your web server.

## Connectors

### Available connectors

### How to add and implement new Connectors

The connector is a service used by the PAPI AS framework in order to retrieve the
user's attributes from the information system where his data are stored (a
database, a LDAP directory service, a file, etc). The connector must be imple-
mented as a class with several required public method. We are going to describe
how to develop a connector to access your information service.

All the connectors reside in the src/Papi/Connectors directory. So create a
directory named {connector_name} under such ubication. For example, to create a
connector named "Simple"[1], we create the directory 'src/Papi/Connectors/Simple'.

Now, under such directory, you must create in a file named 'Connector.php' a class
named 'Connector' belonging to the namespace TeyDe\Papi\Connectors\{connector_name}.
This class will provide the interface needed to access the information system
where the attributes are stored and to return these attributes to the AS. The
interface is composed by three public methods:

        public function __construct($data, $config=null){}

        public function isAuthenticated(){}

        public function getAttributes(){}

The '__construct($data, $config=null)' method is going to initialize the object
with the data collected by the login form. If you are going to use the default
form, then the $data argument is an associative two element array wich keys are
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
rigth params in the '$config['connector']' array.

You should take a look at the existing connectors in order to get a better
insight into the connector development.

## Filters

### Available filters

### How to add and implement new Filters

### TO-DO

* Improve the configuration system
* Improve de logger system
* the getLDAPException method of TeyDe\Papi\Connectors\LDAP\LDAP class is not
implemented (neither in the original simpleSAMLphp class)

[1] This connector is included as example with the software