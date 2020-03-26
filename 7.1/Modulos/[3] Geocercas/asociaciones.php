<?php
error_reporting(E_ALL);ini_set("display_errors", 1);

// --- Para activar el debuggeador:
$Vtiger_Utils_Log = true;

// --- Incluimos estos archivos con las funciones para disparar las inserciones
include_once('vtlib/Vtiger/Module.php');
// --- Para agregar el nuevo módulo a una instancia del menú
include_once('vtlib/Vtiger/Menu.php'); 


/*
|--------------------------------------------------------------------------------
| ACTUALIZAR MODULO ACTUAL
|--------------------------------------------------------------------------------
| Relación M-N Entre el módulo nuevo y una existente
|
*/
// Modulo ya existente
	$MODULENAME = 'Accounts';        
	$moduleInstance = Vtiger_Module::getInstance( $MODULENAME );

// Módulo Recién Creado
	$RELATED_MODULE = 'Geocerca';
	$linkedModule = Vtiger_Module::getInstance( $RELATED_MODULE );
	$relationLabel  = $RELATED_MODULE . 's';

	
// Execute
	$moduleInstance->setRelatedList( $linkedModule, $relationLabel, array('ADD','SELECT'),'get_dependents_list' );                          
	echo "<br>The related module [$relationLabel] was created succesfully for [$MODULENAME] module.\n";        
?>