
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
$(function(){
    $('#table_discover_bt').on('click', '.btnPair', function(){
    	var mac = $(this).closest("tr").find('.MAC_addr').text();
    	bootbox.prompt("Code PIN ? (optionel)", function(result) {                
		  if(result!==null){
		  	pairBT(mac,result);
		  }
		});
    });
    
    $('#table_paired_bt').on('click', '.btnUnpair', function(){
    	unpairBT($(this).closest("tr").find('.MAC_addr').text());
	});
});

function printDiscoverBT() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php", // url du fichier php
        data: {
            action: "DiscoverBT"
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
        	if (data.state != 'ok') {
                $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                return;
            }
            console.log(data);
            $('#table_discover_bt tbody').empty();
            for (var i in data.result.cmd) {
            	if (data.result.cmd[i].mac != null) {
                	var tr = '<tr>';
                	tr += '<td class="MAC_addr">' + data.result.cmd[i].mac + '</td>';
                	tr += '<td>' + data.result.cmd[i].name + '</td>';
                	tr += '<td>' + data.result.cmd[i].type + '</td>';
                	tr += '<td>' + data.result.cmd[i].services + '</td>';
                	if(data.result.cmd[i].type=="bluetooth"){
                	tr += '<td><button type="button" class="btnPair">Appairer</button></td>';
                	}else{
                	tr += '<td></td>';
                	}
                	tr += '</tr>';
                	$('#table_discover_bt tbody').append(tr);
               }
            }
        }
    });
}

function printPairedBT() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php", // url du fichier php
        data: {
            action: "PairedBT"
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
        	if (data.state != 'ok') {
                $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                return;
            }
            console.log(data);
            $('#table_paired_bt tbody').empty();
            for (var i in data.result.cmd) {
            	if (data.result.cmd[i].mac != null) {
                	var tr = '<tr>';
                	tr += '<td class="MAC_addr">' + data.result.cmd[i].mac + '</td>';
                	tr += '<td>' + data.result.cmd[i].name + '</td>';
                	tr += '<td>' + data.result.cmd[i].type + '</td>';
                	tr += '<td>' + data.result.cmd[i].services + '</td>';
                	tr += '<td><button type="button">Connecter</button> | <button type="button" class="btnUnpair">Supprimer</button></td>';
                	tr += '</tr>';
                	$('#table_paired_bt tbody').append(tr);
               }
            }
        }
    });
}

function pairBT(mac,pin) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php", // url du fichier php
        data: {
            action: "pairBT",
            mac: mac,
            pin: pin
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
        	if (data.state != 'ok') {
                $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                return;
            }
            console.log(data);
            printPairedBT();
        }
    });
}

function unpairBT(mac) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php", // url du fichier php
        data: {
            action: "unpairBT",
            mac: mac
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
        	if (data.state != 'ok') {
                $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                return;
            }
            console.log(data);
            printPairedBT();
        }
    });
}

printPairedBT();
