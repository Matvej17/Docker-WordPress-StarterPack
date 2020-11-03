jQuery(document).ready(function($){

	// Fires when an Ajax form submission has completed successfully, and mail has been sent.
	document.addEventListener( 'wpcf7mailsent', function( event ) {

		// Get input type file element
		var inputFile = $('.wpcf7-drag-n-drop-file');
		var $form = inputFile.parents('form');

		// Reset upload list for multiple fields
		if( inputFile.length > 0 ) {
			$.each( inputFile, function(){
				// Reset file counts
				localStorage.setItem( $(this).attr('data-name') + '_count_files', 1 );
			});
		}else {
			// Reset file counts
			localStorage.setItem( inputFile.attr('data-name') + '_count_files', 1 );
		}

		// Remove status / progress bar
		$('.dnd-upload-status', $form ).remove();
		$('.dnd-upload-counter span', $form ).text('0');
		$('span.has-error-msg', $form ).remove();

	}, false );

	window.initDragDrop = function () {

		// Get text object options/settings from localize script
		var TextOJB = dnd_cf7_uploader.drag_n_drop_upload;

		// Support Multiple Fileds
		$('.wpcf7-drag-n-drop-file').CodeDropz_Uploader({
			'color'				:	'#fff',
			'ajax_url'			: 	dnd_cf7_uploader.ajax_url,
			'text'				: 	TextOJB.text,
			'separator'			: 	TextOJB.or_separator,
			'button_text'		:	TextOJB.browse,
			'server_max_error'	: 	TextOJB.server_max_error,
			'on_success'		:	function( input, progressBar, response ){

				// Progressbar Object
				var $progressDetails = $('#' + progressBar, input.parents('.codedropz-upload-wrapper') );
				var $form = input.parents('form');
				var $span =  $('.wpcf7-acceptance', $form );
				var $input = $('input:checkbox', $span);

				// If it's complete remove disabled attribute in button
				if( $span.hasClass( 'optional' ) || $input.is( ':checked' ) || $span.length == 0 || $form.hasClass('wpcf7-acceptance-as-validation') )  {
					setTimeout(function(){ $('input:submit', $form ).removeAttr('disabled'); }, 1);
				}

				// Append hidden input field
				$progressDetails
					.find('.dnd-upload-details')
						.append('<span><input type="hidden" name="'+ input.attr('data-name') +'[]" value="'+ response.data.path +'/'+ response.data.file +'"></span>');

				// Update counter
				var $files_counter = ( Number( localStorage.getItem( input.data('name') + '_count_files' ) ) - 1 );
				$('.dnd-upload-counter span', input.parents('.codedropz-upload-wrapper')).text( $files_counter );
			}
		});

	}

	window.initDragDrop();

});