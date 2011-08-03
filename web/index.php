<?php

use TeyDe\Papi\Core;
use TeyDe\Papi\Connectors;

require_once __DIR__ . '/../silex.phar';
require_once __DIR__ . '/../config.php';

$app = new Silex\Application();
//
// Debug Mode
//
//$app['debug'] = true;

//
// Register Extensions
//
$app->register(new Silex\Extension\SessionExtension());
$app->register(new Silex\Extension\ValidatorExtension());

//
// Looking for the connector to use
//
$app['connector.name'] = $config['as']['connector'];
if (!$app['connector.name'])
{
    throw new Exception('No connector has been defined. You must set the "connector" parameter in the configuration file');
}

//
// Looking for the signin form
//

if (file_exists(dirname(__FILE__) . '/templates/' . $app['connector.name'] . 'SigninForm.php'))
{
    $app['form.template'] = $connector_name . 'SigninForm.php';
} else
{
    $app['form.template'] = 'signinForm.php';
}
//
// GET Action
//
$app->get('/signin', function () use ($config, $app)
        {
//            echo '<pre>';
//            echo 'GET<br>';
//            print_r($app['session']);
//            echo '</pre>';
            /* Take the PAPI GET parameter and put them in the session. It's easier
             * an safer to handle such parameter from the session.
             *  $this -> getUser() gets an object wich represents the session in
             *  symfony
             */            
            if (!$app['session']->get('PAPIREQUEST'))
            {
                $requestParams = array();
                parse_str($app['request']->getQueryString(), $requestParams);

                $sessionParams = array();
                foreach ($requestParams as $k => $v)
                {
                    $sessionParams[$k] = $v;
                }
                $app['session']->set('PAPIREQUEST', $sessionParams);
            }

            $app['session.request'] = $app['session']->get('PAPIREQUEST');
            include(__DIR__ . '/templates/' . $app['form.template'] );
        });

$app->post('/signin', function () use ($config, $app)
        {
//            echo '<pre>';
//            echo 'POST<br>';
//            print_r($app['session']);
//            echo '</pre>';
            $signinData = $app['request']->get('signin');
            $signinParameters = new Core\SigninParameters();
            $signinParameters->bind($signinData);

            $app['validator.errors'] = $app['validator']->validate($signinParameters);

            if (count($app['validator.errors']) == 0) // The form is valid
            {                
                $connector = Connectors\ConnectorFactory::createInstance($signinData, $app['connector.name']);
                $auth = $connector->isAuthenticated();
                if ($auth)
                {
                    $attributes = $connector->getAttributes();
                    
                } else
                {                    
                    $app['session']->setFlash('message',
                            $config['as']['message_no_auth']);

                   
                    return $app->redirect('signin');
                }                
                $papias = new Core\PAPIAS($app['session'], $config);
                
                $redirectTo = $papias
                                ->setAttributes($attributes)
                                ->applyFilters()
                                ->buildAssertion()
                                ->buildRedirection();
                
                return $app -> redirect($redirectTo);
            } else
            {
                include(__DIR__ . '/templates/' . $app['form.template'] );
            }
        });

$app->run();