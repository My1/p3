<?php
// create admin menus

if (!function_exists('pipdig_add_admin_menu')) {
	function pipdig_add_admin_menu() {
	
		global $submenu;
		add_menu_page( 'pipdig', 'pipdig', 'manage_options', 'pipdig', 'pipdig_main_options_page', 'dashicons-star-filled' );
		add_submenu_page( 'pipdig', __('Theme Hooks', 'p3-textdomain'), __('Theme Hooks', 'p3-textdomain'), 'manage_options', 'pipdig-hooks', 'pipdig_hooks_options_page' );
		$submenu['pipdig'][0][0] = 'Guides'; // http://wordpress.stackexchange.com/questions/98226/admin-menus-name-menu-different-from-first-submenu
		
	}
	add_action( 'admin_menu', 'pipdig_add_admin_menu' );
}


if (!function_exists('pipdig_main_options_page')) {
	function pipdig_main_options_page() { 

		?>
		<p>We'll be adding some video tutorials and guides here in future releases. If you have any requests please feel free to tweet us @pipdig :)</p>
		<?php

	}
}

