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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPNoticeBox' ) ) :

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );

class TCPNoticeBox extends TCPCheckoutBox {

	private $errors = array();

	function get_title() {
		return __( 'Checkout notice', 'tcp' );
	}

	function get_class() {
		return 'legal_notice_layer';
	}

	function get_name() {
		return 'notice';
	}

	function after_action() {
		if ( ! isset( $_REQUEST['legal_notice_accept'] ) || strlen( $_REQUEST['legal_notice_accept'] ) == 0 )
			$this->errors['legal_notice_accept'] = __( 'You must accept the conditions!!', 'tcp' );
		return apply_filters( 'tcp_after_notice_box', count( $this->errors ) == 0 );
	}

	function show() {
		$legal_notice_accept = isset( $_REQUEST['legal_notice_accept'] ) ? $_REQUEST['legal_notice_accept'] : ''; ?>
<?php do_action( 'tcp_checkout_before_notice_cart' ); ?>
<div id="legal_notice_layer_info" class="checkout_info clearfix">
	<?php global $thecartpress;
	$legal_notice = tcp_do_template( 'tcp_checkout_notice', false );
	if ( strlen( $legal_notice ) == 0 ) $legal_notice = $thecartpress->get_setting( 'legal_notice', '' );
	if ( strlen( $legal_notice ) > 0 ) : ?>
		<div id="legal_notice"><?php echo tcp_string( 'TheCartPress', 'legal notice', $legal_notice ); ?></div>
		<br />
		<label><?php _e( 'Accept conditions:', 'tcp' );?>
		<input type="checkbox" id="legal_notice_accept" name="legal_notice_accept" value="Y" />
		</label>
		<?php if ( isset( $this->errors['legal_notice_accept'] ) ) : ?><br/><span class="error"><?php echo $this->errors['legal_notice_accept'];?></span><?php endif;?>
	<?php else : ?>
		<input type="hidden" name="legal_notice_accept" value="Y" />
		<p><?php _e( 'When you click on the \'continue\' button the order will be created and if you have chosen an external payment method the system will show a button to go to the external web (usually your bank\'s payment gateway)','tcp' );?></p>
	<?php endif;?>
</div> <!-- legal_notice_layer_info-->
<?php do_action( 'tcp_checkout_after_notice_cart' ); ?>
	<?php return true;
	}
}
endif; // class_exists check