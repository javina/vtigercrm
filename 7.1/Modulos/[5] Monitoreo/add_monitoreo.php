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
| Module.php  ---  Para instanciar el módulo nuevo
| Package.php ---  Para exportar el módulo como un archivo modulo.zip
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
        $MODULENAME = 'Monitoreo';
        $APPTABMENU = 'Support';       
        
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
| Asignamos el nombre e indicamos a que opción del menú pertenece.
| Los valores posibles estan listados a continuación:
| array( 'Analytics', 'Inventory', 'Marketing', 'Sales', 'Support', 'Tools');
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

        $bloque_viaje = new Vtiger_Block();
        $bloque_viaje->label = 'LBL_VIAJE_BLOQ';
        $moduleInstance->addBlock($bloque_viaje);

        $bloque_escalas = new Vtiger_Block();
        $bloque_escalas->label = 'LBL_ESCALAS_BLOQ';
        $moduleInstance->addBlock($bloque_escalas);

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
                $solicitado_por = new Vtiger_Field();
                $solicitado_por->name   = 'solicitado_por';
                $solicitado_por->label  = 'LBL_SOLICITADO_POR';
                $solicitado_por->uitype = 1;                
                $solicitado_por->column = $solicitado_por->name;
                $solicitado_por->columntype = 'VARCHAR(255)';
                $solicitado_por->typeofdata = 'V~O~LE~255';
                $block->addField( $solicitado_por );        


                /*                
                | Campo de Origen o punto de Partida del Monitoreo                            
                */
                $origen  = new Vtiger_Field();
                $origen->name = 'origen';
                $origen->label= 'LBL_ORIGEN';
                $origen->uitype= 21;                
                $origen->column = $origen->name;
                $origen->columntype = 'TEXT';
                $origen->typeofdata = 'V~O';
                $bloque_viaje->addField( $origen );


                /*                
                | Campo de Destino o punto de llegada del Monitoreo                            
                */
                $destino  = new Vtiger_Field();
                $destino->name = 'destino';
                $destino->label= 'LBL_DESTINO';
                $destino->uitype= 21;                
                $destino->column = $destino->name;
                $destino->columntype = 'TEXT';
                $destino->typeofdata = 'V~O';
                $bloque_viaje->addField( $destino );


                /*                
                | Campo de Escalas del monitoreo                          
                */
                $escalas  = new Vtiger_Field();
                $escalas->name = 'escalas';
                $escalas->label= 'LBL_ESCALAS';
                $escalas->uitype= 19;
                $escalas->column = $escalas->name;
                $escalas->columntype = 'TEXT';
                $escalas->typeofdata = 'V~O~LE~250';
                $bloque_escalas->addField( $escalas );

                /*                
                | Campo de Escalas del monitoreo                          
                */
                $observaciones  = new Vtiger_Field();
                $observaciones->name = 'observaciones';
                $observaciones->label= 'LBL_OBSERVACIONES';
                $observaciones->uitype= 19;
                $observaciones->column = $observaciones->name;
                $observaciones->columntype = 'TEXT';
                $observaciones->typeofdata = 'V~O~LE~250';
                $bloque_escalas->addField( $observaciones );


                /*                
                | Campo de Emails a Notificar
                */
                $email_template  = new Vtiger_Field();
                $email_template->name = 'emails';
                $email_template->label= 'LBL_EMAIL_TEMPLATE';
                $email_template->uitype= 19;
                $email_template->column = $email_template->name;
                $email_template->columntype = 'TEXT';
                $email_template->typeofdata = 'V~O~LE~250';
                $bloque_escalas->addField( $email_template );




        /*
        |-----------------------------------------------------------------------------
        | DATE FIELD
        |-----------------------------------------------------------------------------        
        */
                $fecha_inicio  = new Vtiger_Field();
                $fecha_inicio->name = 'fechasalida';
                $fecha_inicio->label= 'LBL_FEHCA_SALIDA';
                $fecha_inicio->uitype= 5;                
                $fecha_inicio->column = $fecha_inicio->name;
                $fecha_inicio->columntype = 'date';
                $fecha_inicio->typeofdata = 'D~O';
                $bloque_viaje->addField( $fecha_inicio );

                $fecha_llegada  = new Vtiger_Field();
                $fecha_llegada->name = 'fechallegada';
                $fecha_llegada->label= 'LBL_FEHCA_LLEGADA';
                $fecha_llegada->uitype= 5;
                $fecha_llegada->column = $fecha_llegada->name;
                $fecha_llegada->columntype = 'date';
                $fecha_llegada->typeofdata = 'D~O';
                $bloque_viaje->addField( $fecha_llegada );


        /*
        |-----------------------------------------------------------------------------
        | TIME FIELD
        |-----------------------------------------------------------------------------        
        */
                $start_time  = new Vtiger_Field();
                $start_time->name = 'hora_salida';
                $start_time->label= 'LBL_START_TIME';
                $start_time->uitype= 14;                
                $start_time->column = $start_time->name;
                $start_time->columntype = 'time';
                $start_time->typeofdata = 'T~O';
                $bloque_viaje->addField( $start_time );


                $end_time  = new Vtiger_Field();
                $end_time->name = 'hora_llegada';
                $end_time->label= 'LBL_END_TIME';
                $end_time->uitype= 14;
                $end_time->column = $end_time->name;
                $end_time->columntype = 'time';
                $end_time->typeofdata = 'T~O';
                $bloque_viaje->addField( $end_time );



        /*
        |-----------------------------------------------------------------------------
        | CHECK FIELD
        |-----------------------------------------------------------------------------        
        */


                $notificar  = new Vtiger_Field();
                $notificar->name = 'notificar';
                $notificar->label= 'LBL_NOTIFICAR_EMAIL';
                $notificar->uitype= 56;
                $notificar->column = $notificar->name;
                $notificar->columntype = 'VARCHAR(3)';
                $notificar->typeofdata = 'C~O';
                $bloque_viaje->addField( $notificar );


                $sin_inicio  = new Vtiger_Field();
                $sin_inicio->name = 'sin_inicio';
                $sin_inicio->label= 'LBL_LATE_START';
                $sin_inicio->uitype= 56;
                $sin_inicio->column = $sin_inicio->name;
                $sin_inicio->columntype = 'VARCHAR(3)';
                $sin_inicio->typeofdata = 'C~O';
                $bloque_viaje->addField( $sin_inicio );


                $tiempo_viaje = new Vtiger_Field();
                $tiempo_viaje->name   = 'tiempo_viaje';
                $tiempo_viaje->label  = 'LBL_TRAVEL_TIME';
                $tiempo_viaje->uitype = 1;                
                $tiempo_viaje->column = $tiempo_viaje->name;
                $tiempo_viaje->columntype = 'VARCHAR(255)';
                $tiempo_viaje->typeofdata = 'V~O~LE~255';
                $bloque_viaje->addField( $tiempo_viaje ); 
                


        /*
        |-----------------------------------------------------------------------------
        | EMAIL FIELD
        |-----------------------------------------------------------------------------        
        */
                $email  = new Vtiger_Field();
                $email->name = 'email';
                $email->label= 'LBL_EMAIL';
                $email->uitype= 13;
                $email->column = $email->name;
                $email->columntype = 'VARCHAR(50)';
                $email->typeofdata = 'E~O';
                $block->addField( $email );


        /*
        |-----------------------------------------------------------------------------
        | LIST FIELD
        |-----------------------------------------------------------------------------        
        */

                $estatus  = new Vtiger_Field();
                $estatus->name = 'mon_estatus';
                $estatus->label= 'LBL_ESTATUS';
                $estatus->uitype= 15;                 // 16 No se Basa en Roles                
                $estatus->column = $estatus->name;
                $estatus->columntype = 'VARCHAR(255)';
                $estatus->typeofdata = 'V~O';        
                $estatus->setPicklistValues( Array ('Registrado', 'Activo', 'Detenido', 'Finalizado', 'Cerrado') );
                $block->addField( $estatus );


                $siniestro  = new Vtiger_Field();
                $siniestro->name = 'mon_tipocierre';
                $siniestro->label= 'LBL_SINIESTRO';
                $siniestro->uitype= 15;                 // 16 No se Basa en Roles
                $siniestro->column = $siniestro->name;
                $siniestro->columntype = 'VARCHAR(255)';
                $siniestro->typeofdata = 'V~O';        
                $siniestro->setPicklistValues( Array ('NA', 'Robo', 'Siniestro') );                            
                $block->addField( $siniestro );


                $notify_time  = new Vtiger_Field();
                $notify_time->name = 'mon_notify_time';
                $notify_time->label= 'LBL_NOTIFICACTION_TIME';
                $notify_time->uitype= 15;                 // 16 No se Basa en Roles
                $notify_time->column = $notify_time->name;
                $notify_time->columntype = 'VARCHAR(255)';
                $notify_time->typeofdata = 'V~O';        
                $notify_time->setPicklistValues( Array ('15 minutos', '30 minutos', '45 minutos', '1 hora', '2 horas') );                            
                $block->addField( $notify_time );


                


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
            
                //Adding tag field
                $field = new Vtiger_Field();
                $field->name = "tags";
                $field->label = "tags";
                $field->table = 'vtiger_monitoreocf';
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

                $contact  = new Vtiger_Field();
                $contact->name = 'contactid';
                $contact->label= 'LBL_MODULE_POPUP_CONTACT';
                $contact->uitype= 10;                
                $contact->column = $contact->name;
                $contact->columntype = 'VARCHAR(255)';
                $contact->typeofdata = 'V~O';
                $block->addField($contact);
                $contact->setRelatedModules( Array(  'Contacts' ));

                $unidad  = new Vtiger_Field();
                $unidad->name = 'unidadidid';
                $unidad->label= 'LBL_MODULE_POPUP_UNIDADES';
                $unidad->uitype= 10;                
                $unidad->column = $unidad->name;
                $unidad->columntype = 'VARCHAR(255)';
                $unidad->typeofdata = 'V~O';
                $block->addField($unidad);
                $unidad->setRelatedModules( Array(  'Unidad' ));

                $operador  = new Vtiger_Field();
                $operador->name = 'operadorid';
                $operador->label= 'LBL_MODULE_POPUP_OPERADORES';
                $operador->uitype= 10;                
                $operador->column = $operador->name;
                $operador->columntype = 'VARCHAR(255)';
                $operador->typeofdata = 'V~O';
                $block->addField($operador);
                $operador->setRelatedModules( Array(  'Operador' ));

                $ruta  = new Vtiger_Field();
                $ruta->name = 'rutaid';
                $ruta->label= 'LBL_RUTA';
                $ruta->uitype= 10;                
                $ruta->column = $ruta->name;
                $ruta->columntype = 'VARCHAR(255)';
                $ruta->typeofdata = 'V~O';
                $block->addField($ruta);
                $ruta->setRelatedModules( Array(  'Ruta' ));

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
                ->addField($origen, 1)                
                ->addField($destino, 2)
                ->addField($operador, 3)                
                ->addField($unidad, 4)                                        
                ->addField($estatus, 5)
                ->addField($account, 6)
                ->addField($solicitado_por, 7)
                ->addField($fecha_inicio, 8)
                ->addField($fecha_llegada, 9);                

                

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
                createModuleDir( $MODULENAME, $e_name, $e_lbl );
                configureInitialSeqNumber( $MODULENAME );         
                addToAppMenu($moduleInstance, $APPTABMENU);   
                echo "The module was created successfully.\n";
        

        /*
        |------------------------------------------------------------------------------------
        | CREA EL DIRECTORIO DEL MÓDULO
        |------------------------------------------------------------------------------------
        |
        */
        function createModuleDir( $MODULENAME,  $e_name, $e_lbl ) {
        
                $newModuleName  = mb_convert_case( $MODULENAME, MB_CASE_TITLE, "utf8" );
                // Calculando la ruta absoluta a los archivos
                $relativeRootPath        = getPaths();
                $sourceClassFilePath     = $relativeRootPath . DIRECTORY_SEPARATOR . 'vtlib/ModuleDir/6.0.0/ModuleName.php';
                $sourceClassLangFilePath = $relativeRootPath . DIRECTORY_SEPARATOR . 'vtlib/ModuleDir/6.0.0/languages/en_us/ModuleName.php';
                $newModulePath           = $relativeRootPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $newModuleName;
                $newLangFileBase         = $relativeRootPath . DIRECTORY_SEPARATOR . 'languages';
                $newClassFilePath        = $newModulePath    . DIRECTORY_SEPARATOR . $newModuleName . '.php';

                // Creamos la carpeta del nuevo módulo
                mkdir( $newModulePath );
                echo "Creating directory $newModulePath ... DONE <br>";

                // Copiamos el archivo de la clase del módulo nuevo
                if (!copy( $sourceClassFilePath , $newClassFilePath )) 
                {
                        echo "ERROR: Can't copy the files, please do it manual<br>";
                        return -1;
                } else {
        
                        // Abrimos el archivo que acabamos de copiar:
                        $data = file_get_contents( $newClassFilePath );                
                        // Reemplazmos los tag que tiene el archivo por nuestros datos
                        $data = str_replace( "ModuleName"          , $newModuleName             , $data );
                        $data = str_replace( "<modulename>"        , strtolower( $MODULENAME )  , $data );
                        $data = str_replace( "<entityfieldname>"   , $e_name                    , $data );        
                        $data = str_replace( "<entityfieldlabel>"  , $e_lbl                     , $data );
                        $data = str_replace( "<entitycolumn>"      , $e_name                    , $data );        

                        // Escribimos el archivo devuelta:
                        file_put_contents( $newClassFilePath , $data );       
                }

                // Ahora vamos a copiar los archivos de Idioma para asegurar la traducción
                copy( $sourceClassLangFilePath , $newLangFileBase . '/es_mx/' . $newModuleName . '.php');
                copy( $sourceClassLangFilePath , $newLangFileBase . '/en_us/' . $newModuleName . '.php');        
                echo "Copying lang Backend Files ... DONE <br>";
                chmod( $newModulePath, 0777);  

        }

        /*
        |------------------------------------------------------------------------------------
        | OBTIENE RUTAS DE LOS ARCHIVOS NECESARIOS
        |------------------------------------------------------------------------------------
        |
        */
        function getPaths()
        {                

                $script_name    = $_SERVER["SCRIPT_FILENAME"];
                $pieces         = explode( DIRECTORY_SEPARATOR, $script_name);
                array_pop($pieces);
                $relativeRootPath = implode( DIRECTORY_SEPARATOR, $pieces);
                //$script_name      = $_SERVR["SCRIPT_FILENAME"];
                //$documentRoot     = $_SERVER["DOCUMENT_ROOT"];                
                //$uri              = explode( DIRECTORY_SEPARATOR , $_SERVER["REQUEST_URI"] );
                //$relativeRootPath = $documentRoot . DIRECTORY_SEPARATOR . $uri[1] . DIRECTORY_SEPARATOR . 's5w';
                return $relativeRootPath;
        }               
            

        /*
        |------------------------------------------------------------------------------------
        | CREAR RELACION CON OTROS MÓDULOS [N-M]
        |------------------------------------------------------------------------------------
        */ 
        function linkModuleToTargetModule( $TARGETMODULE, $MODULENAME ) 
        {
                require_once 'vtlib/Vtiger/Module.php';        
                $Vtiger_Utils_Log = true;
                    
                    $moduleInstance = Vtiger_Module::getInstance( $MODULENAME );
                    if ($moduleInstance || file_exists( 'modules/'.$MODULENAME )) 
                    {
                        $LINKEDMODULE = Vtiger_Module::getInstance( $TARGETMODULE );
                        $relationLabel  = $TARGETMODULE;
                        $moduleInstance->setRelatedList( $LINKEDMODULE, $relationLabel, array('ADD','SELECT'),'get_dependents_list' );                          
                        
                        echo "<br>The related module [$relationLabel] was created succesfully for [$TARGETMODULE] module.\n";        
                    } else 
                    {
                        echo "The module you attempt to work with, doesn't exist.";
                    }
        }


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
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, 'UNIDAD', 1, 1, 1));
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