<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */

include("../PoA.php");

$poa = new PoA("samples");

$auth = $poa->authenticate();

echo "<h1>PAPI 1.5 Example #1</h1>";
echo "<h2>authenticate() + getAttributes() + isAuthenticated()</h2>";
echo "<hr/>";

if ($auth) {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</tt></div></p>";
    echo "<p><strong>getAttributes()</strong>:</p>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>".print_r($poa->getAttributes(),true)."</pre></div>";
} else {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</tt></div></p>";
}

$auth = $poa->isAuthenticated();

if ($auth) {
    echo "<p><strong>isAuthenticated()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</strong></div></tt>";
} else {
    echo "<p><strong>isAuthenticated()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</strong></div></tt>";
}

?>
