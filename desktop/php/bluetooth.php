<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'bluetooth');

$secure=0;
$options=array();
$port = config::byKey('port', 'bluetooth');

echo '<div class="row row-overflow">

    <div class="col-lg-12 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <div class="row">
            <div class="col-lg-12">
               <legend>{{équipements Bluetooth à proximité}}</legend> 
            </div>
        </div>
        <button onclick="printDiscoverBT()">{{Rafraichir}}</button><br><br>
        <table id="table_discover_bt" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{MAC}}</th>
                    <th>{{Nom}}</th>
                    <th>{{Type}}</th>
                    <th>{{Services}}</th>
                    <th>{{Action}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
		<div class="row">
            <div class="col-lg-12">
               <legend>{{équipements Bluetooth déjà appairés}}</legend> 
            </div>
        </div>
        <button onclick="printPairedBT()">{{Rafraichir}}</button><br><br>
        <table id="table_paired_bt" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{MAC}}</th>
                    <th>{{Nom}}</th>
                    <th>{{Type}}</th>
                    <th>{{Services}}</th>
                    <th>{{Action}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
      	
    </div>
</div>';
?>
<script>

</script>
<?php
include_file('desktop', 'bluetooth', 'js', 'bluetooth');
include_file('core', 'plugin.template', 'js');
?>