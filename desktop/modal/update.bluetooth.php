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
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div id='div_updatebluetoothAlert' style="display: none;"></div>
<div class="alert alert-warning">{{Attention la mise à jour peut être longue (20 min)}}</div>
<pre id='pre_bluetoothupdate' style='overflow: auto; height: 90%;with:90%;'></pre>


<script>
	$.ajax({
		type: 'POST',
		url: 'plugins/bluetooth/core/ajax/bluetooth.ajax.php',
		data: {
			action: 'updatebluetooth'
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error, $('#div_updatebluetoothAlert'));
		},
		success: function () {
			getbluetoothLog(1);
		}
	});
	function getbluetoothLog(_autoUpdate) {
		$.ajax({
			type: 'POST',
			url: 'core/ajax/log.ajax.php',
			data: {
				action: 'get',
				logfile: 'bluetooth_update',
			},
			dataType: 'json',
			global: false,
			error: function (request, status, error) {
				setTimeout(function () {
					getJeedomLog(_autoUpdate, _log)
				}, 1000);
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				var log = '';
				var regex = /<br\s*[\/]?>/gi;
				for (var i in data.result.reverse()) {
					log += data.result[i][2].replace(regex, "\n");
					if(log.search("Everything is successfully installed") > 0){
						$('#bt_stopbluetoothDemon').click();
						_autoUpdate=0;
						clearTimeout(t);
					}
				}
				$('#pre_bluetoothupdate').text(log);
				
				$('#pre_bluetoothupdate').scrollTop($('#pre_bluetoothupdate').height() + 200000);
				if (!$('#pre_bluetoothupdate').is(':visible')) {
					_autoUpdate = 0;
				}
				if (init(_autoUpdate, 0) == 1) {
					t = setTimeout(function () {
						getbluetoothLog(_autoUpdate)
					}, 1000);
				}
			}
		});
	}
</script>