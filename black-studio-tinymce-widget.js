// Activate visual editor
function black_studio_activate_visual_editor(id) {
   jQuery('#'+id).addClass("mceEditor");
	if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
		black_studio_deactivate_visual_editor(id);
		tinyMCE.execCommand("mceAddControl", false, id);
    }
}
// Deactivate visual editor
function black_studio_deactivate_visual_editor(id) {
	if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
		if (typeof(tinyMCE.get(id)) == "object") {
			content = tinyMCE.get(id).getContent();
			tinyMCE.execCommand("mceRemoveControl", false, id);
			jQuery('textarea#'+id).val(content);
		}
    }
}
// Activate editor deferred (used upon opening the widget)
function black_studio_open_deferred_activate_visual_editor(id) {
	jQuery('div.widget:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]').each(function() {
		// If textarea is visible and animation/ajax has completed then trigger a click to Visual button and enable the editor
		if (jQuery('div.widget:has(#' + id + ') :animated').size() == 0 && typeof(tinyMCE.get(id)) != "object" && jQuery('#' + id).is(':visible')) {
			jQuery('a[id^=widget-black-studio-tinymce][id$=visual]', jQuery(this).parents('div.widget')).click();
		}
		// Otherwise wait and retry later (animation ongoing)
		else if (typeof(tinyMCE.get(id)) != "object") {
			setTimeout(function(){black_studio_open_deferred_activate_visual_editor(id);id=null;}, 100);
		}
	});
}

// Activate editor deferred (used upon ajax requests)
function black_studio_ajax_deferred_activate_visual_editor(id) {
	jQuery('div.widget:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]').each(function() {
		// If textarea is visible and animation/ajax has completed then trigger a click to Visual button and enable the editor
		if (jQuery.active == 0 && typeof(tinyMCE.get(id)) != "object" && jQuery('#' + id).is(':visible')) {
			jQuery('a[id^=widget-black-studio-tinymce][id$=visual]', jQuery(this).parents('div.widget')).click();
		}
		// Otherwise wait and retry later (animation ongoing)
		else if (jQuery('div.widget:has(#' + id + ') div.widget-inside').is(':visible') && typeof(tinyMCE.get(id)) != "object") {
			setTimeout(function(){black_studio_ajax_deferred_activate_visual_editor(id);id=null;}, 100);
		}
	});
}


// This variable is necessary to handle media inserts into textarea (html mode)
var edCanvas;

// Document ready stuff
jQuery(document).ready(function(){
	// Event handler for widget opening button
	jQuery('div.widget:has(textarea[id^=widget-black-studio-tinymce]) a.widget-action').live('click', function(){
		txt_area = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget'));
		black_studio_open_deferred_activate_visual_editor(txt_area.attr('id'));
		return false;
    });
	// Event handler for widget saving button
	jQuery('input[id^=widget-black-studio-tinymce][id$=savewidget]').live('click', function(){
		txt_area = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget'));
		if (typeof(tinyMCE.get(txt_area.attr('id'))) == "object") {
			black_studio_deactivate_visual_editor(txt_area.attr('id'));
		}
		// Event handler for ajax complete
		jQuery(this).unbind('ajaxSuccess').ajaxSuccess(function(event, xhr, settings) {
			txt_area = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget'));
			black_studio_ajax_deferred_activate_visual_editor(txt_area.attr('id'));
		});
		return true;
    });
	// Event handler for visual switch button
	jQuery('a[id^=widget-black-studio-tinymce][id$=visual]').live('click', function(){
		jQuery(this).addClass('active');
		jQuery('a[id^=widget-black-studio-tinymce][id$=html]', jQuery(this).parents('div.widget')).removeClass('active');
		jQuery('input[id^=widget-black-studio-tinymce][id$=type]', jQuery(this).parents('div.widget')).val('visual');
		black_studio_activate_visual_editor(jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget')).attr('id'));
 		return false;
	});
	// Event handler for html switch button
	jQuery('a[id^=widget-black-studio-tinymce][id$=html]').live('click', function(){
		jQuery(this).addClass('active');
		jQuery('a[id^=widget-black-studio-tinymce][id$=visual]', jQuery(this).parents('div.widget')).removeClass('active');
		jQuery('input[id^=widget-black-studio-tinymce][id$=type]', jQuery(this).parents('div.widget')).val('html');
		black_studio_deactivate_visual_editor(jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget')).attr('id'));
		return false;
	});
	// Set edCanvas variable when adding from media library (necessary when used in HTML mode)
	jQuery('.editor_media_buttons a').live('click', function(){
		edCanvas = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget')).get();
	});
});