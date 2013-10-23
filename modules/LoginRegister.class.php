<?php
/**
 * Login/Register
 *
 * Adds powerful functionality to Login/Register as:
 *  Lock user until administrator unlock
 *  Select a custom page for Login
 *
 * @package TheCartPress
 * @subpackage Modules
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

if ( ! class_exists( 'TCPLoginRegister' ) ) {

class TCPLoginRegister {

	function __construct() {
		add_action( 'tcp_init'									, array( $this, 'tcp_init' ) );
		add_action( 'admin_init'								, array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_tcp_register_and_login'			, array( $this, 'tcp_register_and_login' ) );
		add_action( 'wp_ajax_nopriv_tcp_register_and_login'		, array( $this, 'tcp_register_and_login' ) );
		add_action( 'wp_ajax_tcp_register_and_login_ajax'		, array( $this, 'tcp_register_and_login_ajax' ) );
		add_action( 'wp_ajax_nopriv_tcp_register_and_login_ajax', array( $this, 'tcp_register_and_login_ajax' ) );
		add_action( 'user_register'								, array( $this, 'user_register' ) );
		add_action( 'tcp_admin_menu'							, array( $this, 'tcp_admin_menus' ) );

		add_filter( 'wp_authenticate_user'						, array( $this, 'wp_authenticate_user' ), 1 );

		add_shortcode( 'tcp_my_account'							, array( $this, 'tcp_my_account' ) );
	}

	function tcp_init() {
		if ( ! is_admin() ) {
			if ( thecartpress()->get_setting( 'my_account_as_login_page', false ) !== false ) {
				add_filter( 'login_url'	, array( $this, 'login_url' ), 10, 2 );
				add_filter( 'logout_url', array( $this, 'logout_url' ), 10, 2 );
			}
		}
	}

	function admin_init() {
		add_action( 'tcp_main_settings_after_page'	, array( $this, 'tcp_main_settings_after_page' ) );
		add_action( 'edit_user_profile'				, array( $this, 'edit_user_profile' ), 10 );
		add_action( 'edit_user_profile_update'		, array( $this, 'edit_user_profile_update' ) );

		add_filter( 'tcp_main_settings_action'		, array( $this, 'tcp_main_settings_action' ) );
		add_filter( 'manage_users_columns'			, array( $this, 'manage_users_columns' ) );
		add_filter( 'manage_users_custom_column'	, array( $this, 'manage_users_custom_column' ), 10, 3 );
	}

	function tcp_main_settings_after_page() {
		global $thecartpress;
		if ( ! $thecartpress ) return;
		$my_account_as_login_page = $thecartpress->get_setting( 'my_account_as_login_page', false ); ?>

<h3><?php _e( 'Custom Login', 'tcp' ); ?></h3>

<div class="postbox">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="my_account_as_login_page"><?php _e( 'Select your login page', 'tcp' ); ?></label>
	</th>
	<td>
		<span class="description"><?php _e( 'Allows to change the default login panel for ', 'tcp' ); ?></span>
		<?php $pages = get_posts( array( 'fields' => 'ids', 'post_type' => 'page', 'numberposts' => -1 ) ); ?>
		<select name="my_account_as_login_page">
			<option value="" ><?php _e( 'Default One', 'tcp' ) ?></option>
		<?php foreach( $pages as $page_id ) : ?>
			<option value="<?php echo $page_id; ?>" <?php selected( $my_account_as_login_page, $page_id ); ?>><?php echo get_the_title( $page_id ); ?></option>
		<?php endforeach; ?>
		</select>
		<p class="description"><?php _e( 'You can use the shortcode [tcp_my_account] in your login page, or any other type of login procedure.', 'tcp' ); ?></p>
		<!--<input type="checkbox" id="my_account_as_login_page" name="my_account_as_login_page" value="yes" <?php checked( $my_account_as_login_page ); ?> />-->
	</td>
</tr>
</tbody>
</table>
</div><!-- .postbox --><?php
	}

	function tcp_main_settings_action( $settings ) {
		//$settings['my_account_as_login_page'] = isset( $_POST['my_account_as_login_page'] );
		$my_account_as_login_page = isset( $_POST['my_account_as_login_page'] ) ? $_POST['my_account_as_login_page'] : false;
		if ( $my_account_as_login_page == '' ) $my_account_as_login_page = false;
		$settings['my_account_as_login_page'] = $my_account_as_login_page;
		return $settings;
	}

	function tcp_admin_menus() {
		global $menu;

		$wp_user_search = new WP_User_Query( array(
			'meta_key'		=> 'tcp_locked',
			'meta_value'	=> true,
			'fields'		=> array( 'ID' ),
		) );
		$locked_users = $wp_user_search->get_total();
		if ( $locked_users > 0 ) {
			$menu[70][0] .= '<span class="tcp-locked-users update-plugins count-' . $locked_users . '"><span class="tcp-locked-users-count">' . $locked_users . '</span></span>';
		}
	}

	function wp_authenticate_user( $user ) {
		if ( ! is_wp_error( $user ) ) {
			$locked = tcp_is_user_locked( $user->ID );
			if ( $locked == '' ) $locked = false;
			if ( $locked ) {
				return new WP_Error( 'tcp_locked', __('<strong>ERROR</strong>: User account is waiting for review', 'tcp-fu') );
			}
		}
		return $user;
	}

	function edit_user_profile() {
		if ( ! current_user_can( 'edit_users' ) ) return;
		global $user_id;
		$current_user = wp_get_current_user();
		if ( $current_user->ID == $user_id ) return; // User cannot locked itself ?>
<h3><?php _e( 'User Review', 'tcp' ); ?></h3>
<table class="form-table">
<body>
	<tr>
		<th scope="row">
			<?php _e( 'User locked', 'tcp' ); ?>
		</th>
		<td>
			<?php $locked = tcp_is_user_locked( $user_id ); ?>
			<label for="tcp_locked"><input name="tcp_locked" type="checkbox" id="tcp_locked" value="yes" <?php checked( $locked ); ?> /> <?php _e( 'User account is locked waiting for review', 'tcp' ); ?></label>
			<?php if ( ! $locked ) : $date = tcp_get_user_locked_date( $user_id ); ?>
				<p class="description"><?php printf( __( 'Unlocked since %s', 'tcp' ), date_i18n( 'Y-m-d', $date ) ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
</body>
</table>
<?php }

	function edit_user_profile_update() {
		if ( !current_user_can( 'edit_users' ) ) return;
		global $user_id;// User cannot disable itself
		$current_user = wp_get_current_user();
		if ( $current_user->ID == $user_id ) return;
		$locked = isset( $_POST['tcp_locked'] );
		tcp_set_user_locked( $user_id, $locked );
	}

	function manage_users_columns( $columns ) {
		$columns['tcp_locked'] = 'Review';
		return $columns;
	}

	function manage_users_custom_column( $value, $column_name, $user_id ) {
		if( $column_name == 'tcp_locked' ) {
			$locked = tcp_is_user_locked( $user_id, 'tcp_locked', true );
			if ( $locked === '' ) {
				return __( 'No locked', 'tcp' );
			} else {
				$date = tcp_get_user_locked_date( $user_id );
				if ( $date == 0 ) {
					$out = __( 'No locked', 'tcp' );
				} elseif ( $locked ) {
					$out = sprintf( __( 'Waiting for review since %s', 'tcp' ), date( 'Y-m-d', $date ) );
				} else {
					$out = sprintf( __( 'Unlocked since %s', 'tcp' ), date( 'Y-m-d', $date ) );
				}
				return apply_filters( 'tcp_users_locked_column', $out, $user_id );
			}
		}
		return $value;
	}

	function tcp_register_and_login_ajax() {
		unset( $_REQUEST['tcp_redirect_to'] );
		unset( $_REQUEST['tcp_redirect_error'] );
		$error_msg = $this->_tcp_register_and_login( false );
		die( json_encode( $error_msg ) );
	}

	function tcp_register_and_login() {
		return $this->_tcp_register_and_login();
	}

	function _tcp_register_and_login( $redirect = true ) {
		$error_msg = false;
		$user_name		= isset( $_REQUEST['tcp_new_user_name'] ) && trim( $_REQUEST['tcp_new_user_name'] ) != '' ? $_REQUEST['tcp_new_user_name'] : $error_msg = __( 'User name is required', 'tcp' );
		if ( $error_msg && ! $redirect ) return $error_msg;
		$user_pass		= isset( $_REQUEST['tcp_new_user_pass'] ) ? $_REQUEST['tcp_new_user_pass'] : $error_msg =  __( 'Password is required', 'tcp' );
		if ( $error_msg && ! $redirect ) return $error_msg;
		$user_pass_2	= isset( $_REQUEST['tcp_repeat_user_pass'] ) ? $_REQUEST['tcp_repeat_user_pass'] : $error_msg =  __( 'Repeated Password is required', 'tcp' );
		if ( $error_msg && ! $redirect ) return $error_msg;
		$redirect_to	= isset( $_REQUEST['tcp_redirect_to'] ) ? $_REQUEST['tcp_redirect_to'] : false;
		$redirect_to_error	= isset( $_REQUEST['tcp_redirect_error'] ) ? $_REQUEST['tcp_redirect_error'] : $redirect_to;
		$user_email		= isset( $_REQUEST['tcp_new_user_email'] ) ? $_REQUEST['tcp_new_user_email'] : $error_msg =  __( 'User email is required', 'tcp' );
		if ( $error_msg && ! $redirect ) return $error_msg;
		$login			= isset( $_REQUEST['tcp_login'] );
		$roles			= isset( $_REQUEST['tcp_role'] ) ? explode( ',', $_REQUEST['tcp_role'] ) : array( 'customer' );
		if ( ! is_array( $roles ) ) $roles = array( $roles );
		$locked			= isset( $_REQUEST['tcp_locked'] );
		$user_name = trim( $user_name );
		if ( username_exists( $user_name ) ) {
			$error_msg = __( 'User name exists', 'tcp' );
		} elseif ( $user_pass != $user_pass_2 ) {
			$error_msg = __( 'Password incorrect', 'tcp' );
		} elseif ( ! is_email( $user_email ) ) {
			$error_msg = __( 'Invalid email', 'tcp' );
		} else {
			$sanitized_user_login = sanitize_user( $user_name );
			$error_msg = apply_filters( 'tcp_registration_errors', '', $sanitized_user_login, $user_email );
			if ( strlen( $error_msg ) == 0 ) {
				$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
				if ( is_wp_error( $user_id ) ) {
					$error_msg = $user_id->get_error_message();
				} else {
					$user = new WP_User( $user_id );
					foreach( $roles as $role )
						$user->add_role( $role );
					do_action( 'tcp_register_and_login', $user_id );
					if ( $locked ) tcp_set_user_locked( $user_id );
					if ( $redirect && $login && ! $locked ) {
						$user = wp_signon( array(
							'user_login'	=> $user_name,
							'user_password'	=> $user_pass,
							'remember'		=> false,
						), false );
						if ( is_wp_error( $user ) ) $error_msg = $user->get_error_message();
					}
				}
			}
		}
		if ( $redirect ) {
			if ( $error_msg && $redirect_to_error ) {
				wp_redirect( add_query_arg( 'tcp_register_error', urlencode( $error_msg ), $redirect_to_error ) );
			} elseif ( $redirect_to ) {
				wp_redirect( $redirect_to );
			}
			die();
		}
		return $error_msg;
	}

	function user_register( $user_id ) {
		//$user = new WP_User( $user_id );
		//if ( isset( $_REQUEST['tcp_role'] ) ) $user->add_role( $_REQUEST['tcp_role'] );
		//if ( isset( $_REQUEST['tcp_lock'] ) && $_REQUEST['tcp_lock'] ) tcp_set_user_locked( $user_id, $_REQUEST['tcp_role'] );
		//var_dump( $user );
	}

	function login_url( $login_url, $redirect ) {
		/*$login_url = remove_query_arg( 'redirect_to', $login_url );
		$login_url = add_query_arg( 'redirect_to', tcp_get_the_my_account_url(), $login_url );
		return $login_url;*/
		global $thecartpress;
		if ( ! $thecartpress ) return $login_url;
		$page_id = $thecartpress->get_setting( 'my_account_as_login_page', false );
		if ( $page_id !== false ) {
			$url = get_permalink( $page_id );
			$url = add_query_arg( 'redirect_to', get_permalink(), $url );	
			return $url;
		}
		return $login_url; //add_query_arg( 'redirect_to', get_permalink(), tcp_get_the_my_account_url() );
	}

	function logout_url( $logout_url, $redirect ) {
		$logout_url = remove_query_arg( 'redirect_to', $logout_url );
		$logout_url = add_query_arg( 'redirect_to', get_permalink(), $logout_url );
		return $logout_url;
	}

	function tcp_my_account() {
		ob_start(); ?>
<div class="row-fluid">
	<div class="span<?php echo get_option( 'users_can_register' ) ? '6' : '12'; ?>">
		<?php if ( ! is_user_logged_in() ) : ?><h3><?php _e( 'Login', 'tcp' ); ?></h3><?php endif; ?>
		<?php $args = array( 'see_register' => false );
		if ( isset( $_REQUEST['redirect_to'] ) ) $args['redirect'] = $_REQUEST['redirect_to'];
		tcp_login_form(); ?>
	</div>
<?php if ( ! is_user_logged_in() && get_option( 'users_can_register' ) ) : ?>
	<div class="span6">
		<h3><?php _e( 'Register', 'tcp' ); ?></h3>
		<?php tcp_register_form( array( 'login' => true ) ); ?>
	</div>
<?php endif; ?>
</div><!-- .row-fluid -->

<!--<h2><?php //_e( 'My Adresses', 'tcp-fe' ); ?></h2>
	<?php //_e( 'You have the following dispatch and/or billing addresse(s) at interloom', 'tcp-fe' ); ?>
	<?php //echo $this->tcp_addresses_list(); ?>
	<a href="<?php //tcp_the_my_addresses_url(); ?>"><?php _e( 'See all addresses', 'tcp-fe' ); ?></a>
<h2><?php //_e( 'My orders', 'tcp-fe' ); ?></h2>
	<?php //_e( 'Here is an overview of your current and previous orders', 'tcp_fe' ); ?>
	<?php //echo //$this->tcp_orders_list(); ?>
	<a href="<?php //tcp_the_my_orders_url(); ?>"><?php _e( 'See all orders', 'tcp-fe' ); ?></a>-->
		<?php return ob_get_clean();
	}
}

new TCPLoginRegister();
} // class_exists check