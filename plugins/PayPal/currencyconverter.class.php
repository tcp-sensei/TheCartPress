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

class TCPPaypalCurrencyConverter {

	function __construct() {
		add_action( 'tcp_paypal_show_edit_fields', array( &$this, 'tcp_paypal_show_edit_fields' ) );
		add_filter( 'tcp_paypal_save_edit_fields', array( &$this, 'tcp_paypal_save_edit_fields' ) );
		add_filter( 'tcp_paypal_converted_amount', array( &$this, 'tcp_paypal_converted_amount' ), 10, 2 );
		add_filter( 'tcp_paypal_get_convert_to', array( &$this, 'tcp_paypal_get_convert_to' ), 10, 2 );
	}

	function tcp_paypal_show_edit_fields( $data ) {
		$use_conversion = isset( $data['use_conversion'] ) ? $data['use_conversion'] : 'no';
		$conversion_rate = isset( $data['conversion_rate'] ) ? tcp_number_format( $data['conversion_rate'] ) : 1;
		$convert_to = isset( $data['convert_to'] ) ? $data['convert_to'] : tcp_get_the_currency_iso(); ?>
	<tr valign="top">
		<th scope="row">
			<label for="use_conversion"><?php _e( 'Currency conversion', 'tcp' );?>:</label>
		</th><td>
			<label>
				<input type="radio" name="use_conversion" id="no_conversion" value="no" class="use_conversion" <?php checked( 'no', $use_conversion ); ?>/>
				&nbsp;<?php _e( 'No Currency Conversion', 'tcp' );?>
			</label>
			<br/>
			<label>
				<input type="radio" name="use_conversion" id="google_api" value="google_api" class="use_conversion" <?php checked( 'google_api', $use_conversion ); ?>/>
				&nbsp;<?php _e( 'Use google API', 'tcp' );?>
			</label>
			<?php if ( $use_conversion ): ?>
				<span class="description">
					<?php _e( 'Conversion Rate', 'tcp' ); ?>: <?php echo $this->get_conversion_rate_from_google( $data ); ?>
				</span>
			<?php endif; ?>
			<br/>
			<label>
				<input type="radio" name="use_conversion" id="manually" value="manually" class="use_conversion" <?php checked( 'manually', $use_conversion ); ?>/>
				&nbsp;<?php _e( 'Manually', 'tcp' );?>
			</label>
		</td>
	</tr>

	<tr valign="top" id="tr_conversion_rate">
		<th scope="row">
			<label for="conversion_rate"><?php _e( 'Conversion Rate', 'tcp' );?>:</label>
		</th><td>
			<input type="text" name="conversion_rate" id="conversion_rate" value="<?php echo $conversion_rate; ?>" />
			<span class="description"><?php tcp_number_format_example( 1.5 ); ?></span>
			<p class="description"><?php printf( __( 'Conversion rate to convert %s to the selected one.', 'tcp' ), tcp_get_the_currency_iso() ); ?></p>
		</td>
	</tr>

	<tr valign="top" id="tr_convert_to">
		<th scope="row">
			<label for="convert_to"><?php _e( 'Convert to', 'tcp' );?>:</label>
		</th><td>
			<select name="convert_to" id="convert_to">
			<?php require_once( TCP_DAOS_FOLDER . 'Currencies.class.php' );
			$currencies = Currencies::getAll();
			foreach( $currencies as $currency_row ) : ?>
				<option value="<?php echo $currency_row->iso; ?>" <?php selected( $currency_row->iso, $convert_to ); ?>><?php echo $currency_row->currency; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<script>
	jQuery('.use_conversion').click(function() {
		if (jQuery('#no_conversion').is(':checked')) {
			jQuery('#tr_conversion_rate').hide();
			jQuery('#tr_convert_to').hide();
		} else if (jQuery('#google_api').is(':checked')) {
			jQuery('#tr_conversion_rate').hide();
			jQuery('#tr_convert_to').show();
		} else { //manualy
			jQuery('#tr_conversion_rate').show();
			jQuery('#tr_convert_to').show();
		}
	});
	<?php if ( $use_conversion == 'no' ) : ?>
		jQuery('#tr_conversion_rate').hide();
		jQuery('#tr_convert_to').hide();
	<?php elseif ( $use_conversion == 'google_api' ) : ?>
		jQuery('#tr_conversion_rate').hide();
		jQuery('#tr_convert_to').show();
	<?php else : ?>
		jQuery('#tr_conversion_rate').show();
		jQuery('#tr_convert_to').show();
	<?php endif; ?>
	</script>
	<?php
	}

	function tcp_paypal_save_edit_fields( $data ) {
		$data['use_conversion']		= isset( $_REQUEST['use_conversion'] ) ? $_REQUEST['use_conversion'] : 'no';
		$data['conversion_rate']	= isset( $_REQUEST['conversion_rate'] ) ? tcp_input_number( $_REQUEST['conversion_rate'] ) : 1;
		$data['convert_to']			= isset( $_REQUEST['convert_to'] ) ? $_REQUEST['convert_to'] : 'EUR';
		return $data;
	}

	function tcp_paypal_converted_amount( $amount, $data ) {
		$use_conversion = isset( $data['use_conversion'] ) ? $data['use_conversion'] : 'no';
		switch( $use_conversion ) {
		case 'no' :
			return $amount;
		case 'google_api' :
			$conversion_rate = $this->get_conversion_rate_from_google( $data );
			break;
		case 'manually' :
			$conversion_rate = isset( $data['conversion_rate'] ) ? $data['conversion_rate'] : 1;
		}
		return $amount * $conversion_rate;
	}

	function tcp_paypal_get_convert_to( $currency, $data ) {
		$use_conversion = isset( $data['use_conversion'] ) ? $data['use_conversion'] : 'no';
		if ( $use_conversion == 'no') return $currency;
		elseif ( isset( $data['convert_to'] ) ) return $data['convert_to'];
		else return $currency;
	}

	function get_conversion_rate_from_google( $data ) {
		$from = tcp_get_the_currency_iso();
		$convert_to	= isset( $data['convert_to'] ) ? $data['convert_to'] : 'EUR';
		$hash = sprintf( 'tcp_conversion_rate_from_%s_to_%s', $from, $convert_to );
		if ( false === ( $conversion_rate = get_transient( $hash ) ) ) {
			$url = 'http://www.google.com/ig/calculator?hl=en&q=1' . $from . '%3D%3F' . $convert_to;
			$res = file_get_contents( $url );
			$res = str_replace( array( 'lhs', 'rhs', 'error', 'icc' ), array( '"lhs"', '"rhs"', '"error"', '"icc"'), $res );
			$data = json_decode( $res );
			$conversion_rate = (float)$data->rhs;
			set_transient( $hash, $conversion_rate, 60 * 60 * 6 );//6 hours
		}
		return $conversion_rate;
	}
}
?>
