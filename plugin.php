<?php
/**
 * Plugin Name:       Event Tickets Extension: Attendee Notifications
 * Plugin URI:        https://github.com/soderlind/tribe-ext-event-attendee-notifications
 * GitHub Plugin URI: https://github.com/soderlind/tribe-ext-event-attendee-notifications
 * Description:       Send notifications and reminders
 * Version:           0.0.1
 * Author:            Per Soderlind
 * Author URI:        https://soderlind.no
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-event-attendee-notifications
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

/**
 * Define the base file that loaded the plugin for determining plugin path and other variables.
 *
 * @since 0.0.1
 *
 * @var string Base file that loaded the plugin.
 */
define( 'TRIBE_EXTENSION_EVENT_ATTENDEE_NOTIFICATIONS_FILE', __FILE__ );

/**
 * Register and load the service provider for loading the extension.
 *
 * @since 0.0.1
 */
function tribe_extension_event_attendee_notifications() {
	// When we dont have autoloader from common we bail.
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	// Register the namespace so we can the plugin on the service provider registration.
	Tribe__Autoloader::instance()->register_prefix(
		'\\Tribe\\Extensions\\Soderlind\Notifications\\',
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tribe',
		'event-attendee-notifications'
	);

	// Deactivates the plugin in case of the main class didn't autoload.
	if ( ! class_exists( '\Tribe\Extensions\Soderlind\Notifications\Plugin' ) ) {
		tribe_transient_notice(
			'event-attendee-notifications',
			'<p>' . esc_html__( 'Couldn\'t properly load "Event Tickets Extension: Attendee Notifications" the extension was deactivated.', 'tribe-event-attendee-notifications' ) . '</p>',
			[],
			// 1 second after that make sure the transiet is removed.
			1
		);

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( __FILE__, true );
		return;
	}

	tribe_register_provider( '\Tribe\Extensions\\Soderlind\Notifications\Plugin' );
}

// Loads after common is already properly loaded.
add_action( 'tribe_common_loaded', 'tribe_extension_event_attendee_notifications' );
