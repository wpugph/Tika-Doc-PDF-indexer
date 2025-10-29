<?php
/**
 * Custom Functions class file.
 *
 * @package Tika Doc PDF Indexer Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_attachment', 'tdpi_extract_data' );

/**
 * Extracts the metadata from the attached document.
 *
 * @param [type] $post_id Post id of the attached doc.
 * @return boolean
 */
function tdpi_extract_data( $post_id ) {

	// TODO: immediately exit if not supported cpt.
	global $wpdb;
	$java            = get_option( 'tdpi_java_location', '/usr/bin/java' );
	$tika            = get_option( 'tdpi_tika_jar_location', '/srv/bin/tika-app-1.18.jar' );
	$wp_content_path = get_option( 'tika_wp_content', '/wp-content/' );
	$allowed         = get_option( 'tdpi_supported_ext', array( 'pdf' ) );
	// phpcs:disable -- finding a proper way to include document_root $server variable
	if ( isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
		$wp_base = wp_unslash( ( $_SERVER['DOCUMENT_ROOT'] ) );
	} // phpcs:enable

	$url = get_the_guid( $post_id );

	// Check first if Tika is allowed on this file type.
	$extension = pathinfo( $url, PATHINFO_EXTENSION );
	if ( ! in_array( $extension, $allowed, true ) ) {
		return true;
	}

	$middle   = strpos( $url, $wp_content_path );
	$filename = strstr( $url, $wp_content_path, false );

	$abs_path = $wp_base . $filename;
	$command  = $java . ' -jar ' . $tika . ' -t ' . $abs_path;

	$og = ini_get( 'max_execution_time' );
	set_time_limit( 60 );

	$tika_loc = get_option( 'tdpi_supported_ext' );
	write_log( $middle );
	write_log( $filename );
	write_log( $abs_path );
	write_log( $url );

	$descriptorspec = array(
		0 => array( 'pipe', 'r' ),
		1 => array( 'pipe', 'w' ),
		2 => array( 'file', '/tmp/error-output.txt', 'a' ), // TODO: add custom error logging location.
	);
	// phpcs:disable -- Process handling by Tika
	$process        = proc_open( $command, $descriptorspec, $pipes );
	$file_data      = '';
	while ( ! feof( $pipes[1] ) ) {
		$file_data .= fgets( $pipes[1], 1024 );
	}
	fclose( $pipes[1] );
	// phpcs:enable

	$return_value = proc_close( $process );
	$tika_data    = $file_data;
	set_time_limit( $og );

	if ( get_post_type( $post_id ) === 'tdpi_doc' ) {
		$parentid = wp_get_post_parent_id( $post_id );
		tdpi_save_indexed_data( $parentid, $tika_data );
	} elseif ( get_post_type( $post_id ) === 'attachment' ) {
		$is_enabled_tdpi_solr_override = get_option( 'tdpi_index_attachments', 'on' );
		if ( 'on' === $is_enabled_tdpi_solr_override ) {
			tdpi_save_indexed_data( $post_id, $tika_data );
		}
	}
}

add_action( 'add_meta_boxes', 'tdpi_add_upload_file_metaboxes' );

/**
 * Save indexed data to post id.
 *
 * @param [type] $post_id post id.
 * @param [type] $tika_data extracted tika data.
 * @return void
 */
function tdpi_save_indexed_data( $post_id, $tika_data ) {
	$my_post = array(
		'ID'           => $post_id,
		'post_content' => $tika_data,
	);
	wp_update_post( $my_post );
}

/**
 * Enables the Solr indexing in all attachments.
 *
 * @return array
 */
function get_post_statuses_override() {
	return array( 'inherit', 'publish' );
}

$is_enabled_tdpi_solr_override = get_option( 'tdpi_index_attachments', 'on' );

if ( 'on' === $is_enabled_tdpi_solr_override ) {
	add_filter( 'solr_post_status', 'get_post_statuses_override' );
}

/**
 * Adds an upload metabox.
 *
 * @return void
 */
function tdpi_add_upload_file_metaboxes() {
	add_meta_box( 'tdpi_file_upload', 'File Upload', 'tdpi_file_upload', 'tdpi_doc', 'normal', 'default' );
}

/**
 * Restrict mimetypes on upload.
 *
 * @param [type] $mimes Returns the mimetypes.
 * @return array Retuns the allowed mimetypes.
 */
function tdpi_restrict_mimetypes( $mimes ) {
	$allowed = get_option( 'tdpi_supported_ext', array( 'pdf' ) );

	$allowed_array = array();
	if ( in_array( 'pdf', $allowed, true ) ) {
		$allowed_array['pdf'] = array( 'application/pdf' );
	}
	if ( in_array( 'txt', $allowed, true ) ) {
		$allowed_array['txt'] = array( 'text/plain' );
	}
	if ( in_array( 'doc', $allowed, true ) ) {
		$allowed_array['doc'] = array( 'application/msword' );
	}
	global $post_type;
	if ( 'tdpi_doc' === $post_type ) {
		$mimes = $allowed_array;
	}
	return $mimes;
}

add_filter( 'upload_mimes', 'tdpi_restrict_mimetypes' );

/**
 * Medua uplaoder metabox.
 *
 * @return void
 */
function tdpi_file_upload() {
	global $post;
	$nonce = sanitize_text_field( wp_create_nonce( plugin_basename( __FILE__ ) ) );
	echo '<input type="hidden" name="tdpi_nonce" id="tdpi_nonce" value="' . esc_attr( $nonce ) . '" />';
	global $wpdb;
	$filename   = get_post_meta( $post->ID, $key = 'tdpi_file', true );
	$media_file = get_post_meta( $post->ID, $key = '_wp_attached_file', true );
	if ( ! empty( $media_file ) ) {
		$filename = $media_file;
	} ?>

	<script type = "text/javascript">
		var file_frame;

		jQuery('#postimagediv').hide();

		jQuery('#upload_image_button').live('click', function(tikadata) {
		tikadata.preventDefault();

		if (file_frame) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery(this).data('uploader_title'),
			button: {
				text: jQuery(this).data('uploader_button_text'),
			},
			multiple: false,
			// library: {
			// 		order: 'DESC',
			// 		// [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in', 'menuOrder' ]
			// 		orderby: 'modified',
			// 		// mime type. e.g. 'application/pdf', update this later for additinoal restricitons when uploading.
			// 		type: [ 'application/pdf' ],
			// 		search: null,
			// 	},

		});
		file_frame.on('select', function(){
			attachment = file_frame.state().get('selection').first().toJSON();
			var url = attachment.url;

			var field = document.getElementById("tdpi_file");

			field.value = url;
		});

		file_frame.open();
	});

	</script>

	<div>
		<table style="width:100%">
			<tr valign = "top">
				<td>
					<input type = "text"
						name = "tdpi_file"
						id = "tdpi_file"
						width = "100%"
						readonly
						value = "<?php echo esc_url( $filename ); ?>"
						style = "width: 100%" />
					<input id = "upload_image_button"
						type = "button"
						value = "Upload">
				</td>
			</tr>
		</table>
		<input type = "hidden"
			name = "img_txt_id"
			id = "img_txt_id"
			value = "" />
	</div>
		<?php

		/**
		 * Enqueue scripts for media uploader.
		 *
		 * @return void
		 */
		function admin_scripts() {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}

		/**
		 * Call styles.
		 *
		 * @return void
		 */
		function admin_styles() {
			wp_enqueue_style( 'thickbox' );
		}
		add_action( 'admin_print_scripts', 'admin_scripts' );
		add_action( 'admin_print_styles', 'admin_styles' );
}

/**
 * Saves tika filename and meta.
 *
 * @param [type] $post_id Post id to be saved to.
 * @param [type] $post Post data array to be saved.
 * @return bool
 */
function tdpi_save_tika_meta( $post_id, $post ) {
	if ( isset( $_POST['tdpi_nonce'] ) ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tdpi_nonce'] ) ), plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	if ( isset( $_POST['tdpi_file'] ) ) {
		$tika_meta['tdpi_file'] = sanitize_text_field( wp_unslash( $_POST['tdpi_file'] ) );

		foreach ( $tika_meta as $key => $value ) {
			if ( 'revision' === $post->post_type ) {
				return;
			}
			$value = implode( ',', (array) $value );
			if ( get_post_meta( $post->ID, $key, false ) ) {
				update_post_meta( $post->ID, $key, $value );
			} else {
				add_post_meta( $post->ID, $key, $value );
			}
			if ( ! $value ) {
				delete_post_meta( $post->ID, $key );
			}
		}
	}
}
add_action( 'save_post', 'tdpi_save_tika_meta', 1, 2 );

// TODO:
// 1) convert to class
// cleanup code for metabox
// cleanup code tika meta
// load option file
// transfer tika meta from attachment to doc so it can be indexed
// eliminate notice
// restrict file uploads
// test solar if metas are now indexed
// Create a solr shortcode
// create solr block
// Extension must have at least one checked
// when uploading, media selection cannot choose other accepted file extension
// eliminate unneecessary functions
// on upload existing file, attach data.
// make the upload file required
// default to upload and restric from selcting uploaded file or the recent file should be indexed.
// Settings should file checks if correct or existing..

// Deploy phpcs runners
// use php unit testing
// minimize ignore
