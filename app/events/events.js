(function(){
	var url="app/events/event_case.php";
	eventos_especiales = {
		calendario_msa: function () {
			let data = { tipo: 1, id_planta:1 }
			$.post(url, data, function (json) {
				if (typeof (json) == 'string') { console.log(json); return false; }
				let html="";
				if(json==null){
					html ="<tr><td colspan=14 class='center-align'><b class=' red-text text-darken-3'>Without MSA</b></td></tr>";
					$("#table_calendar_msa > tbody").html(html);
				}else{
					json.sort(function (a, b) { return a.No_Month - b.No_Month });
					for (const key in json) {
						if (json.hasOwnProperty(key)) {
							const value = json[key];
							html += "<tr><td class='p-0'>" + value.Equipo + "</td><td class='p-0'>" + value.Ubicacion+"</td>";
							for (let index = 1; index < 12; index++) {
								if (index == value.No_Month){
									switch (value.Estado) {
										case 0:
											html += "<td class='p-0 blue darken-3 center white-text'><i class='fas fa-clock'></i></td>";
											break;
										case 1:
											html += "<td class='p-0 orange darken-3 center white-text'><i class='fas fa-spinner fa-spin'></i></td>";			
											break;
										case 2:
											html += "<td class='p-0 green darken-3 center white-text'><i class='fas fa-check'></i></td>";			
											break;
										case 3:
											html += "<td class='p-0 red darken-3 center white-text'><i class='fas fa-times'></i></td>";
											break;
									}
								}
								html += "<td class='grey'></td>";
							}
							html +="</tr>";
						}
						$("#table_calendar_msa > tbody").html(html);
					}
				}
				
			}, 'json').done(function(){
				$("#modal_msa").modal();
				$("#modal_msa").modal("open");
			});
		}
	}
})()
