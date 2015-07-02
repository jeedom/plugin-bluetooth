    <?php

    /* This file is part of Jeedom.
     *
     * Jeedom is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * Jeedom is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
     */

    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

    global $jsonrpc;
    if (!is_object($jsonrpc)) {
       throw new Exception(__('JSONRPC object not defined', __FILE__), -32699);
    }
    $params = $jsonrpc->getParams();
    
    if ($jsonrpc->getMethod() == 'deamonRunning') {
       log::add('bluetooth','info','Vérification du statut du service');
       if (bluetooth::deamonRunning()) {
		   $jsonrpc->makeSuccess('1');
       } else {
		   $jsonrpc->makeSuccess('0');
		}
    }

    if ($jsonrpc->getMethod() == 'runDeamon') {
       log::add('bluetooth','info','Démarrage du service bluetooth');
       bluetooth::runDeamon();
	   $jsonrpc->makeSuccess('ok');
    }
    
	if ($jsonrpc->getMethod() == 'stopDeamon') {
       log::add('bluetooth','info','Stop du service bluetooth');
       bluetooth::stopDeamon();
	   $jsonrpc->makeSuccess('ok');
    }
	
    if ($jsonrpc->getMethod() == 'saveConfig') {
		$port_usb = $params['port_usb'];
		$port_server = $params['port_server'];
		$debug = $params['debug'];
       	bluetooth::saveConfig('port',$port_usb);
       	bluetooth::saveConfig('port_server',$port_server);
		bluetooth::saveConfig('enableLogging',$debug);
		//bluetooth::stopDeamon();
       	$jsonrpc->makeSuccess('ok');
    }   
    
    if ($jsonrpc->getMethod() == 'getConfig') {
       $port = config::byKey('port', 'bluetooth');
       $port_server = config::byKey('port_server', 'bluetooth');
       $enableLogging = config::byKey('enableLogging', 'bluetooth');
	   log::add('bluetooth','info','params '.json_encode($params));
       $jsonrpc->makeSuccess(array('port' => $port, 'port_server' => $port_server, 'enableLogging' => $enableLogging));
    }      

    throw new Exception(__('Aucune methode correspondante pour le plugin bluetooth : ' . $jsonrpc->getMethod(), __FILE__));
    ?>
