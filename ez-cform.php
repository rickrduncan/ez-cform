<?php
/**
 *  Plugin Name: 	EZ CForm
 *  Plugin URI: 	http://rickrduncan.com/ez-plugins
 *  Donate link:    https://paypal.me/rickrduncan
 *  Description: 	A no-bloat contact form plugin for WordPress.
 *  Author: 		Rick R. Duncan - B3Marketing, LLC
 *  Author URI: 	http://rickrduncan.com
 *
 * 	Version: 		1.0.0
 *
 *  License: 		GPLv2 or later
 *  License URI: 	http://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * Add 'Settings' link to the plugin on the 'Installed Plugins' page.
 *
 * @since 1.0.0
 */
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ezcform_plugin_action_links' );
function ezcform_plugin_action_links( $links ) {
	$links = array_merge( array( '<a href="' . esc_url( admin_url( '/options-general.php?page=ez-contact' ) ) . '">Settings</a>' ), $links );
	return $links;
}


/**
 * Add the admin options page.
 *
 * @since 1.0.0
 */
add_action('admin_menu', 'ezcform_admin_add_page');
function ezcform_admin_add_page() {
	add_options_page('EZ Contact', 'EZ Contact', 'manage_options', 'ez-contact', 'ezcform_options_page');
}


/**
 * Display the admin options page.
 *
 * @since 1.0.0
 */
function ezcform_options_page() {
	?>
	<div class="wrap">
	<h1>EZ Contact Form Settings</h1>
	<form action="options.php" method="post">
		<?php settings_fields('ezcform_plugin_options'); ?>
		<?php do_settings_sections('ezcform_plugin'); ?>
		<?php submit_button(); ?>
	</form>
	</div>
	<?php
}


/**
 * Add the admin settings and such.
 *
 * @since 1.0.0
 */
add_action('admin_init', 'ez_cform_admin_init');
function ez_cform_admin_init(){
	register_setting( 'ezcform_plugin_options', 'ezcform_plugin_options', 'ezcform_options_validate' );

	add_settings_section('ezcform_main', '', 'ezcform_section_text', 'ezcform_plugin');

	add_settings_field('ezcform_subject', 'Subject of Email', 'ezcform_input_subject', 'ezcform_plugin', 'ezcform_main');
	add_settings_field('ezcform_email', 'Email Address (Send To)', 'ezcform_input_email', 'ezcform_plugin', 'ezcform_main');
	add_settings_field('ezcform_msg', 'Confirmation Message', 'ezcform_input_msg', 'ezcform_plugin', 'ezcform_main');
}


/**
 * Output section text.
 *
 * @since 1.0.0
 */
function ezcform_section_text() {
	echo '<p>Fill in the fields below. Then you simply copy the following shortcode [ez_contact_form] into a page and you\'re done!</p>';
}


/**
 * Output input field for Subject.
 *
 * @since 1.0.0
 */
function ezcform_input_subject() {
	$options = get_option('ezcform_plugin_options');
	echo "<input id='ezcform_subject' name='ezcform_plugin_options[subject]' size='40' type='text' value='{$options['subject']}' required />";
}


/**
 * Output input field for SendTo Email Address.
 *
 * @since 1.0.0
 */
function ezcform_input_email() {
	$options = get_option('ezcform_plugin_options');
	echo "<input id='ezcform_email' name='ezcform_plugin_options[email]' size='40' type='email' value='{$options['email']}' required />";
}


/**
 * Output input field for Thank you Message.
 * This is the message received when a user submits an email using the form.
 *
 * @since 1.0.0
 */
function ezcform_input_msg() {
	$options = get_option('ezcform_plugin_options');
	echo "<input id='ezcform_msg' name='ezcform_plugin_options[msg]' size='40' type='text' value='{$options['msg']}' required />";
}


/**
 * Validate our options.
 *
 * @since 1.0.0
 */
function ezcform_options_validate($input) {

	$options = get_option('ezcform_plugin_options');

	$options['subject'] = sanitize_text_field( esc_attr( $input['subject'] ) );
	$options['email'] = sanitize_email( $input['email'] );
	$options['msg'] = sanitize_text_field( esc_attr( $input['msg'] ) );

	return $options;
}


/**
 * Build our HTML5 contact form.
 * Validation client side is through HTML 5, then handled again on the server side before being sent.
 *
 * @since 1.0.0
 */
function ezcform_build_contact_form() {
	echo '<style>.hawley-griffin {display:none;} .required {color:red;}</style>';
	echo '<form class="ezcform-form" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '?thankyou=1" method="post">';
	echo '<div class="ezcform fields">';
	echo '<label for="ezcf_name" class="ezcform">Name<span class="required">*</span></label>';
	echo '<input name="ezcf_name" id="ezcf_name"  type="text" aria-required="true" value="' . ( isset( $_POST["ezcf_name"] ) ? esc_attr( $_POST["ezcf_name"] ) : '' ) . '" maxlength="125" required>';
	echo '</div>';
	echo '<div class="ezcform fields">';
	echo '<label for="ezcf_email" class="ezcform">Email<span class="required">*</span></label>';
	echo '<input name="ezcf_email" id="ezcf_email" type="email" spellcheck="false" aria-required="true" value="' . ( isset( $_POST["ezcf_email"] ) ? esc_attr( $_POST["ezcf_email"] ) : '' ) . '" maxlength="125" required>';
	echo '</div>';
	echo '<div class="ezcform fields">';
	echo '<label for="ezcf_message" class="ezcform">Message<span class="required">*</span></label>';
	echo '<textarea class="ezcontact" name="ezcf_message" spellcheck="true" rows="5" cols="50" aria-required="true" required>' . ( isset( $_POST["ezcf_message"] ) ? esc_attr( $_POST["ezcf_message"] ) : '' ) . '</textarea>';
	echo '</div>';
	echo '<div class="ezcform fields">';
	echo '<input class="hawley-griffin" name="hg_firstname" id="hg_firstname" aria-required="false" type="text" tabindex="-1" autocomplete="false" hidden>';
	echo '<input class="button ezcform" name="sendEzEmail" type="submit" value="Send Email">';
	echo '</div>';
	echo '</form>';
}


/**
 * Validate and send contact form data
 *
 * @since 1.0.0
 */
function ezcform_validate_send_email() {

	global $globalIsValid;

	$error = false;
	$error_msg = array();
	$email_body = '';

	// if the submit button is clicked, send the email
	if ( isset( $_POST['sendEzEmail'] ) ) {

		/* Honey Pot Test. This field should never have a value. */
		if ( strlen( $_POST["hg_firstname"] ) > 0 ) {
			exit();
		}

		// sanitize form values
		$name 		= sanitize_text_field( $_POST["ezcf_name"] );
		$email 		= sanitize_email( $_POST["ezcf_email"] );
		$message 	= esc_textarea( $_POST["ezcf_message"] );


		// validate first name
		if ( $name == '' ) {
			$error = true;
			$error_msg[] = 'Please enter your name.';
		}
		else {
			$email_body = 'Name: ' . $name . PHP_EOL;
		}

		// validate email address
		if ( $email == '' ) {
			$error = true;
			$error_msg[] = 'Please enter your email address.';
		}
		else {
			$email_body .= 'Email Address: ' . $email . PHP_EOL;
		}

		// validate message
		if ( $message == '' ) {
			$error = true;
			$error_msg[] = 'Please enter a message.';
		}
		else {
			$email_body .= 'Message: ' . $message . PHP_EOL;
		}

		if ( $error ) {
			echo 'ERROR';
			echo '<div style="color:red;">';
			echo '<ul>';
				if(count($error_msg) > 0){
					foreach($error_msg as $e){
						echo '<li>' . $e . '</li>';
    				}
				}
			echo '</ul>';
			echo '</div>';
		}
		else {

			$options = get_option('ezcform_plugin_options');
			$email_sendto = $options['email'];
			$email_subject = $options['subject'];
			$email_msg = $options['msg'];

			$headers = "From: $name <$email>" . "\r\n";

			if ( wp_mail( $email_sendto, $email_subject, $email_body, $headers ) ) {
				echo '<div>';
				echo '<div class="ezcform-confirmartion-msg">'. $email_msg .'</div>';
				echo '<style>.button.ezcform { pointer-events: none; }</style>';
				echo '</div>';
			}
			else {
				echo 'Your email was not sent. Please try again.';
			}
		}
	}
}


/**
 * Build shortcode and functionality
 * Useage: [ez_contact_form]
 *
 * @since 1.0.0
 */
add_shortcode( 'ez_contact_form', 'ezcform_shortcode' );
function ezcform_shortcode() {
	ob_start();
	ezcform_validate_send_email();
	ezcform_build_contact_form();
	return ob_get_clean();
}
?>
