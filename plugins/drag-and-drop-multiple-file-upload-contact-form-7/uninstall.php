<?php

	// if uninstall.php is not called by WordPress, die
	if (!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}

	// Lists of all options
	$options = array('drag_n_drop_heading_tag','drag_n_drop_mail_attachment','drag_n_drop_text','drag_n_drop_separator','drag_n_drop_browse_text','drag_n_drop_error_server_limit','drag_n_drop_error_failed_to_upload','drag_n_drop_error_files_too_large','drag_n_drop_error_invalid_file','drag_n_drop_error_max_file','drag_n_drop_error_min_file','drag_n_drop_disable_btn');

	// Loop and delete options
	foreach( $options as $option ) {
		delete_option( $option );
	}