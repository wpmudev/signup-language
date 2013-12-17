<?php
/*
Plugin Name: Select Language At Signup
Plugin URI: http://premium.wpmudev.org/project/select-language-at-signup
Description: Allows new users to select the language they use at signup
Author: S H Mohanjith (Incsub), Andrew Billits (Incsub)
Version: 1.0.5.1
Author URI: http://premium.wpmudev.org
WDP ID: 60
Network: true
Text Domain: signup_language
*/

/*
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$signup_language_current_version = '1.0.5.1';
static $signup_language;

add_action( 'init', 'signup_language_setup' );
add_action( 'admin_notices', 'signup_language_admin_notice' );
add_action( 'signup_blogform', 'signup_language_signup_form' );
add_filter( 'wpmu_validate_blog_signup', 'signup_language_validate_language' );
add_filter( 'add_signup_meta', 'signup_language_add_signup_meta' );
add_action( 'wpmu_new_blog', 'signup_language_add_language_option', 10, 6 );

/**
 * Loads the plugin text domain
 *
 * @since 1.0
 */
function signup_language_setup() {
	//wp_die(var_dump(WPLANG));
	load_plugin_textdomain('signup_language', false, dirname(plugin_basename(__FILE__)).'/languages');
}

/**
 * Show an error admin notice if the site is not multisite
 *
 * @since 1.0.5
 */
function signup_language_admin_notice() {

	if ( ! is_multisite() ) {
		?>
			<div class="error">
				<p><?php _e( 'The <strong>Select Language At Signup</strong> plugin is only compatible with WordPress Multisite.', 'signup_language' ); ?></p>
			</div>
		<?php
	}
}

/**
 * Renders the selection box on signup form
 *
 * @since 1.0
 */
function signup_language_signup_form( $errors ) {
	include_once( ABSPATH . 'wp-admin/includes/ms.php' );

	$languages = get_available_languages();

	?>
	<div id="language-selection">
		<p class="language-option">
			<label for="language"><?php _e( 'Select a language', 'signup_language' ); ?></label>
			<?php if ( $errmsg = $errors->get_error_message( 'language' ) ): ?>
				<p class="error"><?php echo $errmsg ?></p>
			<?php endif; ?>
			<select name="language" id="language">
				<?php mu_dropdown_languages( $languages, get_site_option( 'WPLANG' ) ); ?>
			</select>
		</p>
	</div>

	<?php

}

/**
 * Validates the language from the signup form
 *
 * @param Array $meta Site meta from the form
 * @return Array New meta array
 */
function signup_language_validate_language( $meta ) {

	global $signup_language;

	$languages = array_merge( get_available_languages(), array( '' ) );
	if ( ! isset( $_POST['language'] ) || ! in_array( $_POST['language'], $languages ) ) {
		$meta['errors']->add( 'language', __( 'Language not allowed', 'signup_language' ) );
	}
	else {
		$meta['WPLANG'] = $_POST['language'];
		$signup_language = $meta['WPLANG'];
	}

	return $meta;

}

/**
 * Adds the language to the blog signup meta table
 *
 * @param Array $meta Current meta
 * @return Array New meta
 */
function signup_language_add_signup_meta( $meta ) {
	global $signup_language;

	$meta['WPLANG'] = $signup_language;

	return $meta;
}

/**
 * Updates the language for the site based on the meta we saved before
 *
 * @param Integer $blog_id
 * @param Integer $user_id
 * @param String $domain
 * @param String $path
 * @param Integer $site_id
 * @param Array $meta
 */
function signup_language_add_language_option( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	switch_to_blog( $blog_id );
	update_option( 'WPLANG', $meta['WPLANG'] );
	restore_current_blog();
}
