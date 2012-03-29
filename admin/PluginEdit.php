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

require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );
require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );

$plugin_id		= isset( $_REQUEST['plugin_id'] ) ? $_REQUEST['plugin_id'] : '';
$plugin_type	= isset( $_REQUEST['plugin_type'] ) ? $_REQUEST['plugin_type'] : tcp_get_plugin_type( $plugin_id );
$instance		= isset( $_REQUEST['instance'] ) ? (int)$_REQUEST['instance'] : 0;

if ( isset( $_REQUEST['tcp_plugin_save'] ) ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id, array() );
	if ( ! $plugin_data ) $plugin_data = array();
	$plugin_data[$instance] = array();
	$plugin_data[$instance]['title'] = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';
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
		$plugin = tcp_get_plugin( $plugin_id );
		$plugin_data[$instance] = $plugin->saveEditfields( $plugin_data[$instance], $instance );
		$plugin_data = apply_filters( 'tcp_plugin_edit_save', $plugin_data, $plugin_id, $instance );
		update_option( 'tcp_plugins_data_' . $plugin_id, $plugin_data );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Instance saved', 'tcp' ); ?>
		</p></div><?php
	} else {?>
		<div id="message" class="error"><p>
			<?php _e( 'The title must be completed', 'tcp' ); ?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_plugin_delete'] ) ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
	do_action( 'tcp_plugin_edit_delete', $plugin_id );
	unset( $plugin_data[$instance] );
	update_option( 'tcp_plugins_data_' . $plugin_id, $plugin_data );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Instance deleted', 'tcp' );?>
	</p></div><?php
}
$plugin			= tcp_get_plugin( $plugin_id );
$plugin_type	= tcp_get_plugin_type( $plugin_id );
$plugin_data	= get_option( 'tcp_plugins_data_' . $plugin_id );
$instance_href	= TCP_ADMIN_PATH . 'PluginEdit.php&plugin_id=' . $plugin_id . '&plugin_type=' . $plugin_type . '&instance=';
?>

<div class="wrap">
<h2><?php //echo __( 'Plugin', 'tcp' ), ':';?> <?php echo $plugin->getTitle();?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>PluginsList.php&plugin_type=<?php echo $plugin_type?>"><?php _e( 'Return to the list', 'tcp' );?></a></li>
</ul><!-- subsubsub -->
<div class="clear"></div>

<div class="instances">
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
			<span><?php echo $title;?></span>&nbsp;|&nbsp;
		<?php else: ?>
			<a href="<?php echo $instance_href, $instance_id;?>"><?php echo $title;?></a>&nbsp;|&nbsp;
		<?php endif;?>
	<?php endforeach;?>
	<?php if ( $plugin->isInstanceable() ) : ?>
	<a href="<?php echo $instance_href, $instance_id + 1;?>"><?php _e( 'new instance', 'tcp' );?></a>
	<?php endif; ?>
<?php else :
	$data = array();
endif;
$new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PENDING;
?>
</div>

<form method="post">
	<input type="hidden" name="plugin_id" value="<?php echo $plugin_id;?>" />
	<input type="hidden" name="plugin_type" value="<?php echo $plugin_type;?>" />
	<input type="hidden" name="instance" value="<?php echo $instance;?>" />
	<table class="form-table">
	<tbody>
	<tr valign="top">
	<th scope="row">
		<label for="title"><?php _e( 'Title', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="text" name="title" id="title" value="<?php echo isset( $data['title'] ) ? $data['title'] : '';?>" />
	</td>
	</tr>
	<tr valign="top">
	<th scope="row">
		<label for="active"><?php _e( 'Active', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="checkbox" name="active" id="active" <?php checked( isset( $data['active'] ) ? $data['active'] : false, true );?> value="yes" />
	</td>
	</tr>
<?php if ( $plugin_type == 'payment' ) : ?>
	<tr valign="top">
	<th scope="row">
		<label for="not_for_downloadable"><?php _e( 'Do not Apply for downloadable products', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="checkbox" name="not_for_downloadable" id="not_for_downloadable" <?php checked( isset( $data['not_for_downloadable'] ) ? $data['not_for_downloadable'] : false, true );?> value="yes" />
	</td>
	</tr>
	<tr valign="top">
	<th scope="row">
		<label for="new_status"><?php _e( 'New status', 'tcp' );?>:</label>
	</th>
	<td>
		<select class="postform" id="new_status" name="new_status"><?php 
		$order_status_list = tcp_get_order_status();
		foreach ( $order_status_list as $order_status ) : ?>
			<option value="<?php echo $order_status['name'];?>"<?php selected( $order_status['name'], $new_status );?>><?php echo $order_status['label']; ?></option>		
		<?php endforeach; ?>
		</select>
		<p class="description"><?php _e( 'If the payment is right, the order status will be the selected one.', 'tcp' );?></p>
	</td>
	</tr>
<?php endif; ?>
	<tr valign="top">
	<th scope="row">
		<label for="all_countries"><?php _e( 'Apply the plugin to all countries', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="checkbox" name="all_countries" id="all_countries" <?php checked( isset( $data['all_countries'] ) ? $data['all_countries'] : '', 'yes' );?> value="yes"
		onclick="if (this.checked) jQuery('.sel_countries').hide(); else jQuery('.sel_countries').show();"/>
	</td>
	</tr>
	<tr valign="top" class="sel_countries" <?php
		$all = isset( $data['all_countries'] ) ? $data['all_countries'] : '';
		if ( $all == 'yes' ) echo 'style="display:none;"';
		?>>
	<th scope="row">
		<label for="countries"><?php _e( 'Apply the plugin only to selected ones', 'tcp' );?>:</label>
	</th>
	<td>
		<?php $selected_countries = isset( $data['countries'] ) ? $data['countries'] : array();?>
		<div style="float:left">
			<select class="postform" id="countries" name="countries[]" multiple="true" size="10" style="height: auto;">
				<?php //$countries = Countries::getAll();
				global $thecartpress;
				if ( $plugin_type == 'shipping' )
					$isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
				else //billing
					$isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
				if ( $isos )
					$countries = Countries::getSome( $isos );
				else
					$countries = Countries::getAll();
				foreach( $countries as $country ) :?>
				<option value="<?php echo $country->iso;?>" <?php tcp_selected_multiple( $selected_countries, $country->iso );?>><?php echo $country->name;?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div>
			<input type="button" value="<?php _e( 'EU', 'tcp');?>" title="<?php _e( 'To select countries from the European Union', 'tcp' );?>" onclick="tcp_select_eu('countries');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp');?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' );?>" onclick="tcp_select_nafta('countries');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp');?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' );?>" onclick="tcp_select_caricom('countries');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp');?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' );?>" onclick="tcp_select_mercasur('countries');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp');?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' );?>" onclick="tcp_select_can('countries');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp');?>" title="<?php _e( 'To select countries from African Union', 'tcp' );?>" onclick="tcp_select_au('countries');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp');?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' );?>" onclick="tcp_select_apec('countries');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp');?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' );?>" onclick="tcp_select_asean('countries');" class="button-secondary"/>
		</div>
	</td>
	</tr>
	<?php do_action( 'tcp_plugin_edit_fields', $data );?>
	<?php $plugin->showEditFields( $data );?>
	</tbody></table>
	<p class="submit">
		<input name="tcp_plugin_save" value="<?php _e( 'save', 'tcp' );?>" type="submit" class="button-primary" />
		<input name="tcp_plugin_delete" value="<?php _e( 'delete', 'tcp' );?>" type="submit" class="button-secondary" />
	</p>
</form>
</div><!-- wrap -->
