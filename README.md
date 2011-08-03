PAPI AUTHENTICATION SERVER
==========================

This software provides an easy-to-deploy PAPI authentication server.

## Installation

* Get the code. You can directly download from this github repository or 
if you prefer a versioned copy, you can use a git client:

	git clone git@github.com:juandalibaba/itePAPIas.git

* Configure the AS by editing the config.php  file. 

* That's all, now you can access the AS through the following URL:
	
	http(s)://yourdomain/path_to_the_web_directory/index.php/signin

## Configuration

The configuration is made by using a single PHP hierarchical associative array.

	<?php

	$config = array(
    		'as' => array(
        		'connector' => 'Simple',
        		'id' => 'example-AS',
        		'pkey_file' => dirname(__FILE__) . '/pkey.pem',
        		'log_file' => '/tmp/as_log',
        		'ttl' => 3600,
        		'message_no_auth' => 'Incorrect user and/or password',
    			),
    		'filters' => array(

    			),
	);

### Meaning of the configuration parameters

* `$config['as']['connector']` : the name of the connector used to retrieve 
the attributes from the information service (database, LDAP, etc)

* `$config['as']['id']`: The identification name of the AS

* `$config['as']['pkey_file']` : The path to the file with the private key 
which identify this AS.

* `$config['as']['log_file']` : The path to the log file

* `$config['as']['ttl']` : Session cookie Time To Live

* `$config['as']['message_no_auth']` : Message to show when the authentication
process is not correct.
## Notes

It's highly recommended to set the 'web' directory as the document root
of your web server.
