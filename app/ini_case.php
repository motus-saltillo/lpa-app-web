<?php
session_start();
ini_set("display_errors",1);
include "ini_class.php";
include "rutas.php";
switch ($_POST['tipo']) {
	case '1':
		$calendario = new calendario();
		$datos_json 	= json_encode($calendario->get_lista_de_auditorias($_POST['id_planta'], $_POST['id_nivel']));
		echo $datos_json;
		break;
	case '2':
		$calendario = new calendario();
		$datos_json 	= json_encode($calendario->validar_estado_auditoria($_POST['id_auditoria'], $_POST['id_planta']));
		echo $datos_json;
		break;
	case '3':
		$login = new login();
		$datos_json 	= json_encode($login->validar_login($_POST['id_user'], $_POST['password'], $_POST['id_planta']));
		echo $datos_json;
		break;
	case '4':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->validar_estado_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel']));
		echo $datos_json;
		break;
	case '5':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->iniciar_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel'], $_POST['id_linea'],$_POST['idIssue']));
		echo $datos_json;
		break;
	case '6':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->set_pp_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel'], $_POST['id_linea']));
		echo $datos_json;
		break;
	case '7':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->insert_name_acargo($_POST['name_acargo'],$_POST['id_auditoria'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '8':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->get_info_auditorias($_POST['id_auditoria'],$_POST['id_planta'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '9':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->set_ok($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '10':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->set_nok($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '11':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->set_na($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '12':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->finalizar_auditoria($_POST['id_auditoria'],$_POST['id_planta'],$_POST['linea'],$_POST['auditor'],$_POST['semana'],$_POST['correo'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '13':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->get_info_no_conformidad($_POST['id_pregunta'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '14':
		$auditorias = new auditorias();
		$file 			= $_FILES['capture_1']['tmp_name'];
		$ext 			= $_FILES['capture_1']['type'];
		$datos_json 	= json_encode($auditorias->guardar_imagen($file,$ext,$_POST['id_pregunta_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '15':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->eliminar_imagen($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '16':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->save_new_finding($_POST['hallazgo'], $_POST['accion'], $_POST['id_departamento'], $_POST['id_planta'], $_POST['id_reporte'],$_POST['id_auditoria'],$_POST['nc'],$_POST['id_pregunta']));
		echo $datos_json;
		break;
	case '17':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->get_lista_departamentos($_POST['id_planta']));
		echo $datos_json;
		break;
	case '18':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->update_data_hallazgo($_POST['id_hallazgo'],$_POST['hallazgo'],$_POST['accion'],$_POST['id_dep'],$_POST['nc'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '19':
		$auditorias = new auditorias();
		$file 			= $_FILES['capture_2']['tmp_name'];
		$ext 			= $_FILES['capture_2']['type'];
		$datos_json 	= json_encode($auditorias->update_imagen_hallazgo($file,$ext,$_POST['id_planta'],$_POST['id_hallazgo'],$_POST['id_pregunta_reporte']));
		echo $datos_json;
		break;
	case '20':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->eliminar_imagen_editar($_POST['id_hallazgo'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '21':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->get_lista_de_hallazgos($_POST['id_auditoria'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '22':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->update_data_hallazgo($_POST['id_hallazgo'], $_POST['hallazgo'], $_POST['accion'], $_POST['id_departamento'], $_POST['nc'], $_POST['id_planta']));
		echo $datos_json;
		break;
	case '23':
		$auditorias = new auditorias();
		$file 			= $_FILES['capture_3']['tmp_name'];
		$ext 			= $_FILES['capture_3']['type'];
		$datos_json 	= json_encode($auditorias->update_imagen_hallazgo($file, $ext, $_POST['id_planta'], $_POST['id_hallazgo'], $_POST['id_pregunta_reporte']));
		echo $datos_json;
		break;
	case '24':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->cambiar_linea($_POST['id_auditoria'],$_POST['id_nivel'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '25':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->send_email_hallazgos($_POST['id_auditoria'],$_POST['id_planta'], $_POST['linea'],$_POST['area']));
		echo $datos_json;
		break;
	case '26':
		$test = new test();
		$datos_json 	= json_encode($test->get_lista_lineas_disponibles_test($_POST['id_planta']));
		echo $datos_json;
		break;
	case '27':
		$test = new test();
		if(isset($_POST['id_issue'])){
			$id_issue=$_POST['id_issue'];
		}else{
			$id_issue=0;
		}
		$datos_json 	= json_encode($test->insert_new_test_audit($_POST['id_linea'],$_POST['id_nivel'],$_POST['id_planta'],$id_issue));
		echo $datos_json;
		break;
	case '28':
		$test = new test();
		$datos_json 	= json_encode($test->get_lista_calendario_test($_POST['id_planta'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '29':
		$test = new test();
		$datos_json 	= json_encode($test->validar_estado_auditoria_test($_POST['id_auditoria'], $_POST['id_planta']));
		echo $datos_json;
		break;
	case '30':
		$test = new test();
		$datos_json 	= json_encode($test->validar_estado_auditoria($_POST['id_auditoria'],$_POST['id_planta'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '31':
		$test = new test();
		$datos_json 	= json_encode($test->get_lista_de_hallazgos($_POST['id_auditoria'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '32':
		$test = new test();
		$datos_json 	= json_encode($test->iniciar_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel'], $_POST['id_linea']));
		echo $datos_json;
		break;
	case '33':
		$test = new test();
		$datos_json 	= json_encode($test->get_info_auditorias($_POST['id_auditoria'],$_POST['id_planta'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '34':
		$test = new test();
		$datos_json 	= json_encode($test->set_ok($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '35':
		$test = new test();
		$datos_json 	= json_encode($test->set_nok($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '36':
		$test = new test();
		$datos_json 	= json_encode($test->set_na($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '37':
		$test = new test();
		$datos_json 	= json_encode($test->save_new_finding($_POST['hallazgo'], $_POST['accion'], $_POST['id_departamento'], $_POST['id_planta'], $_POST['id_reporte'],$_POST['id_auditoria'],$_POST['nc'],$_POST['id_pregunta']));
		echo $datos_json;
		break;
	case '38':
		$test = new test();
		$file 			= $_FILES['capture_2']['tmp_name'];
		$ext 			= $_FILES['capture_2']['type'];
		$datos_json 	= json_encode($test->update_imagen_hallazgo($file,$ext,$_POST['id_planta'],$_POST['id_hallazgo'],$_POST['id_pregunta_reporte']));
		echo $datos_json;
		break;
	case '39':
		$test = new test();
		$file 			= $_FILES['capture_1']['tmp_name'];
		$ext 			= $_FILES['capture_1']['type'];
		$datos_json 	= json_encode($test->guardar_imagen($file,$ext,$_POST['id_pregunta_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '40':
		$test = new test();
		$datos_json 	= json_encode($test->get_info_no_conformidad($_POST['id_pregunta'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '41':
		$test = new test();
		$datos_json 	= json_encode($test->eliminar_imagen($_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '42':
		$test = new test();
		$datos_json 	= json_encode($test->update_data_hallazgo($_POST['id_hallazgo'],$_POST['hallazgo'],$_POST['accion'],$_POST['id_dep'],$_POST['nc'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '43':
		$test = new test();
		$datos_json 	= json_encode($test->eliminar_imagen_editar($_POST['id_hallazgo'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '44':
		$test = new test();
		$file 			= $_FILES['capture_3']['tmp_name'];
		$ext 			= $_FILES['capture_3']['type'];
		$datos_json 	= json_encode($test->update_imagen_hallazgo($file, $ext, $_POST['id_planta'], $_POST['id_hallazgo'], $_POST['id_pregunta_reporte']));
		echo $datos_json;
		break;
	case '45':
		$test = new test();
		$datos_json 	= json_encode($test->finalizar_auditoria($_POST['id_auditoria'],$_POST['id_planta'],$_POST['linea'],$_POST['auditor'],$_POST['semana'],$_POST['correo'],$_POST['id_nivel']));
		echo $datos_json;
		break;
	case '46':
		$test = new test();
		$datos_json 	= json_encode($test->send_email_hallazgos($_POST['id_auditoria'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '47':
		$test = new test();
		$datos_json 	= json_encode($test->set_pp_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel'], $_POST['id_linea']));
		echo $datos_json;
		break;
	case '48':
		$test = new test();
		$datos_json 	= json_encode($test->cambiar_linea($_POST['id_auditoria'],$_POST['id_nivel'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '49':
		$test = new test();
		$datos_json 	= json_encode($test->get_lista_issues_disponibles_test($_POST['id_planta']));
		echo $datos_json;
		break;
	case '50':
		$calendario = new calendario();
		$datos_json 	= json_encode($calendario->validar_tablets_lpa($_POST['id_planta']));
		echo $datos_json;
		break;
	case '51':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->eliminar_hallazgo($_POST['id_hallazgo'],$_POST['id_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '52':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->set_pp_auditoria($_POST['id_auditoria'], $_POST['id_planta'], $_POST['id_nivel'], $_POST['id_linea']));
		echo $datos_json;
		break;
	case '53':
		$update_historial_data = new update_historial_data();
		$datos_json 	= json_encode($update_historial_data->update_data($_POST['id_planta'],$_POST['date']));
		echo $datos_json;
		break;
	case '54':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->guardar_imagen_64($_POST['file'],$_POST['id_pregunta_reporte'],$_POST['id_planta']));
		echo $datos_json;
		break;
	case '55':
		$auditorias = new auditorias();
		$datos_json 	= json_encode($auditorias->editar_guardar_imagen_64($_POST['file'],$_POST['id_pregunta_reporte'],$_POST['id_planta'],$_POST['id_hallazgo']));
		echo $datos_json;
		break;
	default:
		# code...
		break;
}
?>