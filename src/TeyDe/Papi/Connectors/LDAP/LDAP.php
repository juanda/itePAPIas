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

namespace TeyDe\Papi\Connectors\LDAP;

use TeyDe\Papi\Core\PAPIASLog;

/**
 * The LDAP class holds helper functions to access an LDAP database.
 *
 * @author Andreas Aakre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @author Anders Lund, UNINETT AS. <anders.lund@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: Session.php 244 2008-02-04 08:36:24Z andreassolberg $
 */
class LDAP
{
    const ERR_INTERNAL = 1;
    const ERR_NO_USER = 2;
    const ERR_WRONG_PW = 3;
    const ERR_AS_DATA_INCONSIST = 4;
    const ERR_AS_INTERNAL = 5;
    const ERR_AS_ATTRIBUTE = 6;
    /**
     * LDAP link identifier.
     *
     * @var resource
     */
    protected $ldap = null;
    /**
     * LDAP user: authz_id if SASL is in use, binding dn otherwise
     */
    protected $authz_id = null;
    /**
     * Timeout value, in seconds.
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * Private constructor restricts instantiation to getInstance().
     *
     * @param string $hostname
     * @param bool $enable_tls
     * @param bool $debug
     * @param int $timeout
     */
    // TODO: Flesh out documentation.
    public function __construct($hostname, $enable_tls = TRUE, $debug = FALSE, $timeout = 0)
    {

        // Debug.
        PAPIASLog::doLog('Debug: Library - LDAP __construct(): Setup LDAP with ' .
                        'host=\'' . $hostname .
                        '\', tls=' . var_export($enable_tls, true) .
                        ', debug=' . var_export($debug, true) .
                        ', timeout=' . var_export($timeout, true));

        /*
         * Set debug level before calling connect. Note that this passes
         * NULL to ldap_set_option, which is an undocumented feature.
         *
         * OpenLDAP 2.x.x or Netscape Directory SDK x.x needed for this option.
         */
        if ($debug && !ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7))
            PAPIASLog::doLog('Warning: Library - LDAP __construct(): Unable to set debug level (LDAP_OPT_DEBUG_LEVEL) to 7');

        /*
         * Prepare a connection for to this LDAP server. Note that this function
         * doesn't actually connect to the server.
         */
        $this->ldap = @ldap_connect($hostname);
        if ($this->ldap == FALSE)
            throw new $this->makeException('Library - LDAP __construct(): Unable to connect to \'' . $hostname . '\'', self::ERR_INTERNAL);

        /* Enable LDAP protocol version 3. */
        if (!@ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3))
            throw $this->makeException('Library - LDAP __construct(): Failed to set LDAP Protocol version (LDAP_OPT_PROTOCOL_VERSION) to 3', self::ERR_INTERNAL);

        // Set timeouts, if supported.
        // (OpenLDAP 2.x.x or Netscape Directory SDK x.x needed).
        $this->timeout = $timeout;
        if ($timeout > 0)
        {
            if (defined('LDAP_OPT_NETWORK_TIMEOUT'))
            {
                /* This option isn't present before PHP 5.3. */
                if (!@ldap_set_option($this->ldap, constant('LDAP_OPT_NETWORK_TIMEOUT'), $timeout))
                    PAPIASLog::doLog('Warning: Library - LDAP __construct(): Unable to set timeouts (LDAP_OPT_NETWORK_TIMEOUT) to ' . $timeout);
            }
            if (!@ldap_set_option($this->ldap, LDAP_OPT_TIMELIMIT, $timeout))
                PAPIASLog::doLog('Warning: Library - LDAP __construct(): Unable to set timeouts (LDAP_OPT_TIMELIMIT) to ' . $timeout);
        }

        // Enable TLS, if needed.
        if (!preg_match("/ldaps:/i", $hostname) and $enable_tls)
            if (!@ldap_start_tls($this->ldap))
                throw $this->makeException('Library - LDAP __construct(): Unable to force TLS', self::ERR_INTERNAL);
    }

    /**
     * Convenience method to create an LDAPException as well as log the
     * description.
     *
     * @param string $description
     * The exception's description
     * @return Exception
     */
    private function makeException($description, $type = NULL)
    {
        $errNo = 0x00;

        // Log LDAP code and description, if possible.
        if (empty($this->ldap))
        {
            PAPIASLog::error($description);
        } else
        {
            $errNo = @ldap_errno($this->ldap);
        }

        // Decide exception type and return
        if ($type)
        {
            if ($errNo !== 0)
            {
                // Only log real LDAP errors; not success.
                PAPIASLog::error($description . '; cause: \'' . ldap_error($this->ldap) . '\' (0x' . dechex($errNo) . ')');
            } else
            {
                PAPIASLog::error($description);
            }

            switch ($type)
            {
                case self::ERR_INTERNAL:// 1 - ExInternal
                    return new \Exception($description, $errNo);
                case self::ERR_NO_USER:// 2 - ExUserNotFound
                    return new \Exception($description, $errNo);
                case self::ERR_WRONG_PW:// 3 - ExInvalidCredential
                    return new \Exception($description, $errNo);
                case self::ERR_AS_DATA_INCONSIST:// 4 - ExAsDataInconsist
                    return new \Exception($description, $errNo);
                case self::ERR_AS_INTERNAL:// 5 - ExAsInternal
                    return new \Exception($description, $errNo);
            }
        } else
        {
            switch ($errNo)
            {
                case 0x20://LDAP_NO_SUCH_OBJECT
                    PAPIASLog::doLog('Warning: ' . $description . '; cause: \'' . ldap_error($this->ldap) . '\' (0x' . dechex($errNo) . ')');
                    return new \Exception($description, $errNo);
                case 0x31://LDAP_INVALID_CREDENTIALS
                    PAPIASLog::doLog('Info: ' . $description . '; cause: \'' . ldap_error($this->ldap) . '\' (0x' . dechex($errNo) . ')');
                    return new \Exception($description, $errNo);
                case -1://NO_SERVER_CONNECTION
                    PAPIASLog::error($description . '; cause: \'' . ldap_error($this->ldap) . '\' (0x' . dechex($errNo) . ')');
                    return new \Exception($description, $errNo);
                default:
                    PAPIASLog::error($description . '; cause: \'' . ldap_error($this->ldap) . '\' (0x' . dechex($errNo) . ')');
                    return new \Exception($description, $errNo);
            }
        }
    }

    /**
     * Search for DN from a single base.
     *
     * @param string $base
     * Indication of root of subtree to search
     * @param string|array $attribute
     * The attribute name(s) to search for.
     * @param string $value
     * The attribute value to search for.
     * @return string
     * The DN of the resulting found element.
     * @throws SimpleSAML_Error_Exception if:
     * - Attribute parameter is wrong type
     * @throws SimpleSAML_Error_AuthSource if:
     * - Not able to connect to LDAP server
     * - False search result
     * - Count return false
     * - Searche found more than one result
     * - Failed to get first entry from result
     * - Failed to get DN for entry
     * @throws SimpleSAML_Error_UserNotFound if:
     * - Zero entries was found
     */
    private function search($base, $attribute, $value)
    {

        // Create the search filter.
        $attribute = self::escape_filter_value($attribute, FALSE);
        $value = self::escape_filter_value($value);
        if (is_array($attribute))
        {

            // We have more than one attribute.
            $filter = '';
            foreach ($attribute AS $attr)
            {
                $filter .= '(' . $attr . '=' . $value . ')';
            }
            $filter = '(|' . $filter . ')';
        } elseif (is_string($attribute))
        {

            // We have only one attribute.
            $filter = '(' . $attribute . '=' . $value . ')';
        } else
        {
            // We have an unknown attribute type...
            throw $this->makeException('Library - LDAP search(): Search attribute must be an array or a string', self::ERR_INTERNAL);
        }

        // Search using generated filter.
        PAPIASLog::doLog('Debug: Library - LDAP search(): Searching base \'' . $base . '\' for \'' . $filter . '\'');
        // TODO: Should aliases be dereferenced?
        $result = @ldap_search($this->ldap, $base, $filter, array(), 0, 0, $this->timeout);
        if ($result === FALSE)
        {
            throw $this->makeException('Library - LDAP search(): Failed search on base \'' . $base . '\' for \'' . $filter . '\'');
        }

        // Sanity checks on search results.
        $count = @ldap_count_entries($this->ldap, $result);
        if ($count === FALSE)
        {
            throw $this->makeException('Library - LDAP search(): Failed to get number of entries returned');
        } elseif ($count > 1)
        {
            // More than one entry is found. External error
            throw $this->makeException('Library - LDAP search(): Found ' . $count . ' entries searching base \'' . $base . '\' for \'' . $filter . '\'', self::ERR_AS_DATA_INCONSIST);
        } elseif ($count === 0)
        {
            // No entry is fond => wrong username is given (or not registered in the catalogue). User error
            throw $this->makeException('Library - LDAP search(): Found no entries searching base \'' . $base . '\' for \'' . $filter . '\'', self::ERR_NO_USER);
        }


        // Resolve the DN from the search result.
        $entry = @ldap_first_entry($this->ldap, $result);
        if ($entry === FALSE)
            throw $this->makeException('Library - LDAP search(): Unable to retrieve result after searching base \'' . $base . '\' for \'' . $filter . '\'');
        $dn = @ldap_get_dn($this->ldap, $entry);
        if ($dn === FALSE)
            throw $this->makeException('Library - LDAP search(): Unable to get DN after searching base \'' . $base . '\' for \'' . $filter . '\'');
        // FIXME: Are we now shure, if no excepton hawe been thrown, that we are returning av DN?
        return $dn;
    }

    /**
     * Search for a DN.
     *
     * @param string|array $base
     * The base, or bases, which to search from.
     * @param string|array $attribute
     * The attribute name(s) searched for.
     * @param string $value
     * The attribute value searched for.
     * @param bool $allowZeroHits
     * Determines if the method will throw an exception if no hits are found.
     * Defaults to FALSE.
     * @return string
     * The DN of the matching element, if found. If no element was found and
     * $allowZeroHits is set to FALSE, an exception will be thrown; otherwise
     * NULL will be returned.
     * @throws SimpleSAML_Error_AuthSource if:
     * - LDAP search encounter some problems when searching cataloge
     * - Not able to connect to LDAP server
     * @throws SimpleSAML_Error_UserNotFound if:
     * - $allowZeroHits er TRUE and no result is found
     *
     */
    public function searchfordn($base, $attribute, $value, $allowZeroHits = FALSE)
    {

        // Traverse all search bases, returning DN if found.
        $bases = self::arrayize($base);
        $result = NULL;
        foreach ($bases AS $current)
        {
            try
            {
                // Single base search.
                $result = $this->search($current, $attribute, $value);
                // We don't hawe to look any futher if user is found
                if (!empty($result))
                    return $result;
                // If search failed, attempt the other base DNs.
            } catch (\Exception $e)
            {
                // Just continue searching
            }
        }
        // Decide what to do for zero entries.
        PAPIASLog::doLog('Debug: Library - LDAP searchfordn(): No entries found');
        if ($allowZeroHits)
        {
            // Zero hits allowed.
            return NULL;
        } else
        {
            // Zero hits not allowed.
            throw $this->makeException('Library - LDAP searchfordn(): LDAP search returned zero entries for filter \'(' . $attribute . ' = ' . $value . ')\' on base(s) \'(' . join(' & ', $bases) . ')\'', 2);
        }
    }

    /**
     * Bind to LDAP with a specific DN and password. Simple wrapper around
     * ldap_bind() with some additional logging.
     *
     * @param string $dn
     * The DN used.
     * @param string $password
     * The password used.
     * @param array $sasl_args
     * Array of SASL options for SASL bind
     * @return bool
     * Returns TRUE if successful, FALSE if
     * LDAP_INVALID_CREDENTIALS, LDAP_X_PROXY_AUTHZ_FAILURE,
     * LDAP_INAPPROPRIATE_AUTH, LDAP_INSUFFICIENT_ACCESS
     * @throws SimpleSAML_Error_Exception on other errors
     */
    public function bind($dn, $password, array $sasl_args = NULL)
    {
        $authz_id = null;

        if ($sasl_args != NULL)
        {
            if (!function_exists(ldap_sasl_bind))
            {
                $ex_msg = 'Library - missing SASL support';
                throw $this->makeException($ex_msg);
            }

            // SASL Bind, with error handling.
            $authz_id = $sasl_args['authz_id'];
            $error = @ldap_sasl_bind($this->ldap, $dn, $password,
                            $sasl_args['mech'],
                            $sasl_args['realm'],
                            $sasl_args['authc_id'],
                            $sasl_args['authz_id'],
                            $sasl_args['props']);
        } else
        {
            // Simple Bind, with error handling.
            $authz_id = $dn;
            $error = @ldap_bind($this->ldap, $dn, $password);
        }

        if ($error === TRUE)
        {
            // Good.
            $this->authz_id = $authz_id;
            PAPIASLog::doLog('Debug: Library - LDAP bind(): Bind successful with DN \'' . $dn . '\'');
            return TRUE;
        }

        /* Handle errors
         * LDAP_INVALID_CREDENTIALS
         * LDAP_INSUFFICIENT_ACCESS */
        switch (ldap_errno($this->ldap))
        {
            case 32: /* LDAP_NO_SUCH_OBJECT */
            case 47: /* LDAP_X_PROXY_AUTHZ_FAILURE */
            case 48: /* LDAP_INAPPROPRIATE_AUTH */
            case 49: /* LDAP_INVALID_CREDENTIALS */
            case 50: /* LDAP_INSUFFICIENT_ACCESS */
                return FALSE;
                break;
            default;
                break;
        }

        // Bad.
        throw $this->makeException('Library - LDAP bind(): Bind failed with DN \'' . $dn . '\'');
    }

    /**
     * Search a given DN for attributes, and return the resulting associative
     * array.
     *
     * @param string $dn
     * The DN of an element.
     * @param string|array $attributes
     * The names of the attribute(s) to retrieve. Defaults to NULL; that is,
     * all available attributes. Note that this is not very effective.
     * @param int $maxsize
     * The maximum size of any attribute's value(s). If exceeded, the attribute
     * will not be returned.
     * @return array
     * The array of attributes and their values.
     * @see http://no.php.net/manual/en/function.ldap-read.php
     */
    public function getAttributes($dn, $attributes = NULL, $maxsize = NULL)
    {

        // Preparations, including a pretty debug message...
        $description = 'all attributes';
        if (is_array($attributes))
        {
            $description = '\'' . join(',', $attributes) . '\'';
        } else
        // Get all attributes...
        // TODO: Verify that this originally was the intended behaviour. Could $attributes be a string?
            $attributes = array();
        PAPIASLog::doLog('Debug: Library - LDAP getAttributes(): Getting ' . $description . ' from DN \'' . $dn . '\'');

        // Attempt to get attributes.
        // TODO: Should aliases be dereferenced?
        $result = @ldap_read($this->ldap, $dn, 'objectClass=*', $attributes, 0, 0, $this->timeout);
        if ($result === false)
            throw $this->makeException('Library - LDAP getAttributes(): Failed to get attributes from DN \'' . $dn . '\'');
        $entry = @ldap_first_entry($this->ldap, $result);
        if ($entry === false)
            throw $this->makeException('Library - LDAP getAttributes(): Could not get first entry from DN \'' . $dn . '\'');
        $attributes = @ldap_get_attributes($this->ldap, $entry);  // Recycling $attributes... Possibly bad practice.
        if ($attributes === false)
            throw $this->makeException('Library - LDAP getAttributes(): Could not get attributes of first entry from DN \'' . $dn . '\'');

        // Parsing each found attribute into our result set.
        $result = array();  // Recycling $result... Possibly bad practice.
        for ($i = 0; $i < $attributes['count']; $i++)
        {

            // Ignore attributes that exceed the maximum allowed size.
            $name = $attributes[$i];
            $attribute = $attributes[$name];

            // Deciding whether to base64 encode.
            $values = array();
            for ($j = 0; $j < $attribute['count']; $j++)
            {
                $value = $attribute[$j];

                if (!empty($maxsize) && strlen($value) >= $maxsize)
                {
                    // Ignoring and warning.
                    PAPIASLog::doLog('Warning: Library - LDAP getAttributes(): Attribute \'' .
                                    $name . '\' exceeded maximum allowed size by ' + ($maxsize - strlen($value)));
                    continue;
                }

                // Base64 encode jpegPhoto.
                if (strtolower($name) === 'jpegphoto')
                {
                    $values[] = base64_encode($value);
                } else
                    $values[] = $value;
            }

            // Adding.
            $result[$name] = $values;
        }

        // We're done.
        PAPIASLog::doLog('Debug: Library - LDAP getAttributes(): Found attributes \'(' . join(',', array_keys($result)) . ')\'');
        return $result;
    }

    /**
     * Enter description here...
     *
     * @param string $config
     * @param string $username
     * @param string $password
     * @return array|bool
     */
    // TODO: Documentation; only cleared up exception/log messages.
    public function validate($config, $username, $password = null)
    {

        /* Escape any characters with a special meaning in LDAP. The following
         * characters have a special meaning (according to RFC 2253):
         * ',', '+', '"', '\', '<', '>', ';', '*'
         * These characters are escaped by prefixing them with '\'.
         */
        $username = addcslashes($username, ',+"\\<>;*');
        $password = addcslashes($password, ',+"\\<>;*');

        if (isset($config['priv_user_dn']))
            $this->bind($config['priv_user_dn'], $config['priv_user_pw']);
        if (isset($config['dnpattern']))
        {
            $dn = str_replace('%username%', $username, $config['dnpattern']);
        } else
        {
            $dn = $this->searchfordn($config['searchbase'], $config['searchattributes'], $username);
        }

        if ($password != null)
        { /* checking users credentials ... assuming below that she may read her own attributes ... */
            if (!$this->bind($dn, $password))
            {
                PAPIASLog::doLog('Info: Library - LDAP validate(): Failed to authenticate \'' . $username . '\' using DN \'' . $dn . '\'');
                return FALSE;
            }
        }

        /*
         * Retrieve attributes from LDAP
         */
        $attributes = $this->getAttributes($dn, $config['attributes']);
        return $attributes;
    }

    /**
     * Borrowed function from PEAR:LDAP.
     *
     * Escapes the given VALUES according to RFC 2254 so that they can be safely used in LDAP filters.
     *
     * Any control characters with an ACII code < 32 as well as the characters with special meaning in
     * LDAP filters "*", "(", ")", and "\" (the backslash) are converted into the representation of a
     * backslash followed by two hex digits representing the hexadecimal value of the character.
     *
     * @static
     * @param array $values Array of values to escape
     * @return array Array $values, but escaped
     */
    public static function escape_filter_value($values = array(), $singleValue = TRUE)
    {
        // Parameter validation
        if (!is_array($values))
        {
            $values = array($values);
        }

        foreach ($values as $key => $val)
        {
            // Escaping of filter meta characters
            $val = str_replace('\\', '\5c', $val);
            $val = str_replace('*', '\2a', $val);
            $val = str_replace('(', '\28', $val);
            $val = str_replace(')', '\29', $val);

            // ASCII < 32 escaping
            $val = self::asc2hex32($val);

            if (null === $val)
                $val = '\0';  // apply escaped "null" if string is empty

                $values[$key] = $val;
        }
        if ($singleValue)
            return $values[0];
        return $values;
    }

    /**
     * Borrowed function from PEAR:LDAP.
     *
     * Converts all ASCII chars < 32 to "\HEX"
     *
     * @param string $string String to convert
     *
     * @static
     * @return string
     */
    public static function asc2hex32($string)
    {
        for ($i = 0; $i < strlen($string); $i++)
        {
            $char = substr($string, $i, 1);
            if (ord($char) < 32)
            {
                $hex = dechex(ord($char));
                if (strlen($hex) == 1)
                    $hex = '0' . $hex;
                $string = str_replace($char, '\\' . $hex, $string);
            }
        }
        return $string;
    }

    /**
     * Convert SASL authz_id into a DN
     */
    private function authzid_to_dn($searchBase, $searchAttributes, $authz_id)
    {
        if (preg_match("/^dn:/", $authz_id))
            return preg_replace("/^dn:/", "", $authz_id);

        if (preg_match("/^u:/", $authz_id))
            return $this->searchfordn($searchBase, $searchAttributes,
                    preg_replace("/^u:/", "", $authz_id));

        return $authz_id;
    }

    /**
     * ldap_exop_whoami accessor, if available. Use requested authz_id
     * otherwise.
     *
     * ldap_exop_whoami is not yet included in PHP. For reference, the
     * feature request: http://bugs.php.net/bug.php?id=42060
     * And the patch against lastest PHP release:
     * http://cvsweb.netbsd.org/bsdweb.cgi/pkgsrc/databases/php-ldap/files/ldap-ctrl-exop.patch
     */
    public function whoami($searchBase, $searchAttributes)
    {
        $authz_id = '';

        if (function_exists('ldap_exop_whoami'))
        {
            if (ldap_exop_whoami($this->ldap, $authz_id) !== true)
                throw $this->getLDAPException('LDAP whoami exop failure');
        } else
        {
            $authz_id = $this->authz_id;
        }

        $dn = $this->authzid_to_dn($searchBase, $searchAttributes, $authz_id);

        if (!isset($dn) || ($dn == ''))
            throw $this->getLDAPException('Cannot figure userID');

        return $dn;
    }

    private static function arrayize($data, $index = 0)
    {
        if (is_array($data))
        {
            return $data;
        } else
        {
            return array($index => $data);
        }
    }

}
?>