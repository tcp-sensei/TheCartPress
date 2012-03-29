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

require_once( dirname( dirname( __FILE__ ) ).'/daos/Countries.class.php' );
require_once( dirname( dirname( __FILE__ ) ).'/daos/Taxes.class.php' );
require_once( dirname( dirname( __FILE__ ) ).'/daos/TaxRates.class.php' );
add_action( 'admin_footer', 'tcp_states_footer_scripts' );

?><h2><?php _e( 'Taxes Rates', 'tcp' );?></h2><?php
if ( isset( $_REQUEST['tcp_add_tax_rate'] ) ) {
	$_REQUEST['country_iso']	= isset( $_REQUEST['country_iso'] ) ? $_REQUEST['country_iso'] : 'all';
	$_REQUEST['region_id']		= isset( $_REQUEST['region_id'] ) ? $_REQUEST['region_id'] : 'all';
	$_REQUEST['region']			= isset( $_REQUEST['region'] ) ? $_REQUEST['region'] : '';
	$_REQUEST['post_code']		= isset( $_REQUEST['post_code'] ) ? $_REQUEST['post_code'] : 'all';
	$_REQUEST['tax_id']			= isset( $_REQUEST['tax_id'] ) ? $_REQUEST['tax_id'] : '-1';
	$_REQUEST['rate']			= isset( $_REQUEST['rate'] ) ? tcp_input_number($_REQUEST['rate']) : 0;
	$_REQUEST['label']			= isset( $_REQUEST['label'] ) ? $_REQUEST['label'] : '';
	if ( $_REQUEST['post_code'] == '' ) $_REQUEST['post_code'] = 'all';
	TaxRates::insert( $_REQUEST );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax rate inserted', 'tcp' );?>
		</p></div><?php
} elseif ( isset( $_REQUEST['tcp_delete_tax_rate'] ) ) {
	if ( isset( $_REQUEST['tax_rate_id'] ) ) {
		$tax_rate_id  =  $_REQUEST['tax_rate_id'];
		TaxRates::delete( $tax_rate_id );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax rate deleted', 'tcp' );?>
		</p></div><?php
	}
}
?>
<ul class="subsubsub">
</ul>
<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Country', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Region', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Post Code', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Tax', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Rate', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Country', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Region', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Post Code', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Tax', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Rate', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column">&nbsp;</th>
</tfoot>
<tbody>
<?php
	$language_iso = tcp_get_admin_language_iso();
	$taxRates = TaxRates::getAll();
	if ( is_array( $taxRates ) && count( $taxRates ) > 0 )
		foreach( $taxRates as $taxRate ) : ?>
<tr>
	<td><?php $country = Countries::get( $taxRate->country_iso, $language_iso ); echo isset( $country->name ) ? $country->name : __( 'All', 'tcp' );?></td>
	<td><?php if ( $taxRate->region_id == 'all' ) _e( 'All', 'tcp' ); else echo $taxRate->region;?></td>
	<td><?php if ( $taxRate->post_code == 'all' ) _e( 'All', 'tcp' ); else echo $taxRate->post_code;?></td>
	<td><?php $tax = Taxes::get( $taxRate->tax_id ); echo isset( $tax->title ) ? $tax->title : __( 'All', 'tcp' );?></td>
	<td><?php echo tcp_number_format( $taxRate->rate, 3 );?></td>
	<td><?php echo $taxRate->label;?>&nbsp;</td>
	<td>
	<a href="#" onclick="jQuery('.delete_tax_rate').hide();jQuery('#delete_<?php echo $taxRate->tax_rate_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a>
	<div id="delete_<?php echo $taxRate->tax_rate_id;?>" class="delete_tax_rate" style="display:none; width: 75%;border: 1px dotted orange; padding: 2px">
		<form method="post">
			<input type="hidden" name="tax_rate_id" value="<?php echo $taxRate->tax_rate_id;?>" />
			<p><?php _e( 'Do you really want to delete the tax rate?', 'tcp' );?></p>
			<input type="submit" name="tcp_delete_tax_rate" value="<?php _e( 'Yes', 'tcp' );?>" class="button-secondary"/> |
			<a href="#" onclick="jQuery('#delete_<?php echo $taxRate->tax_rate_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
		</form>
	</div>
	</td>
</tr>
		<?php endforeach;?>
<tr>
	<form method="post">
	<td>
		<select id="country_id" name="country_iso">
			<option value="all" ><?php _e( 'all', 'tcp' );?></option><?php
				$countries = Countries::getAll( $language_iso );
			global $thecartpress;
			$country_id = $thecartpress->settings['country'];
			foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $country_id )?>><?php echo $item->name;?></option>
			<?php endforeach;?>
		</select>
	</td>
	<td>
	<?php $regions = array(); //array( 'id' => array( 'name', ), 'id' => array( 'name', ), ... )
	$regions = apply_filters( 'tcp_tax_rates_load_regions', $regions );?>
		<select id="region_id" name="region_id">
			<option value="all" ><?php _e( 'all', 'tcp' );?></option>
		<?php if ( is_array( $regions ) && count( $regions ) > 0 ) : ?>
			<?php foreach( $regions as $id => $region ) : ?>
			<option value="<?php echo $id;?>">><?php echo $region['name'];?></option>
			<?php endforeach;?>
		<?php endif;?>
		</select>
		<input type="hidden" id="region" name="region" value=""/>
	</td>
	<td>
		<input type="text" id="post_code" name="post_code" value="" size="10" maxlength="255" />
	</td>
	<td>
		<select name="tax_id">
		<?php $taxes = Taxes::getAll( );
		if ( is_array( $taxes ) && count( $taxes ) > 0 ) : ?>
			<option value="-1" ><?php _e( 'all', 'tcp' );?></option>
			<?php foreach ( $taxes as $tax ) : ?>
			<option value="<?php echo $tax->tax_id;?>"><?php echo $tax->title;?></option>
			<?php endforeach;?>
		<?php endif;?>
		</select>
	</td>
	<td>
		<input type="text" id="rate" name="rate" value="" size="8" maxlength="15" />%
	</td>
	<td>
		<input type="text" id="label" name="label" value="" size="10" maxlength="255" />
	</td>
	<td>
		<input type="submit" name="tcp_add_tax_rate" value="<?php _e( 'add', 'tcp' );?>" class="button-secondary" />
	</td>
	</form>
</tr>
</tbody>
</table>
