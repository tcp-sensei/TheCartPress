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

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );

$rel_type = isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : 'CAT_POST-CAT_PROD';

if ( isset( $_REQUEST['tcp_save_related_categories'] ) ) {
	$terms_1_id = $_REQUEST['tcp_term_1_id'];
	foreach( $terms_1_id as $term_1_id ) {
		RelEntities::deleteAll( $term_1_id, $rel_type );
		$id = 'tcp_term_2_id_' . $term_1_id;
		if ( isset( $_REQUEST[$id] ) )
			foreach( $_REQUEST[$id] as $term_2_id )
				if ( $term_2_id > 0) {
					RelEntities::insert( $term_1_id, $term_2_id,  $rel_type );?>
					<div id="message" class="updated"><p>
						<?php _e( 'Relations created', 'tcp' );?>
					</p></div><?php
				}
	}
} ?>
<div class="wrap">
<form method="post">
	<input type="hidden" name="rel_type" value="<?php echo $rel_type;?>" />
	<?php screen_icon( 'tcp-related-cats' ); ?><h2><?php _e( 'Related categories', 'tcp' );?></h2>
	<ul class="subsubsub">
	<?php if ( $rel_type == 'CAT_POST-CAT_PROD' ) : ?>
		<li class="current"><strong><?php _e( 'Cat. of Posts &raquo; Cat. of Products', 'tcp' );?></strong></li>
	<?php else : ?>
		<li><a href="<?php echo TCP_ADMIN_PATH;?>RelatedCats.php&rel_type=CAT_POST-CAT_PROD"><?php _e( 'Cat. of Posts &raquo; Cat. of Products', 'tcp' );?></a></li>
	<?php endif;?>
		<li>|</li>
	<?php if ( $rel_type == 'CAT_POST-CAT_POST' ) : ?>
		<li class="current"><strong><?php _e( 'Cat. of Posts &raquo; Cat. of Posts', 'tcp' );?></strong></li>
	<?php else : ?>
		<li><a href="<?php echo TCP_ADMIN_PATH;?>RelatedCats.php&rel_type=CAT_POST-CAT_POST"><?php _e( 'Cat. of Posts &raquo; Cat. of Posts', 'tcp' );?></a></li>
	<?php endif;?>
		<li>|</li>
	<?php if ( $rel_type == 'CAT_PROD-CAT_POST' ) : ?>
		<li class="current"><strong><?php _e( 'Cat. of Products &raquo; Cat. of Posts', 'tcp' );?></strong></li>
	<?php else : ?>
		<li><a href="<?php echo TCP_ADMIN_PATH;?>RelatedCats.php&rel_type=CAT_PROD-CAT_POST"><?php _e( 'Cat. of Products &raquo; Cat. of Posts', 'tcp' );?></a></li>
	<?php endif;?>
		<li>|</li>
	<?php if ( $rel_type == 'CAT_PROD-CAT_PROD' ) : ?>
		<li class="current"><strong><?php _e( 'Cat. of Products &raquo; Cat. of Products', 'tcp' );?></strong></li>
	<?php else : ?>
		<li><a href="<?php echo TCP_ADMIN_PATH;?>RelatedCats.php&rel_type=CAT_PROD-CAT_PROD"><?php _e( 'Cat. of Products &raquo; Cat. of Products', 'tcp' );?></a></li>
	<?php endif;?>
	</ul>
	<div class="clear"></div>

	<p><input type="submit" value="<?php _e( 'Save', 'tcp' );?>" class="button-primary" name="tcp_save_related_categories" /></p>
	<table class="widefat fixed" cellspacing="0"><!-- Assigned -->
		<thead>
		<tr>
			<th scope="col" class="manage-column"><?php _e( 'Categories', 'tcp' );?></th>
			<th scope="col" class="manage-column"><?php _e( 'Related categories', 'tcp' );?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th scope="col" class="manage-column"><?php _e( 'Categories', 'tcp' );?></th>
			<th scope="col" class="manage-column"><?php _e( 'Related categories', 'tcp' );?></th>
		</tr>
		</tfoot>
		<tbody>
		<?php
		if ( $rel_type == 'CAT_POST-CAT_PROD' || $rel_type == 'CAT_POST-CAT_POST') {
			$taxonomy = 'category';
			if ( $rel_type == 'CAT_POST-CAT_PROD' ) {
				$second_taxonomy = 'tcp_product_category';
			} else { //CAT_POST-CAT_POST
				$second_taxonomy = 'category';
			}
		} elseif ( $rel_type == 'CAT_PROD-CAT_PROD' || $rel_type == 'CAT_PROD-CAT_POST' ) {
			$taxonomy = 'tcp_product_category';
			if ( $rel_type == 'CAT_PROD-CAT_PROD' ) {
				$second_taxonomy = 'tcp_product_category';
			} else { //CAT_PROD-CAT_POST
				$second_taxonomy = 'category';
			}
		}
		$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
		$second_terms = get_terms( $second_taxonomy, array( 'hide_empty' => false ) );
		foreach( $terms as $term ) :
			$term_id = tcp_get_default_id( $term->term_id, $taxonomy );
			if ( $term_id != $term->term_id ) continue; ?>
		<tr>
			<td><?php echo $term->name;?><input type="hidden" name="tcp_term_1_id[]" value="<?php echo $term->term_id;?>" /></td>
			<td>
				<select name="tcp_term_2_id_<?php echo $term->term_id;?>[]" multiple size="8" style="height: auto;">
					<option value=""><?php _e( 'no one', 'tcp' );?></option>
				<?php
				$res = RelEntities::select( $term->term_id, $rel_type );
				$ids = array();
				if ( $res ) foreach( $res as $row ) $ids[] = $row->id_to;
				foreach( $second_terms as $second_term ) : 
					$term_id = tcp_get_default_id( $second_term->term_id, $second_taxonomy );
					if ( $term_id != $second_term->term_id ) continue; ?>
					<option value="<?php echo $second_term->term_id;?>" <?php tcp_selected_multiple( $ids, $second_term->term_id );?>><?php echo $second_term->name;?></option>
				<?php endforeach;?>
				</select>
			</td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<p><input type="submit" value="<?php _e( 'Save', 'tcp' );?>" class="button-primary" name="tcp_save_related_categories" /></p>
</form>
</div>
