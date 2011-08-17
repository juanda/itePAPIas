<?php
/**
 * @copyright Copyright 2005-2010 RedIRIS, http://www.rediris.es/
 *
 * This file is part of phpPoA2.
 *
 * phpPoA2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpPoA2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpPoA2. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version 2.0
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 * @package phpPoA2
 * @subpackage Internationalization
 */

/* WARNING WARNING WARNING WARNING WARNING
 *
 * Please ensure you save this file as UTF-8 text!
 *
 * WARNING WARNING WARNING WARNING WARNING
 */

// TODO: classify messages per engine

/**
 * An array with all the internationalized messages used by phpPoA2.
 * @global array $poa_messages
 * @name $messages
 * @ignore
 */
$poa_messages = array(
// messages for fatal error page
'fatal-error'           => 'Error fatal',
'error-desc'            => 'Ha ocurrido un error inesperado. Por favor, contacte con el administrador y especifique el identificador de sesi&oacute;n y la traza de depuraci&oacute;n mostradas a continuaci&oacute;n.',
'error-message'         => 'Mensaje de error',
'session-id'            => 'Identificador de sesi&oacute;n',
'backtrace'             => 'Traza de depuraci&oacute;n',

'invalid-php-version'   => 'Version de PHP inválida. La extensión PAPI requiere al menos PHP %s.',
'extension-required'    => 'La extensión "%s" es un requisito, pero no está cargada.',
'library-required'      => 'La librería "%s" es un requisito, pero no se encuentra.',
'authenticating-via'    => 'Autenticando mediante el motor "%s".',
'authorize-user-via'    => 'Autorizando al usuario "%s" mediante el motor "%s".',
'query-authz'           => 'Comprobando la autorización con todos los motores disponibles.',
'query-authz-via'       => 'Comprobando la autorización con el motor "%s".',
'authz-engine-err'      => 'No se puede ejecutar el motor de autorización desconocido "%s", revise la configuración.',
'authz-levels-err'      => 'No se han definido niveles de autorización para este PoA.',
'authn-engine-err'      => 'No hay ningún motor de autenticación definido, revise la configuración.',
'check-authn-status'    => 'Comprobando el estado de la autenticación mediante el motor "%s".',
'invalid-config'        => 'Configuración inválida',
'config-not-found'      => 'No se puede configurar el PoA, el fichero "%s" no se ha encontrado.',
'config-err-php'        => 'No se puede configurar el PoA, "$%1$s" o "$%1$s[\'%2$s\']" no encontrados.',
'config-err-ini'        => 'No se puede configurar el PoA, no se encontró la sección "\'%s\'".',
'config-param-err'      => 'No se puede configurar el PoA, no se encontró el parámetro "\'%s\'".',
'cannot-open-log'       => 'No se puede abrir el fichero de log "%s".',
'cannot-write-log'      => 'No se puede escribir en el fichero de log "%s".',
'class-not-found'       => 'No se puede encontrar la clase "%s".',
'authn-success'         => 'Autenticación satisfactoria mediante el motor "%s".',
'authn-err'             => 'Error en la autenticación.',
'authz-err'             => 'Usuario %s no autorizado.',
'authz-expired'         => 'La autorización ha expirado para el usuario "%s".',
'authz-default-fallback'=> 'No se pudo tomar una decisión de autorización, se toma la acción por defecto.',
'valid-cookie'          => 'La cookie es valida.',
'cookie-not-found'      => 'Cookie "%s" no encontrada.',
'cookie-location-err'   => 'Localización inválida para la cookie: "%s".',
'cookie-service-err'    => 'El identificador de servicio "%s" de la cookie no corresponde, debería ser "%s".',
'cookie-expired-err'    => 'Cookie expirada.',
'cookie-rejected-err'   => 'La aserción se corresponde con un filtro de denegación de cookie: "%s".',
'cannot-set-cookie'     => 'No se puede enviar la cookie. Por favor, compruebe que no se ha enviado nada al navegador previamente.',
'empty-cookie-err'      => 'Cookie "%s" vacía.',
'empty-response-err'    => 'Respuesta vacía del AS/GPoA.',
'pubkey-error'          => 'No se puede leer la clave pública del fichero "%s".',
'cannot-decrypt'        => 'No se puede desencriptar la respuesta, compruebe la clave del AS/GPoA.',
'valid-response'        => 'La respuesta del AS/GPoA autentica la aserción "%s".',
'expired-response'      => 'La respuesta del AS/GPoA ha expirado.',
'unknown-request'       => 'Se ha recibido una respuesta del AS/GPoA para una petición desconocida.',
'req-db-purged'         => 'Se han eliminado %d peticiones obsoletas de la base de datos.',
'redirecting'           => 'Redirigiendo al usuario a "%s".',
'cannot-redirect'       => 'No se puede redirigir al usuario.',
'cannot-save-request'   => 'No se pudo guardar la petición original.',
'cannot-open-req-db'    => 'No se puede abrir la base de datos de peticiones.',
'cannot-open-authz-db'  => 'No se puede abrir la base de datos de autorizados.',
'cannot-open-inv-db'    => 'No se puede abrir la base de datos de invitaciones.',
'cannot-fetch-key'      => 'No se puede encontrar la clave "%s" en la base de datos.',
'cannot-del-key'        => 'No se puede borrar la clave "%s" de la base de datos.',
'as-id-error'           => 'El identificador del servidor de autenticación "%s" no coincide con el esperado "%s".',
'continue'              => 'Continuar',
'source-ip-allowed'     => 'La IP de origen "%s" se corresponde con el patrón permitido "%s".',
'source-ip-denied'      => 'La IP de origen "%s" se corresponde con el patrón denegado "%s".',
'invalid-hook'          => 'No se pudo registrar una función o método inválido para el hook "%s".',
'running-hook'          => 'Ejecutando el hook "%s" para "%s".',
'add-hook'              => 'Añadiendo el hook "%s" para "%s".',
'remove-hook'           => 'Eliminando el hook "%s" para "%s".',
'hook-error'            => 'El hook "%s" es inválido.',
'allowed-param-match'   => 'Los siguientes parámetros de la petición están permitidos: [%s].',
'denied-param-match'    => 'Los siguientes parámetros de la petición están denegados: [%s].',
'allowed-attr-match'    => 'Los siguientes atributos del usuario están permitidos: [%s].',
'denied-attr-match'     => 'Los siguientes atributos del usuario están denegados: [%s].',
'invite-non-existant'   => 'No se pudo encontrar la invitación con referencia "%s".',
'missing-attrs'         => 'No se pudo encontrar ninguno de los atributos obligatorios.',
'missing-mail-attr'     => 'No se pudo encontrar el atributo "%s", verificación de e-mail fallida.',
'mail-attr-err'         => 'No se pudo encontrar el parámetro de configuración "EmailVerifyAttribute" o está vacío, verificación de e-mail fallida.',
'mail-attr-alg-err'     => 'No se pudo extraer el algoritmo del atributo "%s" con valor "%s"',
'mail-attr-val-err'     => 'No se pudo extraer el valor del atributo "%s" con valor "%s"',
'mail-verify-err'       => 'No se pudo verificar el e-mail "%s", no se encontró coincidencia con ninguno de los valores recibidos.',
'mail-verify-ok'        => 'La dirección de correo "%s" se ha verificado correctamente con el algoritmo "%s" y el valor "%s".',
'cannot-authorize'      => 'No se pudo autorizar al usuario "%s".',
'cannot-del-invite'     => 'No se pudo eliminar la invitación con referencia "%2$s" para el usuario "%1$s".',
'invite-sent-to'        => 'Se ha enviado una invitación a la dirección "%s".',
'user-authorized'       => 'El usuario "%s" está ahora autorizado.',
'user-authz-ok'         => 'El usuario "%s" está autorizado.',
'user-authz-err'        => 'El usuario "%s" no está autorizado.',
'user-already-authz'    => 'El usuario "%s" ya estaba autorizado.',
'slo-conf-error'        => 'No se puede realizar el logout, revise la configuración.',
'already-logged-out'    => 'El usuario no está identificado.',
'local-logout-success'  => 'Se ha realizado el logout local del usuario.',
'slo-logout'            => 'Petición de logout global recibida.',
'slo-requested'         => 'El usuario ha solicitado el logout global.',
);

?>
