/*	Global App Variables */
WOODA = {};
WOODA.GLOBALS = {};
WOODA.user = {};
//WOODA.user.id_office = 3;
/*						*/

/* 	Remote services 	*/ 
call_service = function (end_point,handler)
{
	console.log("URL:" + end_point);
	$.ajax({
		  url: end_point,
		  beforeSend: function( xhr ) {
		    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  }
		})
		  .done(function( data ) {
			if (handler !== undefined)
			{
				data = data.replace('Ã','Ñ').replace('Ã¡','á').replace('Ã³','ó').replace('Ã­','í').replace('Ã©','é');
				handler(data);
			}  
		  });
};


var host_uri = "http://localhost:3000";//local
//var host_uri = "http://localhost:3000";//c9

var const_end_point_company = host_uri + "/getCifInfo?cif=";
var const_end_poiny_company_geo =  host_uri + "/getEmpresasGeo";
//var const_end_point_svc = "https://super-crawler-davidreyblanco.c9users.io/wop/services.php";
var const_end_point_svc = "services.php";

// Production
lookup_company_2 = function (cif,handler)
{
	call_service(const_end_point_company + cif,handler);
};

// ----------------------------------------------------
// 				Overrides production environment
//----------------------------------------------------
lookup_company = function (cif,handler)
{
	call_service(const_end_point_svc + "?cmd=company&cif=" + cif,handler);
};

update_notes = function (cif,notes,handler)
{
	call_service(const_end_point_svc + "?cmd=update-notes&cif=" + cif + '&notes=' + notes,handler);
};

auth_user = function (u,p,handler)
{
	call_service(const_end_point_svc + "?cmd=auth&u=" + u + '&p=' + p,handler);
};

get_suggestions = function (handler,order_list,order_criteria,ipe_filter_criteria,export_filter_criteria,propension_filter_criteria,name_filter,page_id)
{
	if ( WOODA.user.id_office !== undefined)
	{
		var url = const_end_point_svc + "?cmd=suggestions&o=" + WOODA.user.id_office;
		if (order_list !== undefined)
		{
			url += '&order_list='+order_list+'&order_criteria='+order_criteria;
			url += '&ipe_filter_criteria='+ipe_filter_criteria+'&export_filter_criteria='+export_filter_criteria+'&propension_filter_criteria='+propension_filter_criteria+'&name_filter=' + name_filter;
			url += '&page_id='+page_id;
		}
		call_service(url,handler);
	}
};

get_pipeline = function (handler,order_list,order_criteria,visited_criteria,interes_criteria,name_filter,page_id)
{
	if ( WOODA.user.id_office !== undefined)
	{
		var url = const_end_point_svc + "?cmd=pipeline&o=" + WOODA.user.id_office;
		if (order_list !== undefined)
		{
			url += '&order_list='+order_list+'&order_criteria='+order_criteria;
			url += '&visited_criteria='+visited_criteria+'&interes_criteria='+interes_criteria+'&name_filter=' + name_filter;
			url += '&page_id='+page_id;
		}
		call_service(url,handler);
	}
};

get_tables = function (handler)
{
	call_service(const_end_point_svc + "?cmd=tables",handler);
};

get_near_businesses = function (x,y,handler)
{
	if ( WOODA.user.id_office !== undefined)
	{
	//call_service(const_end_poiny_company_geo + "?x=" + x + '&y=' + y,handler);
	call_service(const_end_point_svc + "?cmd=near&x=" + x + '&y=' + y + "&o=" + WOODA.user.id_office,handler);
	}
};

// ------------------------------------------------------------------
// 					Pipeline helpers
//------------------------------------------------------------------

// Filter items by opportunity stage
get_items_by_state = function(items,state,max_items)
{
	var result = Array();
	max_items = max_items === undefined ? 5 : max_items; // 5 by default
	var current_items = 0; // Prevent writing more then x items
	for(var i = 0; i < items.length ;i++)
	{
		var s = items[i].current_stage
		var name = items[i].NAME;
		if (/*s === state &&*/ current_items < max_items &&  name !== '')
		{
			result.push(items[i]);	
			current_items++;
		}
	}
	return result;
};
// --- Reason codes ---

get_reason_codes = function(suggestions)
{
	// ----------------------------------------
	// Deterministic reasons based on CIF number
	// ----------------------------------------

	for(var i = 0; i < suggestions.length; i++)
	{
		var number = parseInt(suggestions[i].CIF.substring(3,9));
		var x = number%11;
		switch (x)
		{
			case 0: 
			case 1: 
			case 2: 
			suggestions[i].reason_code = '- Risk rating';
			break;
			case 3: 
			case 4: 
			case 5: 
			suggestions[i].reason_code = '+ Risk rating';
			break;
			case 6: 
			suggestions[i].reason_code = '+ Capital';
			break;
			case 7: 
			suggestions[i].reason_code = '- Decrease';
			break;
			case 8: 
			suggestions[i].reason_code = '+ Personnnel';
			break;
			case 9: 
			suggestions[i].reason_code = '+ Risk rating Down';
			break;
			case 10: 
			suggestions[i].reason_code = 'Accelerated Growth';
			break;
		}
	}
	return suggestions;
};
// ----

aggregate_expected_value = function(items)
{
	var result = 0;
	for(var i=0; i < items.length;i++) 
	{
		result += parseInt(items[i].EXPECTED_VALUE);
	}
	return result;
};

showModal = function(title,body,button_yes,button_no,function_yes,function_no)
{
	 $("#myModal .modal-title").text(title);
	 $("#myModal .modal-body").text(body);
	 $("#myModal #button_yes").text(button_yes);
	 if (button_no === undefined)
	 {
		 $("#myModal #button_no").css('display','none');	
		
	 }
	 else
	 {
		 $("#myModal #button_no").css('display','block');		 
		 $("#myModal #button_no").text(button_no);
		 if (function_no !== undefined)
		 {
			 $("#myModal #button_no").unbind('click').bind('click',function (e){function_no(e);});
		 }
		 else
		 {
			 $("#myModal #button_no").unbind('click');
		 }
	 }

	 if (function_yes !== undefined)
	 {
		 $("#myModal #button_yes").unbind('click').bind('click',function (e){function_yes(e);});
	 }
	 else
	 {
		 $("#myModal #button_yes").unbind('click');
	 }

	 $("#myModal").modal();
};

showAreYouSure = function(body,function_yes,function_no)
{
	showModal('Confirmar','Estas seguro de '+body+'?','Si','No');
};


showLogout = function()
{
	showModal('Salir de la herramienta','Seguro que quieres salir de la herramienta?','Si','No',function (){show_view('login');});
}

// ---- View update methods
refresh_pipeline = function()
{
	if (WOODA.user.name !== undefined)
	{
		var order_list = $('#modalPipeline #select_order_field').val();
		var order_criteria = $('#modalPipeline #select_order_criteria').val();
		
		var visited_criteria = "SI_" + ($('#modalPipeline #visited_1').is(":checked") ? 1:0) + "," + "NO_" + ($('#modalPipeline #visited_2').is(":checked")?1:0);
		var interes_criteria = "SI_" + ($('#modalPipeline #interes_1').is(":checked") ? 1:0) + "," + "NO_" + ($('#modalPipeline #interes_2').is(":checked")?1:0) + "," + "VACIO_" + ($('#modalPipeline #interes_3').is(":checked")?1:0);
		var name_filter = $('#modalPipeline #filtro_nombre').val().trim();
		var page_num = parseInt($('#pipe_page_num').text()) - 1;
		get_pipeline(function (data) 
			{
					$('.logged_in').css('display','block');															
					var item_list = JSON.parse(data);
					// --- Pagination
					if (item_list.data.length == 0)
					{
						$('#pipe_page_next').css('display','none');// Hide PREV Button
					}
					if (page_num == 0)
					{
						$('#pipe_page_prev').css('display','none');// Hide PREV Button
						$('#pipe_page_next').css('display','block');// Hide PREV Button
					}
					else
					{
						$('#pipe_page_prev').css('display','block');// Hide PREV Button
					}
				    	var scope = angular.element($("#pipeline_view")).scope();
				    	scope.$apply(function(){
				    		
				    		scope.pipeline = item_list.data.slice(0,100);
				    		
				    		setTimeout(function () 
						    {
					    		$('#filter_suggested_pipe').unbind('click').bind('click',function (e){
					    	    	// Reset all			
					    			$('#modalPipeline #filtro_nombre').val('');
					    			$("#modalPipeline").modal();
					    			$('#modalPipeline #button_yes').unbind('click').bind('click',function (e)
					    			{
					    				refresh_pipeline();
					    			});
					    			// $("#modalAddSuggestion").modal();
					    		});
						    }, 1000);
/*
				    		scope.untouched = get_items_by_state(item_list,'U',4);//new Array();
				    		scope.untouched_amount = aggregate_expected_value(scope.untouched);
				    		scope.contacted = get_items_by_state(item_list,'C',5);;
				    		scope.contacted_amount = aggregate_expected_value(scope.contacted);
				    		scope.qualified = get_items_by_state(item_list,'Q',2);
				    		scope.qualified_amount = aggregate_expected_value(scope.qualified);
				    		scope.offered = get_items_by_state(item_list,'O',4);
				    		scope.offered_amount = aggregate_expected_value(scope.offered);
				    		scope.negotiating = get_items_by_state(item_list,'X',5);
				    		scope.closed_amount = aggregate_expected_value(scope.negotiating);
	*/
				    	    //scope.names = suggestion_list;
				    	});
			},order_list,order_criteria,visited_criteria,interes_criteria,name_filter,page_num);	
	}
}

refresh_suggestions = function()
{
	if (/*WOODA !== undefined && WOODA.user !== undefined &&*/ WOODA.user.name !== undefined)
	{
			// Get filters
			var order_list = $('#modalAddSuggestion #select_order_field').val();
			var order_criteria = $('#modalAddSuggestion #select_order_criteria').val();
			
			var ipe_filter_criteria = "1_" + ($('#modalAddSuggestion #ipe_1').is(":checked") ? 1:0) + "," + "2_" + ($('#modalAddSuggestion #ipe_2').is(":checked")?1:0) + "," + "3_" + ($('#modalAddSuggestion #ipe_3').is(":checked")?1:0) ;
			var export_filter_criteria = "0_" + ($('#modalAddSuggestion #export_0').is(":checked") ? 1:0) + "," + "1_" + ($('#modalAddSuggestion #export_1').is(":checked") ? 1:0) + "," + "2_" + ($('#modalAddSuggestion #export_2').is(":checked") ? 1:0)+ "," + "3_" + ($('#modalAddSuggestion #export_3').is(":checked") ? 1:0)+ "," + "4_" + ($('#modalAddSuggestion #export_4').is(":checked") ? 1:0);
			var propension_filter_criteria = "1_" + ($('#modalAddSuggestion #propension_1').is(":checked") ? 1:0) + "," + "2_" + ($('#modalAddSuggestion #propension_2').is(":checked") ? 1:0) + "," + "3_" + ($('#modalAddSuggestion #propension_3').is(":checked") ? 1:0);
			var name_filter = $('#modalAddSuggestion #filtro_nombre').val().trim();
			
			$('#filter_suggested_pipe').button('loading');
			
			var page_num = parseInt($('#suggestion_page_num').text()) - 1;

			get_suggestions(function (data) 
			{
					$('#filter_suggested_pipe').button('reset');
					var suggestions = JSON.parse(data);
					
					// --- Pagination
					if (suggestions.data.length == 0)
					{
						$('#suggestion_page_next').css('display','none');// Hide PREV Button
					}
					if (page_num == 0)
					{
						$('#suggestion_page_prev').css('display','none');// Hide PREV Button
						$('#suggestion_page_next').css('display','block');// Hide PREV Button
					}
					else
					{
						$('#suggestion_page_prev').css('display','block');// Hide PREV Button
					}
					
					var scope = angular.element($("#suggested_view")).scope();
					scope.$apply(function(){
										//suggestions = get_reason_codes(suggestions);
							    		scope.suggestion_list = suggestions.data.slice(0,100);
							    		// Apa�o para vincular los comportamientos
							    		setTimeout(function () 
							    		{
								    		$('.suggestion-item').unbind('click').bind('click',function (e){
								    	    	
								    			 var item = $(e.target).parent().data('cif') !== undefined ? $(e.target).parent() : $(e.target);
								    			
								    			 $('#cif_add_sugestion').html(item.data('cif'));
								    			 $('#name_add_sugestion').html(item.data('name'));
								    			 $('#value_add_sugestion').html("$" + item.data('value'));
								    			 $("#modalAddSuggestion").modal();
								    		});
								    		
								    		$('#filter_suggested').unbind('click').bind('click',function (e){
								    	    	// Reset all					
								    			$('#modalAddSuggestion #filtro_nombre').val('');
								    			$("#modalAddSuggestion").modal();
								    			$('#modalAddSuggestion #button_yes').unbind('click').bind('click',function (e)
								    			{
								    				refresh_suggestions();
								    			});
								    			
								    		});
								    		
								    		$('.suggestion-item-delete').unbind('click').bind('click',function (e){showModal('Confirmar','Quieres anadir esta empresa a tu pipeline?','Si','No',function (n)
								    			{								    			
								    				update_pipe_status('SI',function (result)
								    				{
									    				WOODA.company.in_pipe = 'SI';
									    				// Fix it
											    		$('#company_add_pipe').css('display','none');$('#company_remove_pipe').css('display','block');					
								    				});
								    			});});
								    		
								    		$('.table_suggestions [data-toggle="tooltip"]').tooltip();
											
								    		
							    	    }, 1000);
							    	});
					},order_list,order_criteria,ipe_filter_criteria,export_filter_criteria,propension_filter_criteria,name_filter,page_num);
	}
}

// Muestra datos company
function show_company_data(cif)
{
	$('.wooda_view').css('display','none');  
	display_company(cif,function (){ show_view('company'); });
	$('#update_company_notes').unbind('click').bind('click',function (e)
	{
		var notes = $('#company_notes').val();
		update_notes(cif,notes, function()
		{
			showModal("Informacion","Datos actualizados para la empresa","Cerrar");
		});
		
	});
}

//
update_company_status = function()
{
	var cif = WOODA.company.CIF;
	var user = WOODA.user.name;
	var current_status_v = $('#company_visited_flag').text().trim();
	var current_status_i = $('#company_interest_flag').text().trim();
	var current_status_j = $('#company_justification_flag').text().trim();
	if (current_status_v === 'NO')
	{
		current_status_i = '_';current_status_j = '_';
	}
	if (current_status_j === '')
	{
		current_status_j = '_';
	}
	call_service(const_end_point_svc + "?cmd=update-status&cif=" + cif + '&u=' + user + '&visit=' + current_status_v +'&interest='+current_status_i+'&status_motive='+current_status_j,function ()
	{
//		showModal("Informacion","Datos actualizados para la empresa","Cerrar");
		console.log("Actualizada informacion: " + cif);
		// Refresh views
		refresh_suggestions();
		refresh_pipeline();
	});
};

remove_from_pipe = function()
{
	update_pipe_status('NO',function (result)
			{
				WOODA.company.in_pipe = 'NO';
				// Fix it
	    		$('#company_add_pipe').css('display','block');$('#company_remove_pipe').css('display','none');

			});
};

add_to_pipe = function()
{
	update_pipe_status('SI',function (result)
			{
				WOODA.company.in_pipe = 'SI';
				// Fix it
	    		$('#company_add_pipe').css('display','none');$('#company_remove_pipe').css('display','block');		
	    	
			});
};


update_pipe_status = function(pipe_status,callback)
{
	if (WOODA.company === undefined) return;
	
	var current_pipe_status = WOODA.company.in_pipe;
	if (current_pipe_status !== pipe_status)
	{
		var cif = WOODA.company.CIF;
		var user = WOODA.user.name;
		call_service(const_end_point_svc + "?cmd=update-status&cif=" + cif + '&u=' + user + '&pipe=' + pipe_status,function (result)
		{
			refresh_suggestions();
			refresh_pipeline();
			// ---- FIXIT
			WOODA.company.in_pipe = pipe_status;
			if (pipe_status === 'SI')
			{
	    		$('#company_add_pipe').css('display','none');$('#company_remove_pipe').css('display','block');									
			}
			else
			{
	    		$('#company_add_pipe').css('display','block');$('#company_remove_pipe').css('display','none');									
			}
    		// -----
			if (callback !== undefined)
			{
				callback(result);
			}
			// Update buttons
		});		
	}
};

pagination_pipe_prev = function ()
{
	var page_num = parseInt($('#pipe_page_num').text());
	if (page_num > 1)
	{
		$('#pipe_page_num').text(page_num - 1);
		refresh_pipeline();
	}
};

pagination_pipe_next = function ()
{
	var page_num = parseInt($('#pipe_page_num').text());
	$('#pipe_page_num').text(page_num + 1);
	refresh_pipeline();
};


pagination_suggestion_prev = function ()
{
	var page_num = parseInt($('#suggestion_page_num').text());
	if (page_num > 1)
	{
		$('#suggestion_page_num').text(page_num - 1);
		refresh_suggestions();
	}
};

pagination_suggestion_next = function ()
{
	var page_num = parseInt($('#suggestion_page_num').text());
	$('#suggestion_page_num').text(page_num + 1);
	refresh_suggestions();
}
refresh_status_company = function(use_company_data)
{
	var current_status_v = use_company_data !== undefined ? WOODA.company.visit: $('#company_visited_flag').text().trim();
	var current_status_i = use_company_data !== undefined ? WOODA.company.interest: $('#company_interest_flag').text().trim();
	var current_status_j = use_company_data !== undefined ? WOODA.company.status_motive: $('#company_justification_flag').text().trim();

	// Refesh data
	
	$('#company_visited_flag').text(current_status_v);
	$('#company_interest_flag').text(current_status_i);
	$('#company_justification_flag').text(current_status_j);

	if (current_status_v !== '') // Not ready yet
	{
		if (current_status_v === 'SI')
		{		
			$('.company_visited').css('display','block');
			$('#company_interest_box').css('display','block');
			if (current_status_i === "SI")
			{
					$('#company_justification_box').css('display','block');
					$('.company_interested').css('display','block');
					$('.company_not_interested').css('display','none');		
			}
			else
			{
					$('#company_justification_box').css('display','block');
					$('.company_interested').css('display','none');
					$('.company_not_interested').css('display','block');	
			}
		}
		else
		{
			
			$('.company_visited').css('display','none');
			
			$('.company_interested').css('display','none');
			$('.company_not_interested').css('display','none');		
			
			$('#company_interest_box').css('display','none');
			$('#company_justification_box').css('display','none');
			
		}
	}
};

change_company_visited = function(status)
{
	var current_status = $('#company_visited_flag').text().trim();
	if (current_status !== status)
	{
		// Call update
		$('#company_visited_flag').text(status);	
		$('#company_justification_flag').text('');
		refresh_status_company();
		update_company_status();
	}
};

change_company_interested = function(status)
{
	var current_status = $('#company_interest_flag').text().trim();
	
	if (current_status !== status)
	{
		$('#company_interest_flag').text(status);	
		$('#company_justification_flag').text('');
		refresh_status_company();
		update_company_status();
	}
};

change_company_justification = function(status)
{
	var current_status = $('#company_justification_flag').val();
	if (current_status !== status)
	{
		$('#company_justification_flag').text(status);
		refresh_status_company();
		update_company_status();		
	}
};

forgot_password = function()
{
	var texto = encodeURI($('#userName').val());
	window.open("mailto:soporte.bankinter@equifax.com?subject=Ayuda - He olvidado mi clave&body=Mi usuario es: "+texto);
};

help_support = function()
{
	var texto = encodeURI($('#userName').val());
	window.open("mailto:soporte.bankinter@equifax.com?subject=Ayuda - Consulta&body=Mi usuario es: "+WOODA.user.name);
};

