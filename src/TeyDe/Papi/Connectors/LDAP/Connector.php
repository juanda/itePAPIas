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

class Connector
{

    private $userAttributes = array();
    private $isAuthenticated = false;
    private $username;
    private $password;
    /**
     * String with the location of this configuration.
     * Used for error reporting.
     */
    private $location;
    /**
     * The hostname of the LDAP server.
     */
    private $hostname;
    /**
     * Whether we should use TLS/SSL when contacting the LDAP server.
     */
    private $enableTLS;
    /**
     * Whether debug output is enabled.
     *
     * @var bool
     */
    private $debug;
    /**
     * The timeout for accessing the LDAP server.
     *
     * @var int
     */
    private $timeout;
    /**
     * Whether we need to search for the users DN.
     */
    private $searchEnable;
    /**
     * The username we should bind with before we can search for the user.
     */
    private $searchUsername;
    /**
     * The password we should bind with before we can search for the user.
     */
    private $searchPassword;
    /**
     * Array with the base DN(s) for the search.
     */
    private $searchBase;
    /**
     * The attributes which should match the username.
     */
    private $searchAttributes;
    /**
     * The DN pattern we should use to create the DN from the username.
     */
    private $dnPattern;
    /**
     * The attributes we should fetch. Can be NULL in which case we will fetch all attributes.
     */
    private $attributes;
    /**
     * The user cannot get all attributes, privileged reader required
     */
    private $privRead;
    /**
     * The DN we should bind with before we can get the attributes.
     */
    private $privUsername;
    /**
     * The password we should bind with before we can get the attributes.
     */
    private $privPassword;

    /**
     * Constructor for this configuration parser.
     *
     * @param array $config  Configuration.
     * @param string $location  The location of this configuration. Used for error reporting.
     */
    public function __construct($data, $config)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        
        /* Parse configuration. */        
        $this->hostname = $config['hostname'];
        $this->enableTLS = $config['enable_tls'];
        $this->debug = $config['debug'];
        $this->timeout = $config['timeout'];
        $this->searchEnable = $config['search.enable'];
        $this->privRead = $config['priv.read'];

        if ($this->searchEnable)
        {
            $this->searchUsername = $config['search.username'];
            if ($this->searchUsername !== NULL)
            {
                $this->searchPassword = $config['search.password'];
            }

            $this->searchBase = array($config['search.base']);
            $this->searchAttributes = $config['search.attributes'];
        } else
        {
            $this->dnPattern = $config['dnpattern'];
        }

        /* Are privs needed to get to the attributes? */
        if ($this->privRead)
        {
            $this->privUsername = $config['priv.username'];
            $this->privPassword = $config['priv.password'];
        }

        $this->attributes = $config['attributes'];

        $this->userAttributes = $this->login();

        if($this->userAttributes)
        {
            $this->isAuthenticated = true;
        }
        else
        {
            $this->isAuthenticated = false;
        }
    }

    /**
     * Attempt to log in using the given username and password.
     *
     * Will throw a SimpleSAML_Error_Error('WRONGUSERPASS') if the username or password is wrong.
     * If there is a configuration problem, an Exception will be thrown.
     * 
     * @param arrray $sasl_args  Array of SASL options for LDAP bind.
     * @return array  Associative array with the users attributes.
     */
    private function login(array $sasl_args = NULL)
    {               
        $ldap = new LDAP($this->hostname, $this->enableTLS, $this->debug, $this->timeout);

        if (!$this->searchEnable)
        {
            $ldapusername = addcslashes($this->username, ',+"\\<>;*');
            $dn = str_replace('%username%', $ldapusername, $this->dnPattern);
        } else
        {
            if ($this->searchUsername !== NULL)
            {
                if (!$ldap->bind($this->searchUsername, $this->searchPassword))
                {
                    return false;
                    //throw new \Exception('Error authenticating using search username & password.');
                }
            }

            $dn = $ldap->searchfordn($this->searchBase, $this->searchAttributes, $this->username, TRUE);
            if ($dn === NULL)
            {
                /* User not found with search. */
                PAPIASLog::doLog('Info: ' . $this->location . ': Unable to find users DN. username=\'' . $username . '\'');
                return false;
                //throw new SimpleSAML_Error_Error('WRONGUSERPASS');
            }
        }

        if (!$ldap->bind($dn, $this->password, $sasl_args))
        {
            PAPIASLog::doLog('Info: ' . $this->location . ': ' . $username . ' failed to authenticate. DN=' . $dn);
            return false;
            //throw new \Exception('WRONGUSERPASS');
        }

        /* In case of SASL bind, authenticated and authorized DN may differ */
        if (isset($sasl_args))
            $dn = $ldap->whoami($this->searchBase, $this->searchAttributes);

        /* Are privs needed to get the attributes? */
        if ($this->privRead)
        {
            /* Yes, rebind with privs */
            if (!$ldap->bind($this->privUsername, $this->privPassword))
            {
                throw new \Exception('Error authenticating using privileged DN & password.');
            }
        }

        return $ldap->getAttributes($dn, $this->attributes);
    }

    /**
     * Search for a DN.
     *
     * @param string|array $attribute
     * The attribute name(s) searched for. If set to NULL, values from
     * configuration is used.
     * @param string $value
     * The attribute value searched for.
     * @param bool $allowZeroHits
     * Determines if the method will throw an exception if no
     * hits are found. Defaults to FALSE.
     * @return string
     * The DN of the matching element, if found. If no element was
     * found and $allowZeroHits is set to FALSE, an exception will
     * be thrown; otherwise NULL will be returned.
     * @throws SimpleSAML_Error_AuthSource if:
     * - LDAP search encounter some problems when searching cataloge
     * - Not able to connect to LDAP server
     * @throws SimpleSAML_Error_UserNotFound if:
     * - $allowZeroHits er TRUE and no result is found
     *
     */
    public function searchfordn($attribute, $value, $allowZeroHits)
    {
        $ldap = new LDAP($this->hostname,
                        $this->enableTLS,
                        $this->debug,
                        $this->timeout);

        if ($attribute == NULL)
            $attribute = $this->searchAttributes;

        return $ldap->searchfordn($this->searchBase, $attribute,
                $value, $allowZeroHits);
    }

    public function getAttributes()
    {        
        return $this->userAttributes;
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }    

}
