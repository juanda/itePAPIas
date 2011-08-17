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

function hook(&$params) {
    $params['PAPIOPOA'] = "SAML_SUBJECT";
    $params['URL'] .= "?ORIGINAL_URL=modified_by_hook";
    return false;
}

class Example {
    public function hook(&$clean) {
        $clean = false;
        return false;
    }
}

$poa->addHook("PAPI_REDIRECT_URL_FINISH", new Hook("hook"));
$poa->addHook("PAPI_CLEAN_URL", new Hook(array("Example", "hook")));

$auth = $poa->authenticate();

echo "<h1>PAPI 1.5 Example #3</h1>";
echo "<h2>addHook() + authenticate()</h2>";
echo "<hr/>";

if ($auth) {
    echo "<p><strong>addHook(\"PAPI_REDIRECT_URL_FINISH\", new Hook(\"hook\"))</strong>:";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>function hook(&\$params) {\n";
    echo "    // set the PAPIOPOA to a custom string\n";
    echo "    \$params['PAPIOPOA'] = \"SAML_SUBJECT\";\n\n";
    echo "    // modify the return URL for this request\n";
    echo "    \$params['URL'] .= \"?ORIGINAL_URL=modified_by_hook\";\n\n";
    echo "    // return false unless you want to stop hooks execution\n";
    echo "    return false;\n";
    echo "}</pre></div>";
    echo "<p><strong>addHook(\"PAPI_CLEAN_URL\", new Hook(array(\"Example\", \"hook\")))</strong>:";
    echo "<div style=\"background: #cccccc; padding: 5px\"><pre>class Example {\n";
    echo "    public function hook(&\$clean) {\n";
    echo "        // avoid cleaning the URL when coming back from the GPoA\n";
    echo "        \$clean = false;\n\n";
    echo "        // return false unless you want to stop hooks execution\n";
    echo "        return false;\n";
    echo "    }\n}</pre></div>";
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</tt></div></p>";
}

?>
