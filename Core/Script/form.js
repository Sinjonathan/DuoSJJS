$(function() {
	
	$(".datepicker").datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
    });
});

function validateForm(){
	
	var mode = $('#request').val();
	var formID = $('#formID').val();
	var pluginPath = $('#pluginPath').val();
	var param = [];
	var values = [];
	var cpt = 0;

	$('#'+formID+ ' .field' ).each(function() {
		if (!($(this).attr('type') == 'radio' && !$(this).is(':checked'))) {
			param[cpt] = $(this).attr('id');
			values[cpt] = $(this).val();
			cpt++;
		}
	});
	
	
	$.post(pluginPath + "/Core/Script/script.php", {'formID': formID, 'param': param, 'values': values, 'mode': mode}, function(data) {
		/*if (data == "1") {
			if (mode == "insert") {
				$('#alert-success-insert').css("display", "block").delay(5000).fadeIn(2000);
			}else if (mode == "update") {
				$('#alert-success-update').css("display", "block").delay(5000).fadeIn(2000);
			}
		}else if (data == "0") {
			if (mode == "insert") {
				$('#alert-failure-insert').css("display", "block").delay(5000).fadeIn(2000);
			}else if (mode == "update") {
				$('#alert-failure-update').css("display", "block").delay(5000).fadeIn(2000);
			}
		}else if (data == "-1") {
			$('#alert-error').css("display", "block").delay(5000).fadeIn(2000);
		}*/
	},"text");
}

(function(factory) {
	if ( typeof define === "function" && define.amd ) {
		define([ "../jquery.ui.datepicker" ], factory );
	}else{
		factory( jQuery.datepicker );
	}
}(function(datepicker) {
	datepicker.regional['fr'] = {
		closeText: 'Fermer',
		prevText: 'Précédent',
		nextText: 'Suivant',
		currentText: 'Aujourd\'hui',
		monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
		monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
		dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	datepicker.setDefaults(datepicker.regional['fr']);
	return datepicker.regional['fr'];
}));