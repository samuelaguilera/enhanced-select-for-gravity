<?php
/**
 * Plugin Name: Enhanced Select for Gravity
 * Description: Adds Select2 and Selectizer option to Drop Down and Multi Select fields in Gravity Forms.
 * Author: Samuel Aguilera
 * Version: 1.0-beta-2
 * Author URI: http://www.samuelaguilera.com
 * License: GPL3
 *
 * @package Enhanced Select for Gravity
 */

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'gform_field_appearance_settings', 'esfg_field_settings', 10, 2 );
/**
 * Create settings on position 400 (right after Enable enhanced user interface).
 *
 * @param integer $position The position where the action is currently firing.
 * @param integer $form_id  The ID of the form that the action is being run on.
 */
function esfg_field_settings( $position, $form_id ) {
	if ( 400 === $position ) {
		?>
				<li class="enable_selectize_setting field_setting">
					<input type="checkbox" id="gfield_enable_selectize" onclick="SetFieldProperty('enableSelectize', jQuery(this).is(':checked') ? 1 : 0);" />
					<label for="gfield_enable_selectize" class="inline">
					<?php esc_html_e( 'Enable Selectize', 'gravityforms' ); ?>
						<?php gform_tooltip( 'form_field_enable_selectizer' ); ?>
					</label>
				</li>
				<li class="enable_select2_setting field_setting">
					<input type="checkbox" id="gfield_enable_select2" onclick="SetFieldProperty('enableSelect2', jQuery(this).is(':checked') ? 1 : 0);" />
					<label for="gfield_enable_select2" class="inline">
					<?php esc_html_e( 'Enable Select2', 'gravityforms' ); ?>
						<?php gform_tooltip( 'form_field_enable_select2' ); ?>
					</label>
				</li>
		<?php
	}
}

add_action( 'gform_editor_js', 'esfg_selectizer_editor_script' );
/**
 * Inject supporting script to the form editor page.
 */
function esfg_selectizer_editor_script() {
	?>
	<script type='text/javascript'>
		// Adding setting to fields of type "select" and "multiselect".
		fieldSettings["select"] += ", .enable_selectize_setting";
		fieldSettings["multiselect"] += ", .enable_selectize_setting";
		fieldSettings["select"] += ", .enable_select2_setting";
		fieldSettings["multiselect"] += ", .enable_select2_setting";

		// Binding to the load field settings event to initialize the checkbox.
		jQuery(document).bind("gform_load_field_settings", function(event, field, form){
			jQuery("#gfield_enable_selectize").attr("checked", field["enableSelectize"] == true);
			jQuery("#gfield_enable_select2").attr("checked", field["enableSelect2"] == true);
		});
	</script>
	<?php
}

add_filter( 'gform_tooltips', 'esfg_add_selectizer_tooltips' );
/**
 * Adds tooltips for the settings.
 *
 * @param array $tooltips An array with the existing tooltips.
 */
function esfg_add_selectizer_tooltips( $tooltips ) {
	$tooltips['form_field_enable_selectizer'] = '<h6>Enable Selectize UI enhancer</h6>Check this box to enable Selectize script in your drop down or multi select field.';
	$tooltips['form_field_enable_select2']    = '<h6>Enable Select2 UI enhancer</h6>Check this box to enable Select2 script in your drop down or multiselect field.';
	return $tooltips;
}

/**
 * Check if there's a field with any of our settings enabled.
 *
 * @param array $form The form object.
 */
function esfg_has_enhanced_select( $form ) {

	if ( ! is_array( $form['fields'] ) ) {
		return false;
	}

	foreach ( $form['fields'] as $field ) {
		if ( in_array( $field->type, array( 'select', 'multiselect' ), true ) && $field->enableSelectize ) {
			return 'selectize';
		} elseif ( in_array( $field->type, array( 'select', 'multiselect' ), true ) && $field->enableSelect2 ) {
			return 'select2';
		}
	}

	return false;
}

add_filter( 'gform_field_css_class', 'esfg_custom_class', 10, 3 );
/**
 * Add class to field for the enabled script.
 *
 * @param array $classes The CSS classes to be filtered, separated by empty spaces.
 * @param array $field   Current field object.
 * @param array $form    Current form object.
 */
function esfg_custom_class( $classes, $field, $form ) {
	if ( ( 'select' === $field->type && $field->enableSelectize ) || ( 'multiselect' === $field->type && $field->enableSelectize ) ) {
		$classes .= ' gfield_selectize';
	} elseif ( ( 'select' === $field->type && $field->enableSelect2 ) || ( 'multiselect' === $field->type && $field->enableSelect2 ) ) {
		$classes .= ' gfield_select2';
	}
	return $classes;
}

add_action( 'gform_enqueue_scripts', 'esfg_enqueue_selectize_script', 10, 2 );
/**
 * Enqueue scripts and styles when needed.
 *
 * @param array $form    Current form object.
 * @param bool  $is_ajax Specify if the form is configured to be submitted via AJAX.
 */
function esfg_enqueue_selectize_script( $form, $is_ajax ) {

	$has_enhanced_select = esfg_has_enhanced_select( $form );

	if ( 'selectize' === $has_enhanced_select ) {
		wp_register_script( 'selectize', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js', array( 'jquery' ), '0.12.6', true );
		wp_enqueue_script( 'selectize' );

		wp_register_script( 'add-selectize', plugins_url( '/js/add-selectize.min.js', __FILE__ ), array( 'jquery', 'selectize' ), '1.0', true );
		wp_enqueue_script( 'add-selectize' );

		wp_register_style( 'selectize', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.0/css/selectize.bootstrap2.min.css', array(), '0.12.6' );
		wp_enqueue_style( 'selectize' );
	} elseif ( 'select2' === $has_enhanced_select ) {
		wp_register_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), '4.0.10', true );
		wp_enqueue_script( 'select2' );

		wp_register_script( 'add-select2', plugins_url( '/js/add-select2.min.js', __FILE__ ), array( 'jquery', 'select2' ), '1.0', true );
		wp_enqueue_script( 'add-select2' );

		wp_register_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', array(), '4.0.10' );
		wp_enqueue_style( 'select2' );
	}

}

add_filter( 'gform_preview_styles', 'esfg_enqueue_preview_styles', 10, 2 );
/**
 * Register styles in the preview window.
 *
 * @param array $styles Array of style handles to be enqueued.
 * @param array $form   Current form object.
 */
function esfg_enqueue_preview_styles( $styles, $form ) {

	$has_enhanced_select = esfg_has_enhanced_select( $form );

	if ( 'selectize' === $has_enhanced_select ) {
		wp_register_style( 'selectize', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.0/css/selectize.bootstrap2.min.css', array(), '0.12.6' );
		$styles[] = 'selectize';
	} elseif ( 'select2' === $has_enhanced_select ) {
		wp_register_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', array(), '4.0.10' );
		$styles[] = 'select2';
	}

		return $styles;
}
