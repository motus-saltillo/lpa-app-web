<?php
	include "event_function.php";
	include "../rutas.php";
	switch ($_POST['tipo']) {
		case '1':
			$events = new events();
			$datos_json 	= json_encode($events->get_calendario_msa($_POST['id_planta']));
			echo $datos_json;
			break;
		
		default:
			# code...
			break;
	}
?>