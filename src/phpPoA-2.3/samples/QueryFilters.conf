<?php
// vim:set filetype=php:

/*
 * This is a sample configuration file.
 */

/*
 * Main global configuration. This parameters apply to any PoA defined in this
 * configuration file unless redefined locally.
 */

// a regular expression or an array of them, matching the allowed query patterns.
$queryfilter_cfg['Allowed'] = array(".*");

// a regular expression or an array of them, matching the denied query patterns.
$queryfilter_cfg['Denied'] = array(".*");

// the default behaviour of the engine, whether to accept or reject authorization
// if no filter matches. true to authorize, false in any other case.
$queryfilter_cfg['Default'] = false;

/*
 * Configuration for the site "samples". Define one for each site using the PoA.
 * Parameters defined here will override the general configuration.
 */

// a regular expression or an array of them, matching the allowed query patterns.
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$queryfilter_cfg['samples']['Allowed'] = array(
	"authorized",
	"index\.php\?param=true",
	"param" => "true"
);

// a regular expression or an array of them, matching the denied query patterns..
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$queryfilter_cfg['samples']['Denied'] = array(
	"index\.php\?param=false",
	"param" => "false"
);

/* 
 * Advanced examples of multi-valued parameters and parameter combination.
 */

// match ANY of the patterns for the defined parameter.
// ALLOWED: group=group1 OR group=group2 OR group=group3
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array("group" => "group1", "group2", "group3"),
);

// match ANY of the defined parameters.
// ALLOWED: group=some_known_group AND uid=some_known_uid
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array("group" => "some_known_group",
                           "uid" => "some_known_uid"),
);

// match ANY of the defined parameters, where parameters may match ANY pattern.
// ALLOWED: (group=group1 OR group=group2 OR group=group3)
//          OR (uid=uid1 OR uid=uid2 OR uid=uid3)
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array("group" => array("group1", "group2", "group3"),
                           "uid" => array("user1", "user2", "user3")),
);

// match ANY of the described combinations of parameters, where each combination MUST match
// ALL the parameters that are part of it.
// ALOWED: (group=group1 AND uid=user1) OR
//         (group=group2 AND uid=user2) OR
//         (group=group3 AND uid=user3)
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array(array("group" => "group1", "uid" => "user1"),
                           array("group" => "group2", "uid" => "user2"),
                           array("group" => "group3", "uid" => "user3")),
);

// match ALL the parameters which may match ANY of the patterns specified individually.
// ALLOWED: ((group=group1 OR group=group2) AND (uid=user1 OR uid=user2))
//          OR (group=group3 AND uid=user3)
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array(array("group" => array("group1", "group2"),
                                 "uid" => array("user1", "user2")),
                           array("group" => "group3", "uid" => "user3")),
);

// verify parameters and their combinations
// ALLOWED: (ANY uid AND group=group1) OR
//          (uid=user2 AND group=group2) OR
//          (uid=user3 AND ANY group)
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array(array("uid" => ".*", "group" => "group1"),
                           array("uid" => "user2", "group" => "group2"),
                           array("uid" => "user3", "group" => ".*")),
);

// PLEASE NOTE THAT CONFIGURATIONS LIKE THE FOLLOWING ARE NOT SUPPORTED
$queryfilter_cfg['advanced'] = array(
	'Allowed' => array(array("value1", "value2", "value3")),
);

?>
