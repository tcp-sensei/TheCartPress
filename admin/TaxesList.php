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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );

$tax_id = isset( $_REQUEST['tax_id'] ) ? $_REQUEST['tax_id'] : 0;
if ( isset( $_REQUEST['tcp_edit_tax'] ) ) {
	$error_tax = array();
	if ( ! isset( $_REQUEST['title'] ) && strlen( trim( $_REQUEST ['title'] ) ) == 0 )
		$error_tax['title'][] = __( 'The "title" field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['desc'] ) && strlen( trim( $_REQUEST ['desc'] ) ) == 0 )  //&& is_numeric( $_REQUEST ['tax'] ) )
		$error_tax['desc'][] = __( 'The "desc" field must be completed', 'tcp' );
	if ( count( $error_tax ) == 0 ) {
		Taxes::save( $_REQUEST );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax saved', 'tcp' );?>
		</p></div><?php		
	}
} elseif ( isset( $_REQUEST['tcp_delete_tax'] ) ) {
	if ( $tax_id > 0 )
		if ( $tax_id > 0 ) {
			$old_tax = Taxes::get( $tax_id );
			Taxes::delete( $tax_id );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax deleted', 'tcp' );?>
		</p></div><?php
		}
}
$taxes = Taxes::getAll();
?>
<div class="wrap">
<h2><?php _e( 'List of taxes', 'tcp' );?></h2>
<ul class="subsubsub">
</ul>
<div class="clear"></div>

<?php if ( isset( $error_tax ) && count( $error_tax ) > 0 ) : ?>
<p class="error">
	<?php foreach( $error_tax as $error ) :?>
		<span class="description"><?php echo $error[0];?></span><br />
	<?php endforeach;?>
</p>
<?php endif;?>

<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Id.', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 50%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Id.', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 50%;">&nbsp;</th>
</tfoot>
<tbody>
	<tr>
		<td colspan="3">
			<a href="#" onclick="jQuery('.edit_tax').hide();jQuery('.delete_tax').hide();jQuery('#edit_tax_0').show();"><?php _e( 'create new tax', 'tcp' );?></a>
			<div id="edit_tax_0" class="edit_tax" style="display:none; width: 75%;border: 1px dotted orange; padding: 2px">
				<form method="post" name="frm_edit_0">
				<input type="hidden" name="tax_id" value="0" />
				<input type="hidden" name="tcp_edit_tax" value="y" />
				<h3><?php _e( 'New tax', 'tcp' );?></h3>
				<p>
					<label for="title"><?php _e( 'Title', 'tcp' );?>:</label>
					<input type="text" id="title" name="title" size="40" maxlength="100" value=""/>
				</p><p>
					<label for="tax"><?php _e( 'Description', 'tcp' );?>:</label>
					<input type="text" id="desc" name="desc" size="50" maxlength="255" value=""/>
				</p>
				<p>
				<input name="tcp_edit_tax" value="<?php _e( 'Save', 'tcp' );?>" type="submit" class="button-secondary" />
				&nbsp;<a href="#" onclick="jQuery('#edit_tax_0').hide();"><?php _e( 'Cancel' , 'tcp' );?></a>
				</p>
				</form>
			</div>		
		</td>
		<td>&nbsp;</td>
	</tr>
<?php if ( count( $taxes ) == 0 ) :?>
	<tr><td colspan="4"><?php _e( 'The list of taxes is empty', 'tcp' );?></td></tr>
<?php else :?>
	 <?php foreach( $taxes as $tax ) :?> 
	<tr>
		<td><?php echo $tax->tax_id;?></td>
		<td><?php echo $tax->title;?></td>
		<td><?php echo $tax->desc;?></td>
		<td style="width: 20%;">
		<div><a href="#" onclick="jQuery('.edit_tax').hide();jQuery('.delete_tax').hide();jQuery('#edit_tax_<?php echo $tax->tax_id;?>').show();" class="edit"><?php _e( 'edit', 'tcp' );?></a>
		 | <a href="#" onclick="jQuery('.delete_tax').hide();jQuery('.edit_tax').hide();jQuery('#delete_tax_<?php echo $tax->tax_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		
		<div id="edit_tax_<?php echo $tax->tax_id;?>" class="edit_tax" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_edit_<?php echo $tax->tax_id;?>">
			<input type="hidden" name="tax_id" value="<?php echo $tax->tax_id;?>" />
			<input type="hidden" name="tcp_edit_tax" value="y" />
			<p>
				<label for="title"><?php _e( 'Title', 'tcp' );?>:</label>
				<input type="text" id="title" name="title" size="40" maxlength="100" value="<?php echo $tax->title;?>"/>
			</p><p>
				<label for="desc"><?php _e( 'Description', 'tcp' );?>:</label>
				<input type="text" id="desc" name="desc" size="40" maxlength="255" value="<?php echo $tax->desc;?>"/>
			</p>
			<p>
			<input name="tcp_edit_tax" value="<?php _e( 'Save', 'tcp' );?>" type="submit" class="button-secondary" />
			&nbsp;<a href="#" onclick="jQuery('#edit_tax_<?php echo $tax->tax_id;?>').hide();"><?php _e( 'Cancel' , 'tcp' );?></a>
			</p>
			</form>
		</div>		

		<div id="delete_tax_<?php echo $tax->tax_id;?>" class="delete_tax" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_tax_<?php echo $tax->tax_id;?>">
			<input type="hidden" name="tax_id" value="<?php echo $tax->tax_id;?>" />
			<input type="hidden" name="tcp_delete_tax" value="y" />
			<p><?php _e( 'Do you really want to delete this tax?', 'tcp' );?></p>
			<a href="javascript:document.frm_delete_tax_<?php echo $tax->tax_id;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
			<a href="#" onclick="jQuery('#delete_tax_<?php echo $tax->tax_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
		</div>
		</td>
	</tr>
	<?php endforeach;
endif;?>
</tbody>
</table>

<?php include( dirname( __FILE__ ) . '/TaxesRates.php' ); ?>

</div> <!-- end wrap -->
