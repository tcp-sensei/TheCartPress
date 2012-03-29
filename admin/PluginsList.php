<?php
/**
 * This file is part of TheCartPress.
 * 
 * This progam is free software: you can redistribute it and/or modify
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

$plugin_type = isset( $_REQUEST['plugin_type'] ) ? $_REQUEST['plugin_type'] : 'payment'; ?>

<div class="wrap">
<h2><?php $plugin_type == 'payment' ? _e( 'TheCartPress Payment methods', 'tcp' ) : _e( 'TheCartPress Shipping methods', 'tcp' );?></h2>
<ul class="subsubsub"></ul>
<div class="clear"></div>

<form method="post">
<div class="tablenav">
<p class="search-box">
<label for="plugin_type"><?php _e( 'Plugin type', 'tcp' );?>:</label>
	<select class="postform" id="plugin_type" name="plugin_type">
		<option value="" <?php selected( '', $plugin_type );?>><?php _e( 'all', 'tcp' );?></option>
		<option value="shipping" <?php selected( 'shipping', $plugin_type );?>><?php _e( 'shipping', 'tcp' );?></option>
		<option value="payment" <?php selected( 'payment', $plugin_type );?>><?php _e( 'payment', 'tcp' );?></option>
	</select>
	<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'tcp' );?>" id="post-query-submit" />
</p>
</div>
</form>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' );?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' );?></th>
</tr>
</tfoot>
<tbody>
<?php if ( $plugin_type == 'shipping' ) {
	global $tcp_shipping_plugins;
	$plugins = $tcp_shipping_plugins;
} elseif ( $plugin_type == 'payment' ) {
	global $tcp_payment_plugins;
	$plugins = $tcp_payment_plugins;
} else {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;
	$plugins = $tcp_shipping_plugins + $tcp_payment_plugins;
}
foreach( $plugins as $id => $plugin ) :
	$tr_class = '';
	$data = tcp_get_plugin_data( $id );
	if ( is_array( $data ) && count( $data ) > 0 ) {
		$n_active = 0;
		foreach( $data as $instances )
			if ( $instances['active'] ) $n_active++;
		$out = sprintf( __( 'N<sup>o</sup> of instances: %d, actives: %d ', 'tcp') ,  count( $data ), $n_active );
		if ( $n_active > 0 )
			$tr_class = 'class="tcp_active_plugin"';
	} else {
		$out = __( 'Not in use', 'tcp' );
	}?>
	<tr <?php echo $tr_class;?>>
		<td><a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $plugin_type;?>" title="<?php _e( 'Edit', 'tcp' );?>"><?php echo $plugin->getTitle();?></a>
		<div class="tcp_plugins_edit" style="display: none;"><a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $plugin_type;?>"><?php _e( 'edit', 'tcp' );?></a></div></td>
		<td><?php echo $plugin->getDescription();?></td>
		<td><?php echo $out;?></td>
	</tr>
<?php endforeach;?>
</tbody></table>
</div> <!-- end wrap -->
