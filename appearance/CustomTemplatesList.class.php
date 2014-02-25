<?php
/**
 * Custom Templates
 *
 * @package TheCartPress
 * @subpackage Appearance
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

if ( !class_exists( 'TCPCustomTemplatesList' ) ) :

class TCPCustomTemplatesList {

	function __construct() {
		add_action( 'tcp_admin_menu', array( &$this, 'tcp_admin_menu' ), 60 );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_appearance();
		$page = add_submenu_page( $base, __( 'Custom Templates Settings', 'tcp' ), __( 'Custom Templates', 'tcp' ), 'tcp_edit_settings', 'custom_templates_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'  => 'overview',
			'title' => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize default Custom Templates.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-custom-templates' ); ?><h2><?php _e( 'Custom Templates', 'tcp' ); ?></h2>
	<p><?php _e( 'This screen allows to set which templates to use in case to show the different elements of your site.', 'tcp' ); ?></p>
<?php if ( ! empty( $this->updated ) ) : ?>
<div id="message" class="updated">
<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
</div>
<?php endif; ?>

<div class="clear"></div>

<form method="post">
<?php submit_button( null, 'primary', 'save-custom_templates-settings' ); ?>
<table class="widefat fixed" cellspacing="0">
<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name id', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Template', 'tcp' ); ?></th>
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name id', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Template', 'tcp' ); ?></th>
	</tr>
</tfoot>
<tbody>

<?php $templates = tcp_get_custom_templates();
$post_types = get_post_types( array( 'show_in_nav_menus' => true ), object );
foreach( $post_types as $post_type ) : ?>
	<tr class="tcp_<?php echo $post_type->name; ?> alternate">
		<td><?php echo $post_type->labels->name; ?></td>
		<td><?php echo $post_type->name; ?></td>
		<?php $archives = array_merge( tcp_get_custom_files( 'single-' ), $templates );
		$custom_template = tcp_get_custom_template_by_post_type( $post_type->name ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_post_type_template_id[]" value="<?php echo $post_type->name; ?>"/>
			<select name="tcp_custom_post_type_template[]" id="tcp_custom_post_type_template">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
			<?php foreach( $archives as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
			<?php endforeach;
			if ( $custom_template && ! array_key_exists( $custom_template, $archives ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
			<?php endif; ?>
			</select>
		</td>
	</tr>

	<tr class="tcp_<?php echo $post_type->name; ?> ">
		<td style="padding-left: 2em;"><?php _e( 'Archives', 'tcp' ); ?></td>
		<td>&nbsp;</td>
		<?php $archives = array_merge( tcp_get_custom_files( 'archive-' ), $templates );
		$custom_template = tcp_get_custom_archive_by_post_type( $post_type->name ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_post_type_archive_id[]" value="<?php echo $post_type->name; ?>"/>
			<select name="tcp_custom_post_type_archive[]" id="tcp_custom_post_type_archive">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
			<?php foreach( $archives as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
			<?php endforeach;
			if ( $custom_template && ! array_key_exists( $custom_template, $archives ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
			<?php endif; ?>
			</select>
		</td>
	</tr>

	<?php $taxonomies = get_object_taxonomies( $post_type->name, object );
	$archives = array_merge( tcp_get_custom_files( 'taxonomy-' ), $templates );
	foreach( $taxonomies as $taxonomy_object ) : ?>
	<tr class="alternate">
		<td style="padding-left: 2em;"><?php echo $taxonomy_object->labels->name; ?></td>
		<td>&nbsp;</td>
		<?php $custom_template = tcp_get_custom_template_by_taxonomy( $taxonomy_object->name ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_taxonomy_template_id[]" value="<?php echo $taxonomy_object->name; ?>"/>
			<select name="tcp_custom_taxonomy_template[]" id="tcp_custom_taxonomy_template">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
			<?php foreach( $archives as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
			<?php endforeach; 
			if ( $custom_template && ! array_key_exists( $custom_template, $archives ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
			<?php endif; ?>
			</select>
		</td>
	</tr>
		<?php $taxonomy = get_taxonomy( $taxonomy_object->name );
		$args = array (
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		);
		$terms = get_terms( $taxonomy_object->name, 'orderby=name&hide_empty=false');
		foreach( $terms as $term ) : ?>
		<td style="padding-left: 4em;"><?php echo $term->name; ?></td>
		<td><?php echo $term->slug; ?></td>
		<?php $custom_template = tcp_get_custom_template_by_term( $term->term_id ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_term_template_id[]" value="<?php echo $term->term_id; ?>"/>
			<select name="tcp_custom_term_template[]" id="tcp_custom_term_template_<?php echo $term->term_id; ?>">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
			<?php foreach( $archives as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
			<?php endforeach; 
			if ( $custom_template && ! array_key_exists( $custom_template, $archives ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
			<?php endif; ?>
			</select>
		</td>
	</tr>
	<?php endforeach;
	endforeach;
endforeach; ?>
</tbody>
</table>
<?php wp_nonce_field( 'tcp_custom_templates_settings' ); ?>
<?php submit_button( null, 'primary', 'save-custom_templates-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_custom_templates_settings' );	

		if ( isset( $_REQUEST['save-custom_templates-settings'] ) ) {
			$post_types = $_REQUEST['tcp_custom_post_type_template_id'];
			$templates = $_REQUEST['tcp_custom_post_type_template'];
			foreach( $post_types as $id => $post_type ) {
				tcp_set_custom_template_by_post_type( $post_type, $templates[$id] );
			}
			$post_types = $_REQUEST['tcp_custom_post_type_archive_id'];
			$templates = $_REQUEST['tcp_custom_post_type_archive'];
			foreach( $post_types as $id => $post_type ) {
				tcp_set_custom_archive_by_post_type( $post_type, $templates[$id] );
			}
			$taxonomies = $_REQUEST['tcp_custom_taxonomy_template_id'];
			$templates = $_REQUEST['tcp_custom_taxonomy_template'];
			foreach( $taxonomies as $id => $taxonomy ) {
				tcp_set_custom_template_by_taxonomy( $taxonomy, $templates[$id] );
			}
			$terms = $_REQUEST['tcp_custom_term_template_id'];
			$templates = $_REQUEST['tcp_custom_term_template'];
			foreach( $terms as $id => $term_id ) {
				tcp_set_custom_template_by_term( $term_id, $templates[$id] );
			}
			$this->updated = true;
		}
	}
}

new TCPCustomTemplatesList();
endif; // class_exists check