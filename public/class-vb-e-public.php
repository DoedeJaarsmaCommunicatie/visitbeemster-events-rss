<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://doedejaarsma.nl/
 * @since      1.0.0
 *
 * @package    Vb_E
 * @subpackage Vb_E/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Vb_E
 * @subpackage Vb_E/public
 * @author     Doede Jaarsma communicatie <support@doedejaarsma.nl>
 */
class Vb_E_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
 
	public function register_rest_route() {
        require_once __DIR__ . '/controllers/class-vb-e-Upcoming-Events.php';
        
        $api = new Vbe_Upcoming_Events();
        $api->register_routes();
    }
}
