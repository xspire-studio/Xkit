"use strict";

(function($){
	$(".acf-field[data-type='typography'] .js-select2").select2();

	/*
	 * Initializing variables
	 */
	var objStandartFonts = JSON.parse(typographyVar.listStandartFonts);
	var objGoogleFonts = JSON.parse(typographyVar.listGoogleFonts);


	/*
	 * Font Family list by attribute
	 */
	$(".acf-field[data-type='typography'] .font-familys").each(function(){
		$(this).select2( {
			query: function (query) {
				//StandartFonts
				var standartFonts = new Array();
				var key;
				for (key in  objStandartFonts) {
					var match1 = objStandartFonts[key].toLowerCase();
					var match2 = query.term.toLowerCase();

					if(match1.indexOf(match2) > -1){
						standartFonts.push({id: objStandartFonts[key], text: objStandartFonts[key], data: 100000+parseInt(key) });
					}
				}

				//googleFonts
				var googleFonts = new Array();
				var key;   
				for (key in  objGoogleFonts) {
					var match1 = objGoogleFonts[key].family.toLowerCase();
					var match2 = query.term.toLowerCase();

					if(match1.indexOf(match2) > -1){
						googleFonts.push({id: objGoogleFonts[key].family, text: objGoogleFonts[key].family, data: key });
					}
				}

				// full list
				var data = { results: [
							{ text: 'Standart fonts', children: standartFonts },
							{ text: 'Google fonts',   children: googleFonts }
						] };

				query.callback(data);
			},
			initSelection : function (element, callback) {
				var elementText = $(element).val();

				var data = {id: elementText, text: elementText};
				callback(data);
			}
		});
	});

	/*
	 * First loade Font Weight
	 */
	$(".acf-field[data-type='typography'] .select2-weight").each(function(){
		var name_font = "";
		name_font = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();

		var is_standart = $.inArray( name_font, objStandartFonts );

		var list = [];
		var key, i, l;

		if( is_standart >= 0 ){
			list.push({id: "400" , text: "400"});
			list.push({id: "bold" , text: "bold"});
		} else {
			for (key in  objGoogleFonts) {
				if (objGoogleFonts[key].family == name_font) {
					for (i in  objGoogleFonts[key].variants) {
						if (objGoogleFonts[key].variants[i] == "regular") {
							list.push({id: "400" , text: "400"});
						} else {
							list.push({id: objGoogleFonts[key].variants[i] , text: objGoogleFonts[key].variants[i]});
						}
					}
				}
			}
		}

		$(this).select2({
			data: list,
			placeholder: '400',
		});

		for (l in  list) {
			if( list['l'] == '400' ){
				var selectWeight = '400';
				break;
			}
			else{
				var selectWeight = $(this).val();
			}
		}

		$(this).select2('data', { id: selectWeight, text: selectWeight });
	});

	/*
	 * Loade Font Weight by attribute.
	 */
	$(".acf-field[data-type='typography'] .font-familys.select2-container").each(function(){

		var selElement = $(this);

		$(this).on("change", function(e) {
			if( e.added.data >= 100000 ){
				var is_standart = true;
				preview(objStandartFonts[e.added.data-100000], "400", $(this));
			} else {
				var is_standart = false;
				preview(objGoogleFonts[e.added.data].family, "400", $(this));
			}

			selElement.parents(".acf-field[data-type='typography']").find(".acf-typography-font-weight input.select2-container.font-weight").select2({
				
				query: function (query) {
					var data = {results: []}, i;
					
					if( is_standart ){
						data.results.push({id: "400" , text: "400"});
						data.results.push({id: "bold" , text: "bold"});
					} else {
						for (i in  objGoogleFonts[e.added.data].variants) {
							if (objGoogleFonts[e.added.data].variants[i] == "regular") {
								data.results.push({ id: "400" , text: "400" });
							} else{
								data.results.push({ id: objGoogleFonts[e.added.data].variants[i] , text: objGoogleFonts[e.added.data].variants[i] });
							}
						}
					}

					query.callback(data);
				},

				initSelection : function (element, callback) {
					var data = {id: "400" , text: "400"};
					selElement.parents(".acf-field[data-type='typography']").find(".acf-typography-font-weight input.select2-container.font-weight").val("400");

					callback(data);
				}
			});
		});
	});

	/*
	* Font style change.
	*/
	$(".acf-field[data-type='typography'] .font-weight").each(function(){
		$(this).on("change",function(e){
			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();

			preview(name, e.added.id, $(this));
		});
	});

	/*
	 * Font size change.
	 */
	$(".acf-field[data-type='typography'] .sizeF").each(function(){
		$(this).on("input",function(){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("font-size", $(this).val() + "px");

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();
			
			preview(name, stl, $(this));
		});
	});

	/*
	 * Font line change.
	 */
	$(".acf-field[data-type='typography'] .lineF").each(function(){
		$(this).on("input",function(){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("line-height", $(this).val()+ "px");

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();
			
			preview(name, stl, $(this));
		});
	});

	/*
	 * Letter Spacing change.
	 */
	$(".acf-field[data-type='typography'] .letter-spacing").each(function(){
		$(this).on("input",function(){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("letter-spacing", $(this).val()+ "px");

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();

			preview(name, stl, $(this));
		});
	});

	/*
	 * Font Style change.
	 */
	$(".acf-field[data-type='typography'] .font-style").each(function(){
		$(this).on("change",function(){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("font-style", $(this).val());

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();

			preview(name, stl, $(this));
		});
	});

	/*
	 * Font align change.
	 */
	$(".acf-field[data-type='typography'] .alignF").each(function(){
		$(this).on("change",function(){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("text-align", $(this).val());

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();

			preview(name, stl, $(this));
		});
	});

	/*
	 * Font color.
	 */
	$(".acf-field[data-type='typography'] .rey-color").wpColorPicker({
		change: function(event, ui){
			$(this).parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("color", ui.color);

			var name = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-familys").val();
			var stl = $(this).parents(".acf-field[data-type='typography']").find(".acf-typography-font-familys input.select2-container.font-weight").val();

			preview(name, stl, $(this));
		}
	});
	

	/*
	 * Preview function.
	 */
	function preview(name, stl, element) {
		element.parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").css("font-family", name);

		var css = element.parents(".acf-field[data-type='typography']").find(".acf-typography-preview .preview_font").attr('style');
		
		var is_standart = $.inArray( name, objStandartFonts );

		var font = ( is_standart >= 0 ) ? "" : "&font=" + name;

		jQuery(element).parents(".acf-field[data-type='typography']").find(".acf-typography-preview iframe").attr( "src", typographyVar.dir + "preview.php?css="+css+font+"&wi="+stl);
	}
})(jQuery);