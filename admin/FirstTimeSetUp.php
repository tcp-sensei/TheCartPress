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

function tcp_get_plugins_info( $plugins ) {
	$out = '';
	$total_active = 0;
	if ( is_array( $plugins ) && count( $plugins ) > 0 ) {
		$out = '<table width="50%" style="padding-left: 5em;border: 1px lightgrey solid" border="0">';
		foreach( $plugins as $id => $plugin ) {
			$tr_class = '';
			$data = tcp_get_plugin_data( $id );
			if ( is_array( $data ) && count( $data ) > 0 ) {
				$n_active = 0;
				foreach( $data as $instances ) {
					if ( $instances['active'] ) $n_active++;
				}
				$total_active += $n_active;
				$use = sprintf( __( 'N<sup>o</sup> of instances: %d, actives: %d ', 'tcp') ,  count( $data ), $n_active );
			} else {
				$use = __( 'Not in use', 'tcp' );
			}
			$out .= '<tr><td>' . $plugin->getTitle() . '</td><td>' . $use . '</td></tr>';
		}
		$out .= '</table>';
		if ( $total_active == 0 ) $out .= '<p><strong style="color:red">NOTICE: None is active!</strong></p>';
	}
	return $out;
}

function tcp_get_step_title( $step ) {
	if ( $step > 0 )
		return sprintf( __( 'Step %d', 'tcp' ), $step );
	elseif ( $step == 1 )
		return sprintf( __( 'Final step', 'tcp' ) );
}

require_once( TCP_ADMIN_FOLDER . 'Settings.class.php' );
$settings = new TCPSettings( false );
$step = isset( $_REQUEST['step'] ) ? $_REQUEST['step'] : 1;
if ( isset( $_REQUEST['tcp_next_step'] ) ) {
	$step++;
} elseif ( isset( $_REQUEST['tcp_prev_step'] ) ) {
	$step--;
} elseif ( isset( $_REQUEST['tcp_save'] ) ) {
	global $thecartpress;
	if ( $_REQUEST['tcp_settings']['decimal_point'] == '' ) {
		if ( $_REQUEST['tcp_settings']['thousands_separator'] == '.' ) {
			$_REQUEST['tcp_settings']['decimal_point'] = ',';
		} else {
			$_REQUEST['tcp_settings']['decimal_point'] = '.';
		}
	} elseif ( $_REQUEST['tcp_settings']['decimal_point'] == $_REQUEST['tcp_settings']['thousands_separator'] ) {
		if ( $_REQUEST['tcp_settings']['thousands_separator'] == '.' ) {
			$_REQUEST['tcp_settings']['decimal_point'] = ',';
		} else {
			$_REQUEST['tcp_settings']['decimal_point'] = '.';
		}
	}
	$thecartpress->settings['user_registration']	= isset( $_REQUEST['tcp_settings']['user_registration'] ) ? true : false;
	$thecartpress->settings['emails']				= $_REQUEST['tcp_settings']['emails'];
	$thecartpress->settings['from_email']			= $_REQUEST['tcp_settings']['from_email'];
	$thecartpress->settings['currency']				= $_REQUEST['tcp_settings']['currency'];
	$thecartpress->settings['currency_layout']		= $_REQUEST['tcp_settings']['currency_layout'];
	$thecartpress->settings['decimal_currency']		= $_REQUEST['tcp_settings']['decimal_currency'];
	$thecartpress->settings['decimal_point']		= $_REQUEST['tcp_settings']['decimal_point'];
	$thecartpress->settings['thousands_separator']	= $_REQUEST['tcp_settings']['thousands_separator'];
	$thecartpress->settings['country']				= $_REQUEST['tcp_settings']['country'];
	$thecartpress->settings['use_default_loop']		= $_REQUEST['tcp_settings']['use_default_loop'];
	update_option( 'tcp_settings', $thecartpress->settings );
	$step = -1;
}?>
<div class="wrap">
<div class="tcp_first_time_setup">
<br>
</div>
<h2><?php _e( 'First time setup', 'tcp' ); ?></h2>
<ul class="subsubsub">
</ul>
<br>
<h2><?php echo tcp_get_step_title( $step ); ?></h2>
<form method="post">
<input type="hidden" name="step" value="<?php echo $step; ?>"/>
<?php if ( $step == -1 ) :
	$values = array(); ?>
	<p><?php _e( 'Congratulations! You have finished to setup your eCommerce software.', 'tcp' ); ?></p>
	<p><?php _e( 'The eCommerce has actived the following shipping and payments methods:', 'tcp' ); ?></p>
	<h3><?php _e( 'Shipping methods', 'tcp' ); ?></h3>
	<?php global $tcp_shipping_plugins; echo tcp_get_plugins_info( $tcp_shipping_plugins ); ?>
	<p><?php _e( 'To activate or deactivate Shipping methods you have to visit the <a href="admin.php?page=thecartpress/admin/PluginsListShipping.php">Shipping page</a>.', 'tcp' ); ?></p>
	<h3><?php _e( 'Payment methods', 'tcp' ); ?></h3>
	<?php global $tcp_payment_plugins; echo tcp_get_plugins_info( $tcp_payment_plugins ); ?>
	<p><?php _e( 'To activate or deactivate Payment methods you have to visit the <a href="admin.php?page=thecartpress/admin/PluginsList.php">Payment page</a>.', 'tcp' ); ?></p>
	<p><?php _e( 'Remmember, you have more configuration settings in the <a href="admin.php?page=tcp_settings_page">Settings page</a>.', 'tcp' ); ?></p>
<?php elseif ( $step == 1 ) :
	$values = array( 'country' );
	$country = isset( $_REQUEST['tcp_settings']['country'] ) ? $_REQUEST['tcp_settings']['country'] : false; ?>
	<p><?php _e( 'Please, select the base country', 'tcp' ); ?></p>
	<table class="form-table" style="display: table;">
	<tbody>
	<tr valign="top">
		<th scope="row"><label for="country"><?php _e( 'Base country', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_country( $country ); ?></td>
	</tr>
	</tbody>
	</table>
<?php elseif ( $step == 2 ) :
	$values					= array( 'currency', 'currency_layout', 'decimal_currency', 'decimal_point', 'thousands_separator' );
	$currency				= isset( $_REQUEST['tcp_settings']['currency'] ) ? $_REQUEST['tcp_settings']['currency'] : false;
	$currency_layout		= isset( $_REQUEST['tcp_settings']['currency_layout'] ) ? $_REQUEST['tcp_settings']['currency_layout'] : false;
	$decimal_currency		= isset( $_REQUEST['tcp_settings']['decimal_currency'] ) ? $_REQUEST['tcp_settings']['decimal_currency'] : false;
	$decimal_point			= isset( $_REQUEST['tcp_settings']['decimal_point'] ) ? $_REQUEST['tcp_settings']['decimal_point'] : false;
	$thousands_separator	= isset( $_REQUEST['tcp_settings']['thousands_separator'] ) ? $_REQUEST['tcp_settings']['thousands_separator'] : false;
?>
	<p><?php _e( 'Please, select the currency settings to use along the Store', 'tcp' ); ?></p>
	<table class="form-table" style="display: table;">
	<tbody>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="currency"><?php _e( 'Currency', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_currency( $currency ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="currency_layout"><?php _e( 'Currency layout', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_currency_layout( $currency_layout ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="decimal_currency"><?php _e( 'Number of decimals', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_decimal_currency( $decimal_currency ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="decimal_point"><?php _e( 'Decimal point', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_decimal_point( $decimal_point ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="thousands_separator"><?php _e( 'Thousands separator', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_thousands_separator( $thousands_separator ); ?></td>
	</tr>
	</tbody>
	</table>
<?php elseif ( $step == 3 ) :
	$values = array( 'user_registration', 'emails', 'from_email' );
	$user_registration	= isset( $_REQUEST['tcp_settings']['user_registration'] );
	$emails				= isset( $_REQUEST['tcp_settings']['emails'] ) ? $_REQUEST['tcp_settings']['emails'] : get_option('admin_email');
	$from_email			= isset( $_REQUEST['tcp_settings']['from_email'] ) ? $_REQUEST['tcp_settings']['from_email'] : false; ?>
	<p><?php _e( 'Please, select the checkout settings', 'tcp' ); ?></p>
	<table class="form-table" style="display: table;">
	<tbody>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="user_registration"><?php _e( 'User registration', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_user_registration( $user_registration ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="emails"><?php _e( 'emails', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_emails( $emails ); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="from_email"><?php _e( 'From email', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_from_email( $from_email ); ?></td>
	</tr>
	</tbody>
	</table>
<?php else : //if ( $step == 4 ) :
	$values = array( 'use_default_loop' );
	$use_default_loop = isset( $_REQUEST['tcp_settings']['use_default_loop'] ); ?>
		<table class="form-table" style="display: table;">
	<tbody>
	<tr valign="top">
		<th scope="row" style="align:left"><label for="use_default_loop"><?php _e( 'Use default loop', 'tcp' ); ?></label>:</th>
		<td><?php $settings->show_use_default_loop( $use_default_loop ); ?></td>
	</tr>
	</tbody>
	</table>
	<p><?php _e( 'The first time setup has finished. Press "Save" to save all the settings.', 'tcp'); ?></p>
<?php endif; ?>
<p>
	<?php if ( $step > 1 ) {
		submit_button( __( 'Previuos step', 'tcp' ), 'secondary', 'tcp_prev_step', false, array( 'id' => 'tcp_prev_step' ) );
	}
	if ( $step > 0 && $step < 4 ) {
		submit_button( __( 'Next step', 'tcp' ), 'secondary', 'tcp_next_step', false, array( 'id' => 'tcp_next_step' ) );
	} elseif ( $step == 4 ) {
		echo '&nbsp;&nbsp;';
		submit_button( __( 'Save', 'tcp' ), 'primary', 'tcp_save', false, array( 'id' => 'tcp_save' ) );
	}?>
</p>

<?php if ( isset( $_REQUEST['tcp_settings'] ) && is_array( $_REQUEST['tcp_settings'] ) && count( $_REQUEST['tcp_settings'] ) > 0 ) {
	foreach( $_REQUEST['tcp_settings'] as $id => $value ) {
		if ( ! in_array( $id, $values ) ) { ?>
			<input type="hidden" name="tcp_settings[<?php echo $id; ?>]" value="<?php echo $value; ?>" />
	<?php }
	}
} ?>
</form>
</div><!-- .wrap -->