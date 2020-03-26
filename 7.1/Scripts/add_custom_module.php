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
| Se sugiere que sea una sola palabra, primera letra Mayúscula.
| Ej. (Accounts, Assets, Documents, Invoices)
| Si se utilizan dos palabras, se sugiere que estén seguidas iniciando con mayúsculas
| Ej. (HelpDesk, PriceBooks, SalesOrder)
|
*/
        $MODULENAME = 'ModuleName';
/*
|------------------------------------------------------------------------------------
| APP TAB MENU
|------------------------------------------------------------------------------------
| Asignamos el nombre e indicamos a que opción del menú pertenece.
| Los valores posibles estan listados a continuación:
| ['Analytics', 'Inventory', 'Marketing', 'Sales', 'Support', 'Tools']
|
*/        
        $APPTABMENU = 'Inventory';       
        
/*
|------------------------------------------------------------------------------------
| VALIDACIÓN
|------------------------------------------------------------------------------------
| Validamos que el modulo no exista para poder crearlo.
|
*/
        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);        
        if ($moduleInstance || file_exists('modules/'.$MODULENAME)) 
        {
            echo "The module that you attempt to create is already present.\n";
            echo "We suggest to choose a different name.";
            echo '<pre>';var_dump(getPaths());echo '</pre>';  
            exit(0);                
        }

/*
|------------------------------------------------------------------------------------
| CREACION DEL MÓDULO
|------------------------------------------------------------------------------------
| Creamos la instancia del módulo y se guarda en la base de datos
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
| createNewTable() crea la tabla extra necesaria para esta versión, de lo contrario.
| no nos dejará guardar registros...¿?
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
| [] name       : El nombre del campo como será localizado en la tabla [vtiger_fields]
| [] label      : La etiqueta del campo que se utiliza en el archivo de idiomas. Recomendación, utilizar prefijo: LBL_NOMBRE_CAMPO
| [] uitype     : El tipo de campo que será creado. Referencia: https://wiki.vtiger.com/index.php/UI_Types
| [] column     : Nombre de la columna en la tabla [vtiger_nuevo_modulo]
| [] columntype : Tipo de columna en la tabla de la base de datos, va en función al uiType.
| [] typeofdata : Se basa en columntype: 
|                 V~M = Varchar Mandatory (campo texto obligatorio) 
|                 V~O = Varchar Optional (campo texto opcional)
|                 Referencia: https://wiki.vtiger.com/index.php/TypeOfData
|
*/

/*
|------------------------------------------------------------------------------------
| NOTAS ESPECÍFICAS  Ver 7.0.1
|------------------------------------------------------------------------------------
| 
| El módulo deben tener 8 campos obligatorios
|
| [1] Identificador del módulo (campo obligatorio)
| [2] Campo de numeración del registro (consecutivo)
| [3] Campo de Asignao A:
| [4] Campo de Creado en:
| [5] Campo de Modificado en:
| [6] Campo de Descripción: // Opcional pero recomendable
| [7] Campo de Source:
| [8] Campo de Tags:
|
*/
        /*
        |-----------------------------------------------------------------------------
        | [1] ENTITY FIELD
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
        | [2] CAMPO CONSECUTIVO DEL MODULO
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
        | Ejemplo de un campo de Tipo Texto de 25 caracteres de longitud
        |
        */
                $text_field = new Vtiger_Field();
                $text_field->name   = 'text';
                $text_field->label  = 'LBL_TEXT_FIELD';
                $text_field->uitype = 1;                
                $text_field->column = $text_field->name;
                $text_field->columntype = 'VARCHAR(100)';
                $text_field->typeofdata = 'V~O~LE~25';
                // $text_field->typeofdata = 'V~M~LE~25'; // Mandatory
                $block->addField( $text_field );    
 
        /*
        |-----------------------------------------------------------------------------
        | TEXTAREA FIELD
        |-----------------------------------------------------------------------------        
        | Ejemplo de un campo de Tipo Area de Texto
        |
        */
                $text_area  = new Vtiger_Field();
                $text_area->name = 'text_area';
                $text_area->label= 'LBL_TEXT_AREA';
                $text_area->uitype= 19;
                $text_area->column = $text_area->name;
                $text_area->columntype = 'TEXT';
                $text_area->typeofdata = 'V~O~LE~250';
                // $text_area->typeofdata = 'V~M~LE~250'; // Mandatory
                $block->addField( $text_area );

        /*
        |-----------------------------------------------------------------------------
        | LIST FIELD
        |-----------------------------------------------------------------------------    
        | Ejemplo de un campo de tipo lista    
        */

                $listname  = new Vtiger_Field();
                $listname->name = 'newmodule_listname'; // Recommend for avoid colision with other list fields module_listname
                $listname->label= 'LBL_ESTATUS';
                $listname->uitype= 15;                 // 16 No se Basa en Roles
                $listname->column = $listname->name;                
                $listname->columntype = 'VARCHAR(255)';
                $listname->typeofdata = 'V~O';        
                $listname->setPicklistValues( Array ('Value1', 'Value2', 'Value3', 'Value4', '...') );
                $block->addField( $listname );

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

                // This should be custom tablename of the field
                $tag_field = new Vtiger_Field();
                $tag_field->name = "tags";
                $tag_field->label = "tags";
                $tag_field->table = 'vtiger_modulenamecf';
                $tag_field->presence = 2;
                $tag_field->displaytype = 6;
                $tag_field->readonly = 1;
                $tag_field->uitype = 1;
                $tag_field->typeofdata = 'V~O';
                $tag_field->columntype = 'varchar(1)';
                $tag_field->quickcreate = 3;
                $tag_field->masseditable = 0;
                $block->addField($tag_field);            

        /*
        |------------------------------------------------------------------------------------
        | CAMPO PARA RELACIONAR A OTRO MÓDULO
        |------------------------------------------------------------------------------------
        | 
        | Relación tipo 1 - 1 entre los módulos involucrados
        | Ej. Este registro podrá asociarse a 1 cuenta.        
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
                    $account->setRelatedModules( Array(  'Accounts', 'Contacts', 'Assets','Leads','Invoices','..' ));
                    Se puede asignar más de un módulo para elegir la relación (similar a una condición de tipo 'o', 'modulo 1 o modulo 2 o modulo n etc..')
                    También se pueden crear varios campos para elegir varios modulos (simular una condición 'y', 'modulo1 y modulo2 y modulo3 ect ..') 
                    Solo hay que jugar con el valor de 'typeofdata', si lo dejamos obligatorio o no.
                */

        /*
        |------------------------------------------------------------------------------------
        | OTRAS CONFIGURACIONES
        |------------------------------------------------------------------------------------
        | 
        | El módulo necesita tener al menos el filtro [ALL]
        | Es necesario asignar tambien como máximo 12 columnas que estarán incluidas el filtro.        
        |
        */        
                $allFilter = new Vtiger_Filter();
                $allFilter->name = 'All';
                $allFilter->isdefault = true;
                $moduleInstance->addFilter($allFilter);
                $allFilter->addField($entityField)                
                ->addField($consecutivo, 1)                
                ->addField($CreatedTime, 2)
                ->addField($text_area, 3)                
                ->addField($text_field, 4)
                ->addField($CreatedTime, 5)                                        
                ->addField($account, 6)
                ->addField($listname, 7)                
                ->addField($assigned_user_id, 8);                
                
                

        /*
        |------------------------------------------------------------------------------------
        | CONFIGURACIÓN DE PERMISOS
        |------------------------------------------------------------------------------------
        |
        |       Permisos del módulo para otros usuarios
        |       Opciones para los permisos:
        |                                       Public_ReadOnly
        |                                       Public_ReadWrite
        |                                       Public_ReadWriteDelete // Default
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
        | OPCIONES PARA WEBSERVICE
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
            $MODULE_PREFIX = 'ModuleName Prefix'; // Puede ser el nombre del modulo, o las 3 primeras letras ...Eje. Accounts - ACC
            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array( $MODULENAME ));
            if (!($adb->num_rows($result))) 
            {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, $MODULE_PREFIX, 1, 1, 1));
            }

            echo 'Configuring Mod Entity: ...DONE <br>';
        }


        /*
        |------------------------------------------------------------------------------------
        | AGREGAR EL MÓDULO AL NUEVO MENU Ver 7.0.1
        |------------------------------------------------------------------------------------
        */  
        function addToAppMenu($moduleInstance, $APPTABMENU)
        {
            global $adb;

            $APPTABMENU = strtoupper($APPTABMENU);            
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