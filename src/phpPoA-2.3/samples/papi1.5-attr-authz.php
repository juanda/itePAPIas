<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage AttributeFilterAuthorizationEngine
 */

include("../PoA.php");

$poa = new PoA("samples");

$poa->authenticate();
$attrs = $poa->getAttributes();

function attrHook(&$attrs, &$allowed, &$denied) {
    array_push($allowed['sHO'], preg_quote($attrs['sHO']));
    return false;
}

echo "<h1>PAPI 1.5 Example #6</h1>";

if (isset($_GET['force'])) {
    $hook = new Hook("attrHook");
    $poa->addHook("ATTRIBUTE_BEFORE_FILTERS", $hook);

    echo "<h2>addHook() + isAuthorized()</h2>";
    echo "<p>This example demonstrates the use of the attribute filtering authorization engine and makes use ";
    echo "of a hook to modify the filters on runtime and therefore modify also the authorization result. If ";
    echo "authorization fails, you will be provided with a link below that will add a hook that forces your ";
    echo "<tt>sHO</tt> attribute to be recognized as a valid one, and be authorized then.</p>";
    echo "<hr/>";

    echo "<p><strong>addHook(\"ATTRIBUTE_BEFORE_FILTERS\", new Hook(\"attrHook\"))</strong>:</p>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>function attrHook(&\$attrs, &\$allowed, &\$denied) {\n";
    echo "    array_push(\$allowed['sHO'], preg_quote(\$attrs['sHO']));\n";
    echo "    return false;\n";
    echo "}</pre></div>";

} else {
    echo "<h2>isAuthorized()</h2>";
    echo "<p>This example demonstrates the use of the attribute filtering authorization engine and makes use ";
    echo "of a hook to modify the filters on runtime and therefore modify also the authorization result. If ";
    echo "authorization fails, you will be provided with a link below that will add a hook that forces your ";
    echo "<tt>sHO</tt> attribute to be recognized as a valid one, and be authorized then.</p>";
    echo "<hr/>";
}

$auth = $poa->isAuthorized("", $attrs, "AttributeFilterAuthzEngine");

if ($auth) {
    echo "<p><strong>isAuthorized(\"\", PoA::getAttributes(), \"AttributeFilterAuthzEngine\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHZ_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>isAuthorized(\"\", PoA::getAttributes(),  \"AttributeFilterAuthzEngine\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHZ_FAILED</tt></div></p>";
    echo "<p><a href=\"?force\">Force authorization</a></p>";
}

?>
