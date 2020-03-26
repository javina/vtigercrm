<?php
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


?>