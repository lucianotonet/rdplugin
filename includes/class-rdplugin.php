<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://lucianotonet.com
 * @since      1.0.0
 *
 * @package    Rdplugin
 * @subpackage Rdplugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rdplugin
 * @subpackage Rdplugin/includes
 * @author     Luciano <contato@lucianotonet.com>
 */
class Rdplugin {

	/**
	 * The RD Station API Url
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected $api_url;

	/**
	 * The RD Station Token
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected $token_rdstation;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rdplugin_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'rdplugin';
		$this->version = '1.0.0';

		$options = get_option( 'rd_plugin_settings' );

		$this->token_rdstation  = isset( $_REQUEST['token_rdstation'] ) ? $_REQUEST['token_rdstation'] : $options['rd_plugin_rdstation_token'];
		$this->api_url    		= isset( $_REQUEST['apiurl_rdstation'] ) ? $_REQUEST['apiurl_rdstation'] : $options['rd_plugin_rdstation_apiurl'];

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rdplugin_Loader. Orchestrates the hooks of the plugin.
	 * - Rdplugin_i18n. Defines internationalization functionality.
	 * - Rdplugin_Admin. Defines all hooks for the admin area.
	 * - Rdplugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rdplugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rdplugin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rdplugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rdplugin-public.php';

		$this->loader = new Rdplugin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rdplugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rdplugin_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rdplugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'rd_plugin_add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'rd_plugin_settings_init' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rdplugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wpcf7_mail_sent', $this, 'addLeadConversionToRdstationCrmViaWpCf7' );
		$this->loader->add_filter( 'request', $this, 'addLeadConversionToRdstationCrmViaAnyForm' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rdplugin_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	public function addLeadConversionToRdstationCrm( $rdstation_token, $identifier, $data_array ) {

		$data_array["token_rdstation"] = $this->rdstation_token;

		if ( empty( $data_array["identificador"] ) && !empty( $identifier ) ) {
			$data_array["identificador"] = $identifier;
		}
		if ( empty( $data_array["email"] ) and isset( $data_array["your-email"] ) ) {
			$data_array["email"] = $data_array["your-email"];
		}
		if ( empty( $data_array["c_utmz"] ) ) {
			$data_array["c_utmz"] = $_COOKIE["__utmz"];
		}

		unset(  $data_array["password"],
			$data_array["password_confirmation"],
			$data_array["senha"],
			$data_array["confirme_senha"],
			$data_array["captcha"],
			$data_array["_wpcf7"],
			$data_array["_wpcf7_version"],
			$data_array["_wpcf7_unit_tag"],
			$data_array["_wpnonce"],
			$data_array["_wpcf7_is_ajax_call"],
			$data_array["your-email"]
		);

		if ( !empty( $data_array["token_rdstation"] ) && !( empty( $data_array["email"] ) && empty( $data_array["email_lead"] ) ) ) {

			$response = wp_remote_post( $this->api_url, array(
					'body' => $data_array
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: $error_message";
			}

		}
	}



	public function addLeadConversionToRdstationCrmViaAnyForm( $input ) {

		// ANY REQUEST THAT CONTAINS EMAIL
		if ( isset( $_REQUEST['email'] ) and !empty( $_REQUEST['email'] ) and is_email( urldecode( $_REQUEST['email'] ) ) ) {

			$form_data['email']    = $_REQUEST['email'];
			$form_data['identifier'] = ( isset( $_REQUEST['identificador'] ) and !empty( $_REQUEST['identificador'] ) ) ? $_REQUEST['identificador'] : null;
			$form_data['nome']   = ( isset( $_REQUEST['nome'] )    and !empty( $_REQUEST['nome'] ) ) ? $_REQUEST['nome'] : null;
			$form_data['empresa']  = ( isset( $_REQUEST['empresa'] )   and !empty( $_REQUEST['empresa'] ) ) ? $_REQUEST['empresa'] : null;
			$form_data['cargo']   = ( isset( $_REQUEST['cargo'] )   and !empty( $_REQUEST['cargo'] ) ) ? $_REQUEST['cargo'] : null;
			$form_data['telefone']  = ( isset( $_REQUEST['telefone'] )   and !empty( $_REQUEST['telefone'] ) ) ? $_REQUEST['telefone'] : null;
			$form_data['celular']  = ( isset( $_REQUEST['celular'] )   and !empty( $_REQUEST['celular'] ) ) ? $_REQUEST['celular'] : null;
			$form_data['website']  = ( isset( $_REQUEST['website'] )   and !empty( $_REQUEST['website'] ) ) ? $_REQUEST['website'] : null;
			$form_data['twitter']  = ( isset( $_REQUEST['twitter'] )   and !empty( $_REQUEST['twitter'] ) ) ? $_REQUEST['twitter'] : null;
			$form_data['c_utmz']  = ( isset( $_REQUEST['c_utmz'] )   and !empty( $_REQUEST['c_utmz'] ) ) ? $_REQUEST['c_utmz'] : null;
			$form_data['created_at'] = ( isset( $_REQUEST['created_at'] )  and !empty( $_REQUEST['created_at'] ) ) ? $_REQUEST['created_at'] : null;
			$form_data['tags']   = ( isset( $_REQUEST['tags'] )    and !empty( $_REQUEST['tags'] ) ) ? $_REQUEST['tags'] : null;

			$this->addLeadConversionToRdstationCrm( $this->token_rdstation, null, $form_data );

		}

		return $input;

	}



	public function addLeadConversionToRdstationCrmViaWpCf7( $cf7 ) {

		// CONTACT FORM 7
		if ( class_exists( 'WPCF7_Submission' ) ) {
			$submission = WPCF7_Submission::get_instance();
			if ( $submission ) {
				$form_data = $submission->get_posted_data();
			}
		}

		$this->addLeadConversionToRdstationCrm( $this->token_rdstation, null, $form_data );

	}

}