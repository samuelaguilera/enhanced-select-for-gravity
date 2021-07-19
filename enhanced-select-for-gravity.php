<?php
/*
Plugin Name: Enhanced Select for Gravity
Plugin URI: https://www.samuelaguilera.com
Description: Adds Selectize for Drop Down and Multi Select fields in Gravity Forms.
Version: 1.0-beta-3
Author: Samuel Aguilera
Author URI: https://www.samuelaguilera.com
License: GPL-3.0+
Text Domain: enhanced-select-for-gravity
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2021 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.

*/

defined( 'ABSPATH' ) || die();

// Defines the current version of the Enhanced Select for Gravity Add-On.
define( 'GF_ENHANCED_SELECT_VERSION', '1.0-beta-3' );

// Defines the minimum version of Gravity Forms required to run Enhanced Select for Gravity Add-On.
define( 'GF_ENHANCED_SELECT_MIN_GF_VERSION', '2.4' );

// After Gravity Forms is loaded, load the Add-On.
add_action( 'gform_loaded', array( 'GF_Enhanced_Select_Bootstrap', 'load_addon' ), 5 );

/**
 * Loads the Enhanced Select for Gravity Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 1.0-beta-3
 */
class GF_Enhanced_Select_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since  1.0-beta-3
	 */
	public static function load_addon() {

		// Requires the class file.
		require_once plugin_dir_path( __FILE__ ) . '/class-gf-enhanced-select.php';

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'GF_Enhanced_Select' );
	}

}

/**
 * Returns an instance of the GF_Enhanced_Select class
 *
 * @since  1.0-beta-3
 *
 * @return GF_Enhanced_Select|bool An instance of the GF_Enhanced_Select class
 */
function gf_enhanced_select() {
	return class_exists( 'GF_Enhanced_Select' ) ? GF_Enhanced_Select::get_instance() : false;
}
