<?php
/**
 * Plugin Name: Tika Doc PDF indexer
 * Version: 1.0.2
 * Plugin URI: https://wordpress.org/plugins/tika-odc-pdf-indexer/
 * Description: This indexes your Docs or PDFs into meta datas when uploaded. Based on Apache Tika.
 * Author: Carl Alberto
 * Author URI: http://carlalberto.code.blog
 * Requires at least: 4.8
 * Tested up to: 5.2.3
 * Text Domain: tika-doc-pdf-indexer
 * Domain Path: /lang/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 * This plugin is dependent to Apache Tika Project https://tika.apache.org/
 *
 * @package WordPress
 * @author Carl Alberto
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-tika-doc-pdf-indexer.php';
require_once 'includes/class-tika-doc-pdf-indexer-settings.php';
require_once 'includes/class-tika-doc-pdf-indexer-functions.php';
require_once 'includes/lib/class-tika-doc-pdf-indexer-admin-api.php';
require_once 'includes/lib/class-tika-doc-pdf-indexer-post-type.php';

/**
 * Returns the main instance of Tika_Doc_PDF_Indexer to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Tika_Doc_PDF_Indexer
 */
function tika_doc_pdf_indexer() {
	$instance = Tika_Doc_PDF_Indexer::instance( __FILE__, '1.0.2' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Tika_Doc_PDF_Indexer_Settings::instance( $instance );
	}

	return $instance;
}

tika_doc_pdf_indexer();
