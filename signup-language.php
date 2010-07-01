<?php
/*
Plugin Name: signup_language
Plugin URI: 
Description:
Author: Andrew Billits
Version: 1.0.1
Author URI:
WDP ID: 60
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

$signup_language_current_version = '1.0.1';
//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
//check for activating
if ($_GET['key'] == '' || $_GET['key'] === ''){
	add_action('admin_head', 'signup_language_make_current');
	signup_language_language_import();
}
add_action('signup_blogform', 'signup_language_signup_form');
//add_action('signup_finished', 'signup_language_signup_form_process');
add_filter('wpmu_validate_blog_signup', 'signup_language_signup_form_process');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//
function signup_language_make_current() {
	global $wpdb, $signup_language_current_version;
	if (get_site_option( "signup_language_version" ) == '') {
		add_site_option( 'signup_language_version', '0.0.0' );
	}
	
	if (get_site_option( "signup_language_version" ) == $signup_language_current_version) {
		// do nothing
	} else {
		//update to current version
		update_site_option( "signup_language_installed", "no" );
		update_site_option( "signup_language_version", $signup_language_current_version );
	}
	signup_language_global_install();
	//--------------------------------------------------//
	if (get_option( "signup_language_version" ) == '') {
		add_option( 'signup_language_version', '0.0.0' );
	}
	
	if (get_option( "signup_language_version" ) == $signup_language_current_version) {
		// do nothing
	} else {
		//update to current version
		update_option( "signup_language_version", $signup_language_current_version );
		signup_language_blog_install();
	}
}

function signup_language_blog_install() {
	global $wpdb, $signup_language_current_version;
	//$signup_language_table1 = "";
	//$wpdb->query( $signup_language_table1 );
}

function signup_language_global_install() {
	global $wpdb, $signup_language_current_version;
	if (get_site_option( "signup_language_installed" ) == '') {
		add_site_option( 'signup_language_installed', 'no' );
	}
	
	if (get_site_option( "signup_language_installed" ) == "yes") {
		// do nothing
	} else {
	
		$signup_language_table1 = "CREATE TABLE `" . $wpdb->base_prefix . "signup_language` (
  `language_ID` bigint(20) unsigned NOT NULL auto_increment,
  `language_blog_domain` varchar(255) NOT NULL,
  `language_blog_path` varchar(255) NOT NULL,
  `language_value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`language_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;";
		$signup_language_table2 = "";
		$signup_language_table3 = "";
		$signup_language_table4 = "";
		$signup_language_table5 = "";

		$wpdb->query( $signup_language_table1 );
		//$wpdb->query( $signup_language_table2 );
		//$wpdb->query( $signup_language_table3 );
		//$wpdb->query( $signup_language_table4 );
		//$wpdb->query( $signup_language_table5 );
		update_site_option( "signup_language_installed", "yes" );
	}
}

function signup_language_language_import() {
	global $nationality_list, $wpdb, $wp_roles, $current_user;
	if (get_settings("signup_language_imported") == '1') {
		// it's alreadt installed
	} else {
		$bloginfo = get_blog_details( $wpdb->blogid, false );
		$tmp_language_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->base_prefix . "signup_language WHERE language_blog_domain = '" . $bloginfo->domain . "' AND language_blog_path = '" . $bloginfo->path . "'");
		if ($tmp_language_count >= '1') {
			//info was provided at signup!
			$tmp_lang = $wpdb->get_var("SELECT language_value FROM " . $wpdb->base_prefix . "signup_language WHERE language_blog_domain = '" . $bloginfo->domain . "' AND language_blog_path = '" . $bloginfo->path . "'");
			update_option('WPLANG', $tmp_lang);
			//fix global nationality table
		} else {
			//no info provided at signup, do nothing
		}
		add_option('signup_language_imported', '1');
	}
}

function signup_language_signup_form_process($content) {
	global $wpdb;
	
	$content_original = $content;
	extract($content);
	
	if ( $errors->get_error_code() ) {
		//error
	} else {
		//no error
		$wpdb->query( "INSERT INTO " . $wpdb->base_prefix . "signup_language (language_blog_domain, language_blog_path, language_value) VALUES ( '" . $domain . "', '" . $path . "', '" . $_POST['language'] . "' )" );
	}

	return $content_original;
}
//------------------------------------------------------------------------//
//---Output Functions-----------------------------------------------------//
//------------------------------------------------------------------------//

function signup_language_signup_form() {
	include_once(ABSPATH . 'wp-admin/includes/ms.php');
	$lang_files = glob( ABSPATH . LANGDIR . '/*.mo' );
	$lang = get_option('WPLANG');
	
	if( is_array( $lang_files ) && count($lang_files) >= 1 ) {
		?>
		<label for="blog_type"><?php _e('Language') ?>:</label>
		<select name="language" id="language" style="width: 100%; text-align: left; font-size: 20px;">
		<?php
        echo '<option value=""'.((empty($lang)) ? 'selected="selected"': '').'>'.__('English').'</option>';
        foreach ( (array) $lang_files as $key => $val ) {
            $code_lang = basename( $val, '.mo' );
            echo '<option value="'.$code_lang.'"'.(($lang == $code_lang) ? ' selected="selected"' : '').'> '.format_code_lang($code_lang).'</option>';
        }
        ?>
        </select>
        <?php
	} // languages
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Support Functions----------------------------------------------------//
//------------------------------------------------------------------------//

?>
