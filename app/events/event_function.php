<?php
	class events extends ini
	{
		function get_calendario_msa($id_planta){
			$sql = "SELECT * FROM MSA_V_Calendario WHERE MONTH(Fecha_Inicio)='".date("m")."' AND YEAR(Fecha_Inicio)='".date("Y")."'";
			$this->conexion($id_planta);
			$res = sqlsrv_query($this->conexion,$sql);
			while ($fila=sqlsrv_fetch_object($res)) {
				$fila->No_Month 	= $fila->Fecha_Inicio->format("n");
				$fila->Fecha_Inicio = $fila->Fecha_Inicio->format("Y-m-d");
				$co[] = $fila;
			}
			sqlsrv_close($this->conexion);
			if(isset($co)) return $co;
		}
	}
	class ini 
	{
		public $conexion=null;
		function conexion($id_planta){
			$rutas = new rutas_staticas();
			$user=$this->decrypt("NGR1WmVDeVVCejByZlM2dk5LTEx2UT09OjrHC4pMP1pGfRxz/E5efNDO","sql_user");
			$pass=$this->decrypt("K09mcGJxRVNTR3N2TGNxVDE2MG0rdz09Ojq02FvMC6yOlq+LxCwDFINN","sql_pass");
			$Servidor = "DATABASE\TEST";
			$db = $rutas->db($id_planta);
			$info = array('Database' => $db, 'UID' => $user, 'PWD' => $pass, "CharacterSet"=>"UTF-8");
			$this->conexion = sqlsrv_connect($Servidor, $info);
		}
		function decrypt($data, $key)
		{
			list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
			return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
		}
	}
	
?>