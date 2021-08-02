<?php

/**
 *	WooCommerce settings page
 *
 *	This code creates a full WooCommerce settings page by extending the WC_Settings_Page class.
 *	By extending the WC_Settings_Page class, we can control every part of the settings page.
 *
 *	@author Ren Ventura <renventura.com>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RV_Custom_WooCommerce_Settings_Page' ) ) :

class RV_Custom_WooCommerce_Settings_Page extends WC_Settings_Page {
	
	public function __construct() {

		$this->id = 'settings-page-slug';
		$this->label = __( 'Settings Page Title', 'text-domain' );

		/**
		 *	Define all hooks instead of inheriting from parent
		 */

		// parent::__construct();

		// Add the tab to the tabs array
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );

		// Add new section to the page
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

		// Add settings
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );

		// Process/save the settings
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 *	Get sections
	 *
	 *	@return array
	 */
	public function get_sections() {

		// Must contain more than one section to display the links
		// Make first element's key empty ('')
		$sections = array(
			'' => __( 'Overview', 'text-domain' ),
			'license' => __( 'License', 'text-domain' )
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 *	Output sections
	 */
	public function output_sections() {

		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 *	Get settings array
	 *
	 *	@return array
	 */
	public function get_settings() {

		global $current_section;

		$settings = array();

		if ( $current_section == 'license' ) {

			$settings = array(

				/**
				 *	For settings types, see:
				 *	https://github.com/woocommerce/woocommerce/blob/fb8d959c587ee95f543e682e065192553b3cc7ec/includes/admin/class-wc-admin-settings.php#L246
				 */

				// License input
				array(
					'title' => __( 'License Settings', 'text-domain' ),
					'type' => 'title',
					'desc' =>  __( 'Manage your license settings for the WooCommerce Custom Redirects plugin.', 'text-domain' ),
					'id' => 'woocommerce_redirects_license_settings'
				),
				array(
					'title' => __( 'License Key', 'text-domain' ),
					'type' => 'text',
					'desc' => __( 'Add your license key.', 'text-domain' ),
					'desc_tip' => true,
					'id' => 'woocommerce_redirects_license',
					'css' => 'min-width:300px;',
				),
				array(
					'type' => 'sectionend',
					'id' => 'woocommerce_redirects_license_settings'
				),
			);
		
		} else {

			// Overview
			$settings = array();
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 *	Output the settings
	 */
	public function output() {
		$settings = $this->get_settings();
		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 *	Process save
	 *
	 *	@return array
	 */
	public function save() {

		global $current_section;

		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}
}

endif;

new RV_Custom_WooCommerce_Settings_Page;
