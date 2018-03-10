/* Black Studio TinyMCE Widget - JS - Editor setup */

/* global bstw, tinyMCEPreInit */

( function( $ ) {

	function bstwEditorSetup( ed ) {
		ed.on( 'keyup change', function() {
			if ( 'visual' === bstw( ed.id ).get_mode() ) {
				bstw( ed.id ).update_content();
			}
			$( '#' + ed.id ).change();
		});
		$( '#' + ed.id ).addClass( 'active' ).removeClass( 'activating' );
	}

	for ( var id in tinyMCEPreInit.mceInit ) { // eslint-disable-line vars-on-top
		if ( 0 <= id.search( 'black-studio-tinymce' ) ) {
			tinyMCEPreInit.mceInit[ id ].setup = bstwEditorSetup;
		}
	}

}( jQuery ) );
