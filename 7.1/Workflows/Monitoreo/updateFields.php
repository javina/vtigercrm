<?php
/**
 * Función para actualizar los correos de la cuenta en el monitoreo
 *
 * @return void
 * @author JLAM
 **/
function updateFields( $entity )
{
	//error_reporting(E_ALL);ini_set("display_errors", 1);
	/*
  	|---------------------------------------------------------------------------
  	| Configuración
  	|---------------------------------------------------------------------------
  	| Incluimos archivos y variables para procesar la información
  	*/
	require_once "config.php";
	require_once "include/database/PearDatabase.php";
	require_once "include/utils/utils.php";
	require_once "modules/Monitoreo/Monitoreo.php";
	
	
	
	global $current_user, $currentModule, $adb, $log;

	// Vamos a Obtener la información del Monitoreo recién creado
	$elementos = explode("x", $entity->id);	
	$id = $elementos[1];
	// Obtenemos una instancia del Monitoreo
	$focus = new Monitoreo();
  	$focus->id = $id;
  	$focus->retrieve_entity_info($id, $currentModule);  
  	$accoutnid = $focus->column_fields['accountid'];

  	// Buscamos los correos de la cuenta seleccionada
  	$consulta = "SELECT a.cf_838 as correos FROM vtiger_accountscf a				 
				 INNER JOIN vtiger_crmentity e ON e.crmid = a.accountid AND e.deleted=0
				 WHERE accountid = $accoutnid";

	// Obtenemos los datos
	$resultado  = $adb->query($consulta);	
	$renglon    = $adb->fetch_array($resultado);
	$correos    = $renglon['correos']; 	

	//$focus->column_fields['cf_840'] = $correos;
	//$focus->save('Monitoreo');
	// Actualizamos la información de los correos de la cuenta al monitoreo
	$update = "Update vtiger_monitoreocf set cf_840 = '$correos' WHERE monitoreoid = '$id'";
    $adb->query($update);	
  
}
?>