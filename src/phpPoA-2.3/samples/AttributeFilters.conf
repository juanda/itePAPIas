<?php
// vim:set filetype=php:

/*
 * This is a sample configuration file.
 * @package phpPoA2
 * @subpackage AttributeFilterAuthorizationEngine
 */

/*
 * Main global configuration. This parameters apply to any PoA defined in this
 * configuration file unless redefined locally.
 */

// group definitions
$REDIRIS_G = "rediris\.es";
$ALL_G = ".*";

// a regular expression or an array of them, matching the allowed attr patterns.
$attributefilter_cfg['Allowed']['sHO'] = array($ALL_G);

// a regular expression or an array of them, matching the denied attr patterns.
$attributefilter_cfg['Denied']['sHO'] = array($ALL_G);

// the default behaviour of the engine, whether to accept or reject authorization
// if no filter matches.
$attributefilter_cfg['Default'] = false;

/*
 * Configuration for the site "samples". Define one for each site using the PoA.
 * Parameters defined here will override the general configuration.
 */

// a pair attribute_name => attribute_value, where the value can be a regular expression
// matched the allowed attribute values.
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$attributefilter_cfg['samples']['Allowed'] = array_merge($attributefilter_cfg['Allowed'],
                                           array("sHO" => array($REDIRIS_G, "example.es")));

// a pair attribute_name => attribute_value, where the value can be a regular expression
// matched the denied attribute values.
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$attributefilter_cfg['samples']['Denied'] = $attributefilter_cfg['Denied'];

/*
 * Advanced examples of multi-valued attributes and attribute combination
 */

// match ANY of the patterns for the defined attribute.
// ALLOWED: group=group1 OR group=group2 OR group=group3
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array("group" => "group1", "group2", "group3"),
);

// match ANY of the defined attributes.
// ALLOWED: group=some_known_group AND uid=some_known_uid
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array("group" => "some_known_group",
	                   "uid" => "some_known_uid"),
);

// match ANY of the defined attributes, where attributes may match ANY pattern.
// ALLOWED: (group=group1 OR group=group2 OR group=group3)
//          OR (uid=uid1 OR uid=uid2 OR uid=uid3)
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array("group" => array("group1", "group2", "group3"),
	                   "uid" => array("user1", "user2", "user3")),
);

// match ANY of the described combinations of attributes, where each combination MUST match
// ALL the attributes that are part of it.
// ALOWED: (group=group1 AND uid=user1) OR
//         (group=group2 AND uid=user2) OR
//         (group=group3 AND uid=user3)
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array(array("group" => "group1", "uid" => "user1"),
	                   array("group" => "group2", "uid" => "user2"),
                           array("group" => "group3", "uid" => "user3")),
);

// match ALL the attributes which may match ANY of the patterns specified individually.
// ALLOWED: ((group=group1 OR group=group2) AND (uid=user1 OR uid=user2))
//          OR (group=group3 AND uid=user3)
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array(array("group" => array("group1", "group2"),
	                         "uid" => array("user1", "user2")),
	                   array("group" => "group3", "uid" => "user3")),
);

// verify attributes and their combinations
// ALLOWED: (ANY uid AND group=group1) OR
//          (uid=user2 AND group=group2) OR
//          (uid=user3 AND ANY group)
$attributefilter_cfg['advanced'] = array(
	'Allowed' => array(array("uid" => ".*", "group" => "group1"),
	                   array("uid" => "user2", "group" => "group2"),
	                   array("uid" => "user3", "group" => ".*")),
);

// PLEASE NOTE THAT CONFIGURATIONS LIKE THE FOLLOWING ARE NOT SUPPORTED
$queryfilter_cfg['advanced'] = array(
        'Allowed' => array(array("value1", "value2", "value3")),
);

?>
