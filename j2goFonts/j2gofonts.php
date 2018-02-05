<?php
/*
Plugin Name: j2goFonts
Plugin URI: 
Description: j2goFonts is a google fonts importer from http://www.google.com/fonts
Version: 1.0
Author: Johndy Dejito
Author URI: coming soon
License: GPL2 or later
*/


/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) {
	
		echo 'english ng kotse ay car';
		exit;
		
	}

add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/j2gofonts-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
	
//call plugin settings
	include_once dirname( __FILE__ ) . '/j2gofonts-settings.php';

	


add_action('wp_head', 'import_font');
function import_font(){

	$opts = get_option('wp_j2gofonts_opts');
	if (!empty($opts)) {
	
		if($opts['cf']){ $font = str_replace(' ','+',$opts['cf']);}else{	$font = str_replace(' ','+',$opts['font_name']);}
		echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.$font.'" media="screen">';
	}
}

add_action( 'wp_head', 'j2gofont_inline_css',100 );
function j2gofont_inline_css() {
	
	$opts = get_option('wp_j2gofonts_opts');
	if (!empty($opts)) {
		if($opts['cf']){ $ff = $opts['cf'];}else{	$ff = $opts['font_name'];}
		$ff = ($opts['overrides'] != 0)? $ff.' !important' : $ff;
		if($opts['target']){
			$css = $opts['target']."{
				font-family:".$ff.";
				font-size:".$opts['size']."px;
				color:".$opts['color'].";}\n";
		}
		if($opts['custom']){
			$css .= $opts['custom'];
		}
	echo "<style>".$css."</style>";
	}
}
?>