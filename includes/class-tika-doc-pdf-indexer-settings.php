<?php
/**
 * Settings class file.
 *
 * @package WordPress Plugin Template/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Tika_Doc_PDF_Indexer_Settings {

	/**
	 * The single instance of Tika_Doc_PDF_Indexer_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.1
	 */
	private static $instance = null; //phpcs:ignore

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.1
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.1
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.1
	 */
	public $settings = array();

	/**
	 * Allowed html.
	 *
	 * @var array
	 */
	public $allowed_htmls_form = [
		'a'      => [
			'href'  => [],
			'title' => [],
		],
		'input'  => [
			'id'          => [],
			'type'        => [],
			'name'        => [],
			'placeholder' => [],
			'value'       => [],
			'class'       => [],
			'checked'     => [],
		],
		'select' => [
			'id'          => [],
			'type'        => [],
			'name'        => [],
			'placeholder' => [],
			'value'       => [],
			'multiple'    => [],
		],
		'option' => [
			'id'          => [],
			'type'        => [],
			'name'        => [],
			'placeholder' => [],
			'value'       => [],
			'multiple'    => [],
			'selected'    => [],
		],
		'label'  => [
			'for'   => [],
			'title' => [],
		],
		'span'   => [
			'class' => [],
			'title' => [],
		],
		'div'    => [
			'class' => [],
			'id'    => [],
		],
		'table'  => [
			'scope' => [],
			'title' => [],
			'class' => [],
			'role'  => [],
		],
		'tbody'  => [
			'scope' => [],
			'title' => [],
			'class' => [],
			'role'  => [],
		],
		'th'     => [
			'scope' => [],
			'title' => [],
		],
		'tr'     => [],
		'td'     => [],
		'p'      => [],
		'br'     => [],
		'h2'     => [],
		'em'     => [],
		'strong' => [],
		'form'   => [
			'method'      => [],
			'type'        => [],
			'name'        => [],
			'placeholder' => [],
			'value'       => [],
			'multiple'    => [],
			'selected'    => [],
			'action'      => [],
			'enctype'     => [],
		],

	];

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'tdpi_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'Tika Indexer Settings', 'tika-doc-pdf-indexer' ), __( 'Tika Indexer Settings', 'tika-doc-pdf-indexer' ), 'manage_options', $this->parent->_token . '_settings', array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below.
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field.
		// If you're not including an image upload then you can leave this function call out.
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.1', true );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'tika-doc-pdf-indexer' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'description' => __( 'Essential settings that will make the PDF Doc Indexer work.', 'tika-doc-pdf-indexer' ),
			'fields'      => array(
				array(
					'id'          => 'java_location',
					'label'       => __( 'Java binaries location', 'tika-doc-pdf-indexer' ),
					'description' => __( 'This is where the java binary is located relative to server root eg. /usr/bin/java', 'tika-doc-pdf-indexer' ),
					'type'        => 'text',
					'default'     => '/usr/bin/java',
					'placeholder' => __( '/usr/bin/java', 'tika-doc-pdf-indexer' ),
				),
				array(
					'id'          => 'tika_jar_location',
					'label'       => __( 'Tika jar file location', 'tika-doc-pdf-indexer' ),
					'description' => __( 'This is where the jar file location relative to server root eg. /srv/bin/tika-app-1.18.jar', 'tika-doc-pdf-indexer' ),
					'type'        => 'text',
					'default'     => '/srv/bin/tika-app-1.18.jar',
					'placeholder' => __( '/srv/bin/tika-app-1.18.jar', 'tika-doc-pdf-indexer' ),
				),
				array(
					'id'          => 'tika_wp_content',
					'label'       => __( 'wp-content location override', 'tika-doc-pdf-indexer' ),
					'description' => __( 'Modify this if the wp-content folder is in a non standard location other than /wp-content/', 'tika-doc-pdf-indexer' ),
					'type'        => 'text',
					'default'     => '/wp-content/',
					'placeholder' => __( '/wp-content/', 'tika-doc-pdf-indexer' ),
				),
				array(
					'id'          => 'supported_ext',
					'label'       => __( 'Supported Extensions', 'tika-doc-pdf-indexer' ),
					'description' => __( 'You can select multiple suported file types.', 'tika-doc-pdf-indexer' ),
					'type'        => 'select_multi',
					'options'     => array(
						'pdf' => 'PDF',
						'doc' => 'Doc',
						'txt' => 'Text',
					),
					'default'     => array( 'pdf' ),
				),
				array(
					'id'          => 'php_timeout_override',
					'label'       => __( 'PHP execution timeout override', 'tika-doc-pdf-indexer' ),
					'description' => __( 'This is the php execution timeout, depends on the server if this can be overridden. Consult with your host. Default is 60 seconds', 'tika-doc-pdf-indexer' ),
					'type'        => 'text',
					'default'     => '60',
					'placeholder' => __( '60 seconds', 'tika-doc-pdf-indexer' ),
				),
				array(
					'id'          => 'index_attachments',
					'label'       => __( 'Always index attachments', 'tika-doc-pdf-indexer' ),
					'description' => __( 'All supported attachments will be indexed. Works with the Solr Search for WP plugin to search for the Attachment Post Type', 'tika-doc-pdf-indexer' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
				array(
					'id'          => 'enable_tdpi_cpt',
					'label'       => __( 'Enable Documents Custom Post type', 'tika-doc-pdf-indexer' ),
					'description' => __( 'Enable Documents Custom Post type', 'tika-doc-pdf-indexer' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
			),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				$data['title'] = '';
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = $this->settings[ $section['id'] ]['description'] . "\n";
		echo esc_html( $html );
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html      = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Tika Doc PDF Indexer Settings', 'tika-doc-pdf-indexer' ) . '</h2>' . "\n";

			$tab = '';

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . sanitize_text_field( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . sanitize_text_field( __( 'Save Settings', 'tika-doc-pdf-indexer' ) ) . '" />' . "\n";
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";

		echo wp_kses( $html, $this->allowed_htmls_form );
	}

	/**
	 * Main Tika_Doc_PDF_Indexer_Settings Instance
	 *
	 * Ensures only one instance of Tika_Doc_PDF_Indexer_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.1
	 * @static
	 * @see Tika_Doc_PDF_Indexer()
	 * @param object $parent Object instance.
	 * @return Main Tika_Doc_PDF_Indexer_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $parent );
		}
		return self::$instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?' ), $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?' ), $this->parent->_version ) );
	} // End __wakeup()
}
