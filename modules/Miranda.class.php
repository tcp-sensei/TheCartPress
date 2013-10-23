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

class TCPMiranda {

	private $pages;

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'tcp_main_settings_page', array( $this, 'tcp_main_settings_page' ) );
			add_filter( 'tcp_main_settings_action', array( $this, 'tcp_main_settings_action' ) );
		}
	}

	function get_page( $page_slug ) {
		if ( isset( $this->pages[$page_slug] ) )
			return $this->pages[$page_slug];
		else
			return false;
	}

	function add_page( $title, $page_slug ) {
		$this->pages[$page_slug] = new TCPMirandaPage( $title );
	}

	function add_section( $page_slug, $title, $section_slug ) {
		$page = $this->pages[$page_slug];
		if ( $page ) $page->add( new TCPMirandaSection( $title ), $section_slug );
	}

	/**
	 *
	 * @parm page is array( object name, class path)
	 */
	function add_item( $page_slug, $section_slug, $title, $desc, $item_page, $icon = false ) {
		$page = $this->pages[$page_slug];
		if ( $page ) {
			$section = $page->getItem( $section_slug );
			if ( $section ) $section->add( new TCPMirandaItem( $title, false, $item_page, $icon ) );
		}
	}

	function tcp_load_miranda() {
		global $tcp_miranda;
		if ( $tcp_miranda ) {
			$page_slug = $_REQUEST['page'];
		 	$page = $tcp_miranda->get_page( $page_slug );
		 	if ( $page ) exit( $page->show( false ) );
		 }
	}

	function admin_footer() { ?>
<script>	
jQuery('.toplevel_page_thecartpress\\/TheCartPress\\.class').click(function(event) {
	var feedback = jQuery('#tcp_miranda_feedback');
	feedback.show();
	jQuery.ajax({
		async	: true,
		type    : "POST",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: {
			action	: 'tcp_load_miranda',
			page	: 'settings'
		},
		success : function(response) {
			feedback.hide();
			jQuery('div#unity-Panel').html(response);
		},
		error	: function(response) {
			feedback.hide();
		},
	});
	event.stopPropagation();
	return false;
});
</script><?php
	}

	function admin_init() {
		global $thecartpress;
		if ( $thecartpress && $thecartpress->get_setting( 'activate_miranda', true ) ) {
			wp_register_script( 'tcp_miranda', plugins_url( 'thecartpress/js/tcp_miranda.js' ) );
			wp_enqueue_script( 'tcp_miranda' );
			wp_enqueue_style( 'tcp_miranda', plugins_url( 'thecartpress/css/tcp_miranda.css' ) );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			add_action( 'admin_footer', array( &$this, 'admin_footer' ) );

			add_action( 'wp_ajax_tcp_load_miranda', array( &$this, 'tcp_load_miranda' ) );
			add_action( 'wp_ajax_tcp_miranda_load_admin_panel', array( &$this, 'tcp_miranda_load_admin_panel' ) );
			add_action( 'wp_ajax_tcp_miranda_save_admin_panel', array( &$this, 'tcp_miranda_save_admin_panel' ) );
		}
	}

	function tcp_miranda_load_admin_panel() {
		if ( isset( $_REQUEST['page'] ) && isset( $_REQUEST['class'] ) ) {
			if ( isset( $_REQUEST['page'] ) ) require_once( $_REQUEST['page'] );
			$class = $_REQUEST['class'];
			$obj = new $class();
			$obj->admin_page(); ?>
<script>
jQuery(':submit').on('click', function(event) {
	var form = jQuery(this).closest('form');
	data = 'action=tcp_miranda_save_admin_panel&class=<?php echo $_REQUEST['class']; ?>&page=<?php echo $_REQUEST['page']; ?>&' + form.serialize();
	jQuery.ajax({
		async	: true,
		type    : "POST",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: data,
		success : function(response) {
			jQuery('div#TB_ajaxContent').html(response);
		},
		error	: function(response) {
		},
	});
	event.stopPropagation();
	return false;
});
</script>
			<?php exit();
		}
	}

	function tcp_miranda_save_admin_panel() {
		if ( isset( $_REQUEST['page'] ) && isset( $_REQUEST['class'] ) ) {
			require_once( $_REQUEST['page'] );
			$class = $_REQUEST['class'];
			$obj = new $class();
			$obj->admin_action();
			$this->tcp_miranda_load_admin_panel();
		}
	}

	function tcp_main_settings_page() {
		global $thecartpress;
		$activate_miranda = $thecartpress->get_setting( 'activate_miranda', true ); ?>
	<tr valign="top">
		<th scope="row">
			<label for="activate_miranda"><?php _e( 'Activate miranda', 'tcp' ); ?></label>
		</th>
		<td>
			<input type="checkbox" id="activate_miranda" name="activate_miranda" value="yes" <?php checked( true, $activate_miranda ); ?> />
			<span class="description"><?php _e( 'Activate miranda for the next generation of Graphical Interface.', 'tcp' ); ?></span>
		</td>
	</tr><?php
	}

	function tcp_main_settings_action( $settings ) {
		$settings['activate_miranda'] = isset( $_POST['activate_miranda'] ) ? $_POST['activate_miranda'] == 'yes' : false;
		return $settings;
	}
}

class TCPMirandaObject {
	private $title;
	private $description;
	private $icon;

	function __construct( $title, $description = false, $icon = false ) {
		$this->title = $title;
		$this->description = $description;
		$this->icon = $icon;
	}

	function getTitle() {
		return $this->title;
	}

	function getDescription() {
		return $this->description;
	}
	
	function getIcon() {
		return $this->icon;
	}
}

class TCPMirandaContent extends TCPMirandaObject {
	private $items;

	function __construct( $title, $description = false, $icon = false, $items = array() ) {
		parent::__construct( $title, $description, $icon );
		$this->items = $items;
	}
	
	function add( $item, $key = false ) {
		if ( $key === false ) $this->items[] = $item;
		else $this->items[$key] = $item;
		return $item;
	}

	function getItems() {
		return $this->items;
	}

	function getItem( $key ) {
		if ( isset( $this->items[$key] ) ) return $this->items[$key];
		else return false;
	}
}

class TCPMirandaPage extends TCPMirandaContent {

	function add( $item, $key = false ) {
		if ( 'TCPMirandaSection' == get_class( $item ) )
			parent::add( $item, $key );
		return $item;
	}

	function show( $echo = true ) {
		ob_start(); ?>
<div class="tcp-miranda-page">

<h1><?php echo $this->getTitle(); ?></h1>

<?php foreach( $this->getItems() as $section ) $section->show(); ?>

</div><!-- .tcp-miranda-page -->

		<?php $out = ob_get_clean();
		if ( $echo ) echo $out;
		return $out;
	}
}

class TCPMirandaSection extends TCPMirandaContent {

	function add( $item, $key = false ) {
		if ( 'TCPMirandaItem' == get_class( $item ) )
			parent::add( $item, $key = false );
		return $item;
	}

	function show() { ?>

		<section class="tcp-miranda-section clearfix">

		<header>

			<h2><?php echo $this->getTitle(); ?></h2>

		</header>

		<?php foreach( $this->getItems() as $item ) $item->show(); ?>

	</section><?php
	}
}

class TCPMirandaItem extends TCPMirandaObject {
	private $url;

	function __construct( $title, $description = false, $url = false, $icon = false ) {
		parent::__construct( $title, $description, $icon );
		$this->url = $url;
	}

	function getUrl() {
		return $this->url;
	}
	
	function show() {
		$icon = $this->getIcon() !== false ? $this->getIcon() : plugins_url( 'thecartpress/images/miranda/default_settings_48.png' ); ?>
			<article class="tcp-miranda-item">
				<a <?php $this->showUrl(); ?>>
					<!--<img src="<?php echo $icon; ?>" width="48px" height="48px" border="0" />-->
					<img src="<?php echo $icon; ?>" height="48px" border="0" />
					<span><?php echo $this->getTitle(); ?></span>
				</a>
			</article><?php
	}

	function showUrl() {
		if ( is_array( $this->url ) ) {
			$url = admin_url( 'admin-ajax.php' );
			$url = add_query_arg( 'action', 'tcp_miranda_load_admin_panel', $url );
			$url = add_query_arg( 'class', $this->url[0], $url );
			if ( isset( $this->url[1] ) ) $url = add_query_arg( 'page', $this->url[1], $url );
			$url = add_query_arg( 'modal', true, $url );
			$url = add_query_arg( 'height', 600, $url );
			$url = add_query_arg( 'width', 700, $url );
			echo 'href="' . $url . '" class="thickbox"';
		} else {
			$url = $this->url;
			//$url = add_query_arg( 'modal', true, $url );
			//$url = add_query_arg( 'TB_iframe', true, $url );
			$url = add_query_arg( 'height', 600, $url );
			$url = add_query_arg( 'width', 700, $url );
			echo 'href="' . $url . '" class="thickbox"';
		}
	}
}

/**
 * must be implemented by any edit panel to be used into Miranda engine
 */
interface IMirandaPage {

}

$tcp_miranda = new TCPMiranda();
$tcp_miranda->add_page( __( 'Settings', 'tcp' ), 'settings' );
$tcp_miranda->add_section( 'settings', __( 'Default Settings', 'tcp' ), 'default_settings' );
$tcp_miranda->add_section( 'settings', __( 'Payments', 'tcp' ), 'payments' );
?>