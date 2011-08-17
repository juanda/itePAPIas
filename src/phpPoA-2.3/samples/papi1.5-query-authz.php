<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage QueryFilterAuthorizationEngine
 */

include("../PoA.php");

$poa = new PoA("samples");

$apatterns = array();
$dpatterns = array();

function queryHook(&$uri, &$params, &$allowed, &$denied) {
    global $apatterns, $dpatterns;
    $apatterns = $allowed;
    $dpatterns = $denied;
}

$hook = new Hook("queryHook");
$poa->addHook("QUERY_BEFORE_FILTERS", $hook);

$auth = $poa->isAuthorized("", array(), "QueryFilterAuthzEngine");

echo "<h1>PAPI 1.5 Example #5</h1>";
echo "<h2>isAuthorized()</h2>";
echo "<hr/>";

if ($auth) {
    echo "<p><strong>isAuthorized(\"\", array(), \"QueryFilterAuthzEngine\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHZ_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>isAuthorized(\"\", array(), \"QueryFilterAuthzEngine\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHZ_FAILED</tt></div></p>";
}

echo "<h3>Authorized patterns</h3>";

echo "<ul>";
foreach ($apatterns as $pattern) {
    echo "<li><a href=\"papi1.5-query-authz.php?".$pattern."\">".$pattern."</a></li>";
}
echo "</ul>";

echo "<h3>Denied patterns</h3>";

echo "<ul>";
foreach ($dpatterns as $pattern) {
    echo "<li><a href=\"papi1.5-query-authz.php?".$pattern."\">".$pattern."</a></li>";
}
echo "<li><a href=\"papi1.5-query-authz.php\">DEFAULT</a></li>";
echo "</ul>";

?>
