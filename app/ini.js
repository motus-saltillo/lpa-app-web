(function () {
	const url = "app/ini_case.php";
	var datos = {};
	const main_view = $("#main_view");
	inicio = {
		ini: function () {
			this.show_menu[datos.inicio.menu]();
			$("#title_add_text").remove();
		},
		UrlExists: async(url)=>
		{
			var http = new XMLHttpRequest();
			http.open('HEAD', url, false);
			http.send();
			return http.status != 404;
		},
		show_menu: {
			plantas: function () {
				let html = '<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_menu.niveles_uruapan()"><b>SALTILLO</b></button>'
					//'<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_menu.plantas_prueba()"><b>TEST</b></button>';
				$("#menu_inicio").html(html);
				config.change_datos_inicio("menu", "plantas");
				iniComponent.back = null;
			},
			niveles_uruapan: function () {
				let html = '<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(1,1);"><b>Nivel 1</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(1,2);"><b>Nivel 2</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(1,3);"><b>Nivel 3</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(1,4);"><b>Nivel S</b></button>';
				$("#menu_inicio").html(html);
				config.change_datos_inicio("menu", "niveles_uruapan");
				iniComponent.back = inicio.show_menu.plantas;
			},
			niveles_celaya: function () {
				let html = '<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(2,1);"><b>Nivel 1</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(2,2);"><b>Nivel 2</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(2,3);"><b>Nivel 3</b></button>' +
					'<button class="btn blue darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="inicio.show_calendar(2,4);"><b>Nivel S</b></button>';
				$("#menu_inicio").html(html);
				config.change_datos_inicio("menu", "niveles_celaya");
				iniComponent.back = inicio.show_menu.plantas;
			},
			plantas_prueba: function () {
				let html = '<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="test.select_planta(1);"><b>SPARTANBURG</b></button>';
				$("#menu_inicio").html(html);
				config.change_datos_inicio("menu", "plantas_prueba");
				iniComponent.back = inicio.show_menu.plantas;
			},
			niveles_prueba: function () {
				let html = '<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="test.show_calendar(1);"><b>Nivel 1</b></button>' +
					'<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="test.show_calendar(2);"><b>Nivel 2</b></button>' +
					'<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="test.show_calendar(3);"><b>Nivel 3</b></button>' +
					'<button class="btn red darken-3 waves-effect btn-large col s12 m10 offset-m1 my-2" onclick="test.show_calendar(4);"><b>Nivel S</b></button>';
				$("#menu_inicio").html(html);
				config.change_datos_inicio("menu", "niveles_prueba");
				iniComponent.back = inicio.show_menu.plantas_prueba;
			}
		},
		show_calendar: function (planta, nivel) {
			datos.id_planta = planta;
			datos.id_nivel = nivel;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			view.change.calendario();
		}
	}
	calendario = {
		ini: function () {
			$this = this;
			iniComponent.back = view.change.inicio;
			iniComponent.validar_tablets();
			calendario.reset_session_variables();
			$("#title_add_text").remove();
			$("#text_logo").append("<span id='title_add_text'> Nivel " + datos.id_nivel + "</span>");
			$this.set_title();
			

		},
		reset_session_variables: function(){
			delete datos.id_audit_selected;
			delete datos.lista_de_hallazgos;
			delete datos.id_pregunta_reporte_final;
			delete datos.id_pregunta_reporte_final;
			delete datos.id_hallazgo_editar_final;
			delete datos.view_in_auditoria;
			delete datos.id_pregunta_reporte;
			delete datos.id_issue;
			sessionStorage.setItem("datos", JSON.stringify(datos));
		},
		set_title: function () {
			let label_title = $("#label_title");
			switch (datos.id_nivel) {
				case 1:
					label_title.html(moment().subtract(6, "hours").format("DD/MM/YYYY"));
					break;
				case 2:
					label_title.html("Semana " + moment().format("WW"));
					break;
				case 3:
				case 4:
					label_title.html(moment().format("MMMM"));
					break;
				default:
					break;
			}
		},
		select_audit: function (id) {
			datos.id_audit_selected = id;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			$this.validar_auditoria();
		},
		validar_auditoria: function () {
			let data = { tipo: 2, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == 0 || json.estado == 1) {
					//if(json.login == true){
					view.change.auditoria();
					// }else{
					// 	view.change.login();
					// }
				}
			}, "json");
		},
		get_lista_auditorias: function () {
			let data = { tipo: 1, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				let html = "";
				datos.lista_auditorias = json;
				sessionStorage.setItem("datos", JSON.stringify(datos));
				$.each(json, function (index, value) {
					if (datos.id_nivel == 4) {
						html += $this.estrucutura_lista_auditoria(index, value.auditor, value.issue, value.estado);
					} else {
						html += $this.estrucutura_lista_auditoria(index, value.auditor, value.linea, value.estado);
					}
				});
				$("#container_lpa").html(html);
			}, "json").done(function () {
				if (datos.validacion_tablet.valid == 0) {
					$("#text-view-access-denied").addClass("hide");
				} else {
					$("#text-view-access-denied").removeClass("hide");
				}
			});
		},
		estrucutura_lista_auditoria: function (id_auditoria, nombre, linea, estado) {
			let short_name = "";
			if (typeof (nombre.split(' ')[1]) == "undefined") {
				short_name = nombre.split(' ')[0][0] + nombre.split(' ')[0][1];
			} else {
				short_name = nombre.split(' ')[0][0] + nombre.split(' ')[1][0];
			}
			let html = '<div class="card m-3 animated fadeIn " ' + calendario.get_event_onclick(id_auditoria) + '>' +
				'<div class="card-body p-0 ">' +
				'<div class="row m-0 p-0">' +
				'<div class="col s2 m2 xl1 ' + calendario.validar_estado_auditoria(estado) + ' white-text p-3 center-align">' + short_name + '</div>' +
				'<div class="col s6 m4 xl5 p-3"><b>' + nombre + '</b></div>' +
				'<div class="col s4 m5 xl5 p-3 right-align"><b>' + (linea || " ")+ '</b></div>' +
				'<div class="col s12 m1 xl1 p-3 hide-on-small-only center-align"></div>' +
				'</div>' +
				'</div>' +
				'</div>';
			return html;
		},
		validar_acceso_tablet: function () {
			if (datos.validacion_tablet.niveles == null) {
				datos.validacion_tablet.valid = 0;
			}else{
				if (typeof (datos.validacion_tablet.niveles[datos.id_nivel]) != "undefined") {
					if (datos.validacion_tablet.tablets != null) {
						if (typeof (datos.validacion_tablet.tablets[datos.validacion_tablet.ip]) != "undefined") {
							datos.validacion_tablet.valid = 0;
						} else {
							datos.validacion_tablet.valid = 1;
						}
					} else {
						datos.validacion_tablet.valid = 1;
					}
				} else {
					datos.validacion_tablet.valid = 0;
				}
			}
			calendario.get_lista_auditorias();
		},
		get_event_onclick: function(id_auditoria){
			if(datos.validacion_tablet.valid ==0){
				return 'onclick="calendario.select_audit(' + id_auditoria + ')"';
			}else{
				return '';
			}
		},
		validar_estado_auditoria: function (estado) {
			switch (parseInt(estado)) {
				case 0:
					return " blue darken-3";
				case 1:
					return " orange darken-3";
				case 2:
					return " grey darken-3";
				case 3:
					return " red darken-3";
			}
		},
	}
	login = {
		ini: function () {
			$this = this;
			$this.set_events();
			$this.datos_header();
			iniComponent.back = view.change.calendario;
		},
		validar_login: function () {
			let data = { tipo: 3, id_user: datos.lista_auditorias[datos.id_audit_selected].id_user, password: $("#login_password").val(), id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json == true) {
					view.change.auditoria();
				} else {
					$("#login_password").addClass("invalid");
				}
			}, "json").done(function () {
				$("#form_login").find("[type=submit]").removeAttr("disabled").html("LOGIN");
			});

		},
		datos_header: function () {
			$("#title_add_text").remove();
			$("#login_label_name_auditor").html(datos.lista_auditorias[datos.id_audit_selected].auditor);
		},
		set_events: function () {
			$("#form_login").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				$("#form_login").find("[type=submit]").append(" <i class='fas fa-spinner fa-spin'></i>").attr("disabled", "disabled");
				$this.validar_login();
			});
		}
	}
	auditoria = {
		ini: function () {
			$this = this;
			$this.get_lista_departamentos();
			$this.validar_inicializacion();
			$this.get_lista_de_hallazgos();
			$this.ini_components();
			iniComponent.back = view.change.calendario;
			$this.set_events();
		},
		set_pp_en_auditoria: function(){
			let fn=function(){
				let data = { tipo: 52, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel, id_linea: datos.lista_auditorias[datos.id_audit_selected].id_linea }
				$.post(url, data, function (json) {
					if(typeof(json)=='string'){console.log(json); return false;}
					if (json.estado == true) {
						if(json.preguntas==0){
							$this.finalizar_auditoria();
						}else{
							$this.validar_inicializacion();
						}
						$("#modal_set_pp").modal("close");			
					}
				},'json');
			}
			$("#modal_set_pp").modal("open");
			$("#modal_set_pp").find("#btn-confirm").unbind("click").on("click",fn);
		},
		eliminar_hallazgo_final: function(id_hallazgo,id_reporte){
			let fn = function(){
				let data = { tipo: 51, id_hallazgo: id_hallazgo, id_reporte: id_reporte ,id_planta:datos.id_planta}
				$.post(url, data, function (json) {
					if(typeof(json)=='string'){console.log(json); return false;}
					if(json.estado==true){
						delete datos.lista_de_hallazgos[id_reporte][id_hallazgo];
						if(Object.keys(datos.lista_de_hallazgos[id_reporte]).length ==0){
							delete datos.lista_de_hallazgos[id_reporte];
						}
						auditoria.show_hallazgos_encontrados();
						console.log(datos.lista_de_hallazgos);

					}
				},'json').done(function(){
					$("#modal_confirm").modal("close");
				});
			}
			$("#modal_confirm").modal("open");
			$("#modal_confirm").find("#btn-confirm").unbind("click").on("click",fn);
		},
		subir_imagen_evidencia_editar_final: function () {
			$("[editar-tomar-foto-final]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_editar_hallazgo_final")[0]);
			$form_data.append("tipo", 23);
			$form_data.append("id_hallazgo", datos.id_hallazgo_editar_final);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte_final);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != null) {
						if (json.imagen != null) {
							$("[editar-tomar-foto-final]").addClass("hide");
							$("[editar-view-foto-final]").find("img").attr("src", json.imagen);
							$("[editar-view-foto-final]").removeClass("hide");
							datos.lista_de_hallazgos[datos.id_pregunta_reporte_final][datos.id_hallazgo_editar_final].imagen = json.imagen
						}
					}
				}
			});
		},
		save_no_conformidad_final: function () {
			if ($("#editar_final_finding_accion_nc").prop("checked")) { nc = 1; } else { nc = 0; }
			let data = { tipo: 22, hallazgo: $("#editar_final_finding_hallazgo").val(), accion: $("#editar_final_finding_accion").val(), id_departamento: $("#editar_final_finding_departamento").val(), nc: nc, id_hallazgo: datos.id_hallazgo_editar_final, id_planta: datos.id_planta }

			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.get_lista_de_hallazgos();
					setTimeout(() => {
						$this.show_hallazgos_encontrados();
						iniComponent.back();
					}, 100);
				}
			}, "json");
		},
		editar_hallazgo_final: function (id_hallazgo, id_reporte) {
			datos.id_pregunta_reporte_final = id_reporte;
			datos.id_hallazgo_editar_final = id_hallazgo;
			let temp = datos.lista_de_hallazgos[id_reporte][id_hallazgo];
			let temp_id_issue = 0;
			for (var k in $this.lista_preguntas) {
				for (var l in $this.lista_preguntas[k].preguntas) {
					if (l == id_reporte) {
						temp_id_issue = k;
					}
				}
			}
			let pregunta = $this.lista_preguntas[temp_id_issue].preguntas[id_reporte];
			$("#editar_final_finding_no_pregunta").html(pregunta.no_pregunta);
			$("#editar_final_finding_titulo").html(pregunta.titulo);
			$("#editar_final_finding_pregunta").html(pregunta.pregunta);
			$("#form_editar_hallazgo_final").find("[type=submit]").html("Save").removeAttr("disabled");
			$("#form_editar_final_finding,#info_preugnta_finalizar").removeClass("hide");

			$("#t_hallazgos_encontrados,#finalizar_hallazgos_inicio").addClass("hide");
			$("#editar_final_finding_hallazgo").val(temp.hallazgo);
			$("#editar_final_finding_accion").val(temp.accion);
			$("#editar_final_finding_departamento").val(temp.departamento);
			if (temp.nc == 1) {
				$("#editar_final_finding_accion_nc").prop("checked", true);
			} else {
				$("#editar_final_finding_accion_nc").prop("checked", false);
			}
			if (pregunta.h_a == 0) {
				$(".insert_finding_accion_nc").prop("checked", true).attr("disabled", "disabled").attr("checked", "");
				$(".insert_finding_accion_nc_lanbel").removeClass("hide");
			} else {
				$(".insert_finding_accion_nc").removeAttr("disabled").removeAttr("checked");
				$(".insert_finding_accion_nc_lanbel").addClass("hide");
			}
			if (temp.imagen != null) {
				var c_canvas = document.getElementById('editar_final_canvas');
				var context = c_canvas.getContext('2d');
				var img = new Image();
				img.onload = function () {
					var imgWidth = img.naturalWidth,
						imgHeight = img.naturalHeight;
					console.log(imgWidth, imgHeight);
					$this.calcular_canvas_imagen(imgWidth, imgHeight, 200, c_canvas);
					context.drawImage(this, 0, 0, c_canvas.width, c_canvas.height);
				}
				img.src = temp.imagen;
				$("[editar-view-foto-final]").removeClass("hide");
				$("[editar-tomar-foto-final]").addClass("hide");
			} else {
				$("[editar-view-foto-final]").addClass("hide");
				$("[editar-tomar-foto-final]").removeClass("hide");
			}
			iniComponent.back = auditoria.close_editar_hallazgo_final;
			$('select').formSelect();
			M.updateTextFields();
		},
		close_editar_hallazgo_final: function () {
			$("#form_editar_final_finding,#info_preugnta_finalizar").addClass("hide");
			$("#t_hallazgos_encontrados,#finalizar_hallazgos_inicio").removeClass("hide");
			iniComponent.back = auditoria.show_view.lista_preguntas_hallazgos_encontrados
		},
		show_hallazgos_encontrados: function () {
			let html = "";
			if (Object.keys(datos.lista_de_hallazgos).length == 0 || datos.lista_de_hallazgos==null) {
				$this.show_view.lista_pregunta_auditorias();
			}
			$.each(datos.lista_de_hallazgos, function (index, value) {
				$.each(value, function (index2, value2) {
					let nc = "<td class='red darken-3 white-text weight-6 center-align'> N </td>";
					if (value2.nc == 1) {
						nc = "<td class='green darken-3 white-text weight-6 center-align'> NC </td>";
					}
					if($(window).width() <= 600){
						html += '<tr>' + nc +
							'	<td colspan=2>' +
							'		<b><p>' + value2.hallazgo + '</p></b>' +
							'	</td>' +
							'	<td class="right"><div class="btn-small mr-1" onclick="auditoria.editar_hallazgo_final(' + index2 + ',' + index +')"><span class="material-icons p-1">create</span></div>' +
							'   <div class="btn-small red darken-3 mr-1" onclick="auditoria.eliminar_hallazgo_final(' + index2 + ',' + index + ')"><span class="material-icons p-1">remove_circle_outline</span></div> </td>' +
							'</tr>';
					}else{
						html += '<tr>' + nc +
							'	<td>' +
							'		<b><p>' + value2.hallazgo + '</p></b>' +
							'		<label class="weight-7">' + value2.accion + '</label>' +
							'	</td>' +
							'	<td><label><b>' + datos.lista_departamentos[value2.departamento].name + '</b></label></td>' +
							'	<td class="right"><div class="btn-small mr-1" onclick="auditoria.editar_hallazgo_final(' + index2 + ',' + index +')"><span class="material-icons p-1">create</span></div>' +
							'   <div class="btn-small red darken-3 mr-1" onclick="auditoria.eliminar_hallazgo_final(' + index2 + ',' + index + ')"><span class="material-icons p-1">remove_circle_outline</span></div> </td>' +
							'</tr>';
					}
				});
			});
			$("#t_hallazgos_encontrados").html(html);
		},
		validar_finalizado: function () {
			finalizado = true;
			for (var k in $this.lista_preguntas) {
				for (var l in $this.lista_preguntas[k].preguntas) {
					if ($this.lista_preguntas[k].preguntas[l].estado == 0) {
						finalizado = false;
					}
				}
			}
			if (finalizado == true) {
				$this.finalizar_auditoria();
			}
		},
		get_lista_de_hallazgos: function () {
			let data = { tipo: 21, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json == null) {
					datos.lista_de_hallazgos = {};
				} else {
					datos.lista_de_hallazgos = json;
				}
			}, "json");
		},
		update_data_finding: function () {
			let nc = 0;
			if ($("#editar_finding_accion_nc").prop("checked") == true) {
				nc = 1;
			}
			let data = { tipo: 18, id_hallazgo: datos.id_hallazgo_editar, hallazgo: $("#editar_finding_hallazgo").val(), accion: $("#editar_finding_accion").val(), id_dep: $("#editar_finding_departamento").val(), nc: nc, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }

				if (json.estado == true) {
					$("#form_alta_finding").addClass("hide");
					$("#form_editar_finding").addClass("hide");
					$this.get_lista_de_hallazgos();

				}
			}, "json").done(function () {
				auditoria.show_view.lista_preguntas_show_no_conformidad();
			});
		},
		editar_hallazgo: function (id) {
			datos.id_hallazgo_editar = id;
			let info_hallazgo = datos.lista_de_hallazgos[datos.id_pregunta_reporte][id];
			$("#editar_finding_hallazgo").val(info_hallazgo.hallazgo);
			$("#editar_finding_accion").val(info_hallazgo.accion);
			$("#editar_finding_departamento").val(info_hallazgo.departamento);
			if (info_hallazgo.nc == 1) {
				$("#editar_finding_accion_nc").prop("checked", true);
			} else {
				$("#editar_finding_accion_nc").prop("checked", false);
			}
			console.log(info_hallazgo);
			if (info_hallazgo.imagen != null) {
				var c_canvas = document.getElementById('editar_canvas');
				var context = c_canvas.getContext('2d');
				var img = new Image();
				img.onload = function () {
					var imgWidth = img.naturalWidth,
						imgHeight = img.naturalHeight;
					console.log(imgWidth, imgHeight);
					$this.calcular_canvas_imagen(imgWidth, imgHeight, 200, c_canvas);
					context.drawImage(this, 0, 0, c_canvas.width, c_canvas.height);
				}
				img.src = info_hallazgo.imagen;
				$("[editar-view-foto]").removeClass("hide");
				$("[editar-view-foto]").find("img").attr("src", info_hallazgo.imagen);
				$("[editar-tomar-foto]").addClass("hide");
			} else {
				$("[editar-view-foto]").addClass("hide");
				$("[editar-view-foto]").find("img").attr("src", "");
				$("[editar-tomar-foto]").removeClass("hide");
			}
			$this.show_view.lista_preguntas_show_editar_hallazgo();
			M.updateTextFields();
			$('select').formSelect();
		},
		save_no_conformidad: function () {
			if ($("#insert_finding_departamento").val() == null) {
				alert("Selecciona un departamento");
				$("#insert_finding_departamento").addClass("invalid");
				return false;
			}
			if ($("#insert_finding_accion_nc").prop("checked")) { nc = 1; } else { nc = 0; }
			let data = { tipo: 16, hallazgo: $("#insert_finding_hallazgo").val(), accion: $("#insert_finding_accion").val(), id_departamento: $("#insert_finding_departamento").val(), id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta, id_auditoria: datos.id_audit_selected, nc: nc, id_pregunta: $this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].id_pregunta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.get_info_no_conformidad();
				}
			}, "json");
		},
		eliminar_imagen_editar_final: function () {
			let data = { tipo: 20, id_hallazgo: datos.id_hallazgo_editar_final, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$("[editar-view-foto-final]").addClass("hide");
				$("[editar-tomar-foto-final]").find("i").removeClass("fa-spinner fa-spin");
				$("[editar-view-foto-final]").find("img").attr("src", "");
				$("[editar-tomar-foto-final]").removeClass("hide");
				datos.lista_de_hallazgos[datos.id_pregunta_reporte_final][datos.id_hallazgo_editar_final].imagen = null;
			}, "json");
		},
		eliminar_imagen_editar: function () {
			let data = { tipo: 20, id_hallazgo: datos.id_hallazgo_editar, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$("[editar-view-foto]").addClass("hide");
				$("[editar-tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
				$("[editar-view-foto]").find("img").attr("src", "");
				$("[editar-tomar-foto]").removeClass("hide");
				datos.lista_de_hallazgos[datos.id_pregunta_reporte][datos.id_hallazgo_editar].imagen = null;
			}, "json");
		},
		eliminar_imagen: function () {
			let data = { tipo: 15, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$("[view-foto]").addClass("hide");
					$("[tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
					$("[view-foto]").find("img").attr("src", "");
					$("[tomar-foto]").removeClass("hide");
				}
			}, "json");
		},
		no_conformidad: function () {
			$this.show_view.lista_preguntas_show_no_conformidad();
		},
		get_info_no_conformidad: function () {
			$("#form_new_finding")[0].reset();
			let data = { tipo: 13, id_pregunta: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				html = "";
				if (json.hallazgos != null) {
					datos.lista_de_hallazgos[datos.id_pregunta_reporte] = json.hallazgos;
					$.each(json.hallazgos, function (index, value) {
						let nc = "<td class='red darken-3 white-text weight-6 center-align' style='width:62px;'> N </td>";
						if (value.nc == 1) {
							nc = "<td class='green darken-3 white-text weight-6 center-align' style='width:62px;'> NC </td>";
						}
						if($(window).width() <= 600){
							html += '<tr>' + nc +
								'	<td colspan=2>' +
								'		<b><p>' + value.hallazgo + '</p></b>' +
								'	</td>' +
								'	<td class="right"><div class="btn-small mr-1" onclick="auditoria.editar_hallazgo(' + index + ')"><span class="material-icons p-1">create</span></div></td>' +
								'</tr>';
						}else{
							html += '<tr>' + nc +
								'	<td>' +
								'		<b><p>' + value.hallazgo + '</p></b>' +
								'		<label class="weight-7">' + value.accion + '</label>' +
								'	</td>' +
								'	<td><label><b>' + datos.lista_departamentos[value.departamento].name + '</b></label></td>' +
								'	<td class="right"><div class="btn-small mr-1" onclick="auditoria.editar_hallazgo(' + index + ')"><span class="material-icons p-1">create</span></div></td>' +
								'</tr>';
						}
						
					});
					$("#lista_de_hallazgos > tbody").html(html);
					$("#form_alta_finding").addClass("hide");
					$("#lista_de_hallazgos").removeClass("hide");
				} else {
					$("#form_alta_finding").removeClass("hide");
					$("#lista_de_hallazgos").addClass("hide");
				}
				if (json.imagenes != null) {
					var c_canvas = document.getElementById('canvas');
					var context = c_canvas.getContext('2d');
					var img = new Image();
					img.onload = function () {
						var imgWidth = img.naturalWidth,
							imgHeight = img.naturalHeight;
						console.log(imgWidth,imgHeight);
						$this.calcular_canvas_imagen(imgWidth, imgHeight, 200, c_canvas);
						context.drawImage(this, 0, 0, c_canvas.width, c_canvas.height);
					}
					img.src = json.imagenes;
					$("[tomar-foto]").addClass("hide");
					$("[view-foto]").removeClass("hide");
				} else {
					$("[view-foto]").addClass("hide");
					$("[tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
					$("[tomar-foto]").removeClass("hide");
				}
			}, "json").done(function () {
				let info_pregunta = $this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte];
				if (info_pregunta.h_a == 0) {
					$(".insert_finding_accion_nc").prop("checked", true).attr("disabled", "disabled").attr("checked", "");
					$(".insert_finding_accion_nc_lanbel").removeClass("hide");
				} else {
					$(".insert_finding_accion_nc").prop("checked", false).removeAttr("disabled").removeAttr("checked");
					$(".insert_finding_accion_nc_lanbel").addClass("hide");
				}
				$("#new_finding_no_pregunta").html(info_pregunta.no_pregunta);
				$("#new_finding_titulo").html(info_pregunta.titulo);
				$("#new_finding_pregunta").html(info_pregunta.pregunta);
				$("#new_finding_importancia").html(info_pregunta.importancia);
				$("#new_finding_plan_de_reaccion").html(info_pregunta.plan_r);
			});
		},
		set_na: function () {
			$("#lista_preguntas_info_pregunta").find("button").attr("disabled", "disabled");
			$("#btn-set-na").html("<i class='fas fa-spinner fa-spin'></i>");
			let data = { tipo: 11, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					iniComponent.back();
				}
			}, "json").done(function () {
				$("#lista_preguntas_info_pregunta").find("button").removeAttr("disabled");
				$("#btn-set-na").html("N/A");
			});
		},
		set_ok: function () {
			$("#lista_preguntas_info_pregunta").find("button").attr("disabled", "disabled");
			$("#btn-set-s").html("<i class='fas fa-spinner fa-spin'></i>");
			let data = { tipo: 9, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					iniComponent.back();
				}
			}, "json").done(function () {
				$("#lista_preguntas_info_pregunta").find("button").removeAttr("disabled");
				$("#btn-set-s").html("S");
			});
		},
		set_nok: function () {
			let data = { tipo: 10, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					auditoria.show_view.lista_pregunta_auditorias();
				}
			}, "json");
		},
		cambiar_linea: function () {
			let data = { tipo: 24, id_auditoria: datos.id_audit_selected, id_nivel: datos.id_nivel, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					datos.lista_auditorias[datos.id_audit_selected].id_linea = json.id_linea;
					datos.lista_auditorias[datos.id_audit_selected].linea = json.linea;
					$this.validar_inicializacion();
				}
			}, "json");
		},
		validar_inicializacion: function () {
			let data = { tipo: 4, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == 0) {
					auditoria.show_view.opciones_auditoria();
					/** ========== Recordatorio para Segueridad e higiene ============= */
					if(datos.id_planta===2){
						$("#view_alert_higiene").removeClass("hide");
					}else{
						$("#view_alert_higiene").addClass("hide");
					}
					/*******************************************************************/
					if (datos.id_nivel == 2) {
						if (json.cambios == 0) {
							$("#opciones_inicio_cambiar_linea").unbind("click").on("click", function () {
								$this.cambiar_linea();
							}).removeAttr("disabled");
						} else {
							$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
						}
					} else if (datos.id_nivel == 3) {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
						// $("#opciones_inicio_cambiar_linea").unbind("click").on("click", function () {
						// 	$this.cambiar_linea();
						// }).removeAttr("disabled");
					} else if (datos.id_nivel == 4) {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
					} else {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
					}

				} else if (json.estado == 1 && json.auditado == null) {
					if (json.set_pp == null && datos.id_nivel!=4) {
						$("#marcar_pp_all").removeClass("hide").attr("onclick", "auditoria.set_pp_en_auditoria()");
					} else {
						$("#marcar_pp_all").addClass("hide").removeAttr("onclick");
					}
					auditoria.show_view.insert_name_auditor();
				} else {
					$this.get_info_auditoria();
					if (json.set_pp == null && datos.id_nivel != 4) {
						$("#marcar_pp_all").removeClass("hide").attr("onclick", "auditoria.set_pp_en_auditoria()");
					} else {
						$("#marcar_pp_all").addClass("hide").removeAttr("onclick");
					}
				}
				datos.lista_auditorias[datos.id_audit_selected].id_linea = json.lineas.id_linea;
				datos.lista_auditorias[datos.id_audit_selected].linea = json.lineas.name;
				$("#opciones_de_inicio_linea").html("<b>" + json.lineas.name + "</b>");
			}, "json")
		},
		change_id_view_pregunta: function (id_issue, id_reporte) {
			datos.id_pregunta_reporte = id_reporte;
			datos.id_issue = id_issue;
			$this.show_view.lista_preguntas_show_info();
		},
		set_info_pregunta: function () {
			if (typeof($this.lista_preguntas[datos.id_issue]) == "undefined"){
				iniComponent.back();
				return false;
			}
			let info_pregunta = $this.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte];
			$("#info_pregunta_no_pregunta").html(info_pregunta.no_pregunta);
			$("#info_pregunta_titulo").html(info_pregunta.titulo);
			$("#info_pregunta_pregunta").html(info_pregunta.pregunta);
			$("#info_pregunta_importancia").html(info_pregunta.importancia);
			$("#info_pregunta_plan_de_reaccion").html(info_pregunta.plan_r);
			$("#info_pregunta_evento").remove();
			if(info_pregunta.evento_nombre !=  null){
				$("#info_pregunta_no_pregunta").parent().append("<td id='info_pregunta_evento' class='w-5 pink darken-4 white-text center' onclick='" + info_pregunta.evento_funcion +"'><i class='material-icons'>calendar_today</i></td>")
			}
			iniComponent.back = auditoria.show_view.lista_pregunta_auditorias;
		},
		get_info_auditoria: function () {
			let data = { tipo: 8, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$this.lista_preguntas = json.preguntas_lpa;
				$this.show_preguntas();
				$("#lista_pregunta_auditoria_label_linea").html(datos.lista_auditorias[datos.id_audit_selected].linea);
			}, 'json').done(function () {
				if (typeof (datos.view_in_auditoria) == "undefined") {
					$this.show_view.lista_pregunta_auditorias();
				} else {
					$this.show_view[datos.view_in_auditoria]();
				}
			});
		},
		show_preguntas: function () {
			let html = '';
			let cont_issue = 0;
			$.each($this.lista_preguntas, function (index, value) {
				let cont = 0;
				for (var k in value.preguntas) {
					if (value.preguntas[k].estado == 0) {
						cont++;
					}
				}
				if (cont > 0) {
					html += '<div id="issue_' + index + '"> <div class="btn blue-grey darken-3 mt-3">' + value.name + '</div>';
					$.each(value.preguntas, function (index2, value2) {
						if (value2.estado == 0) {
							html += '<div class="card waves-effect p-0 col s12 m12 l12 my-1" onclick="auditoria.change_id_view_pregunta(' + index + ',' + index2 + ')">' +
								'<div class="card-content p-0">' +
								'<table id="tp_' + index2 + '">' +
								'<tr>' +
								'<td class="w-10 blue-grey white-text weight-7 p-2 center-align">' + value2.no_pregunta + '</td>' +
								'<td class="px-4">' +
								'<b>' + value2.titulo + '</b><br>' +
								'<label class="weight-7 " style="font-size: 1rem;">' + value2.pregunta + '</label>' +
								'</td>' +
								'</tr>' +
								'</table>' +
								'</div>' +
								'</div>';
						}
					});
					html += "</div>";
				} else {
					cont_issue++;
				}
			});
			$("#lista_pregunta_auditoria_contenedor").html(html);
		},
		finalizar_auditoria: function () {
			if (Object.keys(datos.lista_de_hallazgos).length == 0) {
				$this.set_fecha_finalizado();
			} else {
				auditoria.show_view.lista_preguntas_hallazgos_encontrados();
			}
		},
		set_fecha_finalizado: function () {
			let data = { tipo: 12, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, linea: datos.lista_auditorias[datos.id_audit_selected].linea, auditor: datos.lista_auditorias[datos.id_audit_selected].auditor, semana: datos.lista_auditorias[datos.id_audit_selected].semana_auditoria, correo: datos.lista_auditorias[datos.id_audit_selected].correo, id_nivel: datos.id_nivel }
			$("#modal_finalizado_label").html("Sending confirmation audit..");
			$("#modal_finalizando").modal("open");
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					console.log(Object.keys(datos.lista_de_hallazgos).length);
					if (Object.keys(datos.lista_de_hallazgos).length > 0) {
						$("#modal_finalizado_label").html("Sending findings email..");
						$this.send_email_hallazgos(data.linea, data.semana);
					} else {
						$("#modal_finalizado_label").html("Updating.");
						auditoria.update_datos_historial();
					}

				}
			}, "json");
		},
		update_datos_historial: ()=>{
			let data={tipo:53,id_planta:datos.id_planta, date: moment().format("YYYY-MM-DD HH:mm:ss")}
			console.log(data);
			$.post(url, data, function (json) {
				if(typeof(json)=='string'){console.log(json); return false;}},'json').done(function(){
				$("[finalizar-auditoria-final]").removeAttr("disabled").html("FINISH");
				$("#modal_finalizando").modal("close");
				view.change.calendario();
			});
		},
		send_email_hallazgos: function (linea, area) {
			let data = { tipo: 25, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, linea, area }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$("#modal_finalizado_label").html("Updating.");
				auditoria.update_datos_historial();
			}, 'json');
		},
		save_name_personal_acargo: function () {
			let data = { tipo: 7, name_acargo: $("#insert_name_personal_acargo").val(), id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.validar_inicializacion();
				} else {
					iniComponent.back();
				}
			}, 'json');
		},
		set_pp_auditoria: function () {
			let data = { tipo: 6, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel, id_linea: datos.lista_auditorias[datos.id_audit_selected].id_linea }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					if(json.preguntas == 0){
						$this.finalizar_auditoria();
					}else{
						$this.validar_inicializacion();
					}
				}
			}, "json");
		},
		iniciar_auditoria: function () {
			let data = { tipo: 5, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel, id_linea: datos.lista_auditorias[datos.id_audit_selected].id_linea, idIssue: datos.lista_auditorias[datos.id_audit_selected].id_issue }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$this.validar_inicializacion();
				}
			}, "json");
		},
		show_view: {
			opciones_auditoria: function () {
				$(".views_auditorias").addClass("hide");
				$("#opciones_auditoria").removeClass("hide");
				$this.set_restricciones_nivel();
			},
			insert_name_auditor: function () {
				$(".views_auditorias").addClass("hide");
				$("#insert_name_auditor").removeClass("hide");
			},
			lista_pregunta_auditorias: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_pregunta_auditorias").removeClass("hide");
				datos.view_in_auditoria = "lista_pregunta_auditorias";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				iniComponent.back = view.change.calendario;
				$this.validar_finalizado();
			},
			lista_preguntas_show_info: function () {
				$(".views_auditorias").addClass("hide");

				$("#lista_preguntas_show_info").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_show_info";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				$this.set_info_pregunta();
			},
			lista_preguntas_show_no_conformidad: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_preguntas_show_no_conformidad").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_show_no_conformidad";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				$this.get_info_no_conformidad();
				iniComponent.back = auditoria.show_view.lista_preguntas_show_info;
			},
			lista_preguntas_show_alta_no_conformidad: function () {
				$("#form_alta_finding").removeClass("hide");
				$("#form_editar_finding").addClass("hide");
				$("#lista_de_hallazgos").addClass("hide");
				$("#form_new_finding")[0].reset();
				iniComponent.back = auditoria.show_view.lista_preguntas_show_lista_findings;
			},
			lista_preguntas_show_lista_findings: function () {
				$("#form_alta_finding").addClass("hide");
				$("#form_editar_finding").addClass("hide");
				$("#lista_de_hallazgos").removeClass("hide");
				$("#form_update_finding")[0].reset();
				iniComponent.back = auditoria.show_view.lista_preguntas_show_info;
			},
			lista_preguntas_show_editar_hallazgo: function () {
				$("#form_editar_finding").removeClass("hide");
				$("#form_alta_finding").addClass("hide");
				$("#lista_de_hallazgos").addClass("hide");
				iniComponent.back = auditoria.show_view.lista_preguntas_show_lista_findings;
			},
			lista_preguntas_hallazgos_encontrados: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_preguntas_hallazgos_encontrados").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_hallazgos_encontrados";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				$this.show_hallazgos_encontrados();
				iniComponent.back = view.change.calendario;
			}
		},
		set_restricciones_nivel: function () {
			switch (datos.id_nivel) {
				case 1:
					$("#opciones_inicio_cambiar_linea").attr("disabled", "disabled");
					break;
				case 4:
					$("#opciones_inicio_cambiar_linea").attr("disabled", "disabled");
					$("#opciones_inicio_pp").attr("disabled", "disabled");
					break;
				default:
					break;
			}
		},
		subir_imagen_evidencia: function (id) {
			$("[tomar-foto]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_new_finding")[0]);
			$form_data.append("tipo", 14);
			$form_data.append("id", id);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != false) {
						$("[tomar-foto]").addClass("hide");
						$("[view-foto]").find("img").attr("src", json.estado);
						$("[view-foto]").removeClass("hide");
					}
					$('.materialboxed').materialbox();
				}
			});
		},
		calcular_canvas_imagen:(real_w, real_h, base,canvas) => {
			let altura = (real_h * base) / real_w;
			canvas.width = base;
			canvas.height = altura;
		},
		readURL: async(input,mini,full,fn_finish,upload)=>{
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					var canvas = document.getElementById(mini);
					var context = canvas.getContext('2d');
					var img = new Image();

					var full_canvas = document.getElementById(full);
					var full_context = full_canvas.getContext('2d');

					
					img.onload = function () {
						var imgWidth = img.naturalWidth,
							imgHeight = img.naturalHeight;

						$this.calcular_canvas_imagen(imgWidth, imgHeight, 200, canvas);
						$this.calcular_canvas_imagen(imgWidth, imgHeight, 800, full_canvas);
						
						context.drawImage(this, 0, 0, canvas.width, canvas.height);
						full_context.drawImage(this, 0, 0, full_canvas.width, full_canvas.height);
						var dataURL = full_canvas.toDataURL();
						upload(dataURL);
					}
					img.src = e.target.result
					fn_finish();
				}

				reader.readAsDataURL(input.files[0]); // convert to base64 string
			}
		},
		subir_imagen_base64: async (file,)=>{
			const json = await $.post(url, {tipo: 54, file, id_pregunta_reporte:datos.id_pregunta_reporte,id_planta:datos.id_planta},null,"json");
			console.log(json);
		},
		subir_imagen_evidencia_editar: function () {
			$("[editar-tomar-foto]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_update_finding")[0]);
			$form_data.append("tipo", 19);
			$form_data.append("id_hallazgo", datos.id_hallazgo_editar);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != null) {
						if (json.imagen != null) {
							$("[editar-tomar-foto]").addClass("hide");
							$("[editar-view-foto]").find("img").attr("src", json.imagen);
							$("[editar-view-foto]").removeClass("hide");
							datos.lista_de_hallazgos[datos.id_pregunta_reporte][datos.id_hallazgo_editar].imagen = json.imagen
						}
					}
				}
			});
		},
		set_events: function () {
			$("#modal_confirm").modal();
			$("#modal_set_pp").modal();
			$("#form_new_finding").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				
				$this.save_no_conformidad();
			});
			$("#form_editar_hallazgo_final").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				$(this).find("[type=submit]").append(" <i class='fas fa-spinner fa-spin'></i>").attr("disabled", "disabled");
				$this.save_no_conformidad_final();
			});
			$("#form_editar_finding").unbind("submit").on("submit", function (e) {
				e.preventDefault(),
					$this.update_data_finding();
			});
			$("#opciones_inicio_iniciar").unbind("click").on("click", function () {
				$(".btn_opciones_inicio").addClass("disabled", "disabled");
				$(this).append(" <i class='fas fa-spinner fa-spin'></i>");
				$this.iniciar_auditoria();
			});
			$("#opciones_inicio_pp").unbind("click").on("click", function () {
				$(".btn_opciones_inicio").addClass("disabled", "disabled");
				$(this).append(" <i class='fas fa-spinner fa-spin'></i>");
				$this.set_pp_auditoria();
			});
			$("#form_insert_name_personal_acargo").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				$(this).find("[type=submit]").append(" <i class='fas fa-spinner fa-spin'></i>").attr("disabled", "disabled");
				$this.save_name_personal_acargo();
			});
			$("[tomar-foto]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".captura_foto").unbind("change").on("change", function () {
				const input = $(this)[0];
				if ($(this)[0].files[0] != "undefined") {
					const fn = ()=>{
						$("[tomar-foto]").addClass("hide");
						$("[view-foto]").removeClass("hide");
					}
					const upload = async (file)=>{
						const json = await $.post(url, { tipo: 54, file, id_pregunta_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }, null, "json");
						console.log(json);
					}
					$this.readURL(input, "canvas", "full_canvas", fn, upload);
				} else {
					console.log("no tiene foto");
				}
			});
			$("[editar-tomar-foto]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".editar-captura_foto").unbind("change").on("change", function () {
				const input = $(this)[0];
				if ($(this)[0].files[0] != "undefined") {
					const fn = () => {
						$("[editar-tomar-foto]").addClass("hide");
						$("[editar-view-foto]").removeClass("hide");
					}
					const upload = async (file) => {
						const json = await $.post(url, { tipo: 55, file, id_hallazgo: datos.id_hallazgo_editar, id_pregunta_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }, null, "json");
						console.log(json);
					}
					$this.readURL(input, "editar_canvas", "editar_full_canvas", fn, upload);
				} else {
					console.log("no tiene foto");
				}
				// if ($(this)[0].files[0] != "undefined") {
				// 	$this.subir_imagen_evidencia_editar();
				// } else {
				// 	console.log("no tiene foto");
				// }
			});

			$("[editar-tomar-foto-final]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".editar_captura_foto_final").unbind("change").on("change", function () {
				const input = $(this)[0];
				if ($(this)[0].files[0] != "undefined") {
					const fn = () => {
						$("[editar-tomar-foto-final]").addClass("hide");
						$("[editar-view-foto-final]").removeClass("hide");
					}
					const upload = async (file) => {
						const json = await $.post(url, { tipo: 55, file, id_hallazgo: datos.id_hallazgo_editar_final, id_pregunta_reporte: datos.id_pregunta_reporte_final, id_planta: datos.id_planta }, null, "json");
						console.log(json);
					}
					$this.readURL(input, "editar_final_canvas", "editar_final_full_canvas", fn, upload);
					//$this.subir_imagen_evidencia_editar_final();
				} else {
					console.log("no tiene foto");
				}
			});
			$("[finalizar-auditoria-final]").unbind("click").on("click", function () {
				$this.set_fecha_finalizado();
				$(this).attr("disabled", "disabled").append(" <i class='fas fa-spinner fa-spin'></i>");
			});
		},
		ini_components: function () {
			$('select').formSelect();
			$('[data-length]').characterCounter();
			$('.modal').modal({ dismissible: false });
		},
		get_lista_departamentos: function () {
			let data = { tipo: 17, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				datos.lista_departamentos = json;
				let html = '<option value="" disabled selected>Departamento</option>';
				$.each(json, function (index, value) {
					html += "<option value='" + index + "'>" + value.name + "</option>";
				});
				$("#insert_finding_departamento, #editar_finding_departamento,#editar_final_finding_departamento").html(html);
				$('select').formSelect();
			}, "json");
		}
	}
	auditoria_test = {
		ini: function () {
			this.get_lista_departamentos();
			this.validar_inicializacion();
			this.get_lista_de_hallazgos();
			this.ini_components();
			iniComponent.back = view.change.calendario_test;
			this.set_events();
		},
		get_lista_departamentos: function () {
			let data = { tipo: 17, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				datos.lista_departamentos = json;
				let html = '<option value="" disabled selected>Departamento</option>';
				$.each(json, function (index, value) {
					html += "<option value='" + index + "'>" + value.name + "</option>";
				});
				$("#insert_finding_departamento, #editar_finding_departamento,#editar_final_finding_departamento").html(html);
				$('select').formSelect();
			}, "json");
		},
		subir_imagen_evidencia_editar_final: function () {
			$("[editar-tomar-foto-final]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_editar_hallazgo_final")[0]);
			$form_data.append("tipo", 44);
			$form_data.append("id_hallazgo", datos.id_hallazgo_editar_final);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte_final);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != null) {
						if (json.imagen != null) {
							$("[editar-tomar-foto-final]").addClass("hide");
							$("[editar-view-foto-final]").find("img").attr("src", json.imagen);
							$("[editar-view-foto-final]").removeClass("hide");
							datos.lista_de_hallazgos[datos.id_pregunta_reporte_final][datos.id_hallazgo_editar_final].imagen = json.imagen
						}
					}
				}
			});
		},
		save_no_conformidad_final: function () {
			if ($("#editar_final_finding_accion_nc").prop("checked")) { nc = 1; } else { nc = 0; }
			let data = { tipo: 42, hallazgo: $("#editar_final_finding_hallazgo").val(), accion: $("#editar_final_finding_accion").val(), id_dep: $("#editar_final_finding_departamento").val(), nc: nc, id_hallazgo: datos.id_hallazgo_editar_final, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.get_lista_de_hallazgos();
					setTimeout(() => {
						auditoria_test.show_hallazgos_encontrados();
						iniComponent.back();
					}, 100);
				}
			}, "json");
		},
		editar_hallazgo_final: function (id_hallazgo, id_reporte) {
			datos.id_pregunta_reporte_final = id_reporte;
			datos.id_hallazgo_editar_final = id_hallazgo;
			let temp = datos.lista_de_hallazgos[id_reporte][id_hallazgo];
			let temp_id_issue = 0;
			for (var k in auditoria_test.lista_preguntas) {
				for (var l in auditoria_test.lista_preguntas[k].preguntas) {
					if (l == id_reporte) {
						temp_id_issue = k;
					}
				}
			}
			let pregunta = auditoria_test.lista_preguntas[temp_id_issue].preguntas[id_reporte];
			$("#editar_final_finding_no_pregunta").html(pregunta.no_pregunta);
			$("#editar_final_finding_titulo").html(pregunta.titulo);
			$("#editar_final_finding_pregunta").html(pregunta.pregunta);
			$("#form_editar_hallazgo_final").find("[type=submit]").html("Save").removeAttr("disabled");
			$("#form_editar_final_finding,#info_preugnta_finalizar").removeClass("hide");

			$("#t_hallazgos_encontrados,#finalizar_hallazgos_inicio").addClass("hide");
			$("#editar_final_finding_hallazgo").val(temp.hallazgo);
			$("#editar_final_finding_accion").val(temp.accion);
			$("#editar_final_finding_departamento").val(temp.departamento);
			if (temp.nc == 1) {
				$("#editar_final_finding_accion_nc").prop("checked", true);
			} else {
				$("#editar_final_finding_accion_nc").prop("checked", false);
			}
			if (pregunta.h_a == 0) {
				$(".insert_finding_accion_nc").prop("checked", true).attr("disabled", "disabled").attr("checked", "");
				$(".insert_finding_accion_nc_lanbel").removeClass("hide");
			} else {
				$(".insert_finding_accion_nc").removeAttr("disabled").removeAttr("checked");
				$(".insert_finding_accion_nc_lanbel").addClass("hide");
			}
			if (temp.imagen != null) {
				$("[editar-view-foto-final]").removeClass("hide");
				$("[editar-tomar-foto-final]").addClass("hide");
			} else {
				$("[editar-view-foto-final]").addClass("hide");
				$("[editar-tomar-foto-final]").removeClass("hide");
			}
			iniComponent.back = auditoria_test.close_editar_hallazgo_final;
			$('select').formSelect();
			M.updateTextFields();
		},
		close_editar_hallazgo_final: function () {
			$("#form_editar_final_finding,#info_preugnta_finalizar").addClass("hide");
			$("#t_hallazgos_encontrados,#finalizar_hallazgos_inicio").removeClass("hide");
			iniComponent.back = auditoria_test.show_view.lista_preguntas_hallazgos_encontrados
		},
		show_hallazgos_encontrados: function () {
			let html = "";
			if (Object.keys(datos.lista_de_hallazgos).length == 0) {
				auditoria_test.show_view.lista_pregunta_auditorias();
			}
			$.each(datos.lista_de_hallazgos, function (index, value) {
				$.each(value, function (index2, value2) {
					let nc = "<td class='red darken-3 white-text weight-6 center-align'> N </td>";
					if (value2.nc == 1) {
						nc = "<td class='green darken-3 white-text weight-6 center-align'> NC </td>";
					}
					if($(window).width() <= 600){
						html += '<tr>' + nc +
							'	<td colspan=2>' +
							'		<b><p>' + value2.hallazgo + '</p></b>' +
							'	</td>' +
							'	<td class="right"><div class="btn-small mr-4" onclick="auditoria_test.editar_hallazgo_final(' + index2 + ',' + index + ',' + value2.id_item + ')"><span class="material-icons p-1">create</span></div></td>' +
							'</tr>';
					}else{
						html += '<tr>' + nc +
							'	<td>' +
							'		<b><p>' + value2.hallazgo + '</p></b>' +
							'		<label class="weight-7">' + value2.accion + '</label>' +
							'	</td>' +
							'	<td><label><b>' + datos.lista_departamentos[value2.departamento].name + '</b></label></td>' +
							'	<td class="right"><div class="btn-small mr-4" onclick="auditoria_test.editar_hallazgo_final(' + index2 + ',' + index + ',' + value2.id_item + ')"><span class="material-icons p-1">create</span></div></td>' +
							'</tr>';
					}
					
				});
			});
			$("#t_hallazgos_encontrados").html(html);
		},
		validar_finalizado: function () {
			finalizado = true;
			for (var k in auditoria_test.lista_preguntas) {
				for (var l in auditoria_test.lista_preguntas[k].preguntas) {
					if (auditoria_test.lista_preguntas[k].preguntas[l].estado == 0) {
						finalizado = false;
					}
				}
			}
			if (finalizado == true) {
				auditoria_test.finalizar_auditoria();
			}
		},
		get_lista_de_hallazgos: function () {
			let data = { tipo: 31, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json == null) {
					datos.lista_de_hallazgos = {};
				} else {
					datos.lista_de_hallazgos = json;
				}
			}, "json");
		},
		update_data_finding: function () {
			let nc = 0;
			if ($("#editar_finding_accion_nc").prop("checked") == true) {
				nc = 1;
			}
			let data = { tipo: 42, id_hallazgo: datos.id_hallazgo_editar, hallazgo: $("#editar_finding_hallazgo").val(), accion: $("#editar_finding_accion").val(), id_dep: $("#editar_finding_departamento").val(), nc: nc, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }

				if (json.estado == true) {
					$("#form_alta_finding").addClass("hide");
					$("#form_editar_finding").addClass("hide");
					auditoria_test.get_lista_de_hallazgos();

				}
			}, "json").done(function () {
				auditoria_test.show_view.lista_preguntas_show_no_conformidad();
			});
		},
		editar_hallazgo: function (id) {
			datos.id_hallazgo_editar = id;
			let info_hallazgo = datos.lista_de_hallazgos[datos.id_pregunta_reporte][id];
			$("#editar_finding_hallazgo").val(info_hallazgo.hallazgo);
			$("#editar_finding_accion").val(info_hallazgo.accion);
			$("#editar_finding_departamento").val(info_hallazgo.departamento);
			if (info_hallazgo.nc == 1) {
				$("#editar_finding_accion_nc").prop("checked", true);
			} else {
				$("#editar_finding_accion_nc").prop("checked", false);
			}
			if (info_hallazgo.imagen != null) {
				assad
				$("[editar-view-foto]").removeClass("hide");
				$("[editar-view-foto]").find("img").attr("src", info_hallazgo.imagen);
				$("[editar-tomar-foto]").addClass("hide");
			} else {
				$("[editar-view-foto]").addClass("hide");
				$("[editar-view-foto]").find("img").attr("src", "");
				$("[editar-tomar-foto]").removeClass("hide");
			}
			auditoria_test.show_view.lista_preguntas_show_editar_hallazgo();
			M.updateTextFields();
			$('select').formSelect();
		},
		save_no_conformidad: function () {
			if ($("#insert_finding_accion_nc").prop("checked")) { nc = 1; } else { nc = 0; }
			let data = { tipo: 37, hallazgo: $("#insert_finding_hallazgo").val(), accion: $("#insert_finding_accion").val(), id_departamento: $("#insert_finding_departamento").val(), id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta, id_auditoria: datos.id_audit_selected, nc: nc, id_pregunta: auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].id_pregunta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.get_info_no_conformidad();
				}
			}, "json");
		},
		eliminar_imagen_editar_final: function () {
			let data = { tipo: 43, id_hallazgo: datos.id_hallazgo_editar_final, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$("[editar-view-foto-final]").addClass("hide");
				$("[editar-tomar-foto-final]").find("i").removeClass("fa-spinner fa-spin");
				$("[editar-view-foto-final]").find("img").attr("src", "");
				$("[editar-tomar-foto-final]").removeClass("hide");
				datos.lista_de_hallazgos[datos.id_pregunta_reporte_final][datos.id_hallazgo_editar_final].imagen = null;
			}, "json");
		},
		eliminar_imagen_editar: function () {
			let data = { tipo: 43, id_hallazgo: datos.id_hallazgo_editar, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				$("[editar-view-foto]").addClass("hide");
				$("[editar-tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
				$("[editar-view-foto]").find("img").attr("src", "");
				$("[editar-tomar-foto]").removeClass("hide");
				datos.lista_de_hallazgos[datos.id_pregunta_reporte][datos.id_hallazgo_editar].imagen = null;
			}, "json");
		},
		eliminar_imagen: function () {
			let data = { tipo: 41, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$("[view-foto]").addClass("hide");
					$("[tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
					$("[view-foto]").find("img").attr("src", "");
					$("[tomar-foto]").removeClass("hide");
				}
			}, "json");
		},
		no_conformidad: function () {
			auditoria_test.show_view.lista_preguntas_show_no_conformidad();
		},
		get_info_no_conformidad: function () {
			$("#form_new_finding")[0].reset();
			let data = { tipo: 40, id_pregunta: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				html = "";
				if (json.hallazgos != null) {
					datos.lista_de_hallazgos[datos.id_pregunta_reporte] = json.hallazgos;
					$.each(json.hallazgos, function (index, value) {
						let nc = "<td class='red darken-3 white-text weight-6 center-align' style='width:62px;'> N </td>";
						if (value.nc == 1) {
							nc = "<td class='green darken-3 white-text weight-6 center-align' style='width:62px;'> NC </td>";
						}
						if ($(window).width() <= 600){
							html += '<tr>' + nc +
								'	<td colspan=2>' +
								'		<b><p>' + value.hallazgo + '</p></b>' +
								'	</td>' +
								'	<td class="right"><div class="btn-small mr-4" onclick="auditoria_test.editar_hallazgo(' + index + ')"><span class="material-icons p-1">create</span></div><div class="btn-small red darken-3 mr-4" onclick="auditoria_test.editar_hallazgo(' + index + ')"><span class="material-icons p-1">remove_circle_outline</span></div></td>'+
								'</tr>';
						}else{
							html += '<tr>' + nc +
								'	<td>' +
								'		<b><p>' + value.hallazgo + '</p></b>' +
								'		<label class="weight-7">' + value.accion + '</label>' +
								'	</td>' +
								'	<td><label><b>' + datos.lista_departamentos[value.departamento].name + '</b></label></td>' +
								'	<td class="right"><div class="btn-small mr-4" onclick="auditoria_test.editar_hallazgo(' + index + ')"><span class="material-icons p-1">create</span></div><div class="btn-small red darken-3 mr-4" onclick="auditoria_test.editar_hallazgo(' + index + ')"><span class="material-icons p-1">remove_circle_outline</span></div></td>' +
								'</tr>';
						}
						
					});
					$("#lista_de_hallazgos > tbody").html(html);
					$("#form_alta_finding").addClass("hide");
					$("#lista_de_hallazgos").removeClass("hide");
				} else {
					$("#form_alta_finding").removeClass("hide");
					$("#lista_de_hallazgos").addClass("hide");
				}
				if (json.imagenes != null) {
					$("[tomar-foto]").addClass("hide");
					$("[view-foto]").find("img").attr("src", json.imagenes);
					$("[view-foto]").removeClass("hide");
				} else {
					$("[view-foto]").addClass("hide");
					$("[tomar-foto]").find("i").removeClass("fa-spinner fa-spin");
					$("[view-foto]").find("img").attr("src", "");
					$("[tomar-foto]").removeClass("hide");
				}
			}, "json").done(function () {
				let info_pregunta = auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte];
				if (info_pregunta.h_a == 0) {
					$(".insert_finding_accion_nc").prop("checked", true).attr("disabled", "disabled").attr("checked", "");
					$(".insert_finding_accion_nc_lanbel").removeClass("hide");
				} else {
					$(".insert_finding_accion_nc").prop("checked", false).removeAttr("disabled").removeAttr("checked");
					$(".insert_finding_accion_nc_lanbel").addClass("hide");
				}
				$("#new_finding_no_pregunta").html(info_pregunta.no_pregunta);
				$("#new_finding_titulo").html(info_pregunta.titulo);
				$("#new_finding_pregunta").html(info_pregunta.pregunta);
				$("#new_finding_importancia").html(info_pregunta.importancia);
				$("#new_finding_plan_de_reaccion").html(info_pregunta.plan_r);
			});
		},
		set_na: function () {
			$("#lista_preguntas_info_pregunta").find("button").attr("disabled", "disabled");
			$("#btn-set-na").html("<i class='fas fa-spinner fa-spin'></i>");
			let data = { tipo: 36, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					iniComponent.back();
				}
			}, "json").done(function () {
				$("#lista_preguntas_info_pregunta").find("button").removeAttr("disabled");
				$("#btn-set-na").html("N/A");
			});
		},
		set_ok: function () {
			$("#lista_preguntas_info_pregunta").find("button").attr("disabled", "disabled");
			$("#btn-set-s").html("<i class='fas fa-spinner fa-spin'></i>");
			let data = { tipo: 34, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					iniComponent.back();
				}
			}, "json").done(function () {
				$("#lista_preguntas_info_pregunta").find("button").removeAttr("disabled");
				$("#btn-set-s").html("S");
			});
		},
		set_nok: function () {
			let data = { tipo: 35, id_reporte: datos.id_pregunta_reporte, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte].estado = 1;
					if ($("#tp_" + data.id_reporte).parent().parent().parent().find(".card").length == 1) {
						$("#tp_" + data.id_reporte).parent().parent().parent().remove();
					} else {
						$("#tp_" + data.id_reporte).parent().parent().remove();
					}
					auditoria_test.show_view.lista_pregunta_auditorias();
				}
			}, "json");
		},
		cambiar_linea: function () {
			let data = { tipo: 48, id_auditoria: datos.id_audit_selected, id_nivel: datos.id_nivel, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					datos.lista_auditorias[datos.id_audit_selected].id_linea = json.id_linea;
					datos.lista_auditorias[datos.id_audit_selected].linea = json.linea;
					auditoria_test.validar_inicializacion();
				} else {
					if (json.estado == false) {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
					}
				}
			}, "json");
		},
		validar_inicializacion: function () {
			let data = { tipo: 30, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == 0) {
					auditoria_test.show_view.opciones_auditoria();
					if (datos.id_nivel == 2) {
						if (json.cambios == 0) {
							$("#opciones_inicio_cambiar_linea").unbind("click").on("click", function () {
								auditoria_test.cambiar_linea();
							}).removeAttr("disabled");
						} else {
							$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
						}
					} else if (datos.id_nivel == 3) {
						$("#opciones_inicio_cambiar_linea").unbind("click").on("click", function () {
							auditoria_test.cambiar_linea();
						}).removeAttr("disabled");
					} else if (datos.id_nivel == 4) {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
					} else {
						$("#opciones_inicio_cambiar_linea").unbind("click").attr("disabled", "disabled");
					}

				} else if (json.estado == 1 && json.auditado == "") {
					auditoria_test.show_view.insert_name_auditor();
				} else {
					auditoria_test.get_info_auditoria();
				}
				datos.lista_auditorias[datos.id_audit_selected].id_linea = json.lineas.id_linea;
				datos.lista_auditorias[datos.id_audit_selected].linea = json.lineas.name;
				$("#opciones_de_inicio_linea").html("<b>" + json.lineas.name + "</b>");
			}, "json");
		},
		change_id_view_pregunta: function (id_issue, id_reporte) {
			datos.id_pregunta_reporte = id_reporte;
			datos.id_issue = id_issue;
			auditoria_test.show_view.lista_preguntas_show_info();
		},
		set_info_pregunta: function () {
			console.log(auditoria_test.lista_preguntas);
			let info_pregunta = auditoria_test.lista_preguntas[datos.id_issue].preguntas[datos.id_pregunta_reporte];
			$("#info_pregunta_no_pregunta").html(info_pregunta.no_pregunta);
			$("#info_pregunta_titulo").html(info_pregunta.titulo);
			$("#info_pregunta_pregunta").html(info_pregunta.pregunta);
			$("#info_pregunta_importancia").html(info_pregunta.importancia);
			$("#info_pregunta_plan_de_reaccion").html(info_pregunta.plan_r);
			iniComponent.back = auditoria_test.show_view.lista_pregunta_auditorias;
		},
		get_info_auditoria: function () {
			let data = { tipo: 33, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				auditoria_test.lista_preguntas = json.preguntas_lpa;
				auditoria_test.show_preguntas();
				$("#lista_pregunta_auditoria_label_linea").html(datos.lista_auditorias[datos.id_audit_selected].linea);
			}, 'json').done(function () {
				if (typeof (datos.view_in_auditoria) == "undefined") {
					auditoria_test.show_view.lista_pregunta_auditorias();
				} else {
					auditoria_test.show_view[datos.view_in_auditoria]();
				}
			});
		},
		show_preguntas: function () {
			let html = '';
			let cont_issue = 0;
			$.each(auditoria_test.lista_preguntas, function (index, value) {
				let cont = 0;
				for (var k in value.preguntas) {
					if (value.preguntas[k].estado == 0) {
						cont++;
					}
				}
				if (cont > 0) {
					html += '<div id="issue_' + index + '"> <div class="btn blue-grey darken-3 mt-3">' + value.name + '</div>';
					$.each(value.preguntas, function (index2, value2) {
						if (value2.estado == 0) {
							html += '<div class="card waves-effect p-0 col s12 m12 l12 my-1" onclick="auditoria_test.change_id_view_pregunta(' + index + ',' + index2 + ')">' +
								'<div class="card-content p-0">' +
								'<table id="tp_' + index2 + '">' +
								'<tr>' +
								'<td class="w-10 blue-grey white-text weight-7 p-2 center-align">' + value2.no_pregunta + '</td>' +
								'<td class="px-4">' +
								'<b>' + value2.titulo + '</b><br>' +
								'<label class="weight-7 " style="font-size: 1rem;">' + value2.pregunta + '</label>' +
								'</td>' +
								'</tr>' +
								'</table>' +
								'</div>' +
								'</div>';
						}
					});
					html += "</div>";
				} else {
					cont_issue++;
				}
			});
			$("#lista_pregunta_auditoria_contenedor").html(html);
		},
		finalizar_auditoria: function () {
			if (Object.keys(datos.lista_de_hallazgos).length == 0) {
				auditoria_test.set_fecha_finalizado();
			} else {
				auditoria_test.show_view.lista_preguntas_hallazgos_encontrados();
			}
		},
		set_fecha_finalizado: function () {
			let data = { tipo: 45, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, linea: datos.lista_auditorias[datos.id_audit_selected].linea, auditor: datos.lista_auditorias[datos.id_audit_selected].auditor, semana: datos.lista_auditorias[datos.id_audit_selected].semana_auditoria, correo: datos.lista_auditorias[datos.id_audit_selected].correo, id_nivel: datos.id_nivel }
			$("#modal_finalizado_label").html("Sending confirmation email..");
			$("#modal_finalizando").modal("open");
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$("[finalizar-auditoria-final]").removeAttr("disabled").html("FINISH");
					if (Object.keys(datos.lista_de_hallazgos).length > 0) {
						$("#modal_finalizado_label").html("Sending Finding emaul.");
						auditoria_test.send_email_hallazgos();
					} else {
						$("#modal_finalizando").modal("close");
						view.change.calendario_test();
					}

				}
			}, "json");
		},
		send_email_hallazgos: function () {
			let data = { tipo: 46, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$("#modal_finalizando").modal("close");
				} else {
					$("#modal_finalizando").modal("close");
				}
				view.change.calendario_test();
			}, 'json');
		},
		save_name_personal_acargo: function () {
			let data = { tipo: 7, name_acargo: $("#insert_name_personal_acargo").val(), id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.validar_inicializacion();
				} else {
					iniComponent.back();
				}
			}, 'json');
		},
		set_pp_auditoria: function () {
			let data = { tipo: 47, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel, id_linea: datos.lista_auditorias[datos.id_audit_selected].id_linea }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.validar_inicializacion();
				}
			}, "json");
		},
		iniciar_auditoria: function () {
			let data = { tipo: 32, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel, id_linea: datos.lista_auditorias[datos.id_audit_selected].id_linea }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					auditoria_test.validar_inicializacion();
				}
			}, "json");
		},
		show_view: {
			opciones_auditoria: function () {
				$(".views_auditorias").addClass("hide");
				$("#opciones_auditoria").removeClass("hide");
				auditoria_test.set_restricciones_nivel();
			},
			insert_name_auditor: function () {
				$(".views_auditorias").addClass("hide");
				$("#insert_name_auditor").removeClass("hide");
			},
			lista_pregunta_auditorias: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_pregunta_auditorias").removeClass("hide");
				datos.view_in_auditoria = "lista_pregunta_auditorias";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				auditoria_test.validar_finalizado();
			},
			lista_preguntas_show_info: function () {
				$(".views_auditorias").addClass("hide");

				$("#lista_preguntas_show_info").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_show_info";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				auditoria_test.set_info_pregunta();
			},
			lista_preguntas_show_no_conformidad: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_preguntas_show_no_conformidad").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_show_no_conformidad";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				auditoria_test.get_info_no_conformidad();
				iniComponent.back = auditoria_test.show_view.lista_preguntas_show_info;
			},
			lista_preguntas_show_alta_no_conformidad: function () {
				$("#form_alta_finding").removeClass("hide");
				$("#form_editar_finding").addClass("hide");
				$("#lista_de_hallazgos").addClass("hide");
				$("#form_new_finding")[0].reset();
				iniComponent.back = auditoria.show_view.lista_preguntas_show_lista_findings;
			},
			lista_preguntas_show_lista_findings: function () {
				$("#form_alta_finding").addClass("hide");
				$("#form_editar_finding").addClass("hide");
				$("#lista_de_hallazgos").removeClass("hide");
				$("#form_update_finding")[0].reset();
				iniComponent.back = auditoria.show_view.lista_preguntas_show_info;
			},
			lista_preguntas_show_editar_hallazgo: function () {
				$("#form_editar_finding").removeClass("hide");
				$("#form_alta_finding").addClass("hide");
				$("#lista_de_hallazgos").addClass("hide");
				iniComponent.back = auditoria.show_view.lista_preguntas_show_lista_findings;
			},
			lista_preguntas_hallazgos_encontrados: function () {
				$(".views_auditorias").addClass("hide");
				$("#lista_preguntas_hallazgos_encontrados").removeClass("hide");
				datos.view_in_auditoria = "lista_preguntas_hallazgos_encontrados";
				sessionStorage.setItem("datos", JSON.stringify(datos));
				auditoria_test.show_hallazgos_encontrados();
				iniComponent.back = view.change.calendario;
			}
		},
		set_restricciones_nivel: function () {
			switch (datos.id_nivel) {
				case 1:
					$("#opciones_inicio_cambiar_linea").attr("disabled", "disabled");
					break;
				case 4:
					$("#opciones_inicio_cambiar_linea").attr("disabled", "disabled");
					$("#opciones_inicio_pp").attr("disabled", "disabled");
					break;
				default:
					break;
			}
		},
		subir_imagen_evidencia: function (id) {
			$("[tomar-foto]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_new_finding")[0]);
			$form_data.append("tipo", 39);
			$form_data.append("id", id);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != false) {
						$("[tomar-foto]").addClass("hide");
						$("[view-foto]").find("img").attr("src", json.estado);
						$("[view-foto]").removeClass("hide");
					}
					$('.materialboxed').materialbox();
				}
			});
		},
		subir_imagen_evidencia_editar: function () {
			$("[editar-tomar-foto]").find("i").addClass("fa-spinner fa-spin");
			let $form_data = new FormData($("#form_update_finding")[0]);
			$form_data.append("tipo", 38);
			$form_data.append("id_hallazgo", datos.id_hallazgo_editar);
			$form_data.append("id_pregunta_reporte", datos.id_pregunta_reporte);
			$form_data.append("id_planta", datos.id_planta);
			$.ajax({
				url: url,
				type: "POST",
				data: $form_data,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (json) {
					if (json != null) {
						if (json.imagen != null) {
							$("[editar-tomar-foto]").addClass("hide");
							$("[editar-view-foto]").find("img").attr("src", json.imagen);
							$("[editar-view-foto]").removeClass("hide");
							datos.lista_de_hallazgos[datos.id_pregunta_reporte][datos.id_hallazgo_editar].imagen = json.imagen
						}
					}
				}
			});
		},
		set_events: function () {
			$("#form_new_finding").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				auditoria_test.save_no_conformidad();
			});
			$("#form_editar_hallazgo_final").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				$(this).find("[type=submit]").append(" <i class='fas fa-spinner fa-spin'></i>").attr("disabled", "disabled");
				auditoria_test.save_no_conformidad_final();
			});
			$("#form_editar_finding").unbind("submit").on("submit", function (e) {
				e.preventDefault(),
					auditoria_test.update_data_finding();
			});
			$("#opciones_inicio_iniciar").unbind("click").on("click", function () {
				$(".btn_opciones_inicio").addClass("disabled", "disabled");
				$(this).append(" <i class='fas fa-spinner fa-spin'></i>");
				auditoria_test.iniciar_auditoria();
			});
			$("#opciones_inicio_pp").unbind("click").on("click", function () {
				$(".btn_opciones_inicio").addClass("disabled", "disabled");
				$(this).append(" <i class='fas fa-spinner fa-spin'></i>");
				auditoria_test.set_pp_auditoria();
			});
			$("#form_insert_name_personal_acargo").unbind("submit").on("submit", function (e) {
				e.preventDefault();
				$(this).find("[type=submit]").append(" <i class='fas fa-spinner fa-spin'></i>").attr("disabled", "disabled");
				auditoria_test.save_name_personal_acargo();
			});
			$("[tomar-foto]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".captura_foto").unbind("change").on("change", function () {
				if ($(this)[0].files[0] != "undefined") {
					auditoria_test.subir_imagen_evidencia($(this).attr("key"));
				} else {
					console.log("no tiene foto");
				}
			});
			$("[editar-tomar-foto]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".editar-captura_foto").unbind("change").on("change", function () {
				if ($(this)[0].files[0] != "undefined") {
					auditoria_test.subir_imagen_evidencia_editar();
				} else {
					console.log("no tiene foto");
				}
			});

			$("[editar-tomar-foto-final]").unbind("click").on("click", function () {
				$(this).siblings("[type=file]").click();
			});
			$(".editar_captura_foto_final").unbind("change").on("change", function () {
				if ($(this)[0].files[0] != "undefined") {
					auditoria_test.subir_imagen_evidencia_editar_final();
				} else {
					console.log("no tiene foto");
				}
			});
			$("[finalizar-auditoria-final]").unbind("click").on("click", function () {
				auditoria_test.set_fecha_finalizado();
				$(this).attr("disabled", "disabled").append(" <i class='fas fa-spinner fa-spin'></i>");
			});
		},
		ini_components: function () {
			$('select').formSelect();
			$('[data-length]').characterCounter();
			$('.modal').modal({ dismissible: false });
		}
	}
	test = {
		ini: function () {
			iniComponent.back = view.change.inicio;
			$("#title_add_text").remove();
			$("#text_logo").append("<span id='title_add_text'> Level " + datos.id_nivel + "</span>");
			this.get_lista_auditorias();
			this.get_datos_select_line_issue();
			this.set_title();
			this.set_events();
		},
		get_datos_select_line_issue: function () {
			if (datos.id_nivel == 4) {
				test.get_lista_issues();
			} else {
				test.get_lista_departamentos();
			}
		},
		get_lista_issues: function () {
			let data = { tipo: 26, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				datos.lista_departamentos = json;
				let html = '<option value="" disabled selected>Departamento</option>';
				$.each(json, function (index, value) {
					html += "<option value='" + index + "'>" + value.name + "</option>";
				});
				$("#insert_finding_departamento, #editar_finding_departamento,#editar_final_finding_departamento").html(html);
				$('select').formSelect();
			}, 'json');
		},
		get_lista_departamentos: function () {
			let data = { tipo: 17, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				datos.lista_departamentos = json;
				let html = '<option value="" disabled selected>Departamento</option>';
				$.each(json, function (index, value) {
					html += "<option value='" + index + "'>" + value.name + "</option>";
				});
				$("#insert_finding_departamento, #editar_finding_departamento,#editar_final_finding_departamento").html(html);
				$('select').formSelect();
			}, "json");
		},
		select_audit: function (id) {
			datos.id_audit_selected = id;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			this.validar_auditoria();
		},
		validar_auditoria: function () {
			let data = { tipo: 30, id_auditoria: datos.id_audit_selected, id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == 0 || json.estado == 1) {
					view.change.auditoria_test();
				}
			}, "json");
		},
		select_planta: function (id_planta) {
			datos.id_planta = id_planta;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			inicio.show_menu.niveles_prueba();
		},
		set_events: function () {
			$("#modal_alta_auditoria").modal();
			$("[key-add-audit]").unbind("click").on("click", function () {
				if (datos.id_nivel == 4) {
					test.show_issues_disponibles();
				} else {
					test.show_lineas_disponibles();
				}
			});
		},
		create_audit: function (id_linea, id_issue) {
			let data = { tipo: 27, id_linea: id_linea, id_nivel: datos.id_nivel, id_planta: datos.id_planta, id_issue: id_issue }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				if (json.estado == true) {
					$("#modal_alta_auditoria").modal("close");
					test.get_lista_auditorias();
				}
			}, 'json');
		},
		show_issues_disponibles: function () {
			let data = { tipo: 49, id_planta: datos.id_planta, id_nivel: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				let html = "";
				for (const key in json) {
					if (json.hasOwnProperty(key)) {
						const value = json[key];
						html += "<div class='btn btn-primary col s12 m12 l12 my-1' onclick='test.create_audit(null," + key + ")'>" + value + "</div>";
					}
				}
				$("#modal_alta_auditoria_content").html(html);
			}, 'json').done(function () {
				$("#modal_alta_auditoria").modal("open");
			});
		},
		show_lineas_disponibles: function () {
			let data = { tipo: 26, id_planta: datos.id_planta, id_nivel: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				let html = "";
				for (const key in json) {
					if (json.hasOwnProperty(key)) {
						const value = json[key];
						html += "<div class='btn btn-primary col s12 m12 l12 my-1' onclick='test.create_audit(" + key + ",null)'>" + value + "</div>";
					}
				}
				$("#modal_alta_auditoria_content").html(html);
			}, 'json').done(function () {
				$("#modal_alta_auditoria").modal("open");
			});
		},
		set_title: function () {
			let label_title = $("#label_title");
			switch (datos.id_nivel) {
				case 1:
					label_title.html(moment().subtract(6, "hours").format("DD/MM/YYYY") + "<span class='material-icons pointer right' key-add-audit>add_circle_outline</span>");
					break;
				case 2:
					label_title.html("Semana " + moment().format("WW") + "<span class='material-icons pointer right' key-add-audit>add_circle_outline</span>");
					break;
				case 3:
				case 4:
					label_title.html(moment().format("MMMM") + "<span class='material-icons pointer right' key-add-audit>add_circle_outline</span>");
					break;
				default:
					break;
			}
		},
		show_calendar: function (nivel) {
			datos.id_nivel = nivel;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			view.change.calendario_test();
		},
		get_lista_auditorias: function () {
			let data = { tipo: 28, view: "test", id_planta: datos.id_planta, id_nivel: datos.id_nivel }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				let html = "";
				datos.lista_auditorias = json;
				sessionStorage.setItem("datos", JSON.stringify(datos));
				$.each(json, function (index, value) {
					if (datos.id_nivel == f4) {
						html += test.estrucutura_lista_auditoria(index, value.auditor, value.issue || '', value.estado);
					} else {
						html += test.estrucutura_lista_auditoria(index, value.auditor, value.linea || '', value.estado);
					}
				});
				$("#container_lpa").html(html);
			}, 'json');
		},
		estrucutura_lista_auditoria: function (id_auditoria, nombre, linea, estado) {
			let short_name = "";
			if (typeof (nombre.split(' ')[1]) == "undefined") {
				short_name = nombre.split(' ')[0][0] + nombre.split(' ')[0][1];
			} else {
				short_name = nombre.split(' ')[0][0] + nombre.split(' ')[1][0];
			}
			let html = '<div class="card m-3 animated fadeIn" onclick="test.select_audit(' + id_auditoria + ')">' +
				'<div class="card-body p-0">' +
				'<div class="row m-0 p-0">' +
				'<div class="col s2 m2 xl1 ' + test.validar_estado_auditoria(estado) + ' white-text p-3 center-align">' + short_name + '</div>' +
				'<div class="col s6 m4 xl5 p-3"><b>' + nombre + '</b></div>' +
				'<div class="col s4 m5 xl5 p-3 right-align"><b>' + linea + '</b></div>' +
				'<div class="col s12 m1 xl1 p-3 hide-on-small-only center-align"></div>' +
				'</div>' +
				'</div>' +
				'</div>';
			return html;
		},
		validar_estado_auditoria: function (estado) {
			switch (parseInt(estado)) {
				case 0:
					return " blue darken-3";
				case 1:
					return " orange darken-3";
				case 2:
					return " grey darken-3";
				case 3:
					return " red darken-3";
			}
		}
	}
	/************* funciones generales ********** */
	view = {
		change: {
			inicio: function () {
				sessionStorage.setItem("view", "inicio");
				$("#marcar_pp_all").addClass("hide");
				view.show();
			},
			calendario: function () {
				sessionStorage.setItem("view", "calendario");
				$("#marcar_pp_all").addClass("hide");
				iniComponent.back = view.change.inicio;
				view.show();
			},
			calendario_test: function () {
				sessionStorage.setItem("view", "calendario_test");
				$("#marcar_pp_all").addClass("hide");
				iniComponent.back = view.change.inicio;
				view.show();
			},
			login: function () {
				sessionStorage.setItem("view", "login");
				$("#marcar_pp_all").addClass("hide");
				view.show();
			},
			auditoria: function () {
				sessionStorage.setItem("view", "auditoria");
				view.show();
			},
			auditoria_test: function () {
				sessionStorage.setItem("view", "auditoria_test");
				view.show();
			}
		},
		show: async function () {
			history.pushState(null, null, location.href);
			if (!await inicio.UrlExists("app/views/" + sessionStorage.getItem("view") + ".html?")){
				sessionStorage.removeItem("view");
				view.valid();
				iniComponent.ini();
			}
			main_view.load("app/views/" + sessionStorage.getItem("view") + ".html?" + Math.random());
			if (datos.inicio.menu == "plantas") { $("#nav-mobile").addClass("hide"); } else { $("#nav-mobile").removeClass("hide"); }
		},
		return: function () {
			sessionStorage.setItem("return", "inicio");
			view.show();
		},
		valid: function () {
			if (!sessionStorage.getItem("datos")) {
				config.change_datos_inicio("menu", "plantas");
			} else {
				datos = JSON.parse(sessionStorage.getItem("datos"));
			}
			if (sessionStorage.getItem("view")) {
				view.show();
			} else {
				view.change.inicio();
			}
		},
	}
	config = {
		change_datos_inicio: function (variable, dato) {
			if (typeof (datos.inicio) == "undefined") { datos.inicio = {}; };
			if (typeof (datos.inicio[variable]) == "undefined") { datos.inicio[variable] = {}; };
			datos.inicio[variable] = dato;
			sessionStorage.setItem("datos", JSON.stringify(datos));
			if (datos.inicio.menu == "plantas") { $("#nav-mobile").addClass("hide"); } else { $("#nav-mobile").removeClass("hide"); }
		},
		set_events: function () {

		}
	}
	iniComponent = {
		timer: 1,
		ini: function () {
			window.onpopstate = function () {
				if (iniComponent.timer == 1) {
					history.go(1);
					if (typeof (iniComponent.back) == "function") {
						iniComponent.back();
					} else {
						alert("error: " + console.log(back));
					}

				}
				iniComponent.timer = 2;
				setTimeout(() => {
					iniComponent.timer = 1;
				}, 100);
			}
		},
		back: null,
		validar_tablets: function () {
			let data = { tipo: 50, id_planta: datos.id_planta }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				datos.validacion_tablet = json;
			}, 'json').done(function(){
				calendario.validar_acceso_tablet();
			});
		}
	}

	view.valid();
	iniComponent.ini();
})()