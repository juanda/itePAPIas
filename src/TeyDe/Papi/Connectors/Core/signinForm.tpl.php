<?php
/*
  This file is part of itePAPIas.
  Foobar is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Foobar is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $app['dir.root'] . '/css/core/default.css' ?>" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $app['dir.root'] . '/css/core/contenido.css'?>" />

        <title>PAPI Authentication Server</title>
        <link rel="shortcut icon" href="/favicon.ico" />
    </head>

    <body>


        <div id="contenedor_general">
            <div id="cabecera">
                <div id="logo"></div>
            </div>
            <div id="wrapper">

                <div id="sf_admin_container">
                    <h1>PAPI Authentication Service</h1>


                    <div class="texto_intro">
                        This is the example login form of the symfony PAPI plugin. If needed, you
                        can change it. In the documentation you can find how to do it.
                    </div>

                    <h1>session init</h1>

                    <div class="caja_login">
                        <div class="sf_admin_form">
                            <?php if ($app['session']->hasFlash('message')): ?>
                                <div class="error"><?php echo $app['session']->getFlash('message') ?></div>
                            <?php endif; ?>

                            <?php if (isset($app['validator.errors'])) : ?>
                                    <div class="error">                 
                                <?php foreach ($app['validator.errors'] as $error) : ?>

                                <?php echo $error->getPropertyPath() . ' : ' . $error->getMessage() ?>
                                        <br/>
                                <?php endforeach; ?>
                                        </div>  
                                <?php endif; ?>
                                          


                                    <form name="f" action="index.php" method="post" >
                                        <!-- Parámetros PAPI -->
                                <?php if (isset($app['session.request'])): ?>
                                <?php foreach ($app['session.request'] as $k => $v): ?>
                                                <input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Fin Parámetros PAPI -->

                                <fieldset>
                                    <div class="sf_admin_form_row">
                                        <div>
                                            <label for="username"><label for="signin_username">Username</label></label>
                                            <div class="content"><input type="text" name="signin[username]" id="signin_username" /></div>
                                        </div>
                                    </div>

                                    <div class="sf_admin_form_row">

                                        <div>
                                            <label for="password"><label for="signin_password">Password</label></label>
                                            <div class="content"><input type="password" name="signin[password]" id="signin_password" /></div>
                                        </div>
                                    </div>
                                </fieldset>


                                <ul class="sf_admin_actions">

                                    <li>
                                        <input type="submit" value="Continue" />
                                    </li>
                                </ul>
                            </form>
                        </div>

                    </div>
                </div>



            </div>

            ﻿<div class="PiePagina">
                <ul>
                    <li><a href="#" title="Aviso legal" target="">Aviso legal</a>|</li>
                    <li><a href="http://www.w3.org/WAI/" title="Accesibilidad" target="">Accesibilidad</a>|</li>

                    <li>
                        <a href="http://www.w3.org/WAI/" title="Logo de la WAI" target="">
                            <img alt="Accesibilidad web" src="/symfonite/web/images/logowai.gif" />            </a>
                    </li>
                </ul>
            </div>
        </div>
    </body>
</html>
