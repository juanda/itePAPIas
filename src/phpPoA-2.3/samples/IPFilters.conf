<?php
// vim:set filetype=php:

/**
 * This is a sample configuration file.
 * @package phpPoA2
 * @subpackage SourceIPAddressAuthorizationEngine
 */

/*
 * Define any groups you need and combine them to allow or disallow authorization in
 * different applications.
 */
$SAMPLE_G = array("127.0.0.1");
$ALL_G = array(".*");

/*
 * Main global configuration. This parameters apply to any PoA defined in this
 * configuration file unless redefined locally.
 */

// SUPPORTED PATTERNS ARE:
// IPV4
// * 127.0.0.1 (single IP)
// * 127.0.0.0 (a whole network in old notation, or in other words all IPs like 127.0.0.X)
// IPV6
// * 0:a:b:c:d:e:f:0 (a whole network, or in other words all IPs like 0:a:b:c:d:e:f:X)
// * 0:a:b:c:d:e:f:: (same as previous)
// * ::e:f (any network whos IPs end in :e:f)

// a regular expression or an array of them, matching the allowed source IP addresses.
$sourceipaddr_cfg['AllowFrom'] = $SAMPLE_G;

// a regular expression or an array of them, matching the denied source IP addresses.
$sourceipaddr_cfg['DenyFrom'] = $ALL_G;

/*
 * Configuration for the site "samples". Define one for each site using the PoA.
 * Parameters defined here will override the general configuration.
 */

// a regular expression or an array of them, matching the allowed source IP addresses.
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$sourceipaddr_cfg['samples']['AllowFrom'] = array_merge($sourceipaddr_cfg['AllowFrom'], array($_SERVER['REMOTE_ADDR']));

// a regular expression or an array of them, matching the denied source IP addresses.
// By default it's merged with the global configuration. Remove the array_merge call if
// you want to override the global settings.
$sourceipaddr_cfg['samples']['DenyFrom'] = array_merge($sourceipaddr_cfg['DenyFrom'], array());

?>
