<?php

use TeyDe\Papi\Core;
use TeyDe\Papi\Connectors;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../src/Silex/silex.phar';
require_once __DIR__ . '/../config/config.php';

$app = new Silex\Application();
//
// Debug Mode
//
$app['debug'] = true;
//
// Register Extensions
//
$app->register(new Silex\Extension\SessionExtension());
$app->register(new Silex\Extension\ValidatorExtension(), array(
    'validator.class_path' => __DIR__ . '/../src',
));

$app['autoloader']->registerNamespaces(array(
    'TeyDe' => __DIR__ . '/../src',
));
$app['autoloader']->register();
//
// Looking for the connector to use
//
$app['connector.name'] = $config['connector']['name'];
if (!$app['connector.name'])
{
    throw new Exception('No connector has been defined. You must set the
        "connector" parameter in the configuration file');
}

//
// Looking for the signin form
//

$app['form.template'] = Connectors\Core\SigninFormFactory::createInstance($app['connector.name']);

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
            include($app['form.template'] );
        });

$app->post('/signin', function () use ($config, $app)
        {
//            echo '<pre>';
//            echo 'POST<br>';
//            print_r($app['session']);
//            echo '</pre>';
            $signinData = $app['request']->get('signin');

            $signinParameters = Connectors\Core\SigninParametersFactory::createInstance($app['connector.name']);
            $signinParameters->bind($signinData);

            $app['validator.errors'] = $app['validator']->validate($signinParameters);

            if (count($app['validator.errors']) == 0) // The form is valid
            {
                $connector = Connectors\Core\ConnectorFactory::createInstance($signinData,
                                $app['connector.name'], $config['connector']['config']);
                $auth = $connector->isAuthenticated();
                if ($auth)
                {
                    $attributes = $connector->getAttributes();
                } else
                {
                    $app['session']->setFlash('message',
                            $config['message_no_auth']);


                    return $app->redirect('signin');
                }
                $papias = new Core\PAPIAS($app['session'], $config);

                $redirectTo = $papias
                                ->setAttributes($attributes)
                                ->applyFilters()
                                ->buildAssertion()
                                ->buildRedirection();

                return $app->redirect($redirectTo);
            } else
            {
                include( $app['form.template'] );
            }
        });

$app->get('/test', function () use ($config, $app)
        {
            require_once(__DIR__ . '/../src/phpPoA-2.3/PoA.php');

            $poa = new PoA("test");
            // comprobamos que estamos autentificados, si no es así se produce una
            // redirección al IdP para solicitar el login y password al hippy.
            $auth = $poa->authenticate();
            $papi_attributes = array();

            if ($auth)
            {
                // recuperamos los atributos devueltos por el IdP
                $papi_attributes = $poa->getAttributes();

                echo '<h2> Prueba OK, estos son los atributos devueltos:</h2><br/>';
                echo '<pre>';
                print_r($papi_attributes);
                echo '</pre>';
                exit;
            } else
            {
                throw new Exception('La prueba no ha tenido éxito');
            }
        });

$app->run();
