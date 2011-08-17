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
 */

/**
 * Supported database types.
 */
define('INVITES_DBA', 'InviteDBADB');
define('INVITES_MYSQL', 'InviteMySQLDB');
define('INVITES_SESSION', 'InviteSessionDB');
define('AUTHORIZED_DBA', 'AuthorizedDBADB');
define('AUTHORIZED_MYSQL', 'AuthorizedMySQLDB');
define('AUTHORIZED_SESSION', 'AuthorizedSessionDB');

/**
 * This hook is executed right before it is checked whether the user was invited or not to
 * continue with authorization.
 * It can be used to trick the engine to believe the user was previously invited (and therefore
 * forcing authorization).
 * The hook receives a boolean parameter representing whether the referenced invitation was found
 * or not.
 *
 * function invitesBeforeAuthorization(&$invite_exists);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("INVITES_BEFORE_AUTHORIZATION", "INVITES_BEFORE_AUTHORIZATION");

/**
 * Invitation based authorization engine.
 * @package phpPoA2
 * @subpackage InviteAuthorizationEngine
 */
class InviteAuthzEngine extends AuthorizationEngine {

    protected $authz_db;
    protected $invites_db;
    protected $valid_hooks = array(INVITES_BEFORE_AUTHORIZATION);

    /**
     * Configure the authorization engine.
     * @param file The configuration file.
     * @param section The section of the configuration file to use.
     */
    public function configure($file, $section) {
        parent::configure($file, $section);

        // configure authorized DB
        $db_t = $this->cfg->getAuthorizedDBType();
        if (class_exists($db_t, true)) {
            $this->authz_db = new $db_t($this->cfg);
        }

        // configure invites DB
        $db_t = $this->cfg->getInvitesDBType();
        if (class_exists($db_t, true)) {
            $this->invites_db = new $db_t($this->cfg);
        }
    }

    /**
     * Check if the specified user is authorized with the given attributes.
     * @param user The name of the user to check authorization for.
     * @param attrs The attributes of the user to match his identity.
     * @return boolean true if the user is authorized, false otherwise.
     */
    public function isAuthorized($user, $attrs) {
        if (!$this->authz_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-authz-db'), E_USER_WARNING);
            return false;
        }

        // retrieve data
        $stored = $this->authz_db->fetch($user);
        $this->authz_db->close();
        if (!$stored) { // not found
            trigger_error(PoAUtils::msg('cannot-fetch-key', array($user)), E_USER_WARNING);
            return false;
        }

        // check expiration
        $now = time();
        if ($stored['expires'] && $now > $stored['expires']) {
            trigger_error(PoAUtils::msg('authz-expired', array($user)), E_USER_WARNING);
            return false;
        }

        // check attributes
        $rslt = false;
        foreach ($this->cfg->getUniqueAttributes() as $attr) {
            if (is_array($attr)) {
                $partial = true;
                foreach ($attr as $compound) {
                    $partial &= isset($stored['attributes'][$compound]) && // attribute is stored
                                isset($attrs[$compound]) && // attribute is set
                                // attribute IS NOT an array and the value is stored
                                ((!is_array($attrs[$compound]) && $stored['attributes'][$compound] === $attrs[$compound]) ||
                                // attribute IS an array and the value stored is in it
                                (is_array($attrs[$compound]) && in_array($stored['attributes'][$compound], $attrs[$compound])));
                }
                $rslt |= $partial;
            } else {
                $rslt |= isset($stored['attributes'][$attr]) && // attribute is stored
                         isset($attrs[$attr]) && // attribute is set
                         // attribute IS NOT an array and the value is stored
                         ((!is_array($attrs[$attr]) && $stored['attributes'][$attr] === $attrs[$attr]) ||
                         // attribute IS an array and the value stored is in it
                         (is_array($attrs[$attr]) && in_array($stored['attributes'][$attr], $attrs[$attr])));
            }
            if ($rslt) break;
        }
        return $rslt;
    }

    /**
     * Get a list of all authorized users.
     * @return array The list of all users currently authorized. An empty
     * array if none found.
     */
    public function getAuthorizedList() {
        $this->registerHandler();
        if (!$this->authz_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-authz-db'), E_USER_WARNING);
            return false;
        }

        $all = $this->authz_db->fetch_all();
        $this->clean();
        return $this->finish($all);
    }

    /**
     * Get a list of all pending invitations.
     * @return array The list of all pending invitations. An empty array if
     * none found.
     */
    public function getPendingInvites() {
        $this->registerHandler();
        if (!$this->invites_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-inv-db'), E_USER_WARNING);
            return false;
        }

        $all = $this->invites_db->fetch_all();
        $this->clean();
        return $this->finish($all);
    }

    /**
     * Authorize the specified user.
     * @param user 
     * @param attrs 
     * @param ref 
     * @param expires 
     * @return boolean true if the user was successfully authorized, false otherwise.
     */
    public function authorize($user, $attrs, $ref, $expires = 0) {
        if (!$this->authz_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-authz-db'), E_USER_WARNING);
            return false;
        }

        if (!$this->invites_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-inv-db'), E_USER_WARNING);
            return false;
        }

        $invited = $this->invites_db->check($ref);
        $exists = $this->authz_db->check($user);

        // run hook before actually performing authorization
        $args = array($invited);
        $this->runHooks(INVITES_BEFORE_AUTHORIZATION, $args);
        $invited = $args[0];

        // the user wasn't previously invited
        if (!$invited) {
            if ($exists) { // the user was previously authorized
                $stored = $this->authz_db->fetch($user);
                $matches = true;
                foreach ($stored['attributes'] as $name => $value) {
                    $matches &= ($attrs[$name] == $value);
                }
                if ($matches) { // is the same user, skip
                    trigger_error(PoAUtils::msg('user-already-authz', array($user)), E_USER_WARNING);
                    return $this->finish(false);
                }
            }
            trigger_error(PoAUtils::msg('invite-non-existant', array($ref)), E_USER_WARNING);
            return $this->finish(false);
        }

        // either the invite exists or we are asked to force authorization
        $invite = $this->invites_db->fetch($ref);

        // check if the user has some of the mandatory attributes
        $unique = $this->cfg->getUniqueAttributes();
        foreach ($unique as $item) {
            $save = array(); // restore on each iteration
            if (is_array($item)) { // a combination of attributes
                $complete = true;
                foreach ($item as $name) {
                    $complete &= !empty($attrs[$name]);
                    $value = $attrs[$name];
                    // multiple values?
                    if (is_array($attrs[$name])) {
                        $value = $attrs[$name][0];
                    }
                    $save[$name] = $value;
                }
                if ($complete) {
                    break; // all attributes found, ok!
                }
            } else { // a single attribute
                if (!empty($attrs[$item])) { // attribute found, ok!
                    $value = $attrs[$item];
                    // multiple values?
                    if (is_array($attrs[$item])) {
                        $value = $attrs[$item][0];
                    }
                    $save[$item] = $value;
                    break;
                }
            }
        }
        if (empty($save)) { // no available attributes!
            trigger_error(PoAUtils::msg('missing-attrs'), E_USER_WARNING);
            return $this->finish(false);
        }

        // e-mail verification
        if (!$this->emailVerify($invite['email'], $attrs)) {
             return $this->finish(false);
        }

        // now save the user in the authorized database
        if (@!$this->authz_db->replace_authorization($user, $save, $invite['email'], $expires)) {
            trigger_error(PoAUtils::msg('cannot-authorize', array($user)), E_USER_WARNING);
            return $this->finish(false);
        }

        // remove invite
        if (@!$this->invites_db->delete($ref)) {
            trigger_error(PoAUtils::msg('cannot-del-invite', array($user, $ref)), E_USER_WARNING);
            return $this->finish(false);
        }

        // look for any other pending invites for this user, and delete them
        $pending = $this->invites_db->fetch_all();
        foreach ($pending as $key => $stored) {
            if ($stored['email'] === $invite['email']) {
                // clean up
                if (@!$this->invites_db->delete($key)) {
                    trigger_error(PoAUtils::msg('cannot-del-invite', array($user, $key)), E_USER_WARNING);
                    return $this->finish(false);
                }
            }
        }

        // success
        trigger_error(PoAUtils::msg('user-authorized', array($user)), E_USER_NOTICE);
        return $this->finish(true);
    }

    /**
     * Revoke authorization for the specified user.
     * @param 
     * @return boolean true if authorization was successfully revoked, false otherwise.
     */
    public function revoke($mail) {
        if (!$this->authz_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-authz-db'), E_USER_WARNING);
            return false;
        }

        // JPC20110124
        // Library should leave the address as is.
        // sanitize email
        //$mail = strtolower($mail);

        // get a list with all users authorized 
        $all = $this->authz_db->fetch_all();

        // iterate to find the user
        foreach ($all as $key => $values) {
            if ($values['email'] == $mail) {
                // found, remove!
                return $this->finish($this->authz_db->delete($key));
            }
        }

        // not found!
        return $this->finish(false);
    }

    /**
     * Send an invitation to an e-mail address (that is, send an e-mail to that
     * address with instructions on how to get authorized and an URL to follow).
     * @param mail The e-mail of the user.
     * @param expires The time (POSIX) when authorization will expire. Use 0 if authorization
     * should never expire. Defaults to 0.
     * @return boolean true if the invitation was correctly sent, false in any other case.
     */
    public function invite($mail, $expires = 0) {
        $this->registerHandler();
        if (!$this->invites_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-inv-db'), E_USER_WARNING);
            return false;
        }

        // JPC20110124
        // Library should leave the address as is.
        // sanitize e-mail
        //$mail = strtolower($mail);

        // generate random reference
        $ref = mt_rand();

        if (@!$this->invites_db->replace_invite($ref, $mail, $expires)) {
            trigger_error(PoAUtils::msg('cannot-save-invite', array($mail)), E_USER_WARNING);
            return $this->finish(false);
        }

        // setup email
        $sep = (!strstr($this->cfg->getInviteURL(), "?")) ? "?" : "&";
        $url = $this->cfg->getInviteURL().$sep."ref=".$ref;
        $text = preg_replace("/##URL##/", $url, $this->cfg->getInviteText());
        $headers = "From: ".$this->cfg->getAdminEmail();
        $command_params = "-f ".$this->cfg->getAdminEmail();

        // send it
        mail($mail, $this->cfg->getInviteSubject(), $text, $headers, $command_params);

        trigger_error(PoAUtils::msg('invite-sent-to', array($mail)), E_USER_WARNING);
        $this->clean();
        return $this->finish(true);
    }

    /**
     * Remove an invitation from the database.
     * @param ref The reference to the invite to remove.
     * @return boolean true if the invite was removed, false otherwise.
     */
    public function removeInvite($ref) {
        $this->registerHandler();
        if (!$this->invites_db->open()) {
            trigger_error(PoAUtils::msg('cannot-open-inv-db'), E_USER_WARNING);
            return false;
        }

        // remove the invite
        $rslt = $this->invites_db->delete($ref);
        trigger_error(PoAUtils::msg('invite-removed', array($ref)), E_USER_WARNING);
        $this->clean();
        return $this->finish($rslt);
    }

    /**
     * Perform e-mail verification for the current user according to the configuration
     * for this site.
     * @param mail The e-mail of the user.
     * @param attrs The array of attributes of the user.
     * @return boolean true if verification succeeds or was not performed, false otherwise.
     */
    protected function emailVerify($mail, $attrs) {
        $mail_attr = $this->cfg->getEmailVerifyAttribute();
        if ($this->cfg->doEmailVerify()) {
            // empty attribute, configuration error!
            if (empty($mail_attr)) {
                trigger_error(PoAUtils::msg('mail-attr-err'), E_USER_WARNING);
                return false;
            }
            // attribute not set, cannot verify
            if (!isset($attrs[$mail_attr])) {
                trigger_error(PoAUtils::msg('missing-mail-attr', array($mail_attr)), E_USER_WARNING);
                return false;
            }

            $alg_re = $this->cfg->getEmailVerifyAlgRegEx();
            $val_re = $this->cfg->getEmailVerifyRegEx();

            // support for attributes with multiple values
            $mail_attrs = $attrs[$mail_attr];
            if (!is_array($attrs[$mail_attr])) {
                $mail_attrs = array($attrs[$mail_attr]);
            }

            $match = false;
            foreach ($mail_attrs as $attr_val) {
                // extract function for each value
                $alg = "sprintf(\"%s\",";
                if (!empty($alg_re)) {
                    if (!preg_match("/".$alg_re."/", $attr_val, $vals_alg)) {
                        trigger_error(PoAUtils::msg('mail-attr-alg-err', array($mail_attr, $attr_val)), E_USER_WARNING);
                        continue;
                    }
                    $alg = $vals_alg[1]."(";
                }

                // extract value
                if (!preg_match("/".$val_re."/", $attr_val, $vals_value)) {
                    trigger_error(PoAUtils::msg('mail-attr-val-err', array($mail_attr, $attr_val)), E_USER_WARNING);
                    continue;
                }
                $received[] = array($alg, $vals_value[1]);
                $match = true;
            }
            // none of the mail attributes matched the regular expressions
            if (!$match) {
                return false;
            }

            // check all possibilities
            foreach ($received as $value) {
                $alg = $value[0];
                $value = $value[1];
                $computed_value = eval("return ".$alg."\"".$mail."\");");
                if ($computed_value != $value) {
                    // no match, continue searching
                    continue;
                }
                // match found, end here
                trigger_error(PoAUtils::msg('mail-verify-ok', array($mail, preg_replace("/\(.*$/", "", $alg), $value)), E_USER_WARNING);
                return true;
            }
            // verification is enabled but we were unable to find any matching e-mail
            trigger_error(PoAUtils::msg('mail-verify-err', array($mail)), E_USER_WARNING);
            return false;
        }
        // verification disabled
        return true;
    }

    /**
     * Close databases and return the same value received as a parameter.
     * @param value The value that must be returned.
     * @return mixed The first param.
     */
    protected function finish($value) {
        $this->invites_db->close();
        $this->authz_db->close();
        return $value;
    }

}

?>
