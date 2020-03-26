<?php

/*
|----------------------------------------------------------------------------------
| Propiedades de la Clase Field
|----------------------------------------------------------------------------------
|
| name 			| Nombre del campo
| label 		| Etiqueta mostrada en la interfaz de usuario
| uitype 		| Tipo de campo (Texto, numerico, Checkbox)
| column 		| Nombre de la columna en la tabla de la base de datos
| typeofdata 	| Se usa en conjunto con uitype.
|
*/

/*
|----------------------------------------------------------------------------------
| UITYPE 	|	TYPEOFDATA 		| 	DESCRIPTION
|----------------------------------------------------------------------------------
|
|	1 		|	V~M 			|	Texto Obligatorio
| 	1		|	V~M~LE~255		|	Texto Obligatorio de Máximo 255 caracteres
|	1		|	V~O 			| 	Texto Opcional
|	11		|	V~M 			| 	Telefono Obligatorio
|	11		|	V~O 			| 	Telefono Opcional
|	13		|	E~M 			| 	Correo Obligatorio
|	13		|	E~O 			| 	Correo Opcional
|	17		|	V~M 			| 	Url Obligatorio
|	17		|	V~O 			| 	Url Opcional
|	21		|	V~M 			| 	Area de Texto Obligatoria
|	21		|	V~O 			| 	Area de Texto Opcional
|	85		|	V~M 			| 	Skype Obligatorio
|	85		|	V~O 			| 	Skype Opcional
-----------------------------------------------------------------------------------
|	7		| 	NN~M 			|	Número con Decimales Obligatorio
|	7		| 	NN~O 			|	Número con Decimales Opcional
|	7 		| 	I~M 			| 	Número Entero Obligatorio
|	7 		| 	I~O 			| 	Número Entero Opcional
| 	9 		| 	N~M~2~2 		| 	Campo de Porcentaje Obligatorio
| 	9 		| 	N~O~2~2 		| 	Campo de Porcentaje Opcional
| 	71 		| 	N~M 			| 	Moneda Obligatorio
| 	71 		| 	N~O 			| 	Moneda Opcional
-----------------------------------------------------------------------------------
|	5 		| 	D~M 			| 	Fecha Obligatoria
|	5 		| 	D~O 			| 	Fecha Opcional
|	14 		| 	T~M 			| 	Tiempo Obligatorio
|	14 		| 	T~O 			| 	Tiempo Opcional
-----------------------------------------------------------------------------------
|	56 		| 	C~M 			| 	CheckBox Obligatorio
|	56 		| 	C~O 			| 	CheckBox Opcional
-----------------------------------------------------------------------------------
|
|
*/


/*
|----------------------------------------------------------------------------------
| Campo Identificador del módulo
|----------------------------------------------------------------------------------
|
| Preferentemente llamarlo como el módulo.
|
*/

	$entityField  = new Vtiger_Field();
	$entityField->name = strtolower( $MODULENAME );
	$entityField->label= ucfirst( $e_name );
	$entityField->uitype= 2;
	$entityField->column = $entityField->name;
	$entityField->columntype = 'VARCHAR(255)';
	$entityField->typeofdata = 'V~M';
	$block->addField( $entityField );
	$moduleInstance->setEntityIdentifier( $entityField );	


?>