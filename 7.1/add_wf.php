<?php

require_once 'include/utils/utils.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
$emm = new VTEntityMethodManager($adb); 

//$emm->addEntityMethod("Module Name","Label", "Path to file" , "Method Name" );
//var_dump($emm->addEntityMethod("HelpDesk", "Cerrar Caso con Fechador", "modules/HelpDesk/workflows/closeTicket.php", "closeTicket"));
//var_dump($emm->addEntityMethod("ODT", "Cerrar Odt", "modules/ODT/workflows/cerrarOdt.php", "cerrarOdt"));

var_dump($emm->addEntityMethod("Monitoreo", "Actualizar Correos", "modules/Workflows/Monitoreo/updateFields.php", "updateFields"));

echo 'addEntityMethod complete!';
?>

