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
			tinyMCE.execCommand("mceRemoveControl", false, id);
		}
    }
}
// Show editor (used upon opening the widget)
function black_studio_show_visual_editor() {
	jQuery('input[id^=widget-black-studio-tinymce][id$=type][value=visual]').each(function() {
		txt_area = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget'));
		if (!txt_area.is(':hidden')) {
			jQuery('a[id^=widget-black-studio-tinymce][id$=visual]', jQuery(this).parents('div.widget')).click();
		}
	});
}

jQuery(document).ready(function(){
	// Event handler for widget opening button
	jQuery('div.widget:has(textarea[id^=widget-black-studio-tinymce]) a.widget-action').live('click', function(){
 		setTimeout(black_studio_show_visual_editor, 500); // This is delayed to let the animation be completed
		return false;
    });
	// Event handler for widget saving button
	jQuery('input[id^=widget-black-studio-tinymce][id$=savewidget]').live('click', function(){
		txt_area = jQuery('textarea[id^=widget-black-studio-tinymce]', jQuery(this).parents('div.widget'));
		editor = tinyMCE.get(txt_area.attr('id'));
		if (typeof(editor) == "object") {
			content = editor.getContent()
			txt_area.val(content);
		}
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
});