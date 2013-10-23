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

if ( isset( $_REQUEST['tcp_save_templates'] ) ) {
	$tcp_post_types = $_REQUEST['tcp_post_types'];
	$product_types = tcp_get_product_types();
	if ( is_array( $product_types ) && count( $product_types ) > 0 ) {
		foreach( $tcp_post_types as $i => $tcp_post_type ) {
			foreach( $product_types as $product_type => $product_type_def ) {
				$tcp_buy_button_template = $_REQUEST['tcp_buy_button_templates-' . $tcp_post_type . '-' . $product_type ][$i];
				if ( $tcp_buy_button_template == '' ) delete_option( 'tcp_buy_button_template-' .  $tcp_post_type . '-' . $product_type );
				else update_option( 'tcp_buy_button_template-' .  $tcp_post_type . '-' . $product_type, $tcp_buy_button_template );
			}
		}
		?><div id="message" class="updated"><p>
		<?php _e( 'Templates updated', 'tcp' );?>
		</p></div><?php
	}
}
?>
<div class="wrap">

<?php screen_icon( 'tcp-buybuttons-templates' ); ?><h2><?php _e( 'Buy Button Templates', 'tcp' ); ?></h2>
<p class="description"><?php _e( 'Select which template to use to display buy buttons for different product types.', 'tcp' ); ?></p>

<form method="post">

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Templates', 'tcp' ); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Templates', 'tcp' ); ?></th>
</tr>
</tfoot>

<tbody>

<?php $post_type_defs = tcp_get_custom_post_types();
$product_types = tcp_get_product_types();
$buy_buttons = TCPBuyButton::get_buy_buttons();
if ( is_array( $post_type_defs ) && count( $post_type_defs ) > 0 ) :
	foreach( $post_type_defs as $post_type => $post_type_def ) : 
		if ( $post_type != 'tcp_dynamic_options' && $post_type_def['is_saleable'] ) : ?>

<tr>
	<td><?php echo $post_type_def['name']; ?><?php if ( strlen( $post_type_def['desc'] ) > 0 ) : ?> (<?php echo $post_type_def['desc']; ?>) <?php endif; ?><input type="hidden" name="tcp_post_types[]" value="<?php echo $post_type; ?>" /></td>
	<td>&nbsp;</td>
</tr>
			<?php foreach( $product_types as $product_type  => $product_type_def ) : ?>
<tr>
	<td style="padding-left: 2em;"><?php echo $product_type_def['label']; ?></td>
	<td>
		<label><?php _e( 'Templates', 'tcp' ); ?>: <br>		

		<?php $selected_buy_button = get_option( 'tcp_buy_button_template-' .  $post_type . '-' . $product_type, '' ); ?>

		<select name="tcp_buy_button_templates-<?php echo $post_type; ?>-<?php echo $product_type; ?>[]">
			<option value="" <?php selected( '', $selected_buy_button ); ?>><?php _e( 'Default', 'tcp' ); ?></option>

			<?php foreach( $buy_buttons as $buy_button ) : ?>

			<option value="<?php echo $buy_button['path']; ?>" <?php selected( $buy_button['path'], $selected_buy_button ); ?>>
				<?php echo $buy_button['label']; ?>
			</option>

			<?php endforeach; ?>

		</select>
		</label>
	</td>
</tr>

			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else : ?>

<tr>
	<td colspan="3"><?php _e( 'The list is empty', 'tcp' ); ?></td>
</tr>

<?php endif; ?>

</tbody>
</table>

<p><input name="tcp_save_templates" id="tcp_save_templates" value="<?php _e( 'Save', 'tcp' );?>" type="submit" class="button-primary" /></p>

</form>
</div>