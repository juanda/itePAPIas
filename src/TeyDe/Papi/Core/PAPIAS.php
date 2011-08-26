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

namespace TeyDe\Papi\Core;

use Symfony\Component\HttpFoundation\Session;
/**
 * This class implements the needed services to build the PAPI assertion from
 * the PAPI request string and the attributes collected by the connector.
 *
 * It also implements a chain of filter to be applied before the assertion is
 * going to be builded. The chain of filters can be set throuht the config file
 * app.yml. // TODO The way to set an implement the chain of filter isn't
 * implemented yet.
 */
class PAPIAS
{

    protected $attributes = array();
    protected $papiRequest;
    protected $assertion = '';
    protected $theURL = '';
    protected $theRef = '';
    protected $config = array();

    /**
     * This constructor parses the PAPI request string (which has been previously
     * copied into the session, that is added as attributes of symfony session class)
     * and build the parameters:
     * -> $theURL
     * -> theRef
     *
     * which are used in the assertion building proccess.
     *
     * This class has been designed with the chain filter design pattern in mind,
     * so most of the methods return the own object. This strategy allows to use
     * an PAPIAS class as follow:
     *
     * $papiasobject -> setAttributes($a) -> applyFilters() -> ...
     *
     *
     * @param session $session
     */
    public function __construct($session, $config)
    {
        $this->config = $config;
        if (!$session instanceof Session)
        {
            throw new \Exception('the argument of the constructor is not a Symfony2 "session" object ');
        }

        
        $this->papiRequest = $session ->get('PAPIREQUEST');

        if (!isset($this->papiRequest['ACTION']) && !isset($this->papiRequest['ATTREQ']))
        {           
            PAPIASLog::error("Unknown request (1). Use the PAPI 1.0 protocol",
                    $config['log_file'], $config['id']);
        }
        if (isset($this->papiRequest['ACTION']) &&
                ($this->papiRequest['ACTION'] != "CHECK" ||
                !isset($this->papiRequest['DATA']) || !isset($this->papiRequest['URL'])))
        {
            PAPIASLog::error("Unknown request (2). Use the PAPI 1.0 protocol",
                    $config['log_file'], $config['id']);
        }
        if (isset($this->papiRequest['ATTREQ']) && (!isset($this->papiRequest['PAPIPOAREF'])
                || !isset($this->papiRequest['PAPIPOAURL'])))
        {
            PAPIASLog::error("Unknown request (3). Use the PAPI 1.0 protocol",
                    $config['log_file'], $config['id']);
        }
        if (isset($this->papiRequest['ACTION']))
        {
            $this->theURL = $this->papiRequest['URL'];
            $this->theRef = $this->papiRequest['DATA'];
        } else
        {
            $this->theURL = $this->papiRequest['PAPIPOAURL'];
            $this->theRef = $this->papiRequest['PAPIPOAREF'];
        }
    }

    /**
     * This method allow to set the attributes retrieved by the connector. The
     * $attributes array must be normalized as follow:
     *
     * It must be an associative array which keys are the attributes name and
     * wich values can be scalars with the attributes values or arrays of values
     * if the attribute is multi-valued. For example:
     *
     * $attributes = array(
     * 'attr1' => val1,
     * 'attr2' => array(val21,val22,val23),
     * );
     * 
     * @param array $attributes
     * @return PAPIAS
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Build the assertion string wich will be encrypted an send afterword. Such
     * assertion is set as an object attribute.
     *
     * The assertion string is composed by pairs attribute_name=attribute_values
     * separated by commas, and must be ended with AS identifier: 'uid@as_id'
     *
     * When the attributes are multi-valued, they are expresed like this:
     *
     * attribute_name=attribute_value1|attribute_value1|...|attribute_valueN
     *
     * @return PAPIAS
     */
    public function buildAssertion()
    {
        // check that uid attribute is present
        if (!isset($this->attributes['uid']))
        {
            throw new \Exception('user identifier parameter "uid" is missing');
        }

        $assertion = '';

        // Add attribute to assertion
        $k = 0;
        foreach ($this->attributes as $key => $value)
        {
            $assertion .= ( 0 == $k) ? '' : ',';
            if (is_array($value))
            {
                $i = 0;
                $lastElemI = count($value) - 1;
                foreach ($value as $value2)
                {
                    $assertion .= ( $i == 0) ? $key . '=' . $value2 : $value2;
                    $assertion .= ( $lastElemI != $i) ? '|' : '';
                    $i++;
                    $k++;
                }
            } else
            {
                $assertion .= $key . '=' . $value;
                $k++;
            }
        }
        // Add assertion identifier
        $assertion .= ( 0 == $k) ? '' : ',';
        $assertion .= $this->attributes['uid'] . '@' . $this->config['id'];
        $this->assertion = $assertion;
//        echo '<pre>';
//        print_r($this->attributes);
//        echo $assertion;
//        echo '</pre>';//
//        exit;
        return $this;
    }

    /**
     * Add some time information to the assertion, encrypt this data with the
     * private key, and build the correct url to which the data will be redirected.
     *
     * @return string
     */
    public function buildRedirection()
    {
        $fp = fopen($this->config['pkey_file'], 'r');
        if (!$fp)
        {
            throw new \Exception('private key file is missing');
        }

        $asId = $this->config['id'];
        $pKey = fread($fp, filesize($this->config['pkey_file']));
        $now = time();
        $ttl = $this->config['ttl'];
        $ext = $now + $ttl;
        $reply = $this->assertion . ":" . $ext . ":" . $now . ":" . $this->theRef;
        $safe = PAPIASCrypto::openssl_encrypt($reply, $pKey, 1024);

        if (strpos($this->theURL, "?"))
        {
            $redirectTo = $this->theURL . "&";
        } else
        {
            $redirectTo = $this->theURL . "?";
        }
        if (isset($this->papiRequest['ACTION']))
        {
            $redirectTo .= "ACTION=CHECKED" . "&" . "DATA=" . urlencode($safe);
            PAPIASLog::doLog("GPoA response to " . $this->theURL . ": " . $reply);
        } else
        {
            $redirectTo .= "AS=" . $asId . "&ACTION=CHECKED" . "&" . "DATA=" . urlencode($safe);
            PAPIASLog::doLog("AS response to " . $this->theURL . ": " . $reply);
        }

        return $redirectTo;

    }

    /**
     * Apply to the attributes retrieved by the connector a set of filters which
     * have been set and sequenced in the app.yml config file.
     * @return PAPIAS
     */
    public function applyFilters()
    {       
        $filters = $this->config['filters'];        
                                
        if (!isset($filters) || !(is_array($filters) && count($filters) > 0))
        {
            return $this;
        }

        foreach($filters as $filter)
        {            
            $className = '\\TeyDe\\Papi\\Filters\\'.$filter['class_name'];

            if(!class_exists($className))
            {
                throw new \Exception('The filter  "'.$className. '" does not exists');
            }

            $configuration = $filter['config'];
            $this -> attributes = call_user_func(array($className ,'execute'), 
                    $this -> attributes, $configuration);
        }        
        return $this;
    }

}
