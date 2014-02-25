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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCheckoutEditor' ) ) :

require_once( TCP_CHECKOUT_FOLDER .'TCPCheckoutManager.class.php' );

class TCPCheckoutEditor {

	function __construct() {
		add_action( 'admin_init'		, array( $this, 'admin_init' ) );
		add_action( 'tcp_admin_menu'	, array( $this, 'tcp_admin_menu' ) );
	}

	function admin_init() {
		add_action( 'wp_ajax_tcp_checkout_steps_save', array( $this, 'tcp_checkout_steps_save' ) );
	}

	function tcp_checkout_steps_save() {
		$steps = $_REQUEST['list'];
		$steps = explode( ',', $steps );
		TCPCheckoutManager::update_steps( $steps );
	}

	function tcp_admin_menu( $thecartpress ) {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = $thecartpress->get_base_settings();
		$page = add_submenu_page( $base, __( 'Checkout Editor', 'tcp' ), __( 'Checkout Editor', 'tcp' ), 'tcp_edit_settings', 'checkout_editor_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'Customize your Checkout Steps.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
		//wp_enqueue_script('custom-background');
		//wp_enqueue_style('farbtastic');
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		if ( isset( $_REQUEST['tcp_save_fields'] ) ) {
			$partial_path = $_REQUEST['tcp_box_path'];
			$class_name = $_REQUEST['tcp_box_name'];
			$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';
			require_once( $initial_path . $partial_path );
			$box = new $class_name();
			$box->save_config_settings();
			$this->updated = true;
		} elseif ( isset( $_REQUEST['tcp_restore_default'] ) ) {
			TCPCheckoutManager::restore_default();
			$this->updated = true;
		}
	}

	function admin_page() { ?>
<div class="wrap">
<?php screen_icon( 'tcp-checkout' ); ?><h2><?php _e( 'Checkout Editor', 'tcp' ); ?></h2>
<p><?php _e( 'This screen allows to change the look of the Checkout. Click in the little triangle at the right of each box to open more options.', 'tcp' ); ?></p>
<ul class="subsubsub"></ul>

<form method="post">
	<input type="submit" name="tcp_restore_default" value="<?php _e( 'Restore default values', 'tcp' ); ?>" class="button-secondary" />
</form>

<div class="clear"></div>
	<?php global $tcp_checkout_boxes;
	$order_steps = TCPCheckoutManager::get_steps(); ?>

	<h3><?php _e( 'Activated boxes', 'tcp' ); ?> <img src="images/loading.gif" class="tcp_checkout_editor_feedback" style="display:none;"/></h3>
	<ul class="tcp_activated_boxes">
	<?php if ( count( $order_steps ) > 0 ) :
		foreach( $order_steps as $class_name ) :
			if ( isset( $tcp_checkout_boxes[$class_name] ) ) : $partial_path = $tcp_checkout_boxes[$class_name];
	if ( is_array( $partial_path ) ) $partial_path = $partial_path['path']; ?>
		<li class="tcp_checkout_step tcp_checkout_step_<?php echo $class_name; ?>" target="<?php echo $class_name; ?>">
			<h4><?php echo $class_name; ?></h4>
			<a href="#open" target="<?php echo $class_name; ?>" class="tcp_checkout_step_open" title="<?php _e( 'Show setup panel', 'tcp' ); ?>"><?php _e( 'open', 'tcp'); ?></a>
			<div id="tcp_checkout_box_edit_<?php echo $class_name; ?>" class="tcp_checkout_box_edit" style="display: none;">
			<form method="post">
				<?php 
				$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';
				require_once( $initial_path . $partial_path );
				$box = new $class_name(); ?>
				<?php if ( $box->show_config_settings() ) : ?>

					<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path; ?>" />
					<input type="hidden" name="tcp_box_name" value="<?php echo $class_name; ?>" />

					<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name; ?>" value="<?php _e( 'save', 'tcp' ); ?>" class="button-primary"/></p>
				<?php endif; ?>
			</form>
			</div>
		</li>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</ul>

	<p class="description"><?php _e( 'Drag and drop to reorder', 'tcp' ); ?></p>

	<?php $order_steps = TCPCheckoutManager::get_steps();
	foreach( $order_steps as $class_name ) {
		if ( isset( $tcp_checkout_boxes[$class_name] ) ) unset( $tcp_checkout_boxes[$class_name] );    
	}
	//$order_steps = array_diff( $tcp_checkout_boxes, $order_steps ); ?>
	<h3><?php _e( 'Deactivated boxes', 'tcp' ); ?></h3>
	<ul class="tcp_deactivated_boxes">
	<?php if ( count( $order_steps ) > 0 ) :
		foreach( $tcp_checkout_boxes as $class_name => $partial_path ) : ?>
		<li class="tcp_checkout_step tcp_checkout_step_<?php echo $class_name; ?>" target="<?php echo $class_name; ?>">
			<h4><?php echo $class_name; ?></h4>
			<a href="#open" target="<?php echo $class_name; ?>" class="tcp_checkout_step_open"><?php _e( 'open', 'tcp'); ?></a>
			<div id="tcp_checkout_box_edit_<?php echo $class_name; ?>" class="tcp_checkout_box_edit" style="display: none;">
			<form method="post">
				<?php if ( file_exists( $initial_path . $partial_path['path'] ) ) :
					require_once( $initial_path . $partial_path['path'] );
					$box = new $class_name(); ?>
					<?php if ( $box->show_config_settings() ) : ?>
						<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path['path']; ?>" />
						<input type="hidden" name="tcp_box_name" value="<?php echo $class_name; ?>" />
						<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name; ?>" value="<?php _e( 'save', 'tcp' ); ?>" class="button-primary"/></p>
					<?php endif; ?>
				<?php endif; ?>
			</form>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	</ul>

	<p class="description"><?php _e( 'Drag and drop to Activate Box area, to add steps to the checkout.', 'tcp' ); ?></p>
	<p class="description"><?php _e( 'If you drag and drop in the deactivated area, the step will be deleted from the checkout.', 'tcp' ); ?></p>

</div><!-- wrap -->
<script>
jQuery(document).ready(function() {
	jQuery('a.tcp_checkout_step_open').each( function() {
		var target = jQuery(this).attr('target');
		jQuery(this).click( function() {
			jQuery('div#tcp_checkout_box_edit_' + target).toggle();
			return false;
		});
	});
	jQuery('ul.tcp_activated_boxes').sortable({
		stop		: function(event, ui) { do_drop(); },
		connectWith	: 'ul.tcp_deactivated_boxes',
		placeholder	: 'tcp_checkout_placeholder',
	});
	jQuery('ul.tcp_deactivated_boxes').sortable({
		stop: function(event, ui) { do_drop(); },
		connectWith	: 'ul.tcp_activated_boxes',
		placeholder	: 'tcp_checkout_placeholder',
	});
});

function do_drop() {
	var lis = [];
	jQuery('ul.tcp_activated_boxes li.tcp_checkout_step').each(function() {
		lis.push(jQuery(this).attr('target'));
	});
	var li_string = lis.join();
	jQuery('.tcp_checkout_editor_feedback').show();
	jQuery.ajax({
		async	: true,
		type    : "GET",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: {
			action	: 'tcp_checkout_steps_save',
			list	: li_string,
		},
		success : function(response) {
			jQuery('.tcp_checkout_editor_feedback').hide();
		},
		error	: function(response) {
			jQuery('.tcp_checkout_editor_feedback').hide();
		},
	});
}
</script>
<?php }
}

new TCPCheckoutEditor();
endif; // class_exists check