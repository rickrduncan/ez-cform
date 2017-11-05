<?php
/**
 * Uninstall - Remove all options created by our plugin.
 *
 */


/**
 * Exit if accessed directly.
 *
 * @since 1.0.0
 */ 
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Delete all the options we created with this plugin.
 *
 * @since 1.0.0
 */
delete_option( 'ezcform_plugin_options' );