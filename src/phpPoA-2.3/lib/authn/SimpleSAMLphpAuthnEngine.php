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
 * @author Elena Lozano <elena.lozano@rediris.es>
 * @filesource
 */

require_once("simplesamlphp/lib/_autoload.php");
/**
 * Supported namespaces for attributes.
 */
define('NS_SAML2_PROTOCOL', 'urn:oasis:names:tc:SAML:2.0:protocol');

/**
 * Authentication engine for the PAPI 1.5 protocol.
 * PLEASE NOTE THAT THIS ENGINE WORKS ONLY FOR WEB-BASED APPLICATIONS.
 * @package phpPoA2
 * @subpackage SimpleSAMLphpAuthnEngine
 */
class SimpleSAMLphpAuthnEngine extends AuthenticationEngine {

    protected $simplesaml;
    protected $status;
    protected $attributes;
    
    public function configure($file,$site) {
        $this->status = AUTHN_FAILED;
        $this->attributes = array();

        if (!class_exists("SimpleSAML_Auth_Simple")) {
            trigger_error(PoAUtils::msg('library-required', array("simpleSAMLphp")), E_USER_ERROR);
        }

        $this->simplesaml = new  SimpleSAML_Auth_Simple($site);       
    }

    public function authenticate() {
       $this->simplesaml->requireAuth();
        if ($this->simplesaml->isAuthenticated()) {
        	$this->status = AUTHN_SUCCESS;
        	$this->attributes = $this->simplesaml->getAttributes();
     		return AUTHN_SUCCESS;
        }else{
       		$this->status = AUTHN_FAILED;
        	return AUTHN_FAILED;
        }
    }

    public function isAuthenticated() {  
            return $this->status;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getAttribute($name, $namespace = NS_SAML2_PROTOCOL) {
    	$attr = null;
    	if(array_key_exists($name, $this->attributes)){
    		$attr = $this->attributes[$name];
    	}
        return $attr;
    }

    public function logout($slo = false) {
        // first check if we really need to logout!
        if (!$this->isAuthenticated()) {
            trigger_error(PoAUtils::msg('already-logged-out', array()), E_USER_NOTICE);
            return true;
        }
        
        $this->simplesaml->logout();
        $this->status = $this->simplesaml->isAuthenticated();
        trigger_error(PoAUtils::msg('local-logout-success', array()), E_USER_NOTICE);
        return true;
    } 
}

?>
