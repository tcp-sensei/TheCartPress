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

require_once( TCP_CHECKOUT_FOLDER .'TCPCheckoutManager.class.php' );

$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';

if ( isset( $_REQUEST['tcp_save_fields'] ) ) {
	$partial_path = $_REQUEST['tcp_box_path'];
	$class_name = $_REQUEST['tcp_box_name'];
	require_once( $initial_path . $partial_path );
	$box = new $class_name();
	$box->save_config_settings();?>
	<div id="message" class="updated"><p>
		<?php printf( __( 'Data for %s saved', 'tcp' ), $class_name );?>
	</p></div><?php
} elseif ( isset( $_REQUEST['tcp_down'] ) ) {
	$tcp_box_name = $_REQUEST['tcp_box_name'];
	$order_steps = TCPCheckoutManager::get_steps();
	$order = 0;
	foreach( $order_steps as $i => $class_name ) {
		if ( $tcp_box_name == $class_name ) {
			$order = $i;
			break;
		}
	}
	$order_steps[$order] = $order_steps[$order + 1];
	$order_steps[$order + 1] = $class_name;
	TCPCheckoutManager::update_steps( $order_steps );
} elseif ( isset( $_REQUEST['tcp_up'] ) ) {
	$tcp_box_name = $_REQUEST['tcp_box_name'];
	$order_steps = TCPCheckoutManager::get_steps();
	$order = 0;
	foreach( $order_steps as $i => $class_name ) {
		if ( $tcp_box_name == $class_name ) {
			$order = $i;
			break;
		}
	}
	$order_steps[$order] = $order_steps[$order - 1];
	$order_steps[$order - 1] = $class_name;
	TCPCheckoutManager::update_steps( $order_steps );
} elseif ( isset( $_REQUEST['tcp_activate'] ) ) {
	$class_name = $_REQUEST['tcp_box_name'];
	TCPCheckoutManager::add_step( $class_name );
} elseif ( isset( $_REQUEST['tcp_deactivate'] ) ) {
	$class_name = $_REQUEST['tcp_box_name'];
	TCPCheckoutManager::remove_step( $class_name );
} elseif ( isset( $_REQUEST['tcp_restore_default'] ) ) {
	TCPCheckoutManager::restore_default();
}
?>
<div class="wrap">
<h2><?php _e( 'Checkout Editor', 'tcp' );?></h2>
<ul class="subsubsub">
</ul>
<form method="post">
<input type="submit" name="tcp_restore_default" value="<?php _e( 'Restore default values', 'tcp' );?>" class="button-secondary" />
</form>
<div class="clear"></div>

<?php global $tcp_checkout_boxes;
$order_steps = TCPCheckoutManager::get_steps();?>
<h3><?php _e( 'Activated boxes', 'tcp' ); ?></h3>
<ul class="tcp_activated_boxes" style="padding-left:4em;">
<?php $number_of_items = count( $order_steps );
if ( $number_of_items > 0 ) :
	$first_item = true;
	foreach( $order_steps as $class_name ) :
		$partial_path = $tcp_checkout_boxes[$class_name];
		$number_of_items--;?>
		<li style="border-bottom: 1px solid grey;">
		<h4>
		<form method="post">
			<?php if ( $first_item ) :
				$first_item = false;
			else :?>
				<input type="submit" name="tcp_up" value="<?php _e( 'Up', 'tcp' );?>" class="button-secondary" />
			<?php endif;?>
			<?php if ( $number_of_items > 0 ) :?>
			<input type="submit" name="tcp_down" value="<?php _e( 'Down', 'tcp' );?>" class="button-secondary" />
			<?php endif;?>
			<input type="submit" name="tcp_deactivate" value="<?php _e( 'Deactivate', 'tcp' );?>" class="button-secondary" />
			<input type="hidden" name="tcp_box_name" value="<?php echo $class_name;?>" />
			<span style="padding-left:5em;"><?php echo $class_name;?></span>
		</form>
		</h4>
		<input type="button" id="tcp_edit_button_<?php echo $class_name;?>" value="<?php _e( 'Show/Hide edit fields', 'tcp' );?>" class="button-secondary" onclick="jQuery('#tcp_edit_<?php echo $class_name;?>').toggle();"/>
		<p id="tcp_no_edit_<?php echo $class_name;?>" style="display: none;"><?php _e( 'No config settings', 'tcp' );?></p>
		<div id="tcp_edit_<?php echo $class_name;?>" style="display: none;">
			<?php require_once( $initial_path . $partial_path );?>
			<?php $box = new $class_name();?>
			<form method="post">
			<table class="form-table">
			<tbody>
			<?php $exists_config = $box->show_config_settings();?>
			</tbody>
			</table>
			<?php if ( $exists_config ) :?>
				<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path;?>" />
				<input type="hidden" name="tcp_box_name" value="<?php echo $class_name;?>" />
				<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name;?>" value="<?php _e( 'save', 'tcp' );?>" class="button-secondary"/></p>
			<?php else :?>
				<script>
					jQuery('#tcp_edit_button_<?php echo $class_name;?>').hide();
					jQuery('#tcp_no_edit_<?php echo $class_name;?>').show();
				</script>
			<?php endif;?>
			</form>
		</div>
		</li>
	<?php endforeach;?>
<?php endif;?>
</ul>

<?php $order_steps = TCPCheckoutManager::get_steps();
foreach( $order_steps as $class_name ) {
	if ( isset( $tcp_checkout_boxes[$class_name] ) ) unset( $tcp_checkout_boxes[$class_name] );
}

$order_steps = array_diff( $tcp_checkout_boxes, $order_steps );?>
<h3><?php _e( 'Deactivated boxes', 'tcp' ); ?></h3>
<ul class="tcp_deactivated_boxes" style="padding-left:4em;">
<?php if ( count( $order_steps ) > 0 ) :
	foreach( $tcp_checkout_boxes as $class_name => $partial_path ) :?>
		<li style="border-bottom: 1px solid grey;">
		<h4>
		<form method="post">
			<?php echo $class_name;?>
			<input type="submit" name="tcp_activate" value="<?php _e( 'Activate', 'tcp' );?>" class="button-secondary" />
			<input type="hidden" name="tcp_box_name" value="<?php echo $class_name;?>" />
		</form>
		</h4>
		<input type="button" id="tcp_edit_button_<?php echo $class_name;?>" value="<?php _e( 'Show/Hide edit fields', 'tcp' );?>" class="button-secondary" onclick="jQuery('#tcp_edit_<?php echo $class_name;?>').toggle();"/>
		<p id="tcp_no_edit_<?php echo $class_name;?>" style="display: none;"><?php _e( 'No config settings', 'tcp' );?></p>
		<div id="tcp_edit_<?php echo $class_name;?>" style="display: none;">
			<?php require_once( $initial_path . $partial_path );?>
			<?php $box = new $class_name();?>
			<form method="post">
			<table class="form-table">
			<tbody>
			<?php $exists_config = $box->show_config_settings();?>
			</tbody>
			</table>
			<?php if ( $exists_config ) :?>
				<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path;?>" />
				<input type="hidden" name="tcp_box_name" value="<?php echo $class_name;?>" />
				<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name;?>" value="<?php _e( 'save', 'tcp' );?>" class="button-secondary"/></p>
			<?php else :?>
				<script>
					jQuery('#tcp_edit_button_<?php echo $class_name;?>').hide();
					jQuery('#tcp_no_edit_<?php echo $class_name;?>').show();
				</script>
			<?php endif;?>
			</form>
		</div>
		</li>
	<?php endforeach;?>
<?php endif;?>
</ul>
</div><!-- wrap -->
