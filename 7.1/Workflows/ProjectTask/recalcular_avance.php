<?php
/**
 * Función para recalcular el avance de un proyecto cuando el
 * porcentaje de avance de las tareas cambia.
 * Date: 04072017 05:48 pm 
 *
 * @return void
 * @version 1.0.0
 * @author JLAM
 **/
function recalcular_avance( $entity ) {
	// Debug Only
	// error_reporting(E_ALL);ini_set("display_errors", 1);
	
	/*
  	|---------------------------------------------------------------------------
  	| Configuración
  	|---------------------------------------------------------------------------
  	| Incluimos archivos y variables para procesar la información
  	*/
	require_once "config.php";
	require_once "include/database/PearDatabase.php";
	require_once "include/utils/utils.php";		
	// Variables globales
	global $current_user, $currentModule, $log;
	// Get DB instance
	$adb = PearDatabase::getInstance();

	// Obtenemos el ID del Proyecto Padre de la Tarea.
	$elementos = explode("x", $entity->data['projectid']);		
	$projectid = $elementos[1];

	// Obtenemos todas las tareas del proyecto
	$consulta = "SELECT  pt.projecttaskprogress FROM vtiger_project p 
	inner join vtiger_projecttask pt on pt.projectid = p.projectid
	inner join vtiger_crmentity e ON e.crmid = p.projectid
	where p.projectid = ?
	AND e.deleted = ?";

	$result = $adb->pquery($consulta, array($projectid, 0));
	$total_tasks = $adb->num_rows($result);

	// Si hay tareas en el proyecto calculamos
	if ($total_tasks > 0) {
		// Total de % de las Tareas
		$porcentaje_total = $total_tasks * 100;
		$acumulado = 0;

		// Para cada tarea Obtenemos el Procetanje y lo acumulamos
		for ($i=0; $i < $total_tasks; $i++) { 			
			$taskprogress = $adb->query_result($result,$i,'projecttaskprogress');
			$acumulado += ($taskprogress == '') ? 0 : $taskprogress;
		}

		// Vamos a redondear el resultado al múltimplo de 5 más cercano (arriba o abajo)
		$delta = 5;
		$n = ($acumulado * 100) / $porcentaje_total;
		$avance = (ceil($n)%$delta === 0) ? ceil($n) : round(($n+$delta/2)/$delta)*$delta;
		// Convertimos el valor a una opción de la lista válida (Incrementos de 5%)
		$avance = $avance .'%';

		// Actualizamos el Avance del Proyecto		
		$sql = "UPDATE vtiger_project SET progress=? WHERE projectid=?";		
		$params = array($avance , $projectid);	
		$adb->pquery($sql, $params);
	}
}
?>