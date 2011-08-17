<?php
// vim:set filetype=php:

/*
 * This is a sample configuration file.
 */

/*
 * Main global configuration. This parameters apply to any PoA defined in this
 * configuration file unless redefined locally.
 */

$invite_cfg = array(

///////////////////////
// DATABASE SETTINGS //
///////////////////////

// INVITES DATABASE
//
// The type of database for invites storage to use.
// One of the following:
// * INVITES_DBA
// * INVITES_MYSQL
// * INVITES_SESSION
// WARNING: please note that using the INVITES_SESSION database
// backend may break applications using sessions underneath.
'InvitesDBType'              => INVITES_DBA,

// the database file.
// WARNING: DBA ONLY!
'InvitesDBFile'              => '../var/db/invites.db4',

// the database server.
// WARNING: MySQL ONLY!
'InvitesDBHost'              => 'db.example.com',

// the database username.
// WARNING: MySQL ONLY!
'InvitesDBUser'              => 'example',

// the user's password.
// WARNING: MySQL ONLY!
'InvitesDBPassword'          => 'example',

// the database name.
// WARNING: MySQL ONLY!
'InvitesDBName'              => 'example',

// the database table prefix.
// WARNING: MySQL ONLY!
'InvitesDBPrefix'            => 'example',

// AUTHORIZED DATABASE
//
// the type of database for authorized list storage to use.
// One of the following:
// * AUTHORIZED_DBA
// * AUTHORIZED_MYSQL
// * AUTHORIZED_SESSION
// WARNING: please note that using the AUTHORIZED_SESSION database
// backend may break applications using sessions underneath.
'AuthorizedDBType'           => AUTHORIZED_DBA,

// the database file.
// WARNING: DBA ONLY!
'AuthorizedDBFile'           => '../var/db/authz.db4',

// the database server.
// WARNING: MySQL ONLY!
'AuthorizedDBHost'           => 'db.example.com',

// the database username.
// WARNING: MySQL ONLY!
'AuthorizedDBUser'           => 'example',

// the user's password.
// WARNING: MySQL ONLY!
'AuthorizedDBPassword'       => 'example',

// the database name.
// WARNING: MySQL ONLY!
'AuthorizedDBName'           => 'example',

// the database table prefix.
// WARNING: MySQL ONLY!
'AuthorizedDBPrefix'         => 'example',

////////////////////
// OTHER SETTINGS //
////////////////////

// admin e-mail address
'AdminEmail'                 => 'webmaster@rediris.es',

// an array with all the possible attributes that will be checked to identify a user.
// If you want to use a combination of attributes, express it as an array of names of
// the attributes. Please note that order matters, and therefore the first attribute
// (or a combination of them) found in the configuration that is available in the attributes
// presented by the user will be used.
'UniqueAttributes'           => array('mail',
                                      array('ePTI', 'sHO'),
                                      array('uid', 'sHO')),

// whether to verify user's e-mail when authorizing or not.
'EmailVerify'                => true,

// the attribute that will be used for e-mail verification
'EmailVerifyAttribute'       => 'mail',

// a regular expression that matches the format of the e-mail verification attribute, with
// a pair of parenthesis surrounding the slice of the regular expression that matches the
// function used to calculate the value received.
//'EmailVerifyAlgRegEx'        => '',

// a regular expression that matches the format of the e-mail verification attribute, with
// a pair of parenthesis surrounding the slice of the regular expression that matches
// the computed value received.
//'EmailVerifyRegEx'           => '(.*)',

// the URL where users will be pointed out in the invitation e-mails to complete their
// access to the application.
'InviteURL'                  => 'http://www.rediris.es/',

// the text that will be sent to the user in the invitation e-mail. Please note that
// you must put the string '##URL##' where you want the invitation URL to appear.
// Omitting that string will cause your invitation e-mails to be sent without that URL
// and therefore they will be useless.
'InviteText'                 => "Please follow the link:\n\n##URL##",

// the subject of the e-mails sent to the user.
'InviteSubject'              => "You've been invited to access our web page",

// the default behaviour of the engine, whether to accept or reject authorization
// if no filter matches.
'Default'                    => false
);

/*
 * Configuration for the site "sample7". Define one for each site using the PoA.
 * Parameters defined here will override the general configuration.
 */
$url = "http";
if (isset($_SERVER['HTTPS'])) $url .= "s";
$url .= "://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];

$invite_cfg['sample7'] = array(
'InviteURL'                  => $url.'?action=authorize',
'EmailVerifyAttribute'       => 'sPUC',
'EmailVerifyAlgRegEx'        => 'urn:mace:terena.org:schac:personalUniqueCode:es:rediris:sir:mbid:\{([^\}]*)\}',
'EmailVerifyRegEx'           => 'urn:mace:terena.org:schac:personalUniqueCode:es:rediris:sir:mbid:\{[^\}]*\}(.*)',

);

?>
