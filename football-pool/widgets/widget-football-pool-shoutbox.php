<?php

/*
 * Football Pool WordPress plugin
 *
 * @copyright Copyright (c) 2024 Antoine Hurkmans
 * @link https://wordpress.org/plugins/football-pool/
 * @license https://plugins.svn.wordpress.org/football-pool/trunk/COPYING
 *
 * This file is part of Football pool.
 *
 * Football pool is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * Football pool is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Football pool.
 * If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Widget: Shoutbox Widget
 */

defined( 'ABSPATH' ) or die( 'Cannot access widgets directly.' );
add_action( 'widgets_init', function() { register_widget( 'Football_Pool_Shoutbox_Widget' ); } );

// dummy var for translation files
$fp_translate_this = __( 'Shoutbox Widget', 'football-pool' );
$fp_translate_this = __( 'a shoutbox for your players. Leave short messages.', 'football-pool' );
$fp_translate_this = __( 'shoutbox', 'football-pool' );
$fp_translate_this = __( 'Number of messages to display', 'football-pool' );

class Football_Pool_Shoutbox_Widget extends Football_Pool_Widget {
	protected $widget = array(
		'name' => 'Shoutbox Widget',
		'description' => 'a shoutbox for your players. Leave short messages.',
		'do_wrapper' => true, 
		
		'fields' => array(
			array(
				'name' => 'Title',
				'desc' => '',
				'id' => 'title',
				'type' => 'text',
				'std' => 'shoutbox'
			),
			array(
				'name' => 'Number of messages to display',
				'desc' => '',
				'id' => 'num_messages',
				'type' => 'text',
				'std' => '20'
			),
		)
	);
	
	public function html( string $title, array $args, array $instance ) {
		global $pool;
		extract( $args );

		if ( isset( $instance['num_messages'] ) && is_numeric( $instance['num_messages'] ) ) {
			$num_messages = (int) $instance['num_messages'];
		} else {
			$num_messages = 20;
		}

		$max_chars = Football_Pool_Utils::get_fp_option(
			'shoutbox_max_chars', FOOTBALLPOOL_SHOUTBOX_MAXCHARS, 'int'
		);
		
		$user_id = get_current_user_id();
		$shoutbox = new Football_Pool_Shoutbox();
		
		// save a new shout?
		$shout = $unsaved_text = Football_Pool_Utils::post_string( 'shouttext' );
		$nonce = Football_Pool_Utils::post_string( FOOTBALLPOOL_NONCE_SHOUTBOX_INPUT_NAME );
		if ( wp_verify_nonce( $nonce, FOOTBALLPOOL_NONCE_SHOUTBOX ) !== false
				&& $shout !== '' && $user_id > 0 ) {
			$save_result = $shoutbox->save_shout( $shout, $user_id, $max_chars );
			if ( $save_result === true ) $unsaved_text = '';
		} else {
			$save_result = false;
		}
		
		if ( $title !== '' ) {
			/** @var string $before_title */
			/** @var string $after_title */
			echo $before_title, $title, $after_title;
		}
		
		$userpage = Football_Pool::get_page_link( 'user' );
		
		$messages = $shoutbox->get_messages( $num_messages );
		if ( count( $messages ) > 0 ) {
			$time_format = get_option( 'time_format', FOOTBALLPOOL_TIME_FORMAT );
			$date_format = get_option( 'date_format', FOOTBALLPOOL_DATE_FORMAT );
			echo '<div class="wrapper fp-shoutbox">';
			foreach ( $messages as $message ) {
				$url = esc_url( add_query_arg( array( 'user' => $message['user_id'] ), $userpage ) );
				$shout_date = new DateTime( Football_Pool_Utils::date_from_gmt( $message['shout_date'] ) );
				/** @noinspection HtmlUnknownTarget */
				$output = sprintf(
					'<p><a class="name" href="%s">%s</a>&nbsp;<span class="date">(%s)</span></p><p class="text">%s</p><hr>',
					$url,
					// note: if name "unknown" is shown for existing users, then define the const
					// FOOTBALLPOOL_ALL_WP_USERS in your wp-config
					$pool->user_name( $message['user_id'] ),
					date_i18n( "{$date_format}, {$time_format}", $shout_date->format( 'U' ) ),
					Football_Pool_Utils::xssafe( $message['shout_text'], null, false )
				);
				echo apply_filters( 'footballpool_shoutbox_widget_html', $output );
			}
			echo '</div>';
		} else {
			echo '<p></p>';
		}

		if ( $user_id > 0 ) {
			echo '<form class="fp-form" action="" method="post">';
			wp_nonce_field( FOOTBALLPOOL_NONCE_SHOUTBOX, FOOTBALLPOOL_NONCE_SHOUTBOX_INPUT_NAME );
			echo '<p><span class="notice">';
			printf( __( '(<span>%s</span> characters remaining)', 'football-pool' ), $max_chars );
			echo '</span><br>';
			$id = Football_Pool_Utils::get_counter_value( 'fp_shoutbox_id' );
			/** @noinspection BadExpressionStatementJS */
			printf( '<textarea id="shouttext-%d" name="shouttext" 
				onkeyup="FootballPool.update_chars( this.id, %d )" title="%s">%s</textarea>',
				$id,
				$max_chars,
				sprintf( __( 'all text longer than %s characters will be removed!', 'football-pool' ),
					$max_chars
				),
				$unsaved_text
			);
			if ( $save_result === false ) {
				echo '<span class="notice error">';
				echo _x( 'Saving your message failed. Please try again.',
					'Failed message for the shoutbox',
					'football-pool'
				);
				echo '</span><br>';
			}
			printf( '<input class="fp-shoutbox-save" type="submit" name="submit" value="%s">',
				_x( 'save', 'Save button for the shoutbox widget', 'football-pool' )
			);
			echo '</p></form>';
		}
	}
	
	public function __construct() {
		$classname = str_replace( '_', '', get_class( $this ) );
		parent::__construct(
			$classname, 
			$this->widget['name'] ?? $classname,
			$this->widget['description']
		);
	}
}
