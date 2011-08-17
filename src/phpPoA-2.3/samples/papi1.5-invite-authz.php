<?php
/**
 * This is a sample file to demonstrate the functionality of the phpPoA2 package.
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage InviteAuthorizationEngine
 */

include("../PoA.php");

$poa = new PoA("sample7");

$auth = $poa->authenticate();
$attrs = $poa->getAttributes();

echo "<h1>PAPI 1.5 Example #7</h1>";
echo "<h2>authenticate() + getAttributes() + isAuthorized() + getAuthorizationEngines()</h2>";
echo "<p>This example demonstrates the use of the invitation based authorization engine. Authentication is automatically ";
echo "triggered and once authenticated, the attributes of the user are collected. The <tt>eduPersonTargetedId</tt> attribute ";
echo "is used to identify users across different sessions, and therefore to authorize and query if the user is authorized.</p>";
echo "<hr/>";

if ($auth) {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHN_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>authenticate()</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHN_FAILED</tt></div></p>";
}

$auth = $poa->isAuthorized($attrs['ePTI'], $attrs, "InviteAuthzEngine");

if ($auth) {
    echo "<p><strong>isAuthorized(\"".$attrs['ePTI']."\", PoA::getAttributes(), \"InviteAuthzEngine\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>AUTHZ_SUCCESS</tt></div></p>";
} else {
    echo "<p><strong>isAuthorized(\"".$attrs['ePTI']."\", PoA::getAttributes(), \"InviteAuthzEngine\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>AUTHZ_FAILED</tt></div></p>";
}

$engines = $poa->getAuthorizationEngines("InviteAuthzEngine");
$engine = $engines['InviteAuthzEngine'];

echo "<hr/>";

// send invite
if (@$_REQUEST['action'] === "Send") {
    echo "<h2>invite() + getAuthorizedList() + getPendingInvites()</h2>";
    echo "<p>An invitation e-mail has been sent to ".htmlentities($_REQUEST['mail']).".";
    echo "<p>Listed below are both sets of currently authorized users and the invitations pending of acceptation. Click the link on each invite to "; 
    echo "accept it. Please note that e-mail verification is done by means of the <tt>sPUC</tt> (<tt>schacPersonalUniqueCode</tt>) attribute, so if you are ";
    echo "not using it or its value does not correspond to the e-mail where the invitation was sent, authorization will fail. If you want to send another ";
    echo "invitation, please use the form below.</p>";
    echo "<hr/>";
    $r = $engine->invite($_REQUEST['mail']);
    if ($r) {
        echo "<p><strong>invite(\"".$_REQUEST['mail']."\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>TRUE</tt></div></p>";
    } else {
        echo "<p><strong>invite(\"".$_REQUEST['mail']."\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>FALSE</tt></div></p>";
    }
} else if (@$_REQUEST['action'] === "authorize") {
    echo "<h2>authorize() + getAuthorizedList() + getPendingInvites()</h2>";
    $r = $poa->authorize($attrs['ePTI'], $attrs, $_REQUEST['ref']);
    if ($r) {
        echo "<p>The user with the <tt>eduPersonTargetedId</tt> '".htmlentities($attrs['ePTI'])."' has been authorized.</p>";
        echo "<p>Listed below are both sets of currently authorized users and the invitations pending of acceptation. Click the link on each invite to "; 
        echo "accept it. Please note that e-mail verification is done by means of the <tt>sPUC</tt> (<tt>schacPersonalUniqueCode</tt>) attribute, so if you are ";
        echo "not using it or its value does not correspond to the e-mail where the invitation was sent, authorization will fail. If you want to send another ";
        echo "invitation, please use the form below.</p>";
        echo "<hr/>";
        echo "<p><strong>authorize(\"".$attrs['ePTI']."\", PoA::getAttributes(), \"".$_REQUEST['ref']."\")</strong>: <div style=\"background: #ccffcc; padding: 5px\"><tt>TRUE</tt></div></p>";
    } else {
        echo "<p>The user with the <tt>eduPersonTargetedId</tt> '".htmlentities($attrs['ePTI'])."' has <strong>NOT</strong> been authorized. Please check the logs ";
        echo "for a detailed explanation of the error.</p>";
        echo "<p>Listed below are both sets of currently authorized users and the invitations pending of acceptation. Click the link on each invite to ";
        echo "accept it. Please note that e-mail verification is done by means of the <tt>sPUC</tt> (<tt>schacPersonalUniqueCode</tt>) attribute, so if you are ";
        echo "not using it or its value does not correspond to the e-mail where the invitation was sent, authorization will fail. If you want to send another ";
        echo "invitation, please use the form below.</p>";
        echo "<hr/>";
        echo "<p><strong>authorize(\"".$attrs['ePTI']."\", PoA::getAttributes(), \"".$_REQUEST['ref']."\")</strong>: <div style=\"background: #ffcccc; padding: 5px\"><tt>FALSE</tt></div></p>";
    }
} else {
    echo "<h2>getAuthorizedList() + getPendingInvites()</h2>";
    echo "<p>Listed below are both sets of currently authorized users and the invitations pending of acceptation. Click the link on each invite to "; 
    echo "accept it. Please note that e-mail verification is done by means of the <tt>sPUC</tt> (<tt>schacPersonalUniqueCode</tt>) attribute, so if you are ";
    echo "not using it or its value does not correspond to the e-mail where the invitation was sent, authorization will fail. If you want to send another ";
    echo "invitation, please use the form below.</p>";
    echo "<hr/>";
}

$authorized = $engine->getAuthorizedList();
$pending = $engine->getPendingInvites();

echo "<p><strong>getAuthorizedList()</strong>:</p>";

echo "<ul>";
if (empty($authorized)) {
    echo "<li>none</li>";
} else {
    foreach ($authorized as $id => $user) {
        $color = "cccccc";
        if ($attrs['ePTI'] === $id) {
            $color = "ccffcc";
        }
        echo "<li><div style=\"background: #".$color."; padding: 5px\"><strong>ePTI</strong>: <tt>".$id."</tt>:";
        echo "<ul>";
        echo "<li><strong>email</strong>: <tt>".$user['email']."</tt></li>";
        echo "<li><strong>attributes</strong>:<ul>";
        foreach ($user['attributes'] as $name => $value) {
            echo "<li><strong>".$name."</strong>: <tt>".$value."</tt></li>";
        }
        echo "</ul></li>";
        echo "<li><strong>since</strong>: <tt>".date("r", $user['since'])."</tt></li>";
        $expires = (!$user['expires']) ? "never" : date("r", $user['expires']);
        echo "<li><strong>expires</strong>: <tt>".$expires."</tt></li>";
        echo "</ul></div></li><br/>";
    }
}
echo "</ul>";

echo "<p><strong>getPendingInvites()</strong>:</p>";

echo "<ul>";
if (empty($pending)) {
    echo "<li>none</li>";
} else  {
    foreach ($pending as $ref => $invite) {
        echo "<li><div style=\"background: #cccccc; padding: 5px\"><strong>reference</strong>: <a href=\"papi1.5-invite-authz.php?action=authorize&ref=".$ref."\"><tt>".$ref."</tt></a>:";
        echo "<ul>";
        echo "<li><strong>email</strong>: <tt>".$invite['email']."</tt></li>";
        echo "<li><strong>since</strong>: <tt>".date("r", $invite['since'])."</tt></li>";
        $expires = (!$invite['expires']) ? "never" : date("r", $invite['expires']);
        echo "<li><strong>expires</strong>: <tt>".$expires."</tt></li>";
        echo "</ul></div></li><br/>";
    }
}
echo "</ul>";

?>
<h3>Send invite</h3>
<form name="invite" action="papi1.5-invite-authz.php" method="get">
  E-mail address:<br/> <input type="text" name="mail"/>
  <input name="action" type="submit" value="Send"/>
</form>
