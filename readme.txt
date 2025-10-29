=== Tika Doc PDF Indexer ===
Contributors: carl-alberto
Tags: tika, indexer
Requires at least: 4.8
Tested up to: 6.8.3
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will automatically index pdf uploaded files from the media manager.

== Description ==

This plugin will automatically index pdf uploaded files from the media manager. Plugin requires Tika installation and Java binaries to run properly. Default configuration works well in Pantheon as all minimum requirements are installed by default.

This plugin will enable the indexing all attached PDFs automaticall and works well in conjunction with the Pantheon Solr plugin https://wordpress.org/plugins/solr-power/

== Installation ==

Installing "Tika Doc PDF Indexer" can be done either by searching for "Tika Doc PDF Indexer" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

After plugin installation:

1. Make sure the plugin has the correct path to the Tika installation and Java installation.
2. Upload PDF files via the media manager.
3. All extracted data from the Attachment Post Type will be saved in the wp_content column.
4. If using the Pantheon Solr plugin, it will be automatically indexed by default whenever a supported file type is uploaded.

== Changelog ==

= 1.1.1 =
* 2025-10-29
* Bump compatibility to WP version 6.8.3

= 1.0.6 =
* 2022-10-24
* Bump compatibility to WP version 6.0.3

= 1.0.5 =
* 2021-10-9
* Made sure that pdf attachments when uploaded are indexed by the WP Solr plugin

= 1.0.4 =
* 2019-11-6
* Made sure that pdf attachments when uploaded are indexed by the WP Solr plugin

= 1.0.0 =
* 2019-10-21
* Initial release
* Only support pdf at the moment
