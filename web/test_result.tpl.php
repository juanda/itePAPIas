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
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $app['dir.root'] . '/css/core/contenido.css' ?>" />

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
                    <h1>PAPI Authentication Service TEST</h1>

                    <h2> Test OK, These are the returned attributes:</h2><br/>
                    <pre>
                        <?php print_r($papi_attributes) ?>
                    </pre> 
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

