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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPPluginsList' ) ) {

class TCPPluginsList {

	private $plugin_type;

	function __construct( $plugin_type = 'payment' ) {
		$this->plugin_type = $plugin_type;
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_plugins' ) ) return;
		$base = thecartpress()->get_base_settings();
		if ( $this->plugin_type == 'payment' ) {
			$title = __( 'Payment Methods', 'tcp' );
			$menu_slug = 'payment_settings';
		} else {
			$title = __( 'Shipping Methods', 'tcp' );
			$menu_slug = 'shipping_settings';
		}
		$page = add_submenu_page( $base, $title, $title, 'tcp_edit_plugins', $menu_slug, array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize Payment and Shipping plugins.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
	}

	function admin_page() { ?>
<div class="wrap">
	<?php screen_icon( 'tcp-' . $this->plugin_type ); ?><h2><?php $this->plugin_type == 'payment' ? _e( 'Payment methods', 'tcp' ) : _e( 'Shipping methods', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<div class="clear"></div>

<form method="post">

<div class="tablenav">
	<p class="search-box">
	<label for="plugin_type"><?php _e( 'Plugin type', 'tcp' ); ?>:</label>
		<select class="postform" id="plugin_type" name="plugin_type">
			<option value="" <?php selected( '', $this->plugin_type ); ?>><?php _e( 'all', 'tcp' ); ?></option>
			<option value="shipping" <?php selected( 'shipping', $this->plugin_type ); ?>><?php _e( 'shipping', 'tcp' ); ?></option>
			<option value="payment" <?php selected( 'payment', $this->plugin_type ); ?>><?php _e( 'payment', 'tcp' ); ?></option>
		</select>
		<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'tcp' ); ?>" id="post-query-submit" />
	</p>
</div>

</form>

<p class="description">
<?php if ( $this->plugin_type == 'payment' ) {
	printf( __( 'More Gateway options, and functionalities, are available via <a href="%s" target="_blank">TheCartPress official extensions</a> site.', 'tcp' ), 'http://extend.thecartpress.com/wordpress-ecommerce/payment-gateways/' );
} else {
	printf( __( 'More Shipping options, and functionalities, are available via <a href="%s" target="_blank">TheCartPress official extensions</a> site.', 'tcp' ), 'http://extend.thecartpress.com/wordpress-ecommerce/shipping-fulfillment/' );
} ?>
</p>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width:50%"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' ); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' ); ?></th>
</tr>
</tfoot>
<tbody>
<?php if ( $this->plugin_type == 'shipping' ) {
	global $tcp_shipping_plugins;
	$plugins = $tcp_shipping_plugins;
} elseif ( $this->plugin_type == 'payment' ) {
	global $tcp_payment_plugins;
	$plugins = $tcp_payment_plugins;
} else {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;
	$plugins = $tcp_shipping_plugins + $tcp_payment_plugins;
}
if ( is_array( $plugins ) ) :
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
		} ?>
		<tr <?php echo $tr_class;?>>
			<td>
				<a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $this->plugin_type;?>" title="<?php printf( __( 'Edit %s', 'tcp' ), $plugin->getTitle() ); ?>" class="tcp_payment_title">
				<?php $icon = $plugin->getIcon();
				if ( $icon ) : ?>
					<img src="<?php echo $icon; ?>" height="48px" />
				<?php else : ?>
					<span style="font-weight:bold;"><?php echo $plugin->getTitle(); ?></span>
				<?php endif; ?>
				</a>
			</td>
			<td>
				<?php echo $plugin->getDescription(); ?>
				<?php $plugin_class = get_class( $plugin );
				$template_id = tcp_template_get_post_id( 'tcp_payment_plugins_' . $plugin_class );
				$template = tcp_do_template( 'tcp_payment_plugins_' . $plugin_class, false ); ?>
				<div>
				<?php if ( strlen( $template ) > 0 ) { ?>
					<div class="tcp_template_div" id="tcp_template_div-<?php echo $plugin_class; ?>" style="display: none;"><?php echo $template; ?></div>
					<?php _e( 'Notice', 'tcp' ); ?>: <a href="post.php?post=<?php echo $template_id; ?>&action=edit"><?php _e( 'Edit', 'tcp' ); ?></a> | <a href="#" class="tcp_div_show_hide" plugin_class="<?php echo $plugin_class; ?>"><?php _e( 'Show/Hide', 'tcp' ); ?></a>
				<?php } else { ?>
					<?php printf( __( 'This method has not a notice associated. Create one using <a href="%s">Notices menu</a>', 'tcp' ), 'edit.php?post_type=tcp_template' ); ?>
				<?php } ?>
				</div>
			</td>
			<td>
				<?php echo $out;?> | <a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $this->plugin_type;?>" title="<?php printf( __( 'Edit %s', 'tcp' ), $plugin->getTitle() ); ?>" class="tcp_payment_title"><?php _e( 'Edit', 'tcp' ); ?></a>
			</td>
		</tr>
	<?php endforeach;
else : ?>
		<tr class="tcp_no_plugins">
			<td colspan="3"><?php _e( 'No plugins', 'tcp' ); ?></td>
		</tr>
<?php endif; ?>
</tbody></table>
<script>
jQuery( '.tcp_div_show_hide' ).click( function( e ) {
	var pc = jQuery( this ).attr( 'plugin_class' );
	jQuery( '#tcp_template_div-' + pc ).toggle(  );
	e.stopPropagation();
	return false;
} );
</script>
</div> <!-- end wrap -->
<?php
	}
}

new TCPPluginsList( 'payment' );
} // class_exists check