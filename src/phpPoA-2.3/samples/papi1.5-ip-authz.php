<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage SourceIPAddrAuthorizationEngine
 */

include("../PoA.php");

$poa = new PoA("samples");

function ipHook(&$ip, &$allowed, &$denied) {
    $allowed[] = $ip;
    return false;
}

echo "<h1>PAPI 1.5 Example #4</h1>";

if (isset($_GET['force'])) {
    $hook = new Hook("ipHook");
    $poa->addHook("SOURCEADDR_BEFORE_FILTERS", $hook);

    echo "<h2>addHook() + isAuthorized()</h2>";
    echo "<hr/>";
    echo "<p><strong>addHook(\"SOURCEADDR_BEFORE_FILTERS\", new Hook(\"ipHook\"))</strong>:</p>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>function ipHook(&\$ip, &\$allowed, &\$denied) {\n";
    echo "    \$allowed[] = \$ip;\n";
    echo "    return false;\n";
    echo "}</pre></div>";

} else {
    echo "<h2>isAuthorized()</h2>";
    echo "<hr/>";
}

$auth = $poa->isAuthorized("", array(), "SourceIPAddrAuthzEngine");

if ($auth) {
    echo "<p><strong>isAuthorized(\"\", array(), \"SourceIPAddrAuthzEngine\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHZ_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>isAuthorized(\"\", array(), \"SourceIPAddrAuthzEngine\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHZ_FAILED</tt></div></p>";
    echo "<p><a href=\"?force\">Force authorization</a></p>";
}

?>
