<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://lucianotonet.com
 * @since      1.0.0
 *
 * @package    Rdplugin
 * @subpackage Rdplugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rdplugin
 * @subpackage Rdplugin/admin
 * @author     Luciano <contato@lucianotonet.com>
 */
class Rdplugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rdplugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rdplugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rdplugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rdplugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rdplugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rdplugin-admin.js', array( 'jquery' ), $this->version, false );

	}




	public function rd_plugin_add_admin_menu(  ) { 

		add_options_page( 'RD Station', 'RD Station', 'manage_options', 'rd_plugin', array( $this, 'rd_plugin_options_page' ) );

	}


	public function rd_plugin_settings_init(  ) { 

		register_setting( 'pluginPage', 'rd_plugin_settings' );

		add_settings_section(
			'rd_plugin_pluginPage_section', 
			__( '', 'rd_plugin' ), 
			array( $this, 'rd_plugin_settings_section_callback' ), 
			'pluginPage'
		);

		add_settings_field( 
			'rd_plugin_rdstation_token', 
			__( 'TOKEN RD STATION', 'rd_plugin' ), 
			array( $this, 'rd_plugin_rdstation_token_render' ), 
			'pluginPage', 
			'rd_plugin_pluginPage_section' 
		);

		add_settings_field( 
			'rd_plugin_rdstation_apiurl', 
			__( 'API URL', 'rd_plugin' ), 
			array( $this, 'rd_plugin_rdstation_apiurl_render' ), 
			'pluginPage', 
			'rd_plugin_pluginPage_section' 
		);


	}


	public function rd_plugin_rdstation_token_render(  ) { 

		$options = get_option( 'rd_plugin_settings' );
		?>
		<input type='text' name='rd_plugin_settings[rd_plugin_rdstation_token]' value='<?php echo $options['rd_plugin_rdstation_token']; ?>'>
		<br>
		<small><?php _e( 'Obtenha o seu token <a href="https://www.rdstation.com.br/integracoes" target="_blank">aqui</a>', 'rd_plugin' ) ?></small>
		<?php

	}

	public function rd_plugin_rdstation_apiurl_render(  ) { 

		$options = get_option( 'rd_plugin_settings' );
		?>
		<input type='text' name='rd_plugin_settings[rd_plugin_rdstation_apiurl]' value='<?php echo isset($options['rd_plugin_rdstation_apiurl']) ? $options['rd_plugin_rdstation_apiurl'] : "http://www.rdstation.com.br/api/1.2/conversions"; ?>' placeholder="http://www.rdstation.com.br/api/1.2/conversions" >
		<?php

	}


	public function rd_plugin_settings_section_callback(  ) { 

		echo __( 'Configurações do plugin RD Station', 'rd_plugin' );

	}


	public function rd_plugin_options_page(  ) { 

		?>
		<form action='options.php' method='post'>
			
			<h2><?php _e( 'RD Station', 'plugin' ) ?></h2>
			
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>
			
		</form>
		<?php

	}

}
