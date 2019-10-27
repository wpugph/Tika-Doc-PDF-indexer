=== Tika Doc PDF Indexer ===
Contributors: carl-alberto
Tags: wordpress, plugin
Requires at least: 4.8
Tested up to: 5.2.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This creates a custom post type and let you upload your documents. Once a doc extract the text from it and put it in the wp_content table. This utilizes the Apache Tika Project https://tika.apache.org/ and this should be installed in your server.

== Installation ==

Installing "Tika Doc PDF Indexer" can be done either by searching for "Tika Doc PDF Indexer" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

After plugin installation:

1. Make sure the plugin has the correct path to the Tika installation and Java installation
2. Add a new document
3. Attach a document by uploading a valid file that can be read by Tika
4. Text data will be saved in the wp_content column

== Changelog ==

= 1.0.0 =
* 2019-10-21
* Initial release
* Only support pdf at the moment
