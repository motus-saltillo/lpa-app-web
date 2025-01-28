<?php
require $_SERVER['DOCUMENT_ROOT']."../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
class test extends ini
{
	function get_lista_issues_disponibles_test($id_planta){
		$this->conexion();
		$sql = "SELECT * FROM LPA_V_List_Items";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Item']] = ($fila['Item']);	
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function cambiar_linea($id_auditoria,$id_nivel,$id_planta){
		$this->conexion();
		if($this->existe_oportunidad_cambiar_linea($id_auditoria,$id_nivel)){
			$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
			if ($this->asignar_linea($id_auditoria, $id_linea)) {
				$this->insertar_historial_cambio_de_linea($id_auditoria);
				$co['estado'] 	= true;
				$co['id_linea'] = $id_linea;
				$co['linea'] 	= $this->get_name_linea($id_auditoria);
			}else{
				$co['estado'] 	= false;
				$co['id_linea'] = $id_linea;
				$co['linea'] 	= $this->get_name_linea($id_auditoria);
			}
		}else{
			$co['estado'] = false;
		}
		
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function insertar_historial_cambio_de_linea($id_auditoria){
		$sql = "INSERT INTO LPA_Cambios_Linea_Test VALUES ($id_auditoria,1)";
		if($this->conexion->query($sql)){
			$co=true;
		}else{
			$co=false;
		}
		if(isset($co)) return $co;
	}
	function existe_oportunidad_cambiar_linea($id_auditoria, $id_nivel){
		if($id_nivel == 3) return true;
		$sql = "SELECT * FROM LPA_V_Cambios_Linea_Test WHERE ID_Calendario=$id_auditoria";
		$co = true;
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = false;
		}
		if(isset($co)) return $co;
	}
	function asignar_linea($id_auditoria,$id_linea){
		$sql = "UPDATE LPA_Calendario_Test SET ID_Linea=$id_linea WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co=true;
		}else{
			$co=false;
		}
		if(isset($co)) return $co;
	}
	function set_pp_auditoria($id_auditoria,$id_planta,$id_nivel,$id_linea){
		$this->conexion();
		if ($this->validar_reporte_auditoria($id_auditoria)) {
			if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
				$co['estado'] = $this->update_reporte_auditoria_pp($id_auditoria);
			} else {
				$co['estado'] = false;
			}
		} else {
			if($this->obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel)){
				if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
					$co['estado'] = $this->update_reporte_auditoria_pp($id_auditoria);
				} else {
					$co['estado'] = false;
				}
			}
		}
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function update_reporte_auditoria_pp($id_auditoria){
		$sql = "UPDATE LPA_V_Reporte_Auditoria_Test SET Estado=1, Tipo_Estado=3, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria AND PP_Activo=0";
		if($this->conexion->query($sql)){
			$co = true;
		}else{
			$co = true;
		}
		if(isset($co)) return $co;
	}
	function send_email_hallazgos($id_auditoria,$id_planta){
		$this->conexion();
		$body = $this->get_body_hallazgo($id_auditoria,$id_planta);
		$departamentos = $this->get_lista_de_correos($body['departamento']);
		$body_email = $this->get_body_mail($body['cuerpo'],800,"LPA Findings",$this->static_data->PHPMailer->BUTTONS->test->Hallazgos->{"Planta_$id_planta"});
		$co = $this->ini_send_email($body_email, $departamentos,"apps","Audit Findings (Test) ",[],[],$this->static_data->BCC->lpa);
		$this->conexion=null;
		if(isset($co)){return $co;}
	}
	function get_lista_de_correos($lista_dep){
		$lista_dep 	= array_unique($lista_dep);
		foreach ($lista_dep as $key => $value) {
			$sql = "SELECT * FROM LPA_V_LIST_RESPONSABLES_POR_DEPARTAMENTO WHERE ID_Departamento=$value";
			$res = $this->conexion->query($sql);
			while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
				$co[] = $fila['Correo'];
			}
			
		}
		if (isset($co)) return array_unique($co);
	}
	function get_body_hallazgo($id_auditoria,$id_planta){
		$sql = "SELECT * FROM LPA_V_Hallazgos_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		$co['cuerpo'] = "<thead>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>No Acción</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Hallazgo</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Fecha</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Fecha Cierre</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Tipo Hallazgo</b></th>
				<thead>";
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['departamento'][] = $fila['ID_Departamento'];
			$co['cuerpo'].= "<tr>
					<td style='border-bottom:1pt solid #cfd8dc; width:100px; padding-top:5px; '>".$fila['Codigo_Accion']."</td>
					<td style='border-bottom:1pt solid #cfd8dc;' ><b>". ($fila['Hallazgo'])."</b><br>".($fila['Accion']). "</td>";
					if($fila['Fecha_Cierre'] != ""){
						$co['cuerpo'] .= "
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;' >" . date("Y-m-d", strtotime($fila['Fecha_Programada'])) . "</td>
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;'>".date("Y-m-d", strtotime($fila['Fecha_Cierre'])) . "</td>
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;'><b>" . $fila['Tipo_Hallazgo'] . "</b></td>";
					}else{
						 $co['cuerpo'] .= "<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828;' >" . date("Y-m-d", strtotime($fila['Fecha_Programada'])) . "</td>
						 <td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828;'> -- </td>
						 <td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828 ;'><b>" . $fila['Tipo_Hallazgo'] . "</b></td>";
					} 
					$co['cuerpo'] .= "
			</tr>";
			//$this->get_name_imagen($fila['ID_Hallazgo'], $id_planta);
		}
		if (isset($co)) return $co;
	}
	function finalizar_auditoria($id_auditoria,$id_planta,$linea,$auditor,$semana,$correo,$id_nivel){
		$this->conexion();
		if($this->ya_es_finalizada($id_auditoria)){
			$this->activar_hallazgo($id_auditoria);
		}else{
			if ($this->finalizar_auditoria_calendario($id_auditoria)) {
				$this->activar_hallazgo($id_auditoria);
				$this->send_mail_confirmacion_finalizada($id_auditoria, $linea, $auditor, $semana,$correo, $id_nivel);
			}
		}
		$co['estado'] = true;
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function activar_this_hallazgo($id_hallazgo){
		$sql = "SELECT ISNULL(MAX(No_Accion)+1,1) AS No_Accion FROM LPA_V_Hallazgos_Test WHERE Publicado=1 AND YEAR(GETDATE()) = YEAR(Fecha_Hallazgos) ORDER BY ID_Hallazgo DESC";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$sql = "UPDATE LPA_Hallazgos_Test SET No_Accion='".($fila['No_Accion'])."', Publicado=1 WHERE ID_Hallazgo=$id_hallazgo";
			$this->conexion->query($sql);
		}
	}
	function finalizar_auditoria_calendario($id_auditoria){
		$sql = "UPDATE LPA_Calendario_Test SET Fecha_Final_Auditoria='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function ya_es_finalizada($id_auditoria){
		$sql = "SELECT Fecha_Final_Auditoria FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila['Fecha_Final_Auditoria'] != ''){
				$co = true;
			}else{
				$co = false;
			}
		}
		if(isset($co)) return $co;
	}
	function send_mail_confirmacion_finalizada($id_auditoria,$linea,$auditor, $semana,$correo, $id_nivel){
		$linea_issue = "Linea";
		if($id_nivel == 4){$linea_issue == "Issue";}
		$dato = "
			<tr><td style='width:20%;'></td><td style='text-align:right;'>Auditor:</td><td> $auditor</td><td></td></tr>
			<tr><td style='width:20%;'></td><td style='text-align:right;'>$linea_issue:</td><td> $linea</td><td></td></tr>";
		$body_email = $this->get_body_mail($dato,600,"Audit Completed <br> #$id_auditoria","");
		$this->ini_send_email($body_email,[$correo],"apps","Audit test completed",[],[],$this->static_data->BCC->lpa);
	}
	function activar_hallazgo($id_auditoria){
		$sql = "SELECT * FROM LPA_Hallazgos_Test WHERE ID_Calendario=$id_auditoria AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$this->activar_this_hallazgo($fila['ID_Hallazgo']);
		}
	}
	function update_imagen_hallazgo($file,$ext,$id_planta,$id_hallazgo, $id_pregunta_reporte){
		$this->conexion();
		$img 		= new img();
		$name_img 	= $img->upload_img($file, uniqid() . ".jpeg", $ext, $this->static_data->RUTAS->STATICS->test->planta->{$id_planta});
		$co 		= $this->update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte);
		$co['imagen'] = $this->static_data->RUTAS->WEB->test->planta->{$id_planta}.$name_img;
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte){
		$this->eliminar_imagen_anterior($id_hallazgo, $id_planta);
		$co = $this->update_name_this_hallazgo($id_hallazgo,$name_img, $id_pregunta_reporte);
		return $co;
	}
	function update_name_this_hallazgo($id_hallazgo,$name_img, $id_pregunta_reporte){
		$sql = "INSERT INTO LPA_Hallazgos_Img_Test VALUES ('$name_img',$id_hallazgo,$id_pregunta_reporte,1)";
		if ($this->conexion->query($sql)) {
			$co['estado'] = true;
		} else {
			$co['estado'] = false;
		}
		if (isset($co)) return $co;
	}
	function eliminar_imagen_editar($id_hallazgo,$id_planta){
		$this->conexion();
		$this->eliminar_imagen_anterior($id_hallazgo,$id_planta);
		$this->conexion=null;
	}
	function eliminar_imagen_anterior($id_hallazgo, $id_planta){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img_Test WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			unlink($this->static_data->RUTAS->STATICS->test->planta->{$id_planta} . $fila['Hallazgo_Img']);
		}
		$sql = "UPDATE LPA_Hallazgos_Img_Test SET Activo=0 WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
	}
	function update_data_hallazgo($id_hallazgo,$hallazgo,$accion,$id_dep,$nc,$id_planta){
		$this->conexion();
		if($nc == 1){
			$fecha = $this->get_fecha_started($id_hallazgo);
			$new_dates= "  Fecha_Programada = '$fecha' , Fecha_Cierre = '$fecha' ";
		}else{
			$fecha  		= date("Y-m-d 23:59:59", strtotime("+1 week this friday"));
			$new_dates 	= "  Fecha_Programada = '$fecha' , Fecha_Cierre='' ";
		}
		$sql = "UPDATE LPA_Hallazgos_Test SET Hallazgo='".$this->str($hallazgo)."', Accion='".$this->str($accion)."', ID_Departamento=$id_dep, Tipo_Hallazgo=$nc, $new_dates WHERE ID_Hallazgo=$id_hallazgo";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_fecha_started($id_hallazgo){
		$sql = "SELECT Fecha_Hallazgo FROM LPA_Hallazgos_Test WHERE ID_Hallazgo=$id_hallazgo";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Fecha_Hallazgo'];
		}
		if(isset($co)) return $co;
	}
	function get_name_imagen($id_hallazgo,$id_planta){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img_Test WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $this->static_data->RUTAS->WEB->test->planta->{$id_planta}.$fila["Hallazgo_Img"];
		}
		if(isset($co)) return $co;
	}
	function eliminar_imagen($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$name_img = $this->get_nombre_imagen($id_reporte_pregunta);
		if(isset($name_img)) $this->eliminar_imagen_ruta($name_img,$id_planta);
		$co['estado'] = true;
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_de_hallazgos($id_auditoria,$id_planta){
		$sql = "SELECT * FROM LPA_Hallazgos_Test WHERE Activo=1 AND ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['hallazgo'] 		= ($fila['Hallazgo']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['accion'] 		= ($fila['Accion']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['nc'] 			= ($fila['Tipo_Hallazgo']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['imagen'] 		= $this->get_name_imagen($fila['ID_Hallazgo'], $id_planta);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['departamento'] 	= ($fila['ID_Departamento']);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function eliminar_imagen_ruta($name,$id_planta){
		if(file_exists($this->static_data->RUTAS->STATICS->test->planta->{$id_planta} . $name)){
			if (unlink($this->static_data->RUTAS->STATICS->test->planta->{$id_planta} . $name)) {
			} else {
			}
		}else{
		}
		
	}
	function get_nombre_imagen($id_reporte_pregunta){
		$sql = "SELECT TOP 1 * FROM LPA_Hallazgos_Img_Test WHERE ID_Reporte=$id_reporte_pregunta AND Activo=1 AND ID_Hallazgo IS NULL";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co=$fila['Hallazgo_Img'];
		}
		$this->conexion->query("UPDATE LPA_Hallazgos_Img_Test SET Activo=0 WHERE ID_Reporte=$id_reporte_pregunta AND ID_Hallazgo IS NULL");
		if(isset($co)) return $co;
	}
	function get_info_no_conformidad($id_pregunta,$id_planta){
		$this->conexion();
		$co['hallazgos'] 	= $this->get_hallazgo_no_conformidad($id_pregunta,$id_planta);
		$co['imagenes'] 			= $this->get_imgs_hallazgos($id_pregunta);
		if(isset($co['imagenes'])) $co['imagenes'] = $this->static_data->RUTAS->WEB->test->planta->{$id_planta} . $co['imagenes'];
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_imgs_hallazgos($id){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img_Test WHERE ID_Reporte=$id AND ID_Hallazgo IS NULL AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Hallazgo_Img'];
		}
		if(isset($co)) return $co;
	}
	function get_hallazgo_no_conformidad($id_pregunta,$id_planta){
		$sql = "SELECT * FROM LPA_Hallazgos_Test WHERE Activo=1 AND ID_Reporte=$id_pregunta";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Hallazgo']]['hallazgo'] 		= ($fila['Hallazgo']);
			$co[$fila['ID_Hallazgo']]['accion'] 		= ($fila['Accion']);
			$co[$fila['ID_Hallazgo']]['nc'] 			= ($fila['Tipo_Hallazgo']);
			$co[$fila['ID_Hallazgo']]['imagen'] 		= $this->get_name_imagen($fila['ID_Hallazgo'],$id_planta);
			$co[$fila['ID_Hallazgo']]['departamento'] 	= ($fila['ID_Departamento']);
		}
		if(isset($co)) return $co;
	}
	function editar_guardar_imagen_64($file,$id_pregunta_reporte,$id_planta,$id_hallazgo){
		$this->conexion();
		$img 		= new img();
		$name_img  = uniqid().".jpeg";
		$img->subir_imagen_base64($this->static_data->RUTAS->STATICS->test->planta->{$id_planta},$file,$name_img);
		$co 		= $this->update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function guardar_imagen_64($file,$id_pregunta_reporte,$id_planta){
		$this->conexion();
		$img 		= new img();
		$name_img  = uniqid().".jpeg";
		$img->subir_imagen_base64($this->static_data->RUTAS->STATICS->test->planta->{$id_planta},$file,$name_img);
		$co 		= $this->insert_name_img($id_pregunta_reporte, $name_img, $id_planta);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function guardar_imagen($file,$ext,$id_pregunta_reporte,$id_planta){
		$this->conexion();
		$img 		= new img();
		$name_img 	= $img->upload_img($file,uniqid().".jpeg",$ext,$this->static_data->RUTAS->STATICS->test->planta->{$id_planta});
		$co 		= $this->insert_name_img($id_pregunta_reporte, $name_img, $id_planta);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function insert_name_img($id_pregunta, $name, $id_planta){
		$sql = "INSERT INTO LPA_Hallazgos_Img_Test VALUES ('$name',NULL,$id_pregunta,1)";
		if($this->conexion->query($sql)){
			$co['estado']= $this->static_data->RUTAS->WEB->test->planta->{$id_planta}.$name;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function save_new_finding($hallazgo, $accion, $id_departamento, $id_planta, $id_reporte,$id_auditoria,$nc, $id_pregunta){
		$start_date = date("Y-m-d H:i:s");
		$end_date 	= date("Y-m-d 23:59:59", strtotime("+1 week this friday"));
		$close_date = "";
		if($nc ==1){
			$end_date = $start_date;
			$close_date = $start_date;
		}
		$sql = "INSERT INTO LPA_Hallazgos_Test VALUES (NULL,'$start_date','$end_date','$close_date',$id_departamento,0,$id_auditoria,1,0,$nc,$id_pregunta,'".$this->str($hallazgo)."','".$this->str($accion)."',$id_reporte)";
		$this->conexion();
		if($this->conexion->query($sql)){
			$co['estado']=true;
			$this->update_hallazgo_imagen($id_reporte);
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function update_hallazgo_imagen($id_reporte){
		$sql = "UPDATE LPA_Hallazgos_Img_Test SET ID_Hallazgo=".$this->get_id_hallazg($id_reporte)." WHERE ID_Reporte=$id_reporte AND Activo=1 AND ID_Hallazgo IS NULL";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function get_id_hallazg($id_reporte){
		$sql = "SELECT TOP 1 ID_Hallazgo FROM LPA_Hallazgos_Test WHERE ID_Reporte=$id_reporte AND Activo=1 ORDER BY ID_Hallazgo DESC";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['ID_Hallazgo'];
		}
		if(isset($co)) return $co;
	}
	function get_lista_departamentos($id_planta){
		$this->conexion();
		$sql = "SELECT * FROM LPA_V_List_Departamentos";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Departamento']]['name'] = ($fila['Departamento']);	
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function set_ok($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$this->eliminar_hallazgos_previos($id_reporte_pregunta);
		$sql = "UPDATE LPA_Reporte_Auditoria_Test SET Estado=1, Tipo_Estado=1, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
		$this->conexion=null;
	}
	function eliminar_hallazgos_previos($id_reporte_pregunta){
		$this->eliminar_hallazgos($id_reporte_pregunta);
	}
	function eliminar_hallazgos($id_reporte){
		$sql = "UPDATE LPA_Hallazgos_Test SET Activo=0 WHERE ID_Reporte=$id_reporte";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
	}
	function set_nok($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$sql = "UPDATE LPA_Reporte_Auditoria_Test SET Estado=1, Tipo_Estado=2, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function set_na($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$this->eliminar_hallazgos_previos($id_reporte_pregunta);
		$sql = "UPDATE LPA_Reporte_Auditoria_Test SET Estado=1, Tipo_Estado=4, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_info_auditorias($id_auditoria,$id_planta,$id_nivel){
		$this->conexion();
		$co['preguntas_lpa'] = $this->get_lista_de_preguntas($id_auditoria,$id_nivel);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_de_preguntas($id_auditoria, $id_nivel){
		$sql = "SELECT * FROM LPA_V_Reporte_Auditoria_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($id_nivel == 4){
				$co[$fila['ID_Linea']]['name'] = ($fila['Linea']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['titulo'] = ($fila['Titulo']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['no_pregunta'] = ($fila['No_Pregunta']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['pregunta'] = ($fila['Pregunta']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['importancia'] = ($fila['Importancia']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['plan_r'] = ($fila['Plan_Reaccion']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['h_a'] = ($fila['Hallazgo_Activo']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['estado'] = ($fila['Estado']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['id_pregunta'] = ($fila['ID_Pregunta']);
			}else{
				$co[$fila['ID_Item']]['name'] = ($fila['Item']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['titulo'] = ($fila['Titulo']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['no_pregunta'] = ($fila['No_Pregunta']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['pregunta'] = ($fila['Pregunta']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['importancia'] = ($fila['Importancia']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['plan_r'] = ($fila['Plan_Reaccion']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['h_a'] = ($fila['Hallazgo_Activo']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['estado'] = ($fila['Estado']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['id_pregunta'] = ($fila['ID_Pregunta']);
			}
			
		}
		if(isset($co)) return $co;
	}
	function iniciar_auditoria($id_auditoria,$id_planta, $id_nivel, $id_linea){
		$this->conexion();
		if($this->validar_reporte_auditoria($id_auditoria)){
			if($this->update_fecha_inicio_auditoria($id_auditoria)){
				$co['estado'] = true;
			}else{
				$co['estado'] = false;
			}
		}else{
			if($this->obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel)){
				if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
					$co['estado'] = true;
				} else {
					$co['estado'] = false;
				}
			}else{
				$co['estado'] = false;
			}
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lineas_nivel_4(){
		$sql = "SELECT TOP 3 * FROM LPA_V_List_Lineas ORDER BY NEWID();";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[]	= $fila['ID_Linea'];
		}
		if(isset($co)) return $co;
	}
	function obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel){
		if($id_nivel == 4){
			$id_lineas = $this->get_lineas_nivel_4();
			foreach ($id_lineas as $key => $value) {
				$sql = "SELECT * FROM LPA_V_Formatos_Linea WHERE ID_Item=$id_linea AND ID_Nivel=$id_nivel AND ID_Linea=$value";
				$res = $this->conexion->query($sql);
				while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
					if ($this->conexion->query("INSERT INTO LPA_Reporte_Auditoria_Test VALUES($id_auditoria," . $fila['ID_Pregunta'] . ",0,NULL,1,0,$value)")) {
						$co = true;
					} else {
						$co = false;
					}
				}
			}
		}else{
			$sql = "SELECT * FROM LPA_V_Formatos_Linea WHERE ID_Linea=$id_linea AND ID_Nivel=$id_nivel";
			$res = $this->conexion->query($sql);
			while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
				if ($this->conexion->query("INSERT INTO LPA_Reporte_Auditoria_Test VALUES($id_auditoria," . $fila['ID_Pregunta'] . ",0,NULL,1,0,$id_linea)")) {
					$co = true;
				} else {
					$co = false;
				}
			}
		}
		if (isset($co)) return $co;
	}
	function update_fecha_inicio_auditoria($id_auditoria){
		$sql = "UPDATE LPA_Calendario_Test SET Fecha_Inicio_Auditoria='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co = true;
		}else{
			$co = false;
		}
		if(isset($co)) return $co;
	}
	function validar_reporte_auditoria($id_auditoria){
		$sql = "SELECT COUNT(*) AS Count FROM LPA_V_Reporte_Auditoria_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila["Count"]>0){
				$co = true;
			}else{
				$co = false;
			}
		}
		if(isset($co)) return $co;
	}
	function validar_estado_auditoria($id_auditoria,$id_planta,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['estado'] 		= $fila['Estado_Auditoria'];
			$co['auditado'] 	= ($fila['Nombre_Auditado']);
			$co['cambios'] 		= $this->get_cambios_linea($id_auditoria);
			$co['lineas'] 		= $this->validar_lineas($id_auditoria,$id_nivel);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function validar_estado_auditoria_test($id_auditoria,$id_planta){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['estado'] 		= $fila['Estado_Auditoria'];
			$co['auditado'] 	= ($fila['Nombre_Auditado']);
			$co['cambios'] 		= $this->get_cambios_linea($id_auditoria);
			$co['lineas'] 		= $this->validar_lineas($id_auditoria,$id_nivel);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function validar_lineas($id_auditoria,$id_nivel){
		switch ($id_nivel) {
			case '1':
				$co['name'] = $this->get_name_linea($id_auditoria);
				$co["id_linea"] = $this->get_id_linea($id_auditoria);
				break;
			case '2':
				if($this->exite_linea_asignada($id_auditoria,$id_nivel)){
					$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
					if($this->asignar_linea($id_auditoria, $id_linea)){
						$co['name'] = $this->get_name_linea($id_auditoria);
						$co['id_linea'] = $id_linea;
					}
				}else{
					$co["name"] = $this->get_name_linea($id_auditoria);
					$co["id_linea"] = $this->get_id_linea($id_auditoria);
				}
				
				break;
			case '3':
				if ($this->exite_linea_asignada($id_auditoria, $id_nivel)) {
					$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
					if ($this->asignar_linea($id_auditoria, $id_linea)) {
						$co['name'] = $this->get_name_linea($id_auditoria);
						$co['id_linea'] = $id_linea;
					}
				} else {
					$co["name"] = $this->get_name_linea($id_auditoria);
					$co["id_linea"] = $this->get_id_linea($id_auditoria);
				}
				break;
			case '4':
				$co = $this->get_datos_nivel_4($id_auditoria);
				break;
		}
		if(isset($co)) return $co;
	}
	function get_datos_nivel_4($id_auditoria){
		$sql = "SELECT Item, ID_Item FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['name'] 	= ($fila['Item']);
			$co['id_linea']	= $fila['ID_Item'];
		}
		if(isset($co)) return $co;
	}
	function get_linea_aleatorio($id_auditoria,$id_nivel)
	{
		$co = [];
		$co['lineas_disponibles'] 		= $this->get_lineas_base();
		$co['list_lineas_asignadas'] 	= $this->get_list_lineas_asignadas($id_auditoria,$id_nivel);

		if (count($co['lineas_disponibles']) > count($co['list_lineas_asignadas'])) {
			$co['unique_list_lineas'] 	= null;
			foreach ($co['lineas_disponibles'] as $key => $value) {
				if (in_array($value, $co['list_lineas_asignadas'])) { } else {
					$co['unique_list_lineas'][] = $value;
				}
			}
			if (count($co['unique_list_lineas']) > 0) {
				$co  	= $co['unique_list_lineas'][array_rand($co['unique_list_lineas'], 1)];
			} else {
				$co 	= 0;
			}
		} else {
			$co  		= $co['lineas_disponibles'][array_rand($co['lineas_disponibles'], 1)];
		}
		return $co;
	}
	function get_lineas_base()
	{
		$sql = "SELECT ID_Linea FROM LPA_V_LIST_LINEAS";
		$co['list_lineas_disponibles'] = null;
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['list_lineas_disponibles'][] = $fila['ID_Linea'];
		}
		return $co['list_lineas_disponibles'];
	}
	function get_list_lineas_asignadas($id_calendario,$id_nivel){
			$sql ="SELECT Fecha_Inicio FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_calendario";
			$co=[];
			$res = $this->conexion->query($sql);
			while($fila=$res->fetch(PDO::FETCH_ASSOC)){
				$date = date("Y-m-d H:i:s");
				if (date("N", strtotime($date)) == 7) $date = date("Y-m-d H:i:s", strtotime("-1 day"));
				$start_date = date("Y-m-d 06:00:00", strtotime($date . " this week monday"));
				$end_date = date("Y-m-d H:i:s", strtotime($start_date . " +7 days"));
				$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE Fecha_Inicio >= '" . $start_date . "' AND Fecha_Inicio < '" . $end_date . "' AND ID_Linea <> 0 AND ID_Nivel=$id_nivel";
				$co['lineas_']=null;
				$res = $this->conexion->query($sql);
				while($fila=$res->fetch(PDO::FETCH_ASSOC)){
					$co['lineas_'][]=$fila['ID_Linea'];
				}
				return $co['lineas_'];
			}
		}
	
	function exite_linea_asignada($id_auditoria,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila['ID_Linea'] > 0){
				$co = false;
			}else{
				$co = true;
			}
		}
		if(isset($co)) return $co;
	}
	function get_id_linea($id_auditoria){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = ($fila['ID_Linea']);
		}
		if(isset($co)) return $co;
	}
	function get_name_linea($id_auditoria){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = ($fila['Linea']);
		}
		if(isset($co)) return $co;
	}
	function get_cambios_linea($id_auditoria)
	{
		$sql = "SELECT COUNT(*) AS CONT FROM LPA_V_Cambios_Linea WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			return $fila['CONT'];
		}
	}
	function get_lista_calendario_test($id_planta,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario_Test WHERE ID_Nivel=$id_nivel AND '" . date('Y-m-d H:i:s') . "' >= Fecha_Inicio AND '" . date('Y-m-d H:i:s') . "' <= Fecha_Limite";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Calendario']]['auditor'] 				= ($fila['Auditor']);
			$co[$fila['ID_Calendario']]['linea'] 				= ($fila['Linea']);
			$co[$fila['ID_Calendario']]['id_turno'] 			= ($fila['ID_Turno']);
			$co[$fila['ID_Calendario']]['id_user'] 				= ($fila['ID_User_GN']);
			$co[$fila['ID_Calendario']]['estado'] 				= ($fila['Estado_Auditoria']);
			$co[$fila['ID_Calendario']]['auditor'] 				= ($fila['Auditor']);
			$co[$fila['ID_Calendario']]['id_issue'] 			= ($fila['ID_Item']);
			$co[$fila['ID_Calendario']]['issue'] 				= ($fila['Item']);
			$co[$fila['ID_Calendario']]['id_linea'] 			= $fila['ID_Linea'];
			$co[$fila['ID_Calendario']]['correo'] 			= $fila['Correo'];
			$co[$fila['ID_Calendario']]['semana_auditoria']  	= date("W",strtotime($fila['Fecha_Inicio']));
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function insert_new_test_audit($id_linea,$id_nivel,$id_planta,$id_item){
		$sql = "INSERT INTO LPA_Calendario_Test VALUES (-1,'".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s",strtotime("+1 day"))."',NULL,NULL,$id_linea,0,0,$id_nivel,1)";
		if($id_nivel == 4){
			$sql = "INSERT INTO LPA_Calendario_Test VALUES (-1,'".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s",strtotime("+1 day"))."',NULL,NULL,0,$id_item,0,$id_nivel,1)";
		}
		$this->conexion();
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_lineas_disponibles_test($id_planta){
		$sql = "SELECT * FROM LPA_V_LIST_LINEAS";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Linea']] = ($fila['Linea']);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	
}

class auditorias extends ini
{
	function marcar_pp_all_auditoria($id_auditoria,$id_planta){
		$sql = "IF EXISTS (SELECT * FROM LPA_Calendario_PP WHERE ID_Calendario=$id_auditoria)
					BEGIN
						UPDATE LPA_Calendario_PP SET Activo=1 WHERE ID_Calendario=$id_auditoria;
					END
				ELSE
					BEGIN
						INSERT INTO LPA_Calendario_PP VALUES($id_auditoria,1);
					END";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function eliminar_hallazgo($id_hallazgo,$id_reporte,$id_planta){
		 $sql = "UPDATE LPA_Hallazgos SET Activo=0 WHERE ID_Hallazgo=$id_hallazgo";
		 $this->conexion();
		 if($this->conexion->query($sql)){
			 $co['estado']=true;
			 $sql = "IF (SELECT COUNT(*) FROM LPA_Hallazgos WHERE ID_Reporte=$id_reporte AND Activo=1) = 0
					BEGIN
						UPDATE LPA_Reporte_Auditoria SET Tipo_Estado=0 WHERE ID_Reporte=$id_reporte 
					END";
			$this->conexion->query($sql);
		 }else{
			 $co['estado']=false;
		 }
		 $this->conexion=null;
		 if(isset($co)) return $co;
	}
	function getButtonForEmail(){
		return "<a href='".$_ENV["URL_ADMIN_WEB"]."/app/lpa/hallazgos/'><i>Ver acciones</i></a>";
	}
	function send_email_hallazgos($id_auditoria,$id_planta, $linea, $area){
		$this->conexion();
		$body = $this->get_body_hallazgo($id_auditoria,$id_planta);
		$departamentos = $this->get_lista_de_correos($body['departamento']);
		$EmailAdministradores = $this->get_lista_correos_administradores("LPA Manachment");
		$body_email = $this->get_body_mail($body['cuerpo'],800,"LPA Findings <br> $linea",$this->getButtonForEmail());
		$co = $this->ini_send_email($body_email, $departamentos,"apps","LPA Audit Completed",[],[],$EmailAdministradores);
		$this->conexion=null;
		if(isset($co)){return $co;}
	}
	function get_lista_correos_administradores($app){
		try {
			$sql = "SELECT * FROM GN_V_Administradores_Apps WHERE Active=1 AND AppName='$app'";
			$res = $this->conexion->query($sql);
			while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
				$co[] = $fila['Correo'];
			}
			return (isset($co)) ?  $co :  [];
		} catch (\Throwable $th) {
			return  [];
		}
		
	}
	function get_lista_de_correos($lista_dep){
		$lista_dep 	= array_unique($lista_dep);
		foreach ($lista_dep as $key => $value) {
			$sql = "SELECT * FROM LPA_V_LIST_RESPONSABLES_POR_DEPARTAMENTO WHERE ID_Departamento=$value";
			$res = $this->conexion->query($sql);
			while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
				$co[] = $fila['Correo'];
			}
			
		}
		if (isset($co)) return array_unique($co);
	}
	function get_body_hallazgo($id_auditoria,$id_planta){
		$sql = "SELECT * FROM LPA_V_Hallazgos WHERE Activo=1 AND ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		$co['cuerpo'] = "
				<thead>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>No Acción</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Hallazgo</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Fecha</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Fecha Cierre</b></th>
					<th style='border-bottom:1pt solid #cfd8dc;'><b>Tipo de Hallazgo</b></th>
				<thead>";
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['departamento'][] = $fila['ID_Departamento'];
			$co['cuerpo'].= "<tr>
					<td style='border-bottom:1pt solid #cfd8dc; width:100px; padding-top:5px; '>".$fila['Codigo_Accion']."</td>
					<td style='border-bottom:1pt solid #cfd8dc;' ><b>". ($fila['Hallazgo'])."</b><br>".($fila['Accion']). "</td>";
					if($fila['Fecha_Cierre'] != ""){
						$co['cuerpo'] .= "
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;' >" . date("Y-m-d", strtotime($fila['Fecha_Programada'])) . "</td>
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;'>".date("Y-m-d", strtotime($fila['Fecha_Cierre'])) . "</td>
						<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#2e7d32;'><b>" . $fila['Tipo_Hallazgo'] . "</b></td>";
					}else{
						 $co['cuerpo'] .= "<td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828;' >" . date("Y-m-d", strtotime($fila['Fecha_Programada'])) . "</td>
						 <td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828;'> -- </td>
						 <td style='border-bottom:1pt solid #cfd8dc; text-align:center; color:#c62828 ;'><b>" . $fila['Tipo_Hallazgo'] . "</b></td>";
					} 
					$co['cuerpo'] .= "
			</tr>";
			//$this->get_name_imagen($fila['ID_Hallazgo'], $id_planta);
		}
		if (isset($co)) return $co;
	}
	function get_lista_de_hallazgos($id_auditoria,$id_planta){
		$sql = "
		SELECT        TOP (100) PERCENT dbo.LPA_Hallazgos.*
FROM            dbo.LPA_Hallazgos INNER JOIN
                         dbo.LPA_Reporte_Auditoria ON dbo.LPA_Hallazgos.ID_Reporte = dbo.LPA_Reporte_Auditoria.ID_Reporte
WHERE        (dbo.LPA_Reporte_Auditoria.Activo = 1) AND (dbo.LPA_Reporte_Auditoria.ID_Calendario = $id_auditoria)";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['hallazgo'] 		= ($fila['Hallazgo']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['accion'] 		= ($fila['Accion']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['nc'] 			= ($fila['Tipo_Hallazgo']);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['imagen'] 		= $this->get_name_imagen($fila['ID_Hallazgo'], $id_planta);
			$co[$fila['ID_Reporte']][$fila['ID_Hallazgo']]['departamento'] 	= ($fila['ID_Departamento']);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function eliminar_imagen_editar($id_hallazgo,$id_planta){
		$this->conexion();
		$this->eliminar_imagen_anterior($id_hallazgo,$id_planta);
		$this->conexion=null;
	}
	function update_imagen_hallazgo($file,$ext,$id_planta,$id_hallazgo, $id_pregunta_reporte){
		$this->conexion();
		$img 		= new img();
		$name_img 	= $img->upload_img($file, uniqid() . ".jpeg", $ext, $_ENV["URL_ADMIN"]."\\files\\lpa\\");
		$co 		= $this->update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte);
		$co['imagen'] = $_ENV["URL_ADMIN_WEB"]."/files/lpa/".$name_img;
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte){
		$this->eliminar_imagen_anterior($id_hallazgo, $id_planta);
		$co = $this->update_name_this_hallazgo($id_hallazgo,$name_img, $id_pregunta_reporte);
		return $co;
	}
	function update_name_this_hallazgo($id_hallazgo,$name_img, $id_pregunta_reporte){
		$sql = "INSERT INTO LPA_Hallazgos_Img VALUES ('$name_img',$id_hallazgo,$id_pregunta_reporte,1)";
		if ($this->conexion->query($sql)) {
			$co['estado'] = true;
		} else {
			$co['estado'] = false;
		}
		if (isset($co)) return $co;
	}
	function eliminar_imagen_anterior($id_hallazgo, $id_planta){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			unlink($_ENV["URL_ADMIN"]."\\files\\lpa\\" . $fila['Hallazgo_Img']);
		}
		$sql = "UPDATE LPA_Hallazgos_Img SET Activo=0 WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
	}
	function update_data_hallazgo($id_hallazgo,$hallazgo,$accion,$id_dep,$nc,$id_planta){
		$this->conexion();
		if($nc == 1){
			$fecha = $this->get_fecha_started($id_hallazgo);
			$new_dates= "  Fecha_Programada = '$fecha' , Fecha_Cierre = '$fecha' ";
		}else{
			$fecha  		= date("Y-m-d 23:59:59", strtotime("+1 week this friday"));
			$new_dates 	= "  Fecha_Programada = '$fecha' , Fecha_Cierre='' ";
		}
		$sql = "UPDATE LPA_Hallazgos SET Hallazgo='".$this->str($hallazgo)."', Accion='".$this->str($accion)."', ID_Departamento=$id_dep, Tipo_Hallazgo=$nc, $new_dates WHERE ID_Hallazgo=$id_hallazgo";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_fecha_started($id_hallazgo){
		$sql = "SELECT Fecha_Hallazgo FROM LPA_Hallazgos WHERE ID_Hallazgo=$id_hallazgo";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Fecha_Hallazgo'];
		}
		if(isset($co)) return $co;
	}
	function save_new_finding($hallazgo, $accion, $id_departamento, $id_planta, $id_reporte,$id_auditoria,$nc, $id_pregunta){
		$start_date = date("Y-m-d H:i:s");
		$end_date 	= date("Y-m-d 23:59:59", strtotime("+1 week this friday"));
		$close_date = "";
		if($nc ==1){
			$end_date = $start_date;
			$close_date = $start_date;
		}
		$sql = "INSERT INTO LPA_Hallazgos VALUES (NULL,'$start_date','$end_date','$close_date',$id_departamento,0,$id_auditoria,1,0,$nc,$id_pregunta,'".$this->str($hallazgo)."','".$this->str($accion)."',$id_reporte)";
		$this->conexion();
		if($this->conexion->query($sql)){
			$co['estado']=true;
			$this->update_hallazgo_imagen($id_reporte);
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function update_hallazgo_imagen($id_reporte){
		$sql = "UPDATE LPA_Hallazgos_Img SET ID_Hallazgo=".$this->get_id_hallazg($id_reporte)." WHERE ID_Reporte=$id_reporte AND Activo=1 AND ID_Hallazgo IS NULL";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function get_id_hallazg($id_reporte){
		$sql = "SELECT TOP 1 ID_Hallazgo FROM LPA_Hallazgos WHERE ID_Reporte=$id_reporte AND Activo=1 ORDER BY ID_Hallazgo DESC";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['ID_Hallazgo'];
		}
		if(isset($co)) return $co;
	}
	function eliminar_imagen($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$name_img = $this->get_nombre_imagen($id_reporte_pregunta);
		if(isset($name_img)) $this->eliminar_imagen_ruta($name_img,$id_planta);
		$co['estado'] = true;
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function eliminar_imagen_ruta($name,$id_planta){
		if(file_exists($_ENV["URL_ADMIN"]."\\files\\lpa\\". $name)){
			if (unlink($_ENV["URL_ADMIN"]."\\files\\lpa\\". $name)) {
			} else {
			}
		}else{
		}
		
	}
	function get_nombre_imagen($id_reporte_pregunta){
		$sql = "SELECT TOP 1 * FROM LPA_Hallazgos_Img WHERE ID_Reporte=$id_reporte_pregunta AND Activo=1 AND ID_Hallazgo IS NULL";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co=$fila['Hallazgo_Img'];
		}
		$this->conexion->query("UPDATE LPA_Hallazgos_Img SET Activo=0 WHERE ID_Reporte=$id_reporte_pregunta AND ID_Hallazgo IS NULL");
		if(isset($co)) return $co;
	}
	function guardar_imagen_64($file,$id_pregunta_reporte,$id_planta=0){
		$this->conexion();
		$img 		= new img();
		$name_img  = uniqid().".jpeg";
		$img->subir_imagen_base64($_ENV["URL_ADMIN"]."\\files\\lpa\\",$file,$name_img);
		$co 		= $this->insert_name_img($id_pregunta_reporte, $name_img, $id_planta);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function editar_guardar_imagen_64($file,$id_pregunta_reporte,$id_planta,$id_hallazgo){
		$this->conexion();
		$img 		= new img();
		$name_img  = uniqid().".jpeg";
		$img->subir_imagen_base64($_ENV["URL_ADMIN"]."\\files\\lpa\\",$file,$name_img);
		$co 		= $this->update_img_name($id_hallazgo, $name_img, $id_planta, $id_pregunta_reporte);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function guardar_imagen($file,$ext,$id_pregunta_reporte,$id_planta){
		$this->conexion();
		$img 		= new img();
		$name_img 	= $img->upload_img($file,uniqid().".jpeg",$ext,$_ENV["URL_ADMIN"]."\\files\\lpa\\");
		$co 		= $this->insert_name_img($id_pregunta_reporte, $name_img, $id_planta);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function insert_name_img($id_pregunta, $name, $id_planta){
		$img = new img();
		$sql = "INSERT INTO LPA_Hallazgos_Img VALUES ('$name',NULL,$id_pregunta,1)";
		if($this->conexion->query($sql)){
			$co['estado']= $_ENV["URL_ADMIN_WEB"]."/files/lpa/".$name;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function get_info_no_conformidad($id_pregunta,$id_planta){
		$this->conexion();
		$img = new img();
		$co['hallazgos'] 	= $this->get_hallazgo_no_conformidad($id_pregunta,$id_planta);
		$co['imagenes'] 			= $this->get_imgs_hallazgos($id_pregunta);
		if(isset($co['imagenes'])) $co['imagenes'] = $_ENV["URL_ADMIN_WEB"]."/files/lpa/" . $co['imagenes'];
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_imgs_hallazgos($id){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img WHERE ID_Reporte=$id AND ID_Hallazgo IS NULL AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Hallazgo_Img'];
		}
		if(isset($co)) return $co;
	}
	function get_hallazgo_no_conformidad($id_pregunta,$id_planta){
		$sql = "SELECT * FROM LPA_Hallazgos WHERE Activo=1 AND ID_Reporte=$id_pregunta";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Hallazgo']]['hallazgo'] 		= ($fila['Hallazgo']);
			$co[$fila['ID_Hallazgo']]['accion'] 		= ($fila['Accion']);
			$co[$fila['ID_Hallazgo']]['nc'] 			= ($fila['Tipo_Hallazgo']);
			$co[$fila['ID_Hallazgo']]['imagen'] 		= $this->get_name_imagen($fila['ID_Hallazgo'],$id_planta);
			$co[$fila['ID_Hallazgo']]['departamento'] 	= ($fila['ID_Departamento']);
		}
		if(isset($co)) return $co;
	}
	function get_name_imagen($id_hallazgo,$id_planta){
		$sql = "SELECT TOP 1 Hallazgo_Img FROM LPA_Hallazgos_Img WHERE ID_Hallazgo=$id_hallazgo AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $_ENV["URL_ADMIN_WEB"]."/files/lpa/".$fila["Hallazgo_Img"];
		}
		if(isset($co)) return $co;
	}
	function finalizar_auditoria($id_auditoria,$id_planta,$linea,$auditor,$semana,$correo,$id_nivel){
		$this->conexion();
		if($this->ya_es_finalizada($id_auditoria)){
			$this->activar_hallazgo($id_auditoria);
		}else{
			if ($this->finalizar_auditoria_calendario($id_auditoria)) {
				$this->activar_hallazgo($id_auditoria);
				$this->send_mail_confirmacion_finalizada($id_auditoria, $linea, $auditor, $semana,$correo, $id_nivel);
			}
		}
		$co['estado'] = true;
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function ya_es_finalizada($id_auditoria){
		$sql = "SELECT Fecha_Final_Auditoria FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila['Fecha_Final_Auditoria'] != ''){
				$co = true;
			}else{
				$co = false;
			}
		}
		if(isset($co)) return $co;
	}
	function send_mail_confirmacion_finalizada($id_auditoria,$linea,$auditor, $semana,$correo, $id_nivel){
		$linea_issue = "Workcenter";
		if($id_nivel == 4){$linea_issue == "Issue";}
		$dato = "
			<tr><td style='width:20%;'></td><td style='text-align:right;'>Auditor:</td><td> $auditor</td><td></td></tr>
			<tr><td style='width:20%;'></td><td style='text-align:right;'>$linea_issue:</td><td> $linea</td><td></td></tr>";
		$body_email = $this->get_body_mail($dato,600,"Audit Completed <br> #$id_auditoria","");
		$this->ini_send_email($body_email,[$correo],"apps","Auditoria LPA finalizada",[],[],[]);
	}
	function activar_hallazgo($id_auditoria){
		$sql = "SELECT * FROM LPA_Hallazgos WHERE ID_Calendario=$id_auditoria AND Activo=1";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$this->activar_this_hallazgo($fila['ID_Hallazgo']);
		}
	}
	function activar_this_hallazgo($id_hallazgo){
		$sql = "UPDATE LPA_Hallazgos SET No_Accion=ISNULL((SELECT MAX(No_Accion)+1 FROM LPA_V_Hallazgos WHERE Publicado=1 AND YEAR(Fecha_Hallazgo) = YEAR(GETDATE())),1), Publicado=1 WHERE ID_Hallazgo=$id_hallazgo";
		$res = $this->conexion->query($sql);
		if($res->rowCount()>0){
			return true;
		}else{
			return false;
		}
	}
	function finalizar_auditoria_calendario($id_auditoria){
		$sql = "UPDATE LPA_Calendario SET Fecha_Final_Auditoria='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function set_ok($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$this->eliminar_hallazgos_previos($id_reporte_pregunta);
		$sql = "UPDATE LPA_Reporte_Auditoria SET Estado=1, Tipo_Estado=1, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
		$this->conexion=null;
	}
	function set_nok($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$sql = "UPDATE LPA_Reporte_Auditoria SET Estado=1, Tipo_Estado=2, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function set_na($id_reporte_pregunta,$id_planta){
		$this->conexion();
		$this->eliminar_hallazgos_previos($id_reporte_pregunta);
		$sql = "UPDATE LPA_Reporte_Auditoria SET Estado=1, Tipo_Estado=4, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Reporte=$id_reporte_pregunta";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function eliminar_hallazgos_previos($id_reporte_pregunta){
		$this->eliminar_hallazgos($id_reporte_pregunta);
	}
	function eliminar_hallazgos($id_reporte){
		$sql = "UPDATE LPA_Hallazgos SET Activo=0 WHERE ID_Reporte=$id_reporte";
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		if(isset($co)) return $co;
	}
	function get_info_auditorias($id_auditoria,$id_planta,$id_nivel){
		$this->conexion();
		$co['preguntas_lpa'] = $this->get_lista_de_preguntas($id_auditoria,$id_nivel);
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_de_preguntas($id_auditoria, $id_nivel){
		$sql = "SELECT * FROM LPA_V_Reporte_Auditoria WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($id_nivel == 4){
				$co[$fila['ID_Linea']]['name'] = ($fila['Linea']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['titulo'] = ($fila['Titulo']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['no_pregunta'] = ($fila['No_Pregunta']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['pregunta'] = ($fila['Pregunta']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['importancia'] = ($fila['Importancia']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['plan_r'] = ($fila['Plan_Reaccion']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['h_a'] = ($fila['Hallazgo_Activo']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['estado'] = ($fila['Estado']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['id_pregunta'] = ($fila['ID_Pregunta']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['evento_nombre'] = ($fila['Evento_Nombre']);
				$co[$fila['ID_Linea']]['preguntas'][$fila['ID_Reporte']]['evento_funcion'] = ($fila['Funcion']);
			}else{
				$co[$fila['ID_Item']]['name'] = ($fila['Item']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['titulo'] = ($fila['Titulo']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['no_pregunta'] = ($fila['No_Pregunta']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['pregunta'] = ($fila['Pregunta']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['importancia'] = ($fila['Importancia']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['plan_r'] = ($fila['Plan_Reaccion']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['h_a'] = ($fila['Hallazgo_Activo']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['estado'] = ($fila['Estado']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['id_pregunta'] = ($fila['ID_Pregunta']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['evento_nombre'] = ($fila['Evento_Nombre']);
				$co[$fila['ID_Item']]['preguntas'][$fila['ID_Reporte']]['evento_funcion'] = ($fila['Funcion']);
			}
			
		}
		if(isset($co)) return $co;
	}
	function insert_name_acargo($name_acargo,$id_auditoria,$id_planta){
		$this->conexion();
		$sql = "IF EXISTS (SELECT * FROM LPA_Calendario_Auditado WHERE ID_Calendario=$id_auditoria)
					BEGIN 
						UPDATE LPA_Calendario_Auditado SET Nombre_Auditado='". $this->str(ucwords($name_acargo))."' WHERE ID_Calendario=$id_auditoria; 
					END
					ELSE
						BEGIN 
							INSERT INTO LPA_Calendario_Auditado VALUES('" . $this->str(ucwords($name_acargo)) . "',$id_auditoria,1);
					END";
		if($this->conexion->query($sql)){
			$co['estado'] = true;
		}else{
			$co['estado'] = false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;
		
	}
	function set_pp_auditoria($id_auditoria,$id_planta,$id_nivel,$id_linea){
		$this->conexion();
		if ($this->validar_reporte_auditoria($id_auditoria)) {
			if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
				$co['estado'] = $this->update_reporte_auditoria_pp($id_auditoria);
			} else {
				$co['estado'] = false;
			}
		} else {
			if($this->obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel)){
				if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
					$co['estado'] = $this->update_reporte_auditoria_pp($id_auditoria);
				} else {
					$co['estado'] = false;
				}
			}
		}
		$this->marcar_pp_all_auditoria($id_auditoria,$id_planta);
		$co['preguntas'] = $this->get_preguntas_restantes($id_auditoria,$id_planta);
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function get_preguntas_restantes($id_auditoria,$id_planta){
		$sql = "SELECT COUNT(*) AS Counter FROM LPA_V_Reporte_Auditoria WHERE Estado=0 AND ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Counter'];
		}
		if(isset($co)) return $co;
	}
	function update_reporte_auditoria_pp($id_auditoria){
		$sql = "UPDATE LPA_V_Reporte_Auditoria SET Estado=1, Tipo_Estado=3, Fecha_Revision='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria AND PP_Activo=0";
		if($this->conexion->query($sql)){
			$co = true;
		}else{
			$co = true;
		}
		if(isset($co)) return $co;
	}
	function iniciar_auditoria($id_auditoria,$id_planta, $id_nivel, $id_linea,$idIssue){
		$this->conexion();
		if($this->validar_reporte_auditoria($id_auditoria)){
			if($this->update_fecha_inicio_auditoria($id_auditoria)){
				$co['estado'] = true;
			}else{
				$co['estado'] = false;
			}
		}else{
			if($this->obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel,$idIssue)){
				if ($this->update_fecha_inicio_auditoria($id_auditoria)) {
					$co['estado'] = true;
				} else {
					$co['estado'] = false;
				}
			}else{
				$co['estado'] = false;
			}
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function update_fecha_inicio_auditoria($id_auditoria){
		$sql = "UPDATE LPA_Calendario SET Fecha_Inicio_Auditoria='".date("Y-m-d H:i:s")."' WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co = true;
		}else{
			$co = false;
		}
		if(isset($co)) return $co;
	}
	function obtner_reporte_auditoria($id_auditoria, $id_linea, $id_nivel,$idIssue=null){
		if($id_nivel == 4){
			$id_lineas = $this->get_lineas_nivel_4($idIssue);
			foreach ($id_lineas as $key => $value) {
				$sql = "SELECT * FROM LPA_V_Formatos_Linea WHERE ID_Item=$id_linea AND ID_Nivel=$id_nivel AND ID_Linea=$value";
				$res = $this->conexion->query($sql);
				while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
					if ($this->conexion->query("INSERT INTO LPA_Reporte_Auditoria VALUES($id_auditoria," . $fila['ID_Pregunta'] . ",0,NULL,1,0,$value)")) {
						$co = true;
					} else {
						$co = false;
					}
				}
			}
		}else{
			$sql = "SELECT * FROM LPA_V_Formatos_Linea WHERE ID_Linea=$id_linea AND ID_Nivel=$id_nivel";
			$res = $this->conexion->query($sql);
			while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
				if ($this->conexion->query("INSERT INTO LPA_Reporte_Auditoria VALUES($id_auditoria," . $fila['ID_Pregunta'] . ",0,NULL,1,0,$id_linea)")) {
					$co = true;
				} else {
					$co = false;
				}
			}
		}
		if (isset($co)) return $co;
	}
	function get_lineas_nivel_4($idItem){
		$sql = "SELECT TOP 5 * FROM ( SELECT DISTINCT ID_Linea, Linea FROM LPA_V_Formatos_Linea WHERE ID_Item=$idItem AND ID_Nivel=4 AND Activo=1) t ORDER BY NEWID()";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[]	= $fila['ID_Linea'];
		}
		if(isset($co)) return $co;
	}
	function validar_reporte_auditoria($id_auditoria){
		$sql = "SELECT COUNT(*) AS Count FROM LPA_V_Reporte_Auditoria WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila["Count"]>0){
				$co = true;
			}else{
				$co = false;
			}
		}
		if(isset($co)) return $co;
	}
	function validar_estado_auditoria($id_auditoria,$id_planta,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['estado'] 		= $fila['Estado_Auditoria'];
			$co['auditado'] 	= ($fila['Nombre_Auditado']);
			$co['cambios'] 		= $this->get_cambios_linea($id_auditoria);
			$co['set_pp'] 		= $this->validar_set_pp($id_auditoria);
			$co['lineas'] 		= $this->validar_lineas($id_auditoria,$id_nivel);
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function validar_set_pp($id_auditoria){
		$sql = "SELECT * FROM LPA_Calendario_PP WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = true;
		}
		if(isset($co)) return $co;
	}
	function validar_lineas($id_auditoria,$id_nivel){
		switch ($id_nivel) {
			case '1':
				$co['name'] = $this->get_name_linea($id_auditoria);
				$co["id_linea"] = $this->get_id_linea($id_auditoria);
				break;
			case '2':
				if($this->exite_linea_asignada($id_auditoria,$id_nivel)){
					$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
					if($this->asignar_linea($id_auditoria, $id_linea)){
						$co['name'] = $this->get_name_linea($id_auditoria);
						$co['id_linea'] = $id_linea;
					}
				}else{
					$co["name"] = $this->get_name_linea($id_auditoria);
					$co["id_linea"] = $this->get_id_linea($id_auditoria);
				}
				
				break;
			case '3':
				if ($this->exite_linea_asignada($id_auditoria, $id_nivel)) {
					$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
					if ($this->asignar_linea($id_auditoria, $id_linea)) {
						$co['name'] = $this->get_name_linea($id_auditoria);
						$co['id_linea'] = $id_linea;
					}
				} else {
					$co["name"] = $this->get_name_linea($id_auditoria);
					$co["id_linea"] = $this->get_id_linea($id_auditoria);
				}
				break;
			case '4':
				$co = $this->get_datos_nivel_4($id_auditoria);
				break;
		}
		if(isset($co)) return $co;
	}
	function get_datos_nivel_4($id_auditoria){
		$sql = "SELECT Item, ID_Item FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co['name'] 	= ($fila['Item']);
			$co['id_linea']	= $fila['ID_Item'];
		}
		if(isset($co)) return $co;
	}
	function cambiar_linea($id_auditoria,$id_nivel,$id_planta){
		$this->conexion();
		if($this->existe_oportunidad_cambiar_linea($id_auditoria,$id_nivel)){
			$id_linea = $this->get_linea_aleatorio($id_auditoria, $id_nivel);
			if ($this->asignar_linea($id_auditoria, $id_linea)) {
				$this->insertar_historial_cambio_de_linea($id_auditoria);
				$co['estado'] 	= true;
				$co['id_linea'] = $id_linea;
				$co['linea'] 	= $this->get_name_linea($id_auditoria);
			}else{
				$co['estado'] 	= false;
				$co['id_linea'] = $id_linea;
				$co['linea'] 	= $this->get_name_linea($id_auditoria);
			}
		}else{
			$co['estado'] = false;
		}
		
		$this->conexion=null;
		if(isset($co)) return $co;
	}

	function get_linea_aleatorio($id_auditoria,$id_nivel)
	{
		$co = [];
		$co['lineas_disponibles'] 		= $this->get_lineas_base();
		$co['list_lineas_asignadas'] 	= $this->get_list_lineas_asignadas($id_auditoria,$id_nivel);
		if (count($co['lineas_disponibles']) > count($co['list_lineas_asignadas']) && $co['list_lineas_asignadas']!=null) {
			$co['unique_list_lineas'] 	= null;
			foreach ($co['lineas_disponibles'] as $key => $value) {
 				if (in_array($value, $co['list_lineas_asignadas'])) { } else {
					$co['unique_list_lineas'][] = $value;
				}
			}
			if (count($co['unique_list_lineas']) > 0) {
				$co  	= $co['unique_list_lineas'][array_rand($co['unique_list_lineas'], 1)];
			} else {
				$co 	= 0;
			}
		} else {
			$co  		= $co['lineas_disponibles'][array_rand($co['lineas_disponibles'], 1)];
		}
		return $co;
	}
	function asignar_linea($id_auditoria,$id_linea){
		$sql = "UPDATE LPA_Calendario SET ID_Linea=$id_linea WHERE ID_Calendario=$id_auditoria";
		if($this->conexion->query($sql)){
			$co=true;
		}else{
			$co=false;
		}
		if(isset($co)) return $co;
	}
	function insertar_historial_cambio_de_linea($id_auditoria){
		$sql = "INSERT INTO LPA_Cambios_Linea VALUES ($id_auditoria,1)";
		if($this->conexion->query($sql)){
			$co=true;
		}else{
			$co=false;
		}
		if(isset($co)) return $co;
	}
	function existe_oportunidad_cambiar_linea($id_auditoria, $id_nivel){
		if($id_nivel == 3) return true;
		$sql = "SELECT * FROM LPA_V_Cambios_Linea WHERE ID_Calendario=$id_auditoria";
		$co = true;
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = false;
		}
		if(isset($co)) return $co;
	}
	function get_cambios_linea($id_auditoria)
	{
		$sql = "SELECT COUNT(*) AS CONT FROM LPA_V_Cambios_Linea WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			return $fila['CONT'];
		}
	}
	
	function exite_linea_asignada($id_auditoria,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if($fila['ID_Linea'] > 0){
				$co = false;
			}else{
				$co = true;
			}
		}
		if(isset($co)) return $co;
	}
	
	function get_lineas_base()
	{
		$sql = "SELECT ID_Linea FROM LPA_V_LIST_LINEAS";
		$co = [];
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[]= $fila['ID_Linea'];
		}
		return $co;
	}
	function get_list_lineas_asignadas($id_calendario,$id_nivel){
			$co = [];
			$date = date("Y-m-d H:i:s");
			if (date("N", strtotime($date)) == 7){
				$date = date("Y-m-d H:i:s", strtotime("-1 day"));
			} 
			$start_date = date("Y-m-d 06:00:00", strtotime($date . " this week monday"));
			$end_date = date("Y-m-d H:i:s", strtotime($start_date . " +7 days"));
			$sql = "SELECT * FROM LPA_V_Calendario WHERE Fecha_Inicio >= '" . $start_date . "' AND Fecha_Inicio < '" . $end_date . "' AND ID_Linea <> 0 AND ID_Nivel=$id_nivel";
			$co['lineas_']=[];
			$res = $this->conexion->query($sql);
			while($fila=$res->fetch(PDO::FETCH_ASSOC)){
				$co['lineas_'][]=$fila['ID_Linea'];
			}
		
			return $co;
		}
	function get_lista_departamentos($id_planta){
		$this->conexion();
		$sql = "SELECT * FROM LPA_V_LIST_DEPARTAMENTOS";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Departamento']]['name'] = ($fila['Departamento']);	
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_name_linea($id_auditoria){
		$sql = "SELECT * FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = ($fila['Linea']);
		}
		if(isset($co)) return $co;
	}
	function get_id_linea($id_auditoria){
		$sql = "SELECT * FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = ($fila['ID_Linea']);
		}
		if(isset($co)) return $co;
	}
}

class login extends ini
{
	function validar_login($id_user, $password, $id_planta)
	{
		$sql = "SELECT Password FROM GN_V_USUARIOS WHERE ID_GN=$id_user";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			if (password_verify($password, $fila['Password'])) {
				$co = true;
				$_SESSION['lpa_login'] = $id_user;
			} else {
				$co = false;
			}
		}
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function logout(){
		session_destroy();
		$co = true;
		return $co;
	}
}

class calendario extends ini
{
	function validar_tablets_lpa($id_planta){
		$co['niveles'] 	= $this->get_lista_niveles($id_planta);
		$co['tablets'] 	= $this->get_lista_tablets($id_planta);
		$co['ip'] 		= $_SERVER['REMOTE_ADDR'];
		if(isset($co)) return $co;
	}
	function get_lista_niveles($id_planta){
		$sql = "SELECT * FROM LPA_Tablets_Niveles WHERE Activo=1";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Nivel']]=$fila['Activo'];
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_tablets($id_planta){
		$sql = "SELECT * FROM LPA_Tablets WHERE Activo=1";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['IP']]=$fila['Nombre'];
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function validar_estado_auditoria($id_auditoria,$id_planta){
		$co['estado'] 	= $this->get_estado_auditoria($id_auditoria, $id_planta);
		$co['login'] 	= $this->get_estado_login($id_auditoria, $id_planta);
		if(isset($co)) return $co;
	}
	function get_estado_login($id_auditoria,$id_planta){
		$sql = "SELECT ID_User_GN FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$id_user = $fila['ID_User_GN'];
		}
		if(isset($_SESSION['lpa_login'])){
			if($_SESSION['lpa_login'] == $id_user){
				$co = true;
			}else{
				$co = true;
			}
		}else{
			$co=false;
		}
		$this->conexion=null;
		if (isset($co)) return $co;
	}
	function get_estado_auditoria($id_auditoria, $id_planta){
		$sql = "SELECT Estado_Auditoria FROM LPA_V_Calendario WHERE ID_Calendario=$id_auditoria";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co = $fila['Estado_Auditoria'];
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
	function get_lista_de_auditorias($id_planta,$id_nivel){
		$sql = "SELECT * FROM LPA_V_Calendario WHERE ID_Nivel=$id_nivel AND '" . date('Y-m-d H:i:s') . "' >= Fecha_Inicio AND '" . date('Y-m-d H:i:s') . "' <= Fecha_Limite";
		$this->conexion();
		$res = $this->conexion->query($sql);
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$co[$fila['ID_Calendario']]['auditor'] 				= ($fila['Auditor']);
			$co[$fila['ID_Calendario']]['linea'] 				= ($fila['Linea']);
			$co[$fila['ID_Calendario']]['id_turno'] 			= ($fila['ID_Turno']);
			$co[$fila['ID_Calendario']]['id_user'] 				= ($fila['ID_User_GN']);
			$co[$fila['ID_Calendario']]['estado'] 				= ($fila['Estado_Auditoria']);
			$co[$fila['ID_Calendario']]['auditor'] 				= ($fila['Auditor']);
			$co[$fila['ID_Calendario']]['id_issue'] 			= ($fila['ID_Item']);
			$co[$fila['ID_Calendario']]['issue'] 				= ($fila['Item']);
			$co[$fila['ID_Calendario']]['id_linea'] 			= $fila['ID_Linea'];
			$co[$fila['ID_Calendario']]['correo'] 				= $fila['Correo'];
			$co[$fila['ID_Calendario']]['semana_auditoria']  	= date("W",strtotime($fila['Fecha_Inicio']));
		}
		$this->conexion=null;
		if(isset($co)) return $co;
	}
}
class update_historial_data extends ini
{
	var $id_planta = null;
	function update_data($id_planta,$date){
		$this->id_planta=$id_planta;
		$start 			=  date("Y-m-d 06:00:00",strtotime($date." monday this week"));
		$end 			= 	date("Y-m-d 06:00:00",strtotime($date." monday next week"));
		$cum_audit 		= $this->get_auditorias_programadas(1,$start,$end);
		$cum_audit2 	= $this->get_auditorias_programadas(2,$start,$end);
		$cum_audit3		= $this->get_auditorias_programadas(3,date("Y-m-01 06:00:00",strtotime($date)),date("Y-m-01 06:00:00",strtotime($date." +1 month")));
		$cum_audits		= $this->get_auditorias_programadas(4,date("Y-m-01 06:00:00",strtotime($date)),date("Y-m-01 06:00:00",strtotime($date." +1 month")));
		$cum_objetivo 	= $this->get_cumplimiento_objetivo($date);


		$programadas  	= ($cum_audit['programada'] + $cum_audit2['programada'] + $cum_audit3['programada'] + $cum_audits['programada']);
		$realizadas 	= ($cum_audit['realizadas']+$cum_audit2['realizadas']+$cum_audit3['realizadas']+$cum_audits['realizadas']);

	

		if($programadas>0){
			$acumulado = round(($realizadas/$programadas)*100,2);
		}else{
			$acumulado =0;
		}
		$this->conexion($this->id_planta);
		$sql = "UPDATE LPA_Cumplimiento_Semanal SET Cumplimiento=$acumulado, Nivel_1=".$cum_audit['porcent'].", Nivel_2=".$cum_audit2['porcent'].", Nivel_3=".$cum_audit3['porcent'].", Nivel_S=".$cum_audits['porcent'].", Puntos_Auditar=".$cum_objetivo['total_puntos'].", Puntos_Conformes=".$cum_objetivo['puntos_conformes'].", Puntos_Inconformes='".$cum_objetivo['puntos_inconformes']."', Puntos_Porcentaje=".$cum_objetivo['porcent']." WHERE SUBSTRING(CAST({ fn WEEK(Fecha) } + 100 AS varchar), 2, 2)=".date("W",strtotime($start));
		if($this->conexion->query($sql)){
			$co['estado']=true;
		}else{
			$co['estado']=false;
		}
		$this->conexion=null;
		if(isset($co)) return $co;

	}
	function get_cumplimiento_objetivo($date){
		$this->conexion($this->id_planta);
		$sql ="SELECT COUNT(*) AS CONT FROM LPA_V_REPORTE_AUDITORIA WHERE Fecha_Revision >= '".date("Y-m-d 06:00:00",strtotime($date." monday this week"))."' AND Fecha_Revision < '".date("Y-m-d 06:00:00",strtotime($date." monday next week"))."'  AND ID_Nivel != 3 AND ID_Nivel != 4";
		$total_puntos=0;
		$puntos_inconformes =0;
		$co = [];
		$res = $this->conexion->query($sql);
		while($fila=$res->fetch(PDO::FETCH_ASSOC)){
			$total_puntos = $fila['CONT'];
		}
		$sql ="SELECT COUNT(*) AS CONT FROM LPA_V_REPORTE_AUDITORIA WHERE Fecha_Revision >= '".date("Y-m-d 06:00:00",strtotime($date." monday this week"))."' AND Fecha_Revision < '".date("Y-m-d 06:00:00",strtotime($date." monday next week"))."' AND ID_Nivel != 3 AND ID_Nivel != 4 AND Tipo_Estado=2";
		$res = $this->conexion->query($sql);
		while($fila=$res->fetch(PDO::FETCH_ASSOC)){
			$puntos_inconformes = $fila['CONT'];
		}


		$sql ="SELECT COUNT(*) AS CONT FROM LPA_V_REPORTE_AUDITORIA WHERE Fecha_Revision >= '".date("Y-m-01 06:00:00",strtotime($date))."' AND Fecha_Revision < '".date("Y-m-01 06:00:00",strtotime($date." +1 month"))."'  AND (ID_Nivel = 3 OR ID_Nivel = 4)";
		$res = $this->conexion->query($sql);
		while($fila=$res->fetch(PDO::FETCH_ASSOC)){
			$total_puntos = ($total_puntos + $fila['CONT']);
		}
		$sql ="SELECT COUNT(*) AS CONT FROM LPA_V_REPORTE_AUDITORIA WHERE Fecha_Revision >= '".date("Y-m-01 06:00:00",strtotime($date))."' AND Fecha_Revision < '".date("Y-m-01 06:00:00",strtotime($date." +1 month"))."'  AND (ID_Nivel = 3 OR ID_Nivel = 4) AND Tipo_Estado=2";
		$res = $this->conexion->query($sql);
		while($fila=$res->fetch(PDO::FETCH_ASSOC)){
			$puntos_inconformes = $puntos_inconformes + $fila['CONT'];
		}
		$co['total_puntos'] 		= $total_puntos;
		$co['puntos_inconformes'] 	= $puntos_inconformes;
		$co['puntos_conformes'] 	= $total_puntos-$puntos_inconformes;
		if($total_puntos==0){
			$co['porcent']=0;
		}else{
			$co['porcent'] 				= (100 - round((($puntos_inconformes/$total_puntos)*100),2));
		}

		
		$this->conexion=null;
		return $co;
		
	}
	function get_auditorias_programadas($nivel,$start,$end){
		$this->conexion($this->id_planta);
		$sql ="SELECT COUNT(*) AS COUNT FROM LPA_V_Calendario WHERE Fecha_Inicio >= '".$start."' AND Fecha_Inicio < '".$end."' AND ID_Nivel=$nivel";
		$co['programada']=0;
		$co['realizadas']=0;
		$res = $this->conexion->query($sql);
		while($fila=$res->fetch(PDO::FETCH_ASSOC)){
				$co['programada'] = $fila['COUNT'];
		}
		$sql2 ="SELECT COUNT(*) AS COUNT FROM LPA_V_Calendario WHERE Fecha_Inicio >= '".$start."' AND Fecha_Inicio < '".$end."' AND Estado_Auditoria=2 AND ID_Nivel=$nivel";
		$res2 = $this->conexion->query($sql2);
		while($fila2=$res2->fetch(PDO::FETCH_ASSOC)){
				$co['realizadas'] = $fila2['COUNT'];
		}
		if($co['programada']>0){
			$co['porcent'] = round(($co['realizadas']/$co['programada'])*100);
		}else{
			$co['porcent'] = 0;	
		}
		$this->conexion=null;
		return $co;
	}
}
class ini
{
	public $conexion;
	public $static_data=null;
	
	function __construct() {
		$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
		$dotenv->load();
	}
	function conexion($id_planta = 1){
		$info= array(
			'Database'=> $_ENV["DB_NAME"], 
			'UID'=>$this->decrypt($_ENV["USER"], $_ENV["USER_KEY"]), 
			'PWD'=>$this->decrypt($_ENV["PASSWORD"], $_ENV["PASSWORD_KEY"]), 
			"CharacterSet"=>"UTF-8");
		$this->conexion = new PDO("sqlsrv:Server=".$_ENV["SERVER"].";Database=" . $info['Database'], $info['UID'], $info['PWD']);
		return $this->conexion;
	}
	function jason_desencriptar($cadena, $key)
	{
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $decrypted;  //Devuelve el string desencriptado
	}
	function str($a)
	{
		$a = trim($a);
		$a = str_replace("'", "''", $a);
		return $a;
	}
	function decrypt($data, $key)
	{
		list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
	}
	function ini_send_email($body, $users, $app, $titulo_mail, $imgs, $files,$bcc)
	{
		$correo = new PHPMailer();
		if (count($users) > 0) {
			foreach ($users as $key => $value) {
				$correo->AddAddress($value);
			}
		}
		if (count($bcc) > 0) {
			foreach ($bcc as $key => $value) {
				$correo->addBCC($value);
			}
		}
		if (count($imgs) > 0) {
			foreach ($imgs as $key => $value) {
				$correo->AddEmbeddedImage($this->url_statica . $value, $key);
			}
		}
		if (count($files) > 0) {
			foreach ($files as $key => $value) {
				$correo->addAttachment($value['ruta'], $value['nombre'] . ".pdf");
			}
		}
		$to = $this->get_datos_mail($app);
		$correo->IsSMTP();
		$correo->SMTPAuth 	= $to["SMTPAuth"];
		$correo->SMTPSecure = $to["SMTPSecure"];
		$correo->Host 		= $to["Host"];
		$correo->Port 		= $to["Port"];
		$correo->Username   = $to["Correo"];
		$correo->Password   = $this->decrypt($to["Password2"],'');
		$correo->SetFrom($to["Correo"], $titulo_mail);
		$correo->Subject = $titulo_mail;
		$correo->Timeout = 30;
		$correo->CharSet = 'UTF-8';
		$correo->MsgHTML($body);
		if (!$correo->Send()) {
			return false;
		} else {
			return true;
		}
	}
	function get_datos_mail($app)
	{
		$sql = "SELECT * FROM GN_V_SERVER_CORREO WHERE Indice='$app'";
		$res = $this->conexion->query($sql);
		$to = null;
		while ($fila=$res->fetch(PDO::FETCH_ASSOC)) {
			$to = $fila;
		}
		return $to;
	}
	function get_body_mail($datos_body, $width, $titulo, $boton)
	{
		$body = '<table bgcolor="#ececec" align="center" style="width:100%!important;table-layout:fixed">
						<tbody>
							<tr>
								<td style="padding-bottom:0px">
									<table style="width:' . $width . 'px; margin:auto" align="center" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>
													<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#353e4a" style="background-color:#353e4a">
														<tbody>
															<tr>
																<td align="center" style="padding-top:8px;padding-bottom:8px;text-align:center;background-color:#353e4a" width="100%">
																	<a title="Motus" style="color:#ffffff;text-decoration:none;font-family:"Arial";font-size:23px" target="_blank" data-saferedirecturl="">
																		<b><i style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><font color="white">Motus Apps</font></i></b>
																	</a>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
												<td>
													<table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#353e4a;font-family:Arial,sans-serif;font-size:15px">
														<tbody>
															<tr>
																<td class="" style="padding-top:20px;font-size:20px;text-align:center"><strong>' . $titulo . '</strong></td>
															</tr>
															<tr>
																<td style="height:10px"></td>
															</tr>
															<tr>
																<td>
																	<table style="width:100%; color:#353e4a;font-family:Arial,sans-serif;">
																		' . $datos_body . '
																	</table>
																</td>
															</tr>
															<tr>
																<td class="" style="color:#353e4a;text-align:center;font-size:12px;line-height:25px;padding-top:20px;padding-right:30px;padding-bottom:10px;padding-left:30px">' . $boton . '</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
												<td>
													<table align="center" bgcolor="#ececec" border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align:center;color:#353e4a;font-family:Arial,sans-serif;font-size:12px">
														<tbody>
															<tr style="padding:0px;">
																<td style="color:#353e4a;text-align:center;font-size:14px;padding-top:15px;padding-right:15px;padding-left:15px padding-bottom:15px">
																	<strong>Motus Integrated Technologies</strong>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>';
		return $body;
	}
}

class img
{
	
	function upload_img($archivo, $img, $ext, $path)
	{
		$target_path = $path;
		$name 				= $img;
		$ext =  explode("/", $ext);
		$ext = end($ext);
		$target_path = $target_path . $name;
		$data = @file_get_contents($archivo);
		if (file_put_contents($target_path, $data)) {
			if ($ext == "png") {
				$image = imagecreatefrompng($target_path);
				imagejpeg($image, $target_path, 70);
				if (imagedestroy($image)) {
					//unlink($path . $img);
				}
				$this->renderizar($img, $path);
			} else {
				$this->renderizar($img, $path);
			}
			$co=$img;
		}
		if(isset($co)) return $co;
	}
	function renderizar($valor, $path)
	{
		$origen = $path . $valor;
		$destino = $path . $valor;
		$destino_temporal = tempnam("../tmp/", "tmp");
		if ($this->adjustPhotoOrientation($origen)) {
			$dato_img = getimagesize($origen);
			$ancho = $dato_img[0];
			$alto = $dato_img[1];
			/*$ancho = $ancho / 4;
			$alto = $alto / 4;*/
			// $ancho = 100;
			// $alto 	= ($ancho * $dato_img[1]) / $dato_img[0];

			if ($this->redimensionar_jpeg($origen, $destino_temporal, $ancho, $alto, 90)) {
				$fp = fopen($destino, "w");
				fputs($fp, fread(fopen($destino_temporal, "r"), filesize($destino_temporal)));
				fclose($fp);
			} else {
				//renderizar($conexion,$valor,$dat);
			}
		}
	}
	function adjustPhotoOrientation($full_filename)
	{
		$exif = exif_read_data($full_filename);
		if ($exif && isset($exif['Orientation'])) {
			$orientation = $exif['Orientation'];

			if ($orientation != 1) {
				$img = @ImageCreateFromJPEG($full_filename);
				if (!$img) {
					$img = ImageCreateFromJPEG(file_get_contents($full_filename));
				}
				$mirror = false;
				$deg    = 0;

				switch ($orientation) {
					case 2:
						$mirror = true;
						break;
					case 3:
						$deg = 180;
						break;
					case 4:
						$deg = 180;
						$mirror = true;
						break;
					case 5:
						$deg = 270;
						$mirror = true;
						break;
					case 6:
						$deg = 270;
						break;
					case 7:
						$deg = 90;
						$mirror = true;
						break;
					case 8:
						$deg = 90;
						break;
				}
				if ($deg) $img = imagerotate($img, $deg, 0);
				if ($mirror) $img = self::mirrorImage($img);
				imagejpeg($img, $full_filename);
			}
		}
		return true;
	}
	function redimensionar_jpeg($img_original, $img_nueva, $img_nueva_anchura, $img_nueva_altura, $img_nueva_calidad)
	{
		// crear una imagen desde el original 
		@$img = ImageCreateFromJPEG($img_original);
		// crear una imagen nueva 
		$thumb = imagecreatetruecolor($img_nueva_anchura, $img_nueva_altura);
		// redimensiona la imagen original copiandola en la imagen 
		if (@imagecopyresampled($thumb, $img, 0, 0, 0, 0, $img_nueva_anchura, $img_nueva_altura, ImageSX($img), ImageSY($img))) {
			ImageJPEG($thumb, $img_nueva, $img_nueva_calidad);
			ImageDestroy($img);
			return true;
		} else {
			return false;
		}
		// guardar la nueva imagen redimensionada donde indicia $img_nueva 

	}
	function subir_imagen_base64($ruta,$imagen,$nombre_imagen){
		if(strlen($imagen)==0) return false;
		$baseFromJavascript = $imagen;
		// Remover la parte de la cadena de texto que no necesitamos (data:image/png;base64,)
		// y usar base64_decode para obtener la ianformación binaria de la imagen
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $baseFromJavascript));

		$filepath = $ruta."".$nombre_imagen; // or image.jpg

		// Finalmente guarda la imágen en el directorio especificado y con la informacion dada
		file_put_contents($filepath,$data);
		$this->redimensionarImagen($filepath,$filepath,600,600,100);
		return true;
	}
	function redimensionarImagen($origin,$destino,$newWidth,$newHeight,$jpgQuality){
		// getimagesize devuelve un array con: anchura,altura,tipo,cadena de 
		// texto con el valor correcto height="yyy" width="xxx"
		$datos=getimagesize($origin);
		$exif = @exif_read_data($origin, 'ANY_TAG');

		// comprobamos que la imagen sea superior a los tamaños de la nueva imagen
		if($datos[0]>$newWidth || $datos[1]>$newHeight)
		{
	
			// creamos una nueva imagen desde el original dependiendo del tipo
			if($datos[2]==1)
				$img=@imagecreatefromgif($origin);
			if($datos[2]==2)
				$img=@imagecreatefromjpeg($origin);
			if($datos[2]==3)
				$img=@imagecreatefrompng($origin);
	
			// Redimensionamos proporcionalmente
			if(rad2deg(atan($datos[0]/$datos[1]))>rad2deg(atan($newWidth/$newHeight)))
			{
				$anchura=$newWidth;
				$altura=round(($datos[1]*$newWidth)/$datos[0]);
			}else{
				$altura=$newHeight;
				$anchura=round(($datos[0]*$newHeight)/$datos[1]);
			}
	
			// creamos la imagen nueva
			$newImage = imagecreatetruecolor($datos[0], $datos[1]);
		
			// redimensiona la imagen original copiandola en la imagen
			@imagecopyresampled($newImage, $img, 0, 0, 0, 0, /*$anchura, $altura*/ $datos[0], $datos[1], $datos[0], $datos[1]);
	
			// guardar la nueva imagen redimensionada donde indicia $destino
			if($datos[2]==1)
				imagegif($newImage,$destino);
			if($datos[2]==2)
				if($exif){
					if(isset($exif['THUMBNAIL'])){
						switch($exif['THUMBNAIL']['Orientation']) {
							case 3:
								$newImage = imagerotate($newImage, 180, 0);
								break;
							case 6:
								$newImage = imagerotate($newImage, -90, 0);
								break;
							case 8:
								$newImage = imagerotate($newImage, 90, 0);
								break;
						}
					}
				}
				imagejpeg($newImage,$destino,$jpgQuality);
			if($datos[2]==3)
				imagepng($newImage,$destino);
			// eliminamos la imagen temporal
			imagedestroy($newImage);
	
			return true;
		}
		return false;
	}
}
?>