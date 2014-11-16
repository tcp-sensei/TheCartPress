<?php
/**
 * Transference
 *
 * Allows pay using transference mode
 *
 * @package TheCartPress
 * @subpackage Plugins
 */

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Transference' ) ) :

class Transference extends TCP_Plugin {

	function getTitle() {
		return 'Transference';
	}

	function getIcon() {
		return plugins_url( 'thecartpress/images/transference.png' );
	}

	function getDescription() {
		return 'Transference payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data, $instace = 0 ) { 
		$bank_format = isset( $data['bank_format'] ) ? $data['bank_format'] : 'four-fields'; ?>
<tr valign="top">
	<th scope="row">
		<label for="notice"><?php _e( 'Notice', 'tcp' ); ?>:</label>
	</th><td>
		<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : ''; ?></textarea>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="owner"><?php _e( 'Owner', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="owner" name="owner" size="40" maxlength="50" value="<?php echo isset( $data['owner'] ) ? $data['owner'] : ''; ?>" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="bank"><?php _e( 'Bank', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="bank" name="bank" size="40" maxlength="50" value="<?php echo isset( $data['bank'] ) ? $data['bank'] : ''; ?>" />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
		<label for="bank_format"><?php _e( 'Bank account format', 'tcp' ); ?>:</label>
	</th><td>
		<select id="bank_format" name="bank_format">
			<option value="sepa" <?php selected( $bank_format, 'sepa' ); ?> >SEPA</option>
			<option value="two-fields" <?php selected( $bank_format, 'two-fields' ); ?> ><?php _e( 'Two Fields', 'tcp' ); ?></option>
			<option value="four-fields" <?php selected( $bank_format, 'four-fields' ); ?> ><?php _e( 'Four Fields', 'tcp' ); ?></option>
		</select>
		<script>
		function tcp_see_bank_fileds( type ) {
			jQuery( '.bank-fields' ).hide();
			jQuery( '.' + type ).show();
		}

		jQuery( '#bank_format' ).on( 'change', function() {
			var type = jQuery( '#bank_format' ).val();
			tcp_see_bank_fileds( type );
			//jQuery( '.bank-fields' ).hide();
			//jQuery( '.' + type ).show();
			return false;
		});

		jQuery( function() {
			var type = jQuery( '#bank_format' ).val();
			tcp_see_bank_fileds( type );
		} );
		</script>
	</td>
</tr>

<tr valign="top" class="bank-fields two-fields" <?php if ( $bank_format == 'four-fields' ) : ?>style="display:none;"<?php endif; ?>>
	<th scope="row">
		<label for="bank_code"><?php _e( 'Bank code', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="bank_code" name="bank_code" size="20" maxlength="20" value="<?php echo isset( $data['bank_code'] ) ? $data['bank_code'] : ''; ?>" />	
	</td>
</tr>
<tr valign="top" class="bank-fields two-fields" <?php if ( $bank_format == 'four-fields' ) : ?>style="display:none;"<?php endif; ?>>
	<th scope="row">
		<label for="account"><?php _e( 'Account', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="account" name="account" size="20" maxlength="20" value="<?php echo isset( $data['account'] ) ? $data['account'] : ''; ?>" />	
	</td>
</tr>

<tr valign="top" class="bank-fields four-fields" <?php if ( $bank_format == 'two-fields' ) : ?>style="display:none;"<?php endif; ?>>
	<th scope="row">
		<label for="account"><?php _e( 'Account', 'tcp' ); ?>:</label>
	</th><td><?php
		if ( isset( $data['account'] ) ) {
			$account1 = substr( $data['account'], 0, 4 );
			$account2 = substr( $data['account'], 4, 4 );
			$account3 = substr( $data['account'], 8, 2 );
			$account4 = substr( $data['account'], 10, 10 );
		} else {
			$account1 = '';
			$account2 = '';
			$account3 = '';
			$account4 = '';
		}?>
		<input type="text" id="account1" name="account1" size="4" maxlength="4" value="<?php echo $account1; ?>" />
		<input type="text" id="account2" name="account2" size="4" maxlength="4" value="<?php echo $account2; ?>" />
		<input type="text" id="account3" name="account3" size="2" maxlength="2" value="<?php echo $account3; ?>" />
		<input type="text" id="account4" name="account4" size="10" maxlength="10" value="<?php echo $account4; ?>" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="iban"><?php _e( 'IBAN', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="iban" name="iban" size="20" maxlength="40" value="<?php echo isset( $data['iban'] ) ? $data['iban'] : ''; ?>" />
	</td>
</tr>
<tr valign="top" class="bank-fields sepa">
	<th scope="row">
		<label for="bic"><?php _e( 'BIC', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="bic" name="bic" size="20" maxlength="40" value="<?php echo isset( $data['bic'] ) ? $data['bic'] : ''; ?>" />
	</td>
</tr>
<tr valign="top" class="bank-fields two-fields four-fields">
	<th scope="row">
		<label for="swift"><?php _e( 'SWIFT', 'tcp' ); ?>:</label>
	</th><td>
		<input type="text" id="swift" name="swift" size="20" maxlength="40" value="<?php echo isset( $data['swift'] ) ? $data['swift'] : ''; ?>" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="redirect"><?php _e( 'Redirect automatically', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="checkbox" id="redirect" name="redirect" value="yes" <?php checked( isset( $data['redirect'] ) ? $data['redirect'] : false ); ?> />
		<p class="description"><?php _e( 'If checked, Checkout page will redirect automatically to Checkout Ok page. Otherwise, customers must click on "Finish" button.', 'tcp' ); ?></p>
	</td>
</tr><?php
	}

	function saveEditFields( $data, $instace = 0 ) {
		$data['notice']		= isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		tcp_register_string( 'TheCartPress', 'pay_Transference-notice', $data['notice'] );
		$data['owner']		= isset( $_REQUEST['owner'] ) ? $_REQUEST['owner'] : '';
		$data['bank_format']= isset( $_REQUEST['bank_format'] ) ? $_REQUEST['bank_format'] : 'four-fields';
		$data['bank_code']	= isset( $_REQUEST['bank_code'] ) ? $_REQUEST['bank_code'] : '';
		$data['bank']		= isset( $_REQUEST['bank'] ) ? $_REQUEST['bank'] : '';
		if ( $data['bank_format'] == 'four-fields' ) {
			$account1 = isset( $_REQUEST['account1'] ) ? $_REQUEST['account1'] : '';
			$account2 = isset( $_REQUEST['account2'] ) ? $_REQUEST['account2'] : '';
			$account3 = isset( $_REQUEST['account3'] ) ? $_REQUEST['account3'] : '';
			$account4 = isset( $_REQUEST['account4'] ) ? $_REQUEST['account4'] : '';
			$data['account'] = $account1 . $account2 . $account3 . $account4;
		} else {
			$data['account'] = isset( $_REQUEST['account'] ) ? $_REQUEST['account'] : '';;
		}
		$data['iban']		= isset( $_REQUEST['iban'] ) ? $_REQUEST['iban'] : '';
		$data['swift']		= isset( $_REQUEST['swift'] ) ? $_REQUEST['swift'] : '';
		$data['bic']		= isset( $_REQUEST['bic'] ) ? $_REQUEST['bic'] : '';
		$data['redirect']	= isset( $_REQUEST['redirect'] );
		return $data;
	}

	function sendPurchaseMail() {
		return false;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_payment_plugin_data( 'Transference', $instance );
		return tcp_string( 'TheCartPress', 'pay_Transference-title', isset( $data['title'] ) ? $data['title'] : $this->getTitle() );
	}

	function getNotice( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$bank_format = isset( $data['bank_format'] ) ? $data['bank_format'] : 'four-fields';
		ob_start();
		$notice = tcp_string( 'TheCartPress', 'pay_Transference-notice', $data['notice'] ); ?>
		<p><?php echo $notice; ?></p>
		<table class="tcp-bank-account">
			<?php if ( strlen( trim( $data['owner'] ) ) > 0 ) : ?><tr><th scope="row"><?php _e( 'Owner', 'tcp' ); ?>: </th><td><?php echo $data['owner']; ?></td></tr><?php endif; ?>
			<tr><th scope="row"><?php _e( 'Bank', 'tcp' ); ?>: </th><td><?php echo $data['bank']; ?></td></tr>
		<?php if ( 'two-fields' == $bank_format ) : ?>
			<tr><th scope="row"><?php _e( 'Bank code', 'tcp' ); ?>: </th><td><?php echo $data['bank_code']; ?></td></tr>
		<?php endif; ?>
		<?php if ( 'two-fields' == $bank_format || 'four-fields' == $bank_format ) : ?>
			<tr><th scope="row"><?php _e( 'Account', 'tcp' ); ?>: </th><td><?php echo $data['account']; ?></td></tr>
			<?php if ( strlen( $data['iban'] ) > 0 )  : ?><tr><th scope="row"><?php _e( 'IBAN', 'tcp' ); ?>: </th><td><?php echo $data['iban']; ?></td></tr><?php endif; ?>
			<?php if ( strlen( $data['swift'] ) > 0 ) : ?><tr><th scope="row"><?php _e( 'SWIFT', 'tcp' ); ?>: </th><td><?php echo $data['swift']; ?></td></tr><?php endif; ?>
		<?php endif; ?>
		<?php if ( 'sepa' == $bank_format ) : ?>
			<?php if ( strlen( $data['iban'] ) > 0 ) : ?><tr><th scope="row"><?php _e( 'IBAN', 'tcp' ); ?>: </th><td><?php echo $data['iban']; ?></td></tr><?php endif; ?>			
			<?php if ( strlen( $data['bic'] ) > 0 )  : ?><tr><th scope="row"><?php _e( 'BIC', 'tcp' ); ?>: </th><td><?php echo $data['bic']; ?></td></tr><?php endif; ?>
		<?php endif; ?>
		</table>
		<?php return ob_get_clean();
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		$url				= tcp_get_the_checkout_ok_url( $order_id );
		$data				= tcp_get_payment_plugin_data( get_class( $this ), $instance, $order_id );
		$redirect			= isset( $data['redirect'] ) ? $data['redirect'] : false;
		$additional			= $this->getNotice( $instance, $shippingCountry, $shoppingCart, $order_id );
		$buy_button_color	= thecartpress()->get_setting( 'buy_button_color' );
		echo $additional;
		if ( ! $redirect ) : ?>
		<p><input type="button" class="tcp_pay_button tcp-btn tcp-btn-lg <?php echo $buy_button_color; ?>" id="tcp_transference" value="<?php _e( 'Finish', 'tcp' ); ?>" onclick="window.location.href='<?php echo $url; ?>';"/></p>
		<?php endif;
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		$new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PENDING;
		Orders::editStatus( $order_id, $new_status, 'no-id' );
		require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
		ActiveCheckout::sendMails( $order_id );//, $additional );
		if ( $redirect ) : ?>
		<script>window.location.href='<?php echo $url; ?>';</script>
		<?php endif;
	}
}
endif; // class_exists check