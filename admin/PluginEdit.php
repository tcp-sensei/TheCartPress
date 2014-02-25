<?php
/**
 * Plugin Editor
 *
 * Allows to Edit any Payment or Shipping methd.
 *
 * @package TheCartPress
 * @subpackage Admin
 */

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

require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );

$plugin_id		= isset( $_REQUEST['plugin_id'] ) ? $_REQUEST['plugin_id'] : '';
$plugin_type	= isset( $_REQUEST['plugin_type'] ) ? $_REQUEST['plugin_type'] : tcp_get_plugin_type( $plugin_id );
$instance		= isset( $_REQUEST['instance'] ) ? (int)$_REQUEST['instance'] : 0;

if ( isset( $_REQUEST['tcp_plugin_save'] ) ) {
	$plugin_data = tcp_get_plugin_data( $plugin_id );
	if ( ! $plugin_data ) $plugin_data = array();
	$plugin_data[$instance] = array();
	$plugin_data[$instance]['title'] = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';
	
	tcp_register_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', $plugin_id . '-title-' . $instance ), $plugin_data[$instance]['title'] );
	//tcp_unregister_string( 'TheCartPress', $plugin_id . '-title' );
	
	if ( strlen($plugin_data[$instance]['title'] ) > 0 ) {
		$plugin_data[$instance]['active'] = isset( $_REQUEST['active'] );
		$plugin_data[$instance]['not_for_downloadable'] = isset( $_REQUEST['not_for_downloadable'] );
		if ( isset( $_REQUEST['all_countries'] ) ) {
			$plugin_data[$instance]['all_countries'] = $_REQUEST['all_countries'];
			$plugin_data[$instance]['countries'] = array();
		} else {
			$plugin_data[$instance]['all_countries']	= '';
			$plugin_data[$instance]['countries'] = isset( $_REQUEST['countries'] ) ? $_REQUEST['countries'] : array();
		}
		$plugin_data[$instance]['new_status'] = isset( $_REQUEST['new_status'] ) ? $_REQUEST['new_status'] : Orders::$ORDER_PENDING;
		$plugin_data[$instance]['unique'] = isset( $_REQUEST['unique'] );
		$plugin = tcp_get_plugin( $plugin_id );
		$plugin_data[$instance] = $plugin->saveEditfields( $plugin_data[$instance], $instance );
		$plugin_data = apply_filters( 'tcp_plugin_edit_save', $plugin_data, $plugin_id, $instance );

		//Save page
		tcp_update_plugin_data( $plugin_id, $plugin_data ); ?>
		<div id="message" class="updated"><p>
			<?php _e( 'Instance saved', 'tcp' ); ?>
		</p></div><?php
	} else {?>
		<div id="message" class="error"><p>
			<?php _e( 'The title must be completed', 'tcp' ); ?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_plugin_delete'] ) ) {
	$plugin_data = tcp_get_plugin_data( $plugin_id );
	do_action( 'tcp_plugin_edit_delete', $plugin_id );
	unset( $plugin_data[$instance] );
	tcp_update_plugin_data( $plugin_id, $plugin_data );
	tcp_unregister_string( 'TheCartPress', $plugin_id . '-title' ); ?>
	<div id="message" class="updated"><p>
		<?php _e( 'Instance deleted', 'tcp' ); ?>
	</p></div><?php
}
$plugin			= tcp_get_plugin( $plugin_id );
$plugin_type	= tcp_get_plugin_type( $plugin_id );
$plugin_data	= tcp_get_plugin_data( $plugin_id );
$instance_href	= TCP_ADMIN_PATH . 'PluginEdit.php&plugin_id=' . $plugin_id . '&plugin_type=' . $plugin_type . '&instance='; ?>

<div class="wrap tcpf">
<h2><img src="<?php echo $plugin->getIcon(); ?>" height="48px" title="<?php echo $plugin->getTitle(); ?>"/></h2>

<div class="postbox">
<form method="post">
	<div class="inside">
		<h4 class="nav-tab-wrapper">
		<?php if ( is_array( $plugin_data ) && count( $plugin_data ) ) :
			$data = isset( $plugin_data[$instance] ) ? $plugin_data[$instance] : array();
			foreach( $plugin_data as $instance_id => $instance_data ) :
				$data_instanced = isset( $plugin_data[$instance_id] ) ? $plugin_data[$instance_id] : array();
				if ( isset( $data_instanced['title'] ) ) {
					$title = $data_instanced['title'];
				} else {
					$title = sprintf( __( 'Instance %d', 'tcp' ), $instance_id );
				}
				if ( $instance_id == $instance ) : ?>
					<a class="nav-tab nav-tab-active" href="javascript:void(0);"><?php echo $title;?></a>
				<?php else: ?>
					<a class="nav-tab" href="<?php echo $instance_href, $instance_id;?>"><?php echo $title;?></a>
				<?php endif;?>
			<?php endforeach;?>
			<?php if ( $plugin->isInstanceable() ) : ?>
			<a class="nav-tab <?php if ( $instance == $instance_id + 1 ) : ?>nav-tab-active" href="javascript:void(0);<?php else : ?>" href="<?php echo $instance_href, $instance_id + 1;?><?php endif; ?>"><?php _e( 'new instance', 'tcp' ); ?> <span class="glyphicon glyphicon-plus-sign"></span></a>
			<?php endif; ?>
			<?php if ( $plugin_type == 'payment' ) {
				$page = 'payment_settings';
			} else {
				$page = 'shipping_settings';
			}
			$url = add_query_arg( 'page', $page, get_admin_url() . 'admin.php' ); ?>
			<a href="<?php echo $url; ?>" class="btn-sm"><?php _e( 'Return to the list', 'tcp' ); ?></a>
		<?php else :
			$data = array();
		endif;
		$new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PENDING; ?>
		</h4>

		<input type="hidden" name="plugin_id" value="<?php echo $plugin_id;?>" />
		<input type="hidden" name="plugin_type" value="<?php echo $plugin_type;?>" />
		<input type="hidden" name="instance" value="<?php echo $instance;?>" />

		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="title"><?php _e( 'Title', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="text" name="title" id="title" value="<?php echo isset( $data['title'] ) ? $data['title'] : '';?>" size="40"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="active"><?php _e( 'Active', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="active" id="active" <?php checked( isset( $data['active'] ) ? $data['active'] : false, true ); ?> value="yes" />
			</td>
		</tr>
	<?php if ( $plugin_type == 'payment' ) : ?>
		<tr valign="top">
			<th scope="row">
				<label for="not_for_downloadable"><?php _e( 'Do not apply to downloadable products', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="not_for_downloadable" id="not_for_downloadable" <?php checked( isset( $data['not_for_downloadable'] ) ? $data['not_for_downloadable'] : false, true ); ?> value="yes" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="new_status"><?php _e( 'New status', 'tcp' ); ?>:</label>
			</th>
			<td>
				<select class="postform" id="new_status" name="new_status">
				<?php $order_status_list = tcp_get_order_status();
				foreach ( $order_status_list as $order_status ) : ?>
					<option value="<?php echo $order_status['name'];?>"<?php selected( $order_status['name'], $new_status ); ?>><?php echo $order_status['label']; ?></option>		
				<?php endforeach; ?>
				</select>
				<p class="description"><?php _e( 'If the payment is right, the order status will be the selected one.', 'tcp' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
		<tr valign="top">
			<th scope="row">
				<label for="all_countries"><?php _e( 'Apply to all countries', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="all_countries" id="all_countries" <?php checked( isset( $data['all_countries'] ) ? $data['all_countries'] : '', 'yes' ); ?> value="yes"
				onclick="if (this.checked) jQuery('.sel_countries').hide(); else jQuery('.sel_countries').show();"/>
			</td>
		</tr>
		<tr valign="top" class="sel_countries" <?php $all = isset( $data['all_countries'] ) ? $data['all_countries'] : '';
			if ( $all == 'yes' ) echo 'style="display:none;"'; ?>>
			<th scope="row">
				<label for="countries"><?php _e( 'Apply only to selected ones', 'tcp' ); ?>:</label>
			</th>
			<td>
				<?php $selected_countries = isset( $data['countries'] ) ? $data['countries'] : array(); ?>
				<div style="float:left">
					<select class="postform" id="countries" name="countries[]" multiple="true" size="10" style="height: auto;">
						<?php global $thecartpress;
						if ( $plugin_type == 'shipping' ) {
							$isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
						} else {//billing
							$isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
						}
						if ( $isos ) {
							$countries = TCPCountries::getSome( $isos );
						} else {
							$countries = TCPCountries::getAll();
						}
						foreach( $countries as $country ) :?>
						<option value="<?php echo $country->iso;?>" <?php tcp_selected_multiple( $selected_countries, $country->iso ); ?>><?php echo $country->name;?></option>
						<?php endforeach;?>
					</select>
				</div>
				<script >
					jQuery( '#countries' ).tcp_convert_multiselect();
				</script>
				<div>
					<select class="tcp_main_select_countries">
						<option value="none"><?php _e( 'None', 'tcp'); ?></option>
						<option value="eu" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>"><?php _e( 'EU', 'tcp'); ?></option>
						<option value="nafta"><?php _e( 'NAFTA', 'tcp'); ?></option>
						<option value="caricom"><?php _e( 'CARICOM', 'tcp'); ?></option>
						<option value="mercasur"><?php _e( 'MERCASUR', 'tcp'); ?></option>
						<option value="can"><?php _e( 'CAN', 'tcp'); ?></option>
						<option value="au"><?php _e( 'AU', 'tcp'); ?></option>
						<option value="apec"><?php _e( 'APEC', 'tcp'); ?></option>
						<option value="asean"><?php _e( 'ASEAN', 'tcp'); ?></option>
						<option value="toggle"><?php _e( 'Toggle', 'tcp'); ?></option>
						<option value="all"><?php _e( 'All', 'tcp'); ?></option>
					</select>
					<script>
					jQuery( '.tcp_main_select_countries' ).on( 'change', function() {
						var org = jQuery( this ).val();
						if ( org == 'eu' ) tcp_select_eu( 'countries' );
						else if ( org == 'nafta' ) tcp_select_nafta( 'countries' );
						else if ( org == 'caricom' ) tcp_select_caricom( 'countries' );
						else if ( org == 'mercasur' ) tcp_select_mercasur( 'countries' );
						else if ( org == 'can' ) tcp_select_can( 'countries' );
						else if ( org == 'au' ) tcp_select_au( 'countries' );
						else if ( org == 'apec' ) tcp_select_apec( 'countries' );
						else if ( org == 'asean' ) tcp_select_asean( 'countries' );
						else if ( org == 'toggle' ) tcp_select_toggle( 'countries' );
						else if ( org == 'none' ) tcp_select_none( 'countries' );
						else if ( org == 'all' ) tcp_select_all( 'countries' );
					} );
					</script>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="unique"><?php _e( 'If applicable, display only this method', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" id="unique" name="unique" value="yes" <?php checked( isset( $data['unique'] ) ? $data['unique'] : false ); ?> />
			</td>
		</tr>
		</tbody>
		</table>
	</div><!-- .inside -->
</div><!-- .postbox -->

<h3><?php if ( $plugin_type == 'shipping' ) { ?>
	<?php _e( 'Settings', 'tcp' ); ?>					
<?php } else { //billing ?>
	<?php _e( 'Account Settings', 'tcp' ); ?>
<?php } ?></h3>

<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
			<?php do_action( 'tcp_plugin_edit_fields', $data, $plugin_type ); ?>
			<?php $plugin->showEditFields( $data ); ?>
		</tbody>
		</table>
	</div><!-- .inside -->
</div><!-- .postbox -->

<p class="submit">
	<input name="tcp_plugin_save" value="<?php _e( 'Save', 'tcp' ); ?>" type="submit" class="button-primary" />
	<input name="tcp_plugin_delete" value="<?php _e( 'Delete', 'tcp' ); ?>" type="submit" class="button-secondary" />
</p>
</form>
</div><!-- wrap -->