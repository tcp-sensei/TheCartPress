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

class TCPCardOffLine extends TCP_Plugin {

	function getTitle() {
		return 'Card off-line payment';
	}

	function getIcon() {
		return plugins_url( 'thecartpress/images/offline.png' );
	}

	function getDescription() {
		return 'Card off line payment.<br/>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data, $instance = 0 ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
		</th><td>
			<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="store_part_number"><?php _e( 'Store part of the number', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="store_part_number" name="store_part_number" <?php checked( $data['store_part_number'] ); ?> value="yes" />
			<span class="description"><?php _e( 'The credit card will be stored as 9999 xxxx xxxx 9999', 'tcp' ); ?></span>
		</td></tr><?php
	}

	function saveEditFields( $data, $instance = 0 ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		$data['store_part_number'] = isset( $_REQUEST['store_part_number'] ) ? $_REQUEST['store_part_number'] == 'yes': false;
		return $data;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_payment_plugin_data( 'TCPCardOffLine', $instance );
		$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		return tcp_string( 'TheCartPress', 'pay_TCPCardOffLine-title', $title );
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		$data				= tcp_get_payment_plugin_data( get_class( $this ), $instance, $order_id );
		$notify_url			= plugins_url( 'thecartpress/plugins/CardOffLine/notify.php' );
		$return_url			= tcp_get_the_checkout_ok_url( $order_id );
		$store_part_number	= isset( $data['store_part_number'] ) ? $data['store_part_number'] : false;
		$new_status			= $data['new_status']; ?>
		<p><?php _e( 'Off line payment:', 'tcp' );?></p>
		<?php if ( isset( $data['notice'] ) ) echo '<p>', $data['notice'], '</p>'; ?>
		<form name="tcp_offline_payment" id="tcp_offline_payment" action="<?php echo $notify_url;?>" method="post">
		<input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
		<input type="hidden" name="return_url" value="<?php echo $return_url;?>" />
		<input type="hidden" name="new_status" value="<?php echo $new_status;?>" />
		<table class="tcp_card_offline_payment">
		<tbody>
			<tr valign="top">
				<th scope="row" class="tcp_card_holder">
					<label for="card_holder"><?php _e( 'Credit card holder', 'tcp' );?>:</label>
				</th>
				<td class="tcp_card_holder">
					<input type="text" id="card_holder" name="card_holder" size="40" maxlength="150" class="input-xlarge"/>
					<p class="error tcp_error alert alert-error" id="tcp_error_card_holder" style="display:none;"></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="tcp_card_number" >
					<label for="card_number_1"><?php _e( 'Card number', 'tcp' );?>:</label>
				</th>
				<td class="tcp_card_number">
					<input type="text" id="card_number_1" name="card_number_1" size="4" maxlength="4" class="input-small"/>
					<?php if ( ! $store_part_number ) : ?>
					<input type="text" id="card_number_2" name="card_number_2" size="4" maxlength="4" class="input-small"/>
					<input type="text" id="card_number_3" name="card_number_3" size="4" maxlength="4" class="input-small"/>
					<?php else : ?>
					<input type="text" id="card_number_2" name="card_number_2" size="4" value="xxxx" readonly class="input-small"/>
					<input type="text" id="card_number_3" name="card_number_3" size="4" value="xxxx" readonly class="input-small"/>
					<?php endif; ?>
					<input type="text" id="card_number_4" name="card_number_4" size="4" maxlength="4" class="input-small"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="tcp_cvc">
					<label><?php _e( 'cvc', 'tcp' );?>:</label>
				</th>
				<td>
					<input type="text" id="cvc" name="cvc" size="4" maxlength="4" class="input-small"/>
					<p class="error tcp_error alert alert-error" id="tcp_error_offline" style="display:none;"></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="tcp_expiration_date">
					<label for="expiration_month"><?php _e( 'Expiration date', 'tcp' );?>:</label>
				</th>
				<td class="tcp_expiration_date">
					<label for="expiration_month"><?php _e( 'Month', 'tcp' );?>:
					<select id="expiration_month" name="expiration_month" class="input-small">
						<?php $current_month = (int)date( 'm' );
						for($i = 1; $i < 13; $i++) : ?>
						<option value="<?php echo $i; ?>" <?php selected( $current_month, $i ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select></label>&nbsp;
					<label for="expiration_year"><?php _e( 'Year', 'tcp' );?>:
					<?php $current_year = date( 'Y' ); ?>
					<select id="expiration_year" name="expiration_year" class="input-small">
						<?php for( $i = $current_year - 1; $i <= $current_year + 10; $i++ ) : ?>
						<option value="<?php echo $i; ?>" <?php selected( $current_year, $i ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="tcp_card_type">
					<label for="card_type"><?php _e( 'Card type', 'tcp' );?>:</label>
				</th>
				<td class="tcp_card_type">
					<?php $types = array (
							array(
								'id'	=> 'maestro',
								'title'	=> 'Maestro',
							),
							array(
								'id'	=> 'mastercard',
								'title'	=> 'Master Card',
							),
							array(
								'id'	=> 'visa',
								'title'	=> 'Visa',
							),
						);
						$types = apply_filters( 'tcp_card_offline_cart_types', $types ); ?>
					<select id="card_type" name="card_type" class="input-medium">
						<?php foreach( $types as $type ) :?>
							<option value="<?php echo $type['id'];?>"><?php echo $type['title'];?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
		</tbody>
		</table>
		<script>
		//http://sites.google.com/site/abapexamples/javascript/luhn-validation
		String.prototype.luhnCheck = function() {
			var luhnArr = [[0,2,4,6,8,1,3,5,7,9], [0,1,2,3,4,5,6,7,8,9]], sum = 0;
			this.replace(/\D+/g,"").replace(/[\d]/g, function(c, p, o){
				sum += luhnArr[ (o.length-p)&1 ][ parseInt(c,10) ];
			});
			return (sum%10 === 0) && (sum > 0);
		};

		function tcp_checkCard() {
			var errors = 0;
			jQuery(".error").hide();
			<?php if ( ! $store_part_number ) { ?>
			var card_number = jQuery('#card_number_1').val() + "" + jQuery('#card_number_2').val() + "" + jQuery('#card_number_3').val() + "" + jQuery('#card_number_4').val();
			if ( ! card_number.luhnCheck() ) {
				jQuery("#tcp_error_offline").html("<?php _e( 'Wrong Card number', 'tcp' );?>");
				jQuery("#tcp_error_offline").show();
				errors++;
			}
			<?php } ?>
			if ( jQuery('#card_holder').val().length < 4 ) {
				jQuery("#tcp_error_card_holder").html("<?php _e( 'The field Card Holder must be completed', 'tcp' );?>");
				jQuery("#tcp_error_card_holder").show();
				errors++;
			}
			if ( errors == 0) {
				jQuery("#tcp_offline_payment").submit();
			}
		}
		</script>
		<p class="tcp_card_offline_execute"><input type="button" onclick="tcp_checkCard();" class="tcp_pay_button btn btn-success" name="tcp_send_off_line_info" value="<?php echo __( 'Send', 'tcp' );?>" class="button-primary"/></p>
		</form>
		<?php
	}
	
	function tcp_admin_order_before_editor( $order_id ) {
		global $wpdb;
		if ( isset( $_REQUEST['tcp_delete_card_data'] ) ) :
			tcp_delete_order_meta( $order_id, 'tcp_card_offlines' ); ?>

			<tr valign="top">
				<th scope="row" colspan="2">
					<strong><?php _e( 'The data from the customer\'s credit card has been permanently removed', 'tcp' );?></strong>
				</th>
			</tr>

		<?php else :
			$offline = tcp_get_order_meta( $order_id, 'tcp_card_offlines' );
			if ( $offline ) : ?>
			<tr valign="top">
			<th scope="row">
				<label for="card_holder"><?php _e( 'Credit Card Holder', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="text" name="card_holder" id="card_holder" size="40" maxlength="150" value="<?php echo $offline['card_holder'];?>" readonly />
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">
				<label for="card_number"><?php _e( 'Card number', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="text" name="card_number_1" id="card_number_1" size="4" maxlength="4" value="<?php echo substr( $offline['card_number'], 0, 4 );?>" readonly />
				<input type="text" name="card_number_2" id="card_number_2" size="4" maxlength="4" value="<?php echo substr( $offline['card_number'], 4, 4 );?>" readonly />
				<input type="text" name="card_number_3" id="card_number_3" size="4" maxlength="4" value="<?php echo substr( $offline['card_number'], 8, 4 );?>" readonly />
				<input type="text" name="card_number_4" id="card_number_4" size="4" maxlength="4" value="<?php echo substr( $offline['card_number'], 12 );?>" readonly />
				<label><?php _e( 'cvc', 'tcp' );?>: </label><input type="text" id="cvc" name="cvc" size="4" maxlength="4" value="<?php echo $offline['cvc'];?>" readonly />
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">
				<label for="expiration_month"><?php _e( 'Expiration date', 'tcp' );?>:</label>:
			</th>
			<td>
				<input type="text" name="expiration_month" id="expiration_month" size="2" maxlength="2" value="<?php echo $offline['expiration_month'];?>" readonly />
				/
				<input type="text" name="expiration_year" id="expiration_year" size="2" maxlength="2" value="<?php echo $offline['expiration_year'];?>" readonly />
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">
				<label for="card_type"><?php _e( 'Credit Card Type', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="text" name="card_type" id="card_type" size="20" maxlength="20" value="<?php echo $offline['card_type'];?>" readonly="true" />
				<a href="javascript:return false;" onclick="jQuery('#delete_card_data').show();" class="delete"><?php _e( 'Delete card data', 'tcp' );?></a>
				<div id="delete_card_data" class="delete_card_data" style="display:none; border: 1px dotted orange; padding: 2px">
					<p><?php _e( 'Do you really want to delete this address?', 'tcp' );?></p>
					<input type="submit" name="tcp_delete_card_data" value="<?php _e( 'Yes' , 'tcp' );?>" class="delete" />
					|
					<a href="javascript:return false;" onclick="jQuery('#delete_card_data').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
				</div>
			</td>
			</tr>
		<?php endif;
		endif;
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'tcp_admin_order_before_editor', array( $this, 'tcp_admin_order_before_editor' ) );
			global $wpdb;
			$sql = 'SHOW TABLES LIKE \'' . $wpdb->prefix . 'tcp_offlines\'';
			$res = (array)$wpdb->get_row( $sql );
			if ( count( $res ) > 0 ) {
				$sql = 'SELECT * FROM `' . $wpdb->prefix . 'tcp_offlines`';
				$res = $wpdb->get_results( $sql );
				foreach( $res  as $row ) {
					tcp_update_order_meta( $row->order_id, 'tcp_card_offlines', array(
						'order_id'			=> $row->order_id,
						'card_holder'		=> $row->card_holder,
						'card_number'		=> $row->card_number,
						'cvc'				=> $row->cvc,
						'expiration_month'	=> $row->expiration_month,
						'expiration_year'	=> $row->expiration_year,
						'card_type'			=> $row->card_type,
						'created_at'		=> $row->created_at,
					) );
				}
				$sql = 'DELETE FROM `' . $wpdb->prefix . 'tcp_offlines`';
				$res = $wpdb->query( $sql );
				$sql = 'DROP TABLE `' . $wpdb->prefix . 'tcp_offlines`';
				$res = $wpdb->query( $sql );
			}
		}
	}
}
?>