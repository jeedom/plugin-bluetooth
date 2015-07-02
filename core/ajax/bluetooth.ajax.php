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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception('401 Unauthorized');
    }
	
	if (init('action') == 'updatebluetooth') {
		bluetooth::updatebluetooth();
		ajax::success();
	}
	
    if (init('action') == 'DiscoverBT') {
    	$arr=bluetooth::DiscoverBT();
    	$return['cmd'] = array();
        foreach ($arr as $cmd) {
            $return['cmd'][] = $cmd;
        }
        ajax::success($return);
        
    }
	
	if (init('action') == 'PairedBT') {
    	$arr=bluetooth::PairedBT();
    	$return['cmd'] = array();
        foreach ($arr as $cmd) {
            $return['cmd'][] = $cmd;
        }
        ajax::success($return);
        
    }
	
	if (init('action') == 'pairBT') {
    	$arr=bluetooth::PairBT(init('mac'),init('pin'));
    	$return['cmd'] = array();
        foreach ($arr as $cmd) {
            $return['cmd'][] = $cmd;
        }
        ajax::success($return);
        
    }

	if (init('action') == 'unpairBT') {
    	$arr=bluetooth::UnpairBT(init('mac'));
    	$return['cmd'] = array();
        foreach ($arr as $cmd) {
            $return['cmd'][] = $cmd;
        }
        ajax::success($return);
        
    }
	
	if (init('action') == 'startDeamon') {
		$type=init('type');
		if($type=='remote'){
			$id=init('id');
			foreach (jeeNetwork::byPlugin('bluetooth') as $jeeNetwork) {
				if ($jeeNetwork->getId() == $id) {
					$jsonrpc = $jeeNetwork->getJsonRpc();
					if (!$jsonrpc->sendRequest('runDeamon',array('plugin' => 'bluetooth'))) {
								throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
					}
				}
			}
		}else{
	        bluetooth::runDeamon();
	    }
		ajax::success();
    }
	
	if (init('action') == 'postSave') {
		$type=init('type');
		if($type=='remote'){
			$id=init('id');
			$port_usb=init('port_usb');
			$port_server=init('port_server');
			$debug=init('debug');
			foreach (jeeNetwork::byPlugin('bluetooth') as $jeeNetwork) {
				if ($jeeNetwork->getId() == $id) {
					$jsonrpc = $jeeNetwork->getJsonRpc();
					if (!$jsonrpc->sendRequest('saveConfig', array('plugin' => 'bluetooth', 'port_usb' => $port_usb, 'port_server' => $port_server, 'debug' => $debug))) {
								throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
					}
				}
			}
		}
		ajax::success();
    }

    throw new Exception('Aucune methode correspondante');
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
