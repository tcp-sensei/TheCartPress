<?php
/**
 * Custom Forms
 *
 * Allows to create forms, using an array of fields
 *
 * @package TheCartPress
 * @subpackage Classes
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCustomForms' ) ) {

class TCPCustomForms {

	/**
	 * Outputs the form using the Checkout format
	 *
	 * @param Array $fields format fields
	 * @param TCPCheckoutBox $checkout the current box
	 * @since 1.3.2
	 * @uses TCPCustomForms::showCheckoutField
	 */
	static function showCheckout( $fields, $checkout, $sorting = false ) {
		if ( is_array( $fields ) and count( $fields ) > 0 ) {
			if ( is_array( $sorting ) and count( $sorting ) > 0 ) { 
				foreach( $sorting as $id ) {
					if ( isset( $fields[$id] ) ) {
						TCPCustomForms::showCheckoutField( $id, $fields[$id], $checkout );
						unset( $fields[$id] );
					}
				}
			}
			if ( count( $fields ) > 0 ) {
				foreach( $fields as $id => $field ) {
					TCPCustomForms::showCheckoutField( $id, $field, $checkout );
				}
			}
		}
	}

	/**
	 * Outputs one field using the Checkout format
	 *
	 * @param String $id Identifier of the field
	 * @param Array $field
	 * @param TCPCheckoutBox $checkout the current box
	 * @since 1.3.2
	 * @uses TCPCustomForms::getAttr
	 * @see TCPCustomForms::showCheckout
	 */
	static function showCheckoutField( $id, $field, $checkout ) {
		$active = isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return; ?>
		<li>
		<?php $callback = isset( $field['callback'] ) ? $field['callback'] : false;
		if ( $callback !== false ) {
			call_user_func( $callback, $id, $field );
		} else {
			$name		= isset( $field['name'] ) ? $field['name'] : $id;
			$required	= isset( $field['required'] ) ? $field['required'] : false;
			$input		= isset( $field['input'] ) ? $field['input'] : 'text';
			$value		= isset( $field['value'] ) ? $field['value'] : '';
			$attrs		= TCPCustomForms::getAttrs( $field ); ?>
			<label for="<?php echo $id; ?>"><?php echo $field['label']; ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
			<input type="<?php echo $input; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value;?>" <?php echo $attrs; ?> />
			<?php $checkout->showErrorMsg( $id );
		} ?>
		</li>
	<?php }

	static function show( $fields )	{
		if ( is_array( $fields ) and count( $fields ) > 0 ) { ?>
			<table class="form-table">
			<?php foreach( $fields as $id => $field ) { ?>

			<?php } ?>
			</table>
		<?php }
	}

	static function showBS( $fields )	{
		foreach( $fields as $id => $field ) { ?>
<div class="control-group" tcp-field-<?php echo $id; ?>">
<?php if ( isset( $field['label'] ) ) { ?>
	<label class="control-label" for="<?php echo $id; ?>"><?php echo $field['label']; ?>
		<?php if ( isset( $field['required'] ) && $field['required'] ) { ?>
			<span class="tcp-required">(*)</span>
		<?php } ?> </label>
<?php } ?>

<?php if ( $field['input'] == 'select' ) { ?>
<select name="<?php echo isset( $field['name'] ) ? $field['name'] : $id; ?>" id="<?php echo $id; ?>">
</select>
<?php } elseif ( $field['input'] == 'textarea' ) { ?>
<textarea name="<?php echo isset( $field['name'] ) ? $field['name'] : $id; ?>" id="<?php echo $id; ?>">
</textarea>
<?php } else { ?>
	<div class="controls">
		<input name="<?php echo isset( $field['name'] ) ? $field['name'] : $id; ?>" id="<?php echo $id; ?>"
		<?php foreach( $field['attrs'] as $key => $value ) echo $key, '="', $value, '" '; ?>
		/>
	</div><!-- .controls -->
	<?php if ( isset( $field['description'] ) ) { ?>
	<p class="help-block"><?php echo $field['description']; ?></p>
	<?php } ?>
<?php } ?>
</div><!-- .control-group -->
		<?php }
	}

	static function checkFields( $fields ) {
		$errors = array();
		foreach( $fields as $field ) {
			if ( isset( $field['required'] ) && $field['required'] ) {
				if ( ! isset( $_REQUEST[$field['name']] )  or trim( $_REQUEST[$field['name']] ) == '' ) {
					$errors[$field['name']] = sprintf( __( 'Field %s is required', 'tcp' ), $field['label'] );
				}
			}
		}
	}

	static function getAttrs( $field ) {
		$attrs = array();
		if ( isset( $field['attrs'] ) && is_array( $field['attrs'] ) ) {
			foreach( $field['attrs'] as $key => $value ) {
				$attrs[] = $key . ' = ' . $value;
			}
		}
		return implode( ' ', $attrs );
	}
}
} // class_exists check