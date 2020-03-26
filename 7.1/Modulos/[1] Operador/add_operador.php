<?php
/*
|-----------------------------------------------------------------------------------
| SCRIPT PARA GENERACIÓN DE UN MODULO DE TIPO ENTIDAD DEL CRM
|-----------------------------------------------------------------------------------
|
| Author:         >>> : M!ndstorm
| Vtiger Version: >>> : 7.0.1
| Description:    >>> : Plantilla para crear módulo tipo entidad (entity module)
|
*/

/*
|-----------------------------------------------------------------------------------
| CLASES DEL CRM 
|-----------------------------------------------------------------------------------
| Clases necesarias para generar módulos del CRM:
|
| Module.php    ---  Para instanciar el módulo nuevo
| Functions.php ---  Para ejecutar funciones de creación de Tablas
|
*/      
        require_once 'vtlib/Vtiger/Module.php';          
        require_once 'vtlib/Vtiger/Functions.php';         
        $Vtiger_Utils_Log = true;

/*
|------------------------------------------------------------------------------------
| NOMBRE DEL MÓDULO
|------------------------------------------------------------------------------------
| Se sugiere Que sea una sola Plabra, 1 letra Mayúscula. (Accounts, Assets, Documents)
| Si se utilizan dos palabras, se sugiere que estén seguidas iniciando con mayúsculas
| (HelpDesk, PriceBooks, SalesOrder)
|
*/
        $MODULENAME = 'Operador';
/*
|------------------------------------------------------------------------------------
| APP TAB MENU
|------------------------------------------------------------------------------------
| Asignamos el nombre e indicamos a que opción del menú pertenece.
| Los valores posibles estan listados a continuación:
| array( 'Analytics', 'Inventory', 'Marketing', 'Sales', 'Support', 'Tools');
|
*/        
        $APPTABMENU = 'Inventory';       
        
/*
|------------------------------------------------------------------------------------
| VALIDAR 
|------------------------------------------------------------------------------------
| Validamos que el modulo no exista para poder crearlo.
|
*/
        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);        
        if ($moduleInstance || file_exists('modules/'.$MODULENAME)) 
        {
                echo "The module that you attempt to create is already present.\n";
                echo "We suggest to choose a different name.";

                    echo '<pre>';
                    var_dump(getPaths());
                    echo '</pre>';  

                exit(0);                
        }

/*
|------------------------------------------------------------------------------------
| CREACION DEL MÓDULO
|------------------------------------------------------------------------------------
| Creamos la instancia del módulo 
| Luego se guarda el módulo.
|
*/

        $moduleInstance = new Vtiger_Module();
        $moduleInstance->name   = $MODULENAME;
        $moduleInstance->parent = $APPTABMENU;
        $moduleInstance->save();        

/*
|------------------------------------------------------------------------------------
| SCHEMA SETUP
|------------------------------------------------------------------------------------
| 
| initTables() crea las tablas necesarias del módulo.
| createNewTable() crea la tabla extra necesaria para esta versión.
|
*/        
        $moduleInstance->initTables();
        createNewTable($moduleInstance);

/*
|------------------------------------------------------------------------------------
| CONFIGURACIÓN DE LOS BLOQUES DEL MÓDULO
|------------------------------------------------------------------------------------
| 
| Primero se definen los bloques donde se almacenan los campos del CRM.
| Siempre deben existir al menos los dos indicados a continuación.
|
*/        
        $block = new Vtiger_Block();
        $block->label = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
        $moduleInstance->addBlock($block);

        $blockcf = new Vtiger_Block();
        $blockcf->label = 'LBL_CUSTOM_INFORMATION';
        $moduleInstance->addBlock($blockcf);


/*
|------------------------------------------------------------------------------------
| CONFIGURACIÓN DE LOS CAMPOS MÓDULO
|------------------------------------------------------------------------------------
| 
| La clase Vtiger_Field() tiene las siguientes propiedades:
|
| [] name       : El nombre del campo como será localizado en la table vtiger_fields.
| [] label      : La etiqueta del campo que se utiliza en el archivo de idiomas. Recomendación, utilizar prefijo: LBL_CAMPO
| [] uitype     : El tipo de campo que será creado. Referencia: https://wiki.vtiger.com/index.php/UI_Types
| [] column     : Nombre de la columna en la tabla vtiger_modulo 
| [] columntype : Tipo de columna en la tabla de la base de datos, va en función al uiType.
| [] typeofdata : Se basa en columntype: 
|                 V~M = Varchar Mandatory (campo texto obligatorio) 
|                 V~O = Varchar Optional (campo texto opcional)
|                 Referencia: https://wiki.vtiger.com/index.php/TypeOfData
|
*/

/*
|------------------------------------------------------------------------------------
| NOTAS ESPECÍFICAS
|------------------------------------------------------------------------------------
| 
| El módulo deben tener 4 campos obligatorios
|
| [1] Identificador del módulo (campo obligatorio)
| [2] Campo de Asignao A:
| [3] Campo de Creado en:
| [4] Campo de Modificado en:
|
*/
        /*
        |-----------------------------------------------------------------------------
        | ENTITY FIELD
        |-----------------------------------------------------------------------------
        | $e_name = Nombre del campo Entidad
        | $e_lbl  = Etiqueta del campo para el archivo de Idiomas
        */         		
                $e_name = strtolower( $MODULENAME );
                $e_lbl  = ucfirst( $e_name );
                $entityField  = new Vtiger_Field();
                $entityField->name = $e_name;
                $entityField->label= $e_lbl;
                $entityField->uitype= 2;                
                $entityField->column = $entityField->name;
                $entityField->columntype = 'VARCHAR(255)';
                $entityField->typeofdata = 'V~M';
                $block->addField( $entityField );
                $moduleInstance->setEntityIdentifier( $entityField );

        /*
        |-----------------------------------------------------------------------------
        | CAMPO CONSECUTIVO DEL MODULO
        |-----------------------------------------------------------------------------
        | Hay que inicializar posteriormente el prefijo y número
        */
                $consecutivo = new Vtiger_Field();
                $consecutivo->name = strtolower( $MODULENAME ).'_no';
                $consecutivo->label = $MODULENAME . ' No';
                $consecutivo->uitype = 4;
                $consecutivo->column = $consecutivo->name;
                $consecutivo->columntype = 'VARCHAR(255)';
                $consecutivo->typeofdata = 'V~O';
                $consecutivo->presence = 0;
                $consecutivo->quickcreate = 3;
                $consecutivo->masseditable = 0;
                $block->addField( $consecutivo );    

        /*
        |-----------------------------------------------------------------------------
        | TEXT FIELD
        |-----------------------------------------------------------------------------        
        */
                $edad = new Vtiger_Field();
                $edad->name   = 'edad';
                $edad->label  = 'LBL_EDAD';
                $edad->uitype = 1;                
                $edad->column = $edad->name;
                $edad->columntype = 'VARCHAR(100)';
                $edad->typeofdata = 'V~O~LE~25';
                $block->addField( $edad );    
 
                /*                
                | Campo del teléfono de contaco del Operador
                */
                $telefono  = new Vtiger_Field();
                $telefono->name = 'telefono';
                $telefono->label= 'LBL_TELEFONO_TEXT_AREA';
                $telefono->uitype= 21;
                $telefono->column = $telefono->name;
                $telefono->columntype = 'TEXT';
                $telefono->typeofdata = 'V~O';
                $block->addField( $telefono );


                /*                
                | Campo de Observaciones del Monitoreo
                */
                $observaciones  = new Vtiger_Field();
                $observaciones->name = 'observaciones';
                $observaciones->label= 'LBL_OBSERVACIONES';
                $observaciones->uitype= 19;
                $observaciones->column = $observaciones->name;
                $observaciones->columntype = 'TEXT';
                $observaciones->typeofdata = 'V~O~LE~250';
                $block->addField( $observaciones );



        /*
        |-----------------------------------------------------------------------------
        | LIST FIELD
        |-----------------------------------------------------------------------------        
        */

                $estatus  = new Vtiger_Field();
                $estatus->name = 'operador_estatus';
                $estatus->label= 'LBL_ESTATUS';
                $estatus->uitype= 15;                 // 16 No se Basa en Roles
                $estatus->column = $estatus->name;                
                $estatus->columntype = 'VARCHAR(255)';
                $estatus->typeofdata = 'V~O';        
                $estatus->setPicklistValues( Array ('Activo', 'Incapacitado', 'Baja', 'Inactivo') );
                $block->addField( $estatus );

        /*
        |------------------------------------------------------------------------------------
        | CAMPOS OBLIGATORIOS
        |------------------------------------------------------------------------------------
        |
        | Estos campos deben existir en el CRM
        |
        */
                $description  = new Vtiger_Field();
                $description->name = 'description';
                $description->label= 'Description';
                $description->uitype= 19;
                $description->column = 'description';
                $description->table = 'vtiger_crmentity';
                $blockcf->addField( $description );

                // Recommended common fields every Entity module should have (linked to core table)
                $assigned_user_id = new Vtiger_Field();
                $assigned_user_id->name = 'assigned_user_id';
                $assigned_user_id->label = 'Assigned To';
                $assigned_user_id->table = 'vtiger_crmentity';
                $assigned_user_id->column = 'smownerid';
                $assigned_user_id->uitype = 53;
                $assigned_user_id->typeofdata = 'V~M';
                $block->addField( $assigned_user_id );

                $CreatedTime = new Vtiger_Field();
                $CreatedTime->name = 'CreatedTime';
                $CreatedTime->label= 'Created Time';
                $CreatedTime->table = 'vtiger_crmentity';
                $CreatedTime->column = 'createdtime';
                $CreatedTime->uitype = 70;
                $CreatedTime->typeofdata = 'T~O';
                $CreatedTime->displaytype= 2;
                $block->addField( $CreatedTime );

                $ModifiedTime = new Vtiger_Field();
                $ModifiedTime->name = 'ModifiedTime';
                $ModifiedTime->label= 'Modified Time';
                $ModifiedTime->table = 'vtiger_crmentity';
                $ModifiedTime->column = 'modifiedtime';
                $ModifiedTime->uitype = 70;
                $ModifiedTime->typeofdata = 'T~O';
                $ModifiedTime->displaytype= 2;
                $block->addField( $ModifiedTime );
            
                if(Vtiger_Field::getInstance('source', $moduleInstance) === false) {
                    // For Source of the record
                    $field = new Vtiger_Field();
                    $field->name = 'source';
                    $field->label = 'Source';
                    $field->table = 'vtiger_crmentity';
                    $field->presence = 2;
                    $field->displaytype = 2; // to disable field in Edit View
                    $field->readonly = 1;
                    $field->uitype = 1;
                    $field->typeofdata = 'V~O';
                    $field->quickcreate = 3;
                    $field->masseditable = 0;
                    $block->addField($field);
                }

            /*
            $moduleModel = Vtiger_Module_Model::getInstance($moduleInstance->name);
            if(Vtiger_Field::getInstance('starred', $moduleInstance) === false && $moduleModel->isStarredEnabled()) {
                //for starred record 
                $field = new Vtiger_Field();
                $field->name = 'starred';
                $field->label = 'starred';
                $field->table = Vtiger_Functions::getUserSpecificTableName($moduleInstance->name);
                $field->presence = 2;
                $field->displaytype = 6;
                $field->readonly = 1;
                $field->uitype = 56;
                $field->typeofdata = 'C~O';
                $field->quickcreate = 3;
                $field->masseditable = 0;
                $block->addField($field);
            }
            */

            
                //Adding tag field
                $field = new Vtiger_Field();
                $field->name = "tags";
                $field->label = "tags";
                $field->table = 'vtiger_operadorcf';
                $field->presence = 2;
                $field->displaytype = 6;
                $field->readonly = 1;
                $field->uitype = 1;
                $field->typeofdata = 'V~O';
                $field->columntype = 'varchar(1)';
                $field->quickcreate = 3;
                $field->masseditable = 0;
                $block->addField($field);            

        /*
        |------------------------------------------------------------------------------------
        | CAMPO PARA RELACIONAR A OTRO MÓDULO
        |------------------------------------------------------------------------------------
        | 
        | Relación tipo 1 - 1 entre los módulos involucrados
        |
        */        
                $account  = new Vtiger_Field();
                $account->name = 'accountid';
                $account->label= 'LBL_MODULE_POPUP_ACCOUNT';
                $account->uitype= 10;
                $account->column = $account->name;
                $account->columntype = 'VARCHAR(255)';
                $account->typeofdata = 'V~O';
                $block->addField($account);
                $account->setRelatedModules( Array(  'Accounts' ));

        /*
        |------------------------------------------------------------------------------------
        | OTRAS CONFIGURACIONES
        |------------------------------------------------------------------------------------
        | 
        | El módulo deben necesita tener por le menos el filtro [ALL]
        | Es necesario asignar tambien como máximo 8 columnas que estarán incluidas el filtro.
        |
        |
        */        
                $allFilter = new Vtiger_Filter();
                $allFilter->name = 'All';
                $allFilter->isdefault = true;
                $moduleInstance->addFilter($allFilter);
                $allFilter->addField($entityField)                
                ->addField($edad, 1)                
                ->addField($telefono, 2)
                ->addField($observaciones, 3)                
                ->addField($CreatedTime, 4)                                        
                ->addField($account, 5)
                ->addField($estatus, 6);                
                

        /*
        |------------------------------------------------------------------------------------
        | OTRAS CONFIGURACIONES
        |------------------------------------------------------------------------------------
        |
        |       Permisos del módulo para otros usuarios
        |       Opciones para los permisos:
        |                                       Public_ReadOnly
        |                                       Public_ReadWrite
        |                                       Public_ReadWriteDelete
        |                                       Private
        */ 
        $moduleInstance->setDefaultSharing();

        /*
        |------------------------------------------------------------------------------------
        | OPCIONES DEL MÓDULO PARA IMPORTAR Y EXPORTAR
        |------------------------------------------------------------------------------------
        |
        | Opciones que aparecen disponibles en el módulo
        |
        */
        $moduleInstance->enableTools(Array('Import', 'Export', 'Merge'));

        /*
        |------------------------------------------------------------------------------------
        | OPCIONES PARA WEBSERVICES
        |------------------------------------------------------------------------------------
        |
        | Initialize Webservice support
        |
        */
        $moduleInstance->initWebservice();
 


        /*
        |------------------------------------------------------------------------------------
        | WIDGET DE ACTUALIZACIONES
        |------------------------------------------------------------------------------------
        | 
        | Para agregar la opción de actualizaciones al módulo:
        | Incluímos el script del módulo "Mod Comments"
        | Obtenemos el Id del módulo que estamos creando
        | Obtenemos una instancia del objeto "ModTracker"      
        | Agreamos el "widget" con el objeto
        |
        */
        
                require_once 'modules/ModTracker/ModTracker.php';
                $TABID = $moduleInstance->id;           
                $detailviewblock = ModTracker::enableTrackingForModule($TABID);     


        /*
        |------------------------------------------------------------------------------------
        | CAMPO PARA COMENTARIOS EN EL MÓDULO
        |------------------------------------------------------------------------------------
        | 
        | Para agregar la opción de comentarios al módulo:
        | Incluímos el script del módulo "Mod Comments"
        | Generamos una instancia del Modulo "ModComments"
        | Obtenemos una instancia del campo "related_to"
        | Generamos la relación del campo con el módulo
        | Agreamos el "widget" al campo personalizado
        |
        */
                require_once 'modules/ModComments/ModComments.php';
                $commentsModule = Vtiger_Module::getInstance( 'ModComments' );
                $fieldInstance  = Vtiger_Field::getInstance( 'related_to', $commentsModule );
                $fieldInstance->setRelatedModules( array($MODULENAME) );        
                $detailviewblock = ModComments::addWidgetTo( $MODULENAME );

        /*
        |------------------------------------------------------------------------------------
        | WIDGET DE DOCUMENTOS
        |------------------------------------------------------------------------------------
        | 
        | Para agregar la opción de documentos al módulo:
        | Incluímos el script del módulo "Documents"
        | Agreamos el "widget" con el objeto
        |
        */
                require_once 'modules/Documents/Documents.php';
                $documentsModule = Vtiger_Module::getInstance('Documents');
                $relationLabel = 'Documents'; 
                $moduleInstance->setRelatedList($documentsModule, $relationLabel, Array('ADD','SELECT'), 'get_attachments');

        /*
        |------------------------------------------------------------------------------------
        | ÚLTIMA CONFGURACIÓN
        |------------------------------------------------------------------------------------
        */            
                //createModuleDir( $MODULENAME, $e_name, $e_lbl );
                configureInitialSeqNumber( $MODULENAME );         
                addToAppMenu($moduleInstance, $APPTABMENU);   
                echo "The module was created successfully.\n";
        

        /*
        |------------------------------------------------------------------------------------
        | CONFIGURAR CONSECUTIVO DEL MÓDULO (MODULE NO)
        |------------------------------------------------------------------------------------
        */     
        function configureInitialSeqNumber( $MODULENAME )
        {
            require_once 'include/utils/utils.php';
            global $adb;

            // Configuramos la secuencia del módulo
            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array( $MODULENAME ));
            if (!($adb->num_rows($result))) 
            {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, 'OPERADOR', 1, 1, 1));
            }

            echo 'Configuring Mod Entity: ...DONE <br>';
        }


        /*
        |------------------------------------------------------------------------------------
        | AGREGAR EL MÓDULO AL NUEVO MENU
        |------------------------------------------------------------------------------------
        */  
        function addToAppMenu($moduleInstance, $APPTABMENU)
        {
            global $adb;
            $APPTABMENU = strtoupper($APPTABMENU);
            //$adb = PearDatabase::getInstance();
            $result = $adb->pquery('SELECT MAX(sequence) AS maxsequence FROM vtiger_app2tab WHERE appname=?', array($APPTABMENU));
            $sequence = 0;
            if ($adb->num_rows($result) > 0) {
                $sequence = $adb->query_result($result, 0, 'maxsequence');
                $sequence = $sequence + 1;                
                $adb->pquery('INSERT INTO vtiger_app2tab(tabid,appname,sequence) VALUES(?,?,?)', array($moduleInstance->getId(), $APPTABMENU, $sequence));
                echo 'Configuring Module to AppMenu: ...DONE <br>';
            
            } else {
                echo 'ERROR: Configuring Module to AppMenu: ...' . $APPTABMENU . ' Option DOES NO EXIST. <br>';
            }   
        }


        /*
        |------------------------------------------------------------------------------------
        | AGREGAR TABLA NUEVA DE LA VERSIÓN 7.0.1
        |------------------------------------------------------------------------------------
        */  
        function createNewTable($moduleInstance)
        {
           $moduleUserSpecificTable = Vtiger_Functions::getUserSpecificTableName($moduleInstance->name);
           Vtiger_Utils::CreateTable($moduleUserSpecificTable, 
                        '(`recordid` INT(25) NOT NULL, 
                           `userid` INT(25) NOT NULL,
                           `starred` VARCHAR(100),
                           Index `record_user_idx` (`recordid`, `userid`)
                            )', true);
            echo 'Configuring '.$moduleUserSpecificTable.': ...DONE <br>';
        }
               
?>