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

echo "<h1>PAPI 1.5 Example #2</h1>";
echo "<h2>authenticate() + addHook() + getAttributes()</h2>";
echo "<hr/>";

function handler($assertion, &$attributes) {
    $attributes = explode("@", $assertion);
    return true;
}

class AttributeHandler {

    public function handle($assertion, &$attributes) {
        $attributes = explode(",", $assertion);
        return true;
    }

}

if ($auth) {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</tt></div></p>";
    echo "<p><strong>addHook(\"PAPI_ATTRIBUTE_PARSER\", new Hook(\"handler\"))</strong>:<br/>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>function handler(\$assertion, &\$attributes) {\n";
    echo "    \$attributes = explode(\"@\", \$assertion);\n";
    echo "    return true;\n";
    echo "}</pre></div>";
    echo "<p><strong>getAttributes()</strong>:</p>";
    $fhook = new Hook("handler");
    $poa->addHook("PAPI_ATTRIBUTE_PARSER", $fhook);
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre style=\"overflow: auto\">".print_r($poa->getAttributes(),true)."</pre></div>";
    echo "<p><strong>addHook(\"PAPI_ATTRIBUTE_PARSER\", new Hook(array(\"AttributeHandler\",\"handler\")))</strong>:<br/>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>class AttributeHandler {\n";
    echo "    public function handler(\$assertion, &\$attributes) {\n";
    echo "        \$attributes = explode(\",\", \$assertion);\n";
    echo "        return true;\n";
    echo "    }\n}</pre></div>";
    $poa->removeHook("PAPI_ATTRIBUTE_PARSER", $fhook);
    $o = new AttributeHandler();
    $ohook = new Hook(array($o, "handle"));
    $poa->addHook("PAPI_ATTRIBUTE_PARSER", $ohook);
    echo "<p><strong>getAttributes()</strong>:</p>";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre style=\"overflow: auto\">".print_r($poa->getAttributes(),true)."</pre></div>";
} else {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</tt></div></p>";
}

?>
