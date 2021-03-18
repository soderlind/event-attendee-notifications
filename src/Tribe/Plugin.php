<?php
namespace Tribe\Extensions\Soderlind\Notifications;

/**
 * Class Plugin
 *
 * @since   0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications
 */
class Plugin extends \tad_DI52_ServiceProvider {
	/**
	 * Stores the version for the plugin.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const VERSION = '0.0.1';

	/**
	 * Stores the base slug for the plugin.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const SLUG = 'event-attendee-notifications';

	/**
	 * Stores the base slug for the extension.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const FILE = TRIBE_EXTENSION_EVENT_ATTENDEE_NOTIFICATIONS_FILE;

	/**
	 * @since 0.0.1
	 *
	 * @var string Plugin Directory.
	 */
	public $plugin_dir;

	/**
	 * @since 0.0.1
	 *
	 * @var string Plugin path.
	 */
	public $plugin_path;

	/**
	 * @since 0.0.1
	 *
	 * @var string Plugin URL.
	 */
	public $plugin_url;

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		// Set up the plugin provider properties.
		$this->plugin_path = trailingslashit( dirname( static::FILE ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir, $this->plugin_path );

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_attendee_notifications', $this );
		$this->container->singleton( 'extension.event_attendee_notifications.plugin', $this );
		$this->container->register( PUE::class );

		if ( ! $this->check_plugin_dependencies() ) {
			// If the plugin dependency manifest is not met, then bail and stop here.
			return;
		}

		// Start binds.

		$this->container->register( Reminder::class );

		// End binds.

		$this->container->register( Hooks::class );
		$this->container->register( Assets::class );
	}


	/**
	 * Connect to Creation of an Attendee for RSVP.
	 *
	 * @since 1.0
	 *
	 * @param array $order Array of Attendees from RSVP Order.
	 *
	 * @return array An array of attendee data from a RSVP Order.
	 */
	public function get_rsvp_contact_data_from_order( $attendee ) {

		// Use the First Attendee for the Order Information.
		// $attendee = reset( $order );
		$names    = $this->split_name( $attendee['holder_name'] );

		$attendee_data['email']      = $attendee['holder_email'];
		$attendee_data['name']       = $attendee['holder_name'];
		$attendee_data['first_name'] = $names['first_name'];
		$attendee_data['last_name']  = $names['last_name'];
		$attendee_data['total']      = 0;
		$attendee_data['date']       = get_the_date( 'U', $attendee['attendee_id'] );
		$attendee_data['status']     = $attendee['order_status'];

		return $attendee_data;
	}

	/**
	 * Spilt Full Name into First, Middle, and Last.
	 * https://stackoverflow.com/a/31330346
	 *
	 * @since 1.0
	 *
	 * @param string $string The Name to parse.
	 *
	 * @return array|bool The First, Middle, and Last Name or False if to many names provided.
	 */
	public function split_name( $string ) {
		$arr        = explode( ' ', $string );
		$num        = count( $arr );
		$first_name = $middle_name = $last_name = null;

		if ( $num == 1 ) {
			list( $last_name ) = $arr;
		} elseif ( $num == 2 ) {
			list( $first_name, $last_name ) = $arr;
		} else {
			list( $first_name, $middle_name, $last_name ) = $arr;
		}

		return ( empty( $first_name ) || $num > 3 ) ? false : compact( 'first_name', 'middle_name', 'last_name' );
	}


	/**
	 * Checks whether the plugin dependency manifest is satisfied or not.
	 *
	 * @since 0.0.1
	 *
	 * @return bool Whether the plugin dependency manifest is satisfied or not.
	 */
	protected function check_plugin_dependencies() {
		$this->register_plugin_dependencies();

		return tribe_check_plugin( static::class );
	}

	/**
	 * Registers the plugin and dependency manifest among those managed by Tribe Common.
	 *
	 * @since 0.0.1
	 */
	protected function register_plugin_dependencies() {
		$plugin_register = new Plugin_Register();
		$plugin_register->register_plugin();

		$this->container->singleton( Plugin_Register::class, $plugin_register );
		$this->container->singleton( 'extension.event_attendee_notifications', $plugin_register );
	}
}


//phpcs:disable
if ( ! function_exists( 'write_log' ) ) {
	/**
	* Utility function for logging arbitrary variables to the error log.
	*
	* Set the constant WP_DEBUG to true and the constant WP_DEBUG_LOG to true to log to wp-content/debug.log.
	* You can view the log in realtime in your terminal by executing `tail -f debug.log` and Ctrl+C to stop.
	*
	* @param mixed $log Whatever to log.
	*/
	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_scalar( $log ) ) {
				error_log( $log );
			} else {
				error_log( print_r( $log, true ) );
			}
		}
	}
}
//phpcs:enable
