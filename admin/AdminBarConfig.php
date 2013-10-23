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
exit();
?>
<div class="wrap">
<h2><?php _e( 'Admin Bar Config', 'tcp' );?></h2>
<ul class="subsubsub">
</ul>
<div class="clear"></div>

<form method="post">
<span class="description"><?php _e( 'Select the menus to hide in the Admin menu bar', 'tcp' );?></span>
<table class="form-table">
<?php
if ( isset( $_REQUEST['tcp_save_admin_bar_config'] ) ) {
	update_option( 'tcp_admin_bar_hidden_items', isset( $_REQUEST['tcp_admin_bar_hidden_items'] ) ? $_REQUEST['tcp_admin_bar_hidden_items'] : array() );
}
$tcp_admin_bar_hidden_items = get_option( 'tcp_admin_bar_hidden_items', array() );

require_once( ABSPATH . WPINC . '/class-wp-admin-bar.php' );
$admin_bar_class = apply_filters( 'wp_admin_bar_class', 'WP_Admin_Bar' );
if ( class_exists( $admin_bar_class ) ) {
	$wp_admin_bar = new $admin_bar_class;
	$wp_admin_bar->initialize();
	$wp_admin_bar->add_menus();
	do_action( 'admin_bar_menu', $wp_admin_bar );
	$menu_bar = $wp_admin_bar->menu;

	foreach( $menu_bar as $id => $menu ) : ?>
	<tr valign="top">
	<th scope="row"><label for="<?php echo $id;?>"><?php echo $menu['title'];?>:</label></th>
	<td>
		<input type="checkbox" id="<?php echo $id;?>" name="tcp_admin_bar_hidden_items[<?php echo $id;?>]" <?php checked( isset( $tcp_admin_bar_hidden_items[$id] ), true );?> />
	</tr><?php
		foreach( $menu as $id => $menu_item )
			if ( $id == 'children' )
				foreach( $menu_item as $id => $item ) : ?>
	<tr valign="top">
	<th scope="row"><label for="<?php echo $id;?>" style="padding-left:5em;"><?php echo $item['title'];?>:</label></th>
	<td>
		<input type="checkbox" id="<?php echo $id;?>" name="tcp_admin_bar_hidden_items[<?php echo $id;?>]" <?php checked( isset( $tcp_admin_bar_hidden_items[$id] ), true );?>/>
	</tr>
				<?php endforeach;
	endforeach;
}
?>
</table>
<p class="submit"><input type="submit" name="tcp_save_admin_bar_config" class="button-primary" value="<?php _e( 'Save Changes', 'tcp'); ?>"/></p>
</form>
</div><!-- .wrap -->
