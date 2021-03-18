<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Extensions\\Soderlind\Notifications\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'extension.event_attendee_notifications.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Extensions\\Soderlind\Notifications\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'extension.event_attendee_notifications.hooks' ), 'some_method' ] );
 * ```
 *
 * @since   0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications;
 */

namespace Tribe\Extensions\Soderlind\Notifications;

/**
 * Class Hooks.
 *
 * @since   0.0.1
 *
 * @package Tribe\Extensions\Soderlind\Notifications;
 */
class Reminder extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_attendee_notifications.reminder', $this );

		$this->send_reminder();
	}

	public function send_reminder() {
		$event_id = isset( $_GET['event_id'] ) ? $_GET['event_id'] : 0;
		// if not event_id try to use post_id
		// $event_id = empty( $event_id ) && isset( $_GET['post_id'] ) ? $_GET['post_id'] : $event_id;
		$event_id = absint( $event_id );

		if ( 0 !== $event_id ) {

			$rsvp = tribe( 'tickets.rsvp' );
			// write_log( (array) $rsvp );

			$order = $rsvp->get_attendees_by_id( $event_id );
			// write_log( (array) $order );
			$temparr = array_unique( array_column( $order, 'purchaser_email' ) );
			// write_log( array_values( array_intersect_key( $order, $temparr ) ) );

			$purchasers = array_values( array_intersect_key( $order, $temparr ) );
			write_log( $purchasers );
			foreach ($purchasers as $purchaser ) {
				write_log( $this->get_rsvp_contact_data_from_order( $purchaser ) );
			}

		}
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



}