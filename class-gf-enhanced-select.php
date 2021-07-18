<?php

defined( 'ABSPATH' ) || die();

// Include the Gravity Forms Add-On Framework.
GFForms::include_addon_framework();

/**
 * Gravity Forms Enhanced Select for Gravity Add-On.
 *
 * @since     1.0-beta-3
 * @package   GravityForms
 * @author    Samuel Aguilera
 * @copyright Copyright (c) 2021, Samuel Aguilera
 */
class GF_Enhanced_Select extends GFAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0-beta-3
	 * @var    GF_Enhanced_Select $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Enhanced Select for Gravity Add-On.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_version Contains the version.
	 */
	protected $_version = GF_ENHANCED_SELECT_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GF_ENHANCED_SELECT_MIN_GF_VERSION;

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'enhanced-select-for-gravity';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'enhanced-select-for-gravity/enhanced-select-for-gravity.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  1.0-beta-3
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://www.samuelaguilera.com';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_title The title of the add-on.
	 */
	protected $_title = 'Enhanced Select for Gravity';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  1.0-beta-3
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Enhanced Select';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0-beta-3
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines the capabilities needed for the Enhanced Select for Gravity Add-On
	 *
	 * @since  1.0-beta-3
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_enhanced-select-for-gravity', 'gravityforms_enhanced-select-for-gravity_uninstall' );

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0-beta-3
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_enhanced-select-for-gravity';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0-beta-3
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_enhanced-select-for-gravity';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0-beta-3
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_enhanced-select-for-gravity_uninstall';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  1.0-beta-3
	 *
	 * @return GF_Enhanced_Select $_instance An instance of the GF_Enhanced_Select class
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new GF_Enhanced_Select();
		}

		return self::$_instance;

	}

	/**
	 * Register initialization hooks.
	 *
	 * @since  1.0-beta-3
	 */
	public function init() {

		parent::init();

		add_filter( 'gform_field_css_class', array( $this, 'enhanced_select_custom_class' ), 10, 3 );

	}

	/**
	 * Register admin initialization hooks.
	 *
	 * @since  1.0-beta-3
	 */
	public function init_admin() {

		parent::init_admin();

		add_action( 'gform_field_appearance_settings', array( $this, 'field_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_action( 'gform_editor_js', array( $this, 'enhanced_select_editor_script' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @since  1.0-beta-3
	 *
	 * @return array
	 */
	public function scripts() {

		$select_js = 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js';

		$scripts = array(
			array(
				'handle'    => 'esfg_add_enhanced_select',
				'src'       => $this->get_base_url() . '/js/add-enhanced-select.min.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'has_enhanced_select' ),
				),
			),
			array(
				'handle'    => 'esfg_enhanced_select',
				'src'       => $select_js,
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'has_enhanced_select' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}

	/**
	 * Register styles.
	 *
	 * @since  1.0-beta-3
	 *
	 * @return array
	 */
	public function styles() {

		$select_js_css = 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap2.min.css';

		$styles = array(
			array(
				'handle'  => 'esfg_enhanced_select_css',
				'src'     => $select_js_css,
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'has_enhanced_select' ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	// # FIELD SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Create settings on position 400 (right after Enable enhanced user interface).
	 *
	 * @param integer $position The position where the action is currently firing.
	 * @param integer $form_id  The ID of the form that the action is being run on.
	 */
	public function field_settings( $position, $form_id ) {
		if ( 400 === $position ) {
			?>
					<li class="enable_esfg_setting field_setting">
						<input type="checkbox" id="gfield_enable_esfg" onclick="SetFieldProperty('enableEnhancedSelect', jQuery(this).is(':checked') ? 1 : 0);" />
						<label for="gfield_enable_esfg" class="inline">
						<?php esc_html_e( 'Enable Enhanced Select', 'gravityforms' ); ?>
							<?php gform_tooltip( 'form_field_enable_esfg' ); ?>
						</label>
					</li>
			<?php
		}
	}

	/**
	 * Adds tooltips for the settings.
	 *
	 * @param array $tooltips An array with the existing tooltips.
	 */
	public function tooltips( $tooltips ) {
		$tooltips['form_field_enable_esfg'] = '<h6>Enable Select2 UI enhancer</h6>Check this box to enable Select2 script in your drop down or multiselect field. "Enable enhanced user interface" must be disabled for ALL the fields of this form.';
		return $tooltips;
	}

	/**
	 * Inject supporting script to the form editor page.
	 */
	public function enhanced_select_editor_script() {
		?>
		<script type='text/javascript'>
			// Adding setting to fields of type "select" and "multiselect".
			fieldSettings["select"] += ", .enable_esfg_setting";
			fieldSettings["multiselect"] += ", .enable_esfg_setting";

			// Binding to the load field settings event to initialize the checkbox.
			jQuery(document).bind("gform_load_field_settings", function(event, field, form){
				jQuery("#gfield_enable_esfg").attr("checked", field["enableEnhancedSelect"] == true);
			});
		</script>
		<?php
	}

	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Check if there's a field with any of our settings enabled.
	 */
	public function has_enhanced_select( $form ) {
		// $this->log_debug( __METHOD__ . '(): Current Form: ' . print_r( $form, true ) );

		if ( ! is_array( $form ) || ! is_array( $form['fields'] ) ) {
			$this->log_debug( __METHOD__ . '(): Form not avaiable or no fields with Enhanced Select enabled.' );
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( in_array( $field->type, array( 'select', 'multiselect' ), true ) && $field->enableEnhancedSelect ) {
				$this->log_debug( __METHOD__ . '(): Field with Enhanced Select enabled: ' . $field->id );
				return true;
			}
		}

		return false;
	}

	/**
	 * Add class to field for the enabled script.
	 *
	 * @param array $classes The CSS classes to be filtered, separated by empty spaces.
	 * @param array $field   Current field object.
	 * @param array $form    Current form object.
	 */
	public function enhanced_select_custom_class( $classes, $field, $form ) {
		if ( in_array( $field->type, array( 'select', 'multiselect' ), true ) && $field->enableEnhancedSelect ) {
			$classes .= ' gfield_enhanced_select';
		}
		return $classes;
	}

}
