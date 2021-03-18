<?php
/**
 * Handles registering all Assets for the Plugin.
 *
 * To remove a Asset you can use the global assets handler:
 *
 * ```php
 *  tribe( 'assets' )->remove( 'asset-name' );
 * ```
 *
 * @since 0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications
 */
namespace Tribe\Extensions\Soderlind\Notifications;

/**
 * Register Assets.
 *
 * @since 0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications
 */
class Assets extends \tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_attendee_notifications.assets', $this );

		$plugin = tribe( Plugin::class );

	}
}
