<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );

class TCPSigninBox extends TCPCheckoutBox {

	private $errors = array();

	function get_title() {
		return __( 'Checkout method', 'tcp' );
	}

	function get_class() {
		return 'identify_layer';
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

	function show() {?>
		<div class="checkout_info clearfix" id="identify_layer_info">
		<?php if ( ! is_user_logged_in() ) : ?>
			<div id="login_form">
				<h4><?php _e( 'Login', 'tcp' );?></h4>
				<?php global $thecartpress;
				$user_registration = isset( $thecartpress->settings['user_registration'] ) ? $thecartpress->settings['user_registration'] : false;
				if ( ! $user_registration ) : ?>
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
					'value_remember'	=> false
				);
				//wp_login_form( $args );
				tcp_login_form( $args ); ?>
			</div><!--login_form -->

			<div id="login_guess">
			<?php if ( get_option( 'users_can_register' ) ) : ?>
				<?php if ( ! $user_registration ) : ?>
					<h4><?php _e( 'Checkout as registered', 'tcp' ); ?></h4>
				<?php endif;?>
				<p><strong><?php _e( 'Register with us for future convenience:', 'tcp' ); ?></strong></p>
				<ul class="disc">
					<li><?php _e( 'Fast and easy checkout', 'tcp' ); ?></li>
					<li><?php _e( 'Easy access to yours orders history and status', 'tcp' ); ?></li>
					<?php //wp_register( '<li>', '</li>', true );?>
					<li><a href="javascript: void(0)" onclick="jQuery('li.tcp_login_and_register').toggle();"><?php _e( 'Register', 'tcp' ); ?></a></li>
					<li class="tcp_login_and_register" style="display:none;"><div id="tcp_login_and_register">
					<?php $url = plugins_url( 'thecartpress/checkout/register_and_login.php' ); ?>
					<form class="tcp_login_and_register" action="<?php echo $url; ?>" method="post">
						<p class="tcp_login_and_register_user_name">
							<label for="tcp_new_user_name"><?php _e( 'Username', 'tcp' ); ?></label>
							<input type="text" name="tcp_new_user_name" size="12" maxlength="12" />
						</p>
						<p>
							<label for="tcp_new_user_pass"><?php _e( 'Password', 'tcp' ); ?></label>
							<input type="password" name="tcp_new_user_pass" size="12" maxlength="12" />
						</p>
						<p>
							<label for="tcp_repeat_user_pass"><?php _e( 'Password', 'tcp' ); ?></label>
							<input type="password" name="tcp_repeat_user_pass" size="12" maxlength="12" />
						</p>
						<p>
							<label for="tcp_user_email"><?php _e( 'E-mail', 'tcp' ); ?></label>
							<input type="text" name="tcp_new_user_email" size="12" maxlength="100"/>
						</p>
						<input type="hidden" name="tcp_redirect_to" value="<?php echo get_permalink(); ?>" />
						<p><input type="submit" value="<?php _e( 'Register', 'tcp' ); ?>" name="tcp_register_action" id="tcp_register_action" class="tcp_checkout_button" /></p>
					</form>
				</div><!-- tcp_login_register -->
				</li>
				</ul>
			<?php endif; ?>
			<?php do_action( 'tcp_checkout_identify' ); ?>
			<?php if ( ! $user_registration ) : ?>
				<h4><?php _e( 'Checkout as a guest', 'tcp' ); ?></h4>
				<p><strong>
				<?php if ( get_option( 'users_can_register' ) ) : ?>
					<?php _e( 'Or you can make as a guest.', 'tcp' ); ?>
				<?php else : ?>
					<?php _e( 'You can make as a guest.', 'tcp' ); ?>
				<?php endif; ?>
				</strong></p>
				<ul>
					<li><?php _e( 'If you prefer this way then press the continue button', 'tcp' ); ?></li>
				</ul>
				<!--<p><input type="submit" name="tcp_continue" id="tcp_continue" value="<?php _e( 'Continue', 'tcp' ); ?>" /></p>-->
			<?php else : ?>
				 <p style="clear: both;"><strong><?php _e( 'User registration is required. Please, log in or register. ', 'tcp' ); ?></strong></p>
			<?php endif; ?>
			</div><!-- login_guess -->
		<?php endif; ?>
		</div> <!-- identify_layer_info -->
		<?php return !$user_registration;
	}
}
?>
