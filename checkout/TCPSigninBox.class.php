<?php
/**
 * This file is part of TheCartPress.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );

class TCPSigninBox extends TCPCheckoutBox {

	private $errors = array();

	function get_title() {
		return __( 'Checkout method', 'tcp' );
	}

	function get_class() {
		return 'identify_layer';
	}

	function get_name() {
		return 'login';
	}

	function before_action() {
		if ( is_user_logged_in() ) {
			return 1;
		} else {
			return 0;
		}
	}

	function is_form_encapsulated() {
		return false;
	}

	function show_config_settings() {
		$settings	= get_option( 'tcp_' . get_class( $this ), array() );
		if ( isset( $settings['display'] ) && $settings['display'] == 'all' ) {
			$display = array( 'guest', 'login', 'register' );
		} else {
			$display = isset( $settings['display'] ) ? (array)$settings['display'] : array( 'guest', 'login', 'register' );
		} ?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="display_guest"><?php _e( 'See Guest option', 'tcp' );?>:</label>
					</th>
					<td>
						<input type="checkbox" name="tcp_display[]" id="display_guest" value="guest" <?php checked( in_array( 'guest', $display ) );?>/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="display_login"><?php _e( 'See Login option', 'tcp' );?>:</label>
					</th>
					<td>
						<input type="checkbox" name="tcp_display[]" id="display_login" value="login" <?php checked( in_array( 'login', $display ) );?>/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="display_register"><?php _e( 'See Register option', 'tcp' );?>:</label>
					</th>
					<td>
						<input type="checkbox" name="tcp_display[]" id="display_register" value="register" <?php checked( in_array( 'register', $display ) );?>/>
					</td>
				</tr>
			</tbody>
		</table>
	<?php return true;
	}

	function save_config_settings() {
		$display = isset( $_REQUEST['tcp_display'] ) ? $_REQUEST['tcp_display'] : array( 'guest' );
		if ( in_array( 'register', $display ) && ! in_array( 'login', $display ) ) $display[] = 'login';
		$settings = array(
			'display' => $display,
		);
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	function show() {?>
<div class="checkout_info clearfix" id="identify_layer_info">
	<?php $settings		= get_option( 'tcp_' . get_class( $this ), array() );
	$display			= isset( $settings['display'] ) ? $settings['display'] : array( 'guest', 'login', 'register' );
	global $thecartpress;
	$user_registration	= $thecartpress->get_setting( 'user_registration', false ); ?>
	<?php if ( ! is_user_logged_in() ) { ?>
		<?php if ( in_array( 'login', $display ) ) { ?>
			<div id="login_form">
				<h4><?php _e( 'Login', 'tcp' ); ?></h4>
				<?php if ( ! $user_registration ) : ?>
					<p><strong><?php _e( 'Already registered?', 'tcp' );?></strong><br/><?php _e( 'Please log in below:', 'tcp' );?></p>
				<?php endif;
				$args = array(
					'echo'				=> true,
					'redirect'			=> get_permalink(),
					'form_id'			=> 'loginform',
					'label_username'	=> __( 'Username', 'tcp' ),
					'label_password'	=> __( 'Password', 'tcp' ),
					'label_remember'	=> __( 'Remember Me', 'tcp' ),
					'label_log_in'		=> __( 'Log In', 'tcp' ),
					'id_username'		=> 'user_login',
					'id_password'		=> 'user_pass',
					'id_remember'		=> 'rememberme',
					'id_submit'			=> 'wp-submit',
					'remember'			=> true,
					'value_username'	=> '',
					'value_remember'	=> false,
					'see_register'		=> false,
				);
				tcp_login_form( $args ); ?>
			</div><!--login_form -->
		<?php } ?>

		<?php if ( in_array( 'guest', $display ) || in_array( 'register', $display ) ) { ?>
			<div id="login_guess">
				<?php if ( get_option( 'users_can_register' ) && in_array( 'register', $display ) ) { ?>
					<?php if ( ! $user_registration ) : ?>
						<h4><?php _e( 'Checkout as registered', 'tcp' ); ?></h4>
					<?php endif;?>
					<p><strong><?php _e( 'Register with us for future convenience:', 'tcp' ); ?></strong></p>
					<?php ob_start(); ?>
					<ul class="disc">
						<li><?php _e( 'Fast and easy checkout', 'tcp' ); ?></li>
						<li><?php _e( 'Easy access to yours orders history and status', 'tcp' ); ?></li>
						<li><a href="javascript: void(0)" onclick="jQuery('li.tcp_login_and_register').toggle();"><?php _e( 'Register', 'tcp' ); ?></a></li>
						<li class="tcp_login_and_register" <?php if ( ! isset( $_REQUEST['tcp_register_error'] ) ) : ?>style="display:none;"<?php endif; ?>><div id="tcp_login_and_register">
						<?php tcp_register_form(); ?>
						</div><!-- tcp_login_register -->
						</li>
					</ul>
					<?php echo apply_filters( 'tcp_checkout_siginbox_registered', ob_get_clean() ); ?>
				<?php } ?>
				<?php do_action( 'tcp_checkout_identify' ); ?>
				<?php if ( ! $user_registration && in_array( 'guest', $display ) ) { ?>
					<?php ob_start(); ?>
					<h4><?php _e( 'Checkout as a guest', 'tcp' ); ?></h4>
					<p><strong>
					<?php if ( get_option( 'users_can_register' ) ) : ?>
						<?php //_e( 'Or you can make as a guest.', 'tcp' ); ?>
					<?php else : ?>
						<?php _e( 'You can make as a guest.', 'tcp' ); ?>
					<?php endif; ?>
					</strong></p>
					<ul>
						<li><?php _e( 'If you prefer this way then press the continue button', 'tcp' ); ?></li>
					</ul>
					<?php echo apply_filters( 'tcp_checkout_siginbox_as_guest', ob_get_clean() ); ?>
					<!--<p><input type="submit" name="tcp_continue" id="tcp_continue" value="<?php _e( 'Continue', 'tcp' ); ?>" /></p>-->
				<?php } else { ?>
					 <p style="clear: both;"><strong><?php _e( 'User registration is required. Please, log in or register. ', 'tcp' ); ?></strong></p>
				<?php } ?>
			</div><!-- login_guess -->
		<?php } ?>
	<?php } ?>
</div> <!-- identify_layer_info -->
		<?php return ! $user_registration;
	}
}
?>