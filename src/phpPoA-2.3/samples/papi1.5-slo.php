<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 */

include("../PoA.php");

$poa = new PoA("sample8");

echo "<h1>PAPI 1.5 Example #8 SP1</h1>";

if (!isset($_REQUEST['logout'])) {
    echo "<h2>authenticate()</h2>";
    echo "<hr/>";
    $auth = $poa->authenticate();
    if ($auth) {
        echo "<p><strong>authenticate()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</tt></div></p>";
    } else {
        echo "<p><strong>authenticate()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</tt></div></p>";
    }
    echo "<h3>Single Logout</h3>";
    echo "<p><a href=\"?logout\">Logout</a></p>";
} else {
    echo "<h2>logout()</h2>";
    echo "<hr/>";
    $loggedout = $poa->logout(true);
    if ($loggedout) {
        echo "<p><strong>logout()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>true</tt></div></p>";
    } else {
        echo "<p><strong>logout()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>false</tt></div></p>";
    }
}

echo "<h3>Single Sign On</h3>";
echo "<p><a href=\"papi1.5-slo.php\">Go to first Service Provider (current)</a></p>";
echo "<p><a href=\"papi1.5-slo-extra.php\">Go to second Service Provider</a></p>";

?>
