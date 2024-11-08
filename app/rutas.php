<?php
	class rutas_staticas
	{
		function get_ruta_img_planta($id_planta,$test){
			if($test ==1){
				switch ($id_planta) {
					case '1':
						return "C:\inetpub\wwwroot\\test.janesville.com.mx\upn_v2\\files\\lpa\\test\\";
						break;
					case '2':
						return "C:\inetpub\wwwroot\\test.janesville.com.mx\upn_v2\\files\\lpa\\test\\";
						break;
				}
			}else{
				switch ($id_planta) {
					case '1':
						return "C:\inetpub\wwwroot\\test.janesville.com.mx\upn_v2\\files\\lpa\\";
						break;
					case '2':
						return "C:\inetpub\wwwroot\\test.janesville.com.mx\upn_v2\\files\\lpa\\";
						break;
				}
			}
		}
		function get_ruta_web_imagenes($id_planta,$test){
			if($test ==1){
				switch ($id_planta) {
					case '1':
						return "http://test.janesville.com.mx/upn_v2/files/lpa/test/";
						break;
					case '2':
						return "http://test.janesville.com.mx/upn_v2/files/lpa/test/";
						break;
				}
			}else{
				switch ($id_planta) {
					case '1':
						return "http://test.janesville.com.mx/upn_v2/files/lpa/";
						break;
					case '2':
						return "http://test.janesville.com.mx/upn_v2/files/lpa/";
						break;
				}
			}
			
		}
		function boton_go_to_acciones($id_planta,$test){
			if($test ==1){
				switch ($id_planta) {
					case '1':
						return "";
						break;
					case '2':
						return "";
						break;
				}
			}else{
				switch ($id_planta) {
					case '1':
						return "<a href='http://test.janesville.com.mx/upn_v2/app/lpa/hallazgos/'><i>Ver acciones</i></a>";
						break;
					case '2':
						return "<a href='http://test.janesville.com.mx/upn_v2/app/lpa/hallazgos/'><i>Ver acciones</i></a>";
						break;
				}
			}
		}
		function db($id_planta){
			switch ($id_planta) {
				case '1':
					return "Apps_Celaya";
					break;
				case '2':
					return "Apps_Celaya";
					break;
				default:
					return "Apps_Celaya";
					break;
			}
		}
	}
	
?>