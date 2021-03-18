<?php
/**
 * Handles the Extension plugin dependency manifest registration.
 *
 * @since 0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications
 */

namespace Tribe\Extensions\Soderlind\Notifications;

use Tribe__Abstract_Plugin_Register as Abstract_Plugin_Register;

/**
 * Class Plugin_Register.
 *
 * @since   0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications
 *
 * @see Tribe__Abstract_Plugin_Register For the plugin dependency manifest registration.
 */
class Plugin_Register extends Abstract_Plugin_Register {
	protected $base_dir     = Plugin::FILE;
	protected $version      = Plugin::VERSION;
	protected $main_class   = Plugin::class;
	protected $dependencies = [
		'parent-dependencies' => [
			'Tribe__Events__Main' => '5.4.0',
			'Tribe__Tickets__Main' => '5.1.1',
			'Tribe__Tickets_Plus__Main' => '5.1.1',
		],
	];
}
