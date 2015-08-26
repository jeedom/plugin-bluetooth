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

/* * ***************************Includes********************************* */

class bluetooth extends eqLogic {
    /*     * *************************Attributs****************************** */
		
    /*     * ***********************Methode static*************************** */

    public static function returnState($_options) {
        $cmd = cmd::byId($_options['cmd_id']);
        if (is_object($cmd)) {
            $cmd->returnState();
        }
    }
	
	public static function DiscoverBT() {
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");	
        $devices=array();	
		log::add('bluetooth','debug','start scanning BT');
		exec("hcitool scan",$values);
		$i=0;
		foreach ($values as $value) {
			$services="";
	        if(trim($value)<>"Scanning ..."){
		        $device=explode("\t", trim($value));
				log::add('bluetooth','info','device '.$i.' : '.trim($device[0]).' nom:'.trim($device[1]));
				$devices[$i]['mac']=trim($device[0]);
				$devices[$i]['name']=trim($device[1]);
				$devices[$i]['type']='bluetooth';
				exec("sudo sdptool browse ".trim($device[0])." | grep Service\ Name",$services);
				$services=str_replace("Service Name: ", "", implode("<br>", $services));
				log::add('bluetooth','debug','services '.$services);
				if($services=="" || $services[0]==""){
					exec("bt-device -s ".trim($device[0])." | grep SrvName",$services);
					log::add('bluetooth','debug','services 2 '.json_encode($services));
					$services=str_replace("SrvName: ", "", implode("<br>", $services));
					$services=str_replace('"', "", $services);
					
				}
				$devices[$i]['services']=$services;
				$i++;
			}
	    }
		exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");	
		log::add('bluetooth','debug','start scanning LE');
		exec("sudo python ../../ressources/testblescan.py",$valuesLE);
		log::add('bluetooth','debug','stop scanning LE : '.json_encode($valuesLE));
		foreach ($valuesLE as $value) {
			$services="";
	        if(trim($value)<>"LE Scan ..."){
		        $device=explode(" ", trim($value));
				log::add('bluetooth','debug','device LE '.$i.' : '.trim($device[0]));
				$devices[$i]['mac']=trim(strtoupper($device[0]));
				$devices[$i]['name']='';
				$devices[$i]['type']='bluetooth LE';
				$devices[$i]['services']='';
				$i++;
			}
	    }
		//$devices=array_unique($devices);
		exec("sudo hciconfig hci0 down");	
        exec("sudo hciconfig hci0 up");
		return $devices;
    }

	public static function PairedBT() {
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");	
        $devices=array();	
		//log::add('bluetooth','debug','start scanning BT');
		exec("sudo bt-device -l",$values);
		$i=0;
		foreach ($values as $value) {
			$services="";
	        if(trim($value)<>"Added devices:"){
		        $device=explode("(", trim($value));
				log::add('bluetooth','info','device '.$i.' : '.trim($device[0]).' nom:'.trim($device[1]));
				$devices[$i]['mac']=str_replace(")", "", trim($device[1]));
				$devices[$i]['name']=trim($device[0]);
				$devices[$i]['type']='bluetooth';
				exec("sudo sdptool browse ".trim($device[1])." | grep Service\ Name",$services);
				$services=str_replace("Service Name: ", "", implode("<br>", $services));
				if($services==""){
					exec("sudo bt-device -s ".trim($device[1])." | grep SrvName",$services);
					$services=str_replace("SrvName: ", "", implode("<br>", $services));
					$services=str_replace('"', "", $services);
				}
				$devices[$i]['services']=$services;
				$i++;
			}
	    }
		return $devices;
    }

	public static function PairBT($mac, $pin) {
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");	
		if($pin<>""){
			exec("sudo ../../ressources/pairing.exp ".$mac." ".$pin);
			//Bluez 4
			//exec("echo ".$pin."|bluez-simple-agent ".config::byKey('port', 'bluetooth')." ".$mac);
		}else{
			exec("sudo ../../ressources/pairing.exp ".$mac);
			//Bluez 4	
			//log::add('bluetooth','debug',"bluez-simple-agent ".config::byKey('port', 'bluetooth')." ".$mac);
			//exec("bluez-simple-agent ".config::byKey('port', 'bluetooth')." ".$mac);
		}
		//Bluez 4
		//exec("bluez-test-device trusted ".$mac." yes");
		exec("sudo bt-device -s ".trim($device[1])." | grep SrvName",$services);
		//Bluez 4
		/*
		if(strpos($services, 'audio')){
			$file = file_get_contents("/etc/asound.conf");
		    if (strpos($file, "bluetooth") !== false) {
		        log::add('bluetooth','debug',"Config audio existante");
		    }else{
		    	exec('cat >> /etc/asound.conf << EOL
					pcm.bluetooth {
					        type bluetooth
					        device "'.$mac.'"
					        profile "auto"
					}
					EOL');
		    }
		}*/
		return true;
    }
    
	public static function UnpairBT($mac) {
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");	
		exec('sudo bluetoothctl << EOF
					agent on
					default-agent
					scan on
					disconnect '.$mac.'
					untrust '.$mac.'
					remove '.$mac.'
					quit 
					EOF');
		//Bluez 4
		//exec("sudo bluez-test-device trusted ".$mac." no");
		//log::add('bluetooth','debug',"sudo bluez-simple-agent ".config::byKey('port', 'bluetooth')." ".$mac." remove");
		//exec("sudo bluez-simple-agent ".config::byKey('port', 'bluetooth')." ".$mac." remove");
		exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." down");	
        exec("sudo hciconfig ".config::byKey('port', 'bluetooth')." up");
		return true;
    }

   	public static function updatebluetooth() {
		log::remove('bluetooth_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('bluetooth_update') . ' 2>&1 &';
		exec($cmd);
	}
	
	
	public static function saveConfig($param, $value ) {
		config::save($param, $value,  'bluetooth');
	    log::add('bluetooth','info','Sauvegarde de la configuration' . $param .' avec '.$value);
    }

    

    /*     * *********************Methode d'instance************************* */
}

class bluetoothCmd extends cmd {
    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

}
