<?php
/**
 * @copyright Copyright 2005-2010 RedIRIS, http://www.rediris.es/
 *
 * This file is part of phpPoA2.
 *
 * phpPoA2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpPoA2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpPoA2. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version 2.0
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage Internationalization
 */

/* WARNING WARNING WARNING WARNING WARNING
 *
 * Please ensure you save this file as UTF-8 text!
 *
 * WARNING WARNING WARNING WARNING WARNING
 */

// TODO: classify messages per engine.

/**
 * An array with all the internationalized messages used by phpPoA2.
 * @global array $poa_messages
 * @name $messages
 */
$poa_messages = array(
// messages for fatal error page
'fatal-error'           => 'Fatal error',
'error-desc'            => 'There was an unexpected error. Please contact the administrator and specify the session identifier and backtrace as shown below.',
'error-message'         => 'Error message',
'session-id'            => 'Session identifier',
'backtrace'             => 'Backtrace',

'invalid-php-version'   => 'Invalid PHP version. PAPI extension requires at least PHP %s.',
'extension-required'    => 'Extension "%s" is required, but it is not loaded.',
'library-required'      => 'Library "%s" is required, but it cannot be found.',
'authenticating-via'	=> 'Authenticating via the "%s" engine.',
'authorize-user-via'    => 'Authorizing user "%s" via the "%s" engine.',
'query-authz'           => 'Querying for authorization with all available engines.',
'query-authz-via'       => 'Querying for authorization with "%s".',
'authz-engine-err'      => 'Cannot run unknown authorization engine "%s", check your configuration file.',
'authz-levels-err'      => 'No authorization levels defined for this PoA.',
'authn-engine-err'      => 'No authentication engine found, check your configuration file.',
'check-authn-status'    => 'Checking current authentication status via the %s engine.',
'invalid-config'        => 'Invalid configuration object.',
'config-not-found'      => 'Cannot configure PoA, configuration file "%s" not found.',
'config-err-php'        => 'Cannot configure PoA, "$%1$s" or "$%1$s[\'%2$s\']" not set.',
'config-err-ini'        => 'Cannot configure PoA, "\'%s\'" section not found.',
'config-param-err'      => 'Cannot configure PoA, "\'%s\'" param not found.',
'cannot-open-log'       => 'Cannot open log file "%s".',
'cannot-write-log'      => 'Cannot write log file "%s".',
'class-not-found'       => 'Cannot find class "%s".',
'authn-success'         => 'Successful authentication with the "%s" engine.',
'authn-err'             => 'Authentication error.',
'authz-err'             => 'User "%s" not authorized.',
'authz-expired'         => 'Authorization has expired for user "%s".',
'authz-default-fallback'=> 'Cannot make any authorization decission, falling to default fallback.',
'valid-cookie'          => 'The cookie is valid',
'cookie-not-found'      => 'Cannot find cookie "%s".',
'cookie-location-err'   => 'Invalid cookie location: "%s".',
'cookie-service-err'    => 'Service ID "%s" does not match for cookie, should be "%s".',
'cookie-expired-err'    => 'Expired cookie.',
'cookie-rejected-err'   => 'Assertion matches rejection cookie filter "%s".',
'cannot-set-cookie'     => 'Cannot set cookie. Please check no output was already sent to the browser.',
'empty-cookie-err'      => 'Empty cookie "%s" found.',
'empty-response-err'    => 'Empty AS/GPoA response.',
'pubkey-error'          => 'Cannot read public key from file "%s".',
'cannot-decrypt'        => 'Cannot decrypt response, please check AS/GPoA public key.',
'valid-response'        => 'AS/GPoA response authenticates assertion "%s".',
'expired-response'      => 'AS/GPoA response is expired.',
'unknown-request'       => 'AS/GPoA response received for an unknown request.',
'req-db-purged'         => 'Purged %d outdated requests from the database.',
'redirecting'           => 'Redirecting the user to "%s".',
'cannot-redirect'       => 'Cannot redirect the user.',
'cannot-save-request'   => 'Cannot save the original request.',
'cannot-open-req-db'    => 'Cannot open request database.',
'cannot-open-authz-db'  => 'Cannot open authorized database.',
'cannot-open-inv-db'    => 'Cannot open invitations database.',
'cannot-fetch-key'      => 'Cannot find database key "%s".',
'cannot-del-key'        => 'Cannot delete database key "%s".',
'as-id-error'           => 'Authentication Server identifier "%s" does not match with expected "%s".',
'continue'              => 'Continue',
'source-ip-allowed'     => 'The source IP "%s" matches allowed filter "%s".',
'source-ip-denied'      => 'The source IP "%s" matches denial filter "%s".',
'invalid-hook'          => 'Trying to set hook "%s" with an invalid function or method.',
'running-hook'          => 'Running hook "%s" for "%s".',
'add-hook'              => 'Adding hook "%s" for "%s".',
'remove-hook'           => 'Removing hook "%s" for "%s".',
'hook-error'            => 'Invalid hook "%s".',
'allowed-param-match'   => 'The following request parameters are allowed: [%s].',
'denied-param-match'    => 'The following request parameters are denied: [%s].',
'allowed-attr-match'    => 'The following user attributes are allowed: [%s].',
'denied-attr-match'     => 'The following user attributes are denied: [%s].',
'invite-non-existant'   => 'Cannot find invitation with reference "%s".',
'missing-attrs'         => 'None of the mandatory attributes were found.',
'missing-mail-attr'     => 'Cannot find "%s" attribute, e-mail verification failed.',
'mail-attr-err'         => 'Email verification attribute unset or empty, verification failed.',
'mail-attr-alg-err'     => 'Cannot extract the algorithm from the attribute "%s" with value "%s".',
'mail-attr-val-err'     => 'Cannot extract the e-mail verification string from the attribute "%s" with value "%s".',
'mail-verify-err'       => 'Cannot verify e-mail "%s", cannot match any of the received values.',
'mail-verify-ok'        => 'Verification success for e-mail "%s" with algorithm "%s" and value "%s".',
'cannot-authorize'      => 'Cannot authorize user "%s".',
'cannot-del-invite'     => 'Cannot remove invite for user "%s" with reference "%s".',
'invite-sent-to'        => 'Invite sent to address "%s".',
'user-authorized'       => 'User "%s" is now authorized.',
'user-authz-ok'         => 'User "%s" is authorized.',
'user-authz-err'        => 'User "%s" is not authorized.',
'user-already-authz'    => 'User "%s" is already authorized.',
'slo-conf-error'        => 'Cannot perform single logout, please review configuration.',
'already-logged-out'    => 'User was not authenticated.',
'local-logout-success'  => 'User was successfully logged out.',
'slo-logout'            => 'Single logout requested.',
'slo-requested'         => 'User requested single logout.',
);

?>
