<?php
/*
Plugin Name: j2goMap
Plugin URI: http://wordpress.greenlapok.com/plugins/j2gomap/
Description: an awesome google map generate with simple UI for users love to travel and adventures
Version: 2.0.1
Author: Johndy Dejito
Author URI: greenlapok.com
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
	$option_name = 'wp_j2gomap_opts' ;
		$new_value = array("marker_api" => "",
							"post_type" => "post",
							"marker_icon" => plugin_dir_url( __FILE__ )."/img/pokemons/pokeball.png",
							"zoom" => "12",
							"m_type" => "ROADMAP",
							"shortcode" => "yes") ;

		if ( get_option( $option_name ) !== true ) {

			$deprecated = '';
			$autoload = 'no';
			add_option( $option_name, $new_value, $deprecated, $autoload );

		}
	//call settings
	include_once dirname( __FILE__ ) . '/j2gomap-settings.php';
	
	//call functions
	include_once dirname( __FILE__ ) . '/j2gomap-functions.php';
	
	//add meta box and meta data(location,marker,marker description)
	include_once dirname( __FILE__ ) . '/j2gomap-metadata.php';
	
	//add shortcode
	include_once dirname( __FILE__ ) . '/j2gomap-shortcode.php';
	
	if (!is_admin() && get_gapi() != null){	add_action("wp_enqueue_scripts", "j2gomap_script_enqueue", 11);	}
	function j2gomap_script_enqueue() {
	   wp_deregister_script('jquery');
	   wp_enqueue_script('jquery lib', plugins_url( '/js/jquery-1.10.2.min.js' , __FILE__ ));
	   wp_enqueue_script('jquery map', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://maps.googleapis.com/maps/api/js?key=".get_gapi()."&language=en", false, null);
	   wp_enqueue_script('jquery map3',plugins_url( '/js/gmap/gmap3.min.js' , __FILE__ ), false, null);
	
		wp_register_style( 'j2gomap-style', plugins_url( '/css/j2gomap-style.css' , __FILE__ ));
		wp_enqueue_style( 'j2gomap-style' );
	}
	
	function j2gomap_css_admin_scripts() {

	wp_register_style( 'j2gomap-css', plugins_url( '/css/j2gomap-admin.css' , __FILE__ ));
	wp_enqueue_style( 'j2gomap-css' );

	}
	add_action( 'admin_enqueue_scripts', 'j2gomap_css_admin_scripts' );
	
	function j2gomap_script() {
		wp_enqueue_script('jquery j2gomap',plugins_url( '/js/j2gomap-popup.js' , __FILE__ ), false, null);
	}
	add_action('wp_footer', 'j2gomap_script');

	
/*
	*adding gmap before or after post post content
	*
*/
	function add_j2gomap_content( $content) {
		global $post;
		$p = 'post_'.$post->ID;
		$address = get_post_meta( $post->ID, 'j2gomap_address', true );
		$zoom = get_post_meta( $post->ID, 'j2gomap_zoom', true );
		$mx = get_post_meta( $post->ID, "j2gomap", true);
		
		if(is_array($mx)){
			
			foreach($mx['key'] as $mz => $v) {
				$pokemons .= plugin_dir_url( __FILE__ ).'/img/pokemons/'.$v['y'].".png+";
				$marker .= $v['a']."+";
				$marker_desc = $v['b'];
			}
		
			$get_gomap_display = get_option('wp_j2gomap_opts');
			$get_post_type = empty($get_gomap_display['post_type']) ? "post" : $get_gomap_display['post_type'];
			$zoom = empty($zoom) ? get_zoom() : $zoom;
			
			
			if ( is_single() && $get_post_type == get_post_type()) {
			
				$display_as = $get_gomap_display['display_as'];
				switch($display_as){
				
					case "popup":
						
							$alt = "".$address.";".$address.";".$marker_desc.";".$zoom.";".get_map_type().";".get_marker_icon();
							$custom_content .='<a href="javascript:void(0);" id="'.$p.'" class="loadj2goMap" alt="'.$alt.'">Show map</a>';
							$custom_content .= $content;
							return $custom_content;
							break;
						
					case "single":
					
							if($get_gomap_display['ba_display'] == "before"){
								$custom_content = '<div class="gomap_wrapper">';
								$custom_content .= load_js_gomap($p,$address,$marker,$marker_desc,$pokemons,$zoom);
								$custom_content .= '<div id="'.$p.'" class="gmap3"></div>';
								$custom_content .= '</div>';
								$custom_content .= $content;
								return $custom_content;
							}else{
								$custom_content = $content;
								$custom_content .= '<div class="gomap_wrapper">';
								$custom_content .= load_js_gomap($p,$address,$marker,$marker_desc,$pokemons,$zoom);
								$custom_content .= '<div id="'.$p.'" class="gmap3"></div>';
								$custom_content .= '</div>';					
								return $custom_content;
							}
						
						break;
				}
			}
			else {
				return $content;
			}
		}
		else {
				return $content;
			}
	}
	add_filter( 'the_content', 'add_j2gomap_content' );
	
	
	function load_js_gomap($pid,$address,$marker,$marker_desc,$pokemon,$zoom){
		
		$markers = explode('+',$marker);
		$pokemons = explode('+',$pokemon);
		$ctr = count($markers);
	
		$gomap_js = 'j2Query(function(){';
		$gomap_js .= 'j2Query("#'.$pid.'").width("'.get_width().'").height("'.get_height().'").gmap3({
						  marker:{
							values:[';
							  if($marker){
								for($i = 0; $i < $ctr; $i++){
									if($i < $ctr){	$nl = ",";	}else{	$nl = "";	}
									$gomap_js .= '{ address: "'.$markers[$i].'",data:"'.$markers[$i].'", options:{icon: "'.$pokemons[$i].'"}}'.$nl;
								}
							  }else{
									$gomap_js .= '{address: "'.$address.'", data:"'.$marker_desc.'", options:{icon: "'.get_marker_icon().'"}}';
							  }
							$gomap_js .='],
							options:{
							  draggable:false
							},
							events:{
							  dragend: function(marker){
								j2Query(this).gmap3({
								  getaddress:{
									latLng:marker.getPosition()
								  }
								});
							  },mouseover: function(marker, event, context){
								var map = j2Query(this).gmap3("get"),
								  infowindow = j2Query(this).gmap3({get:{name:"infowindow"}});
								if (infowindow){
								  infowindow.open(map, marker);
								  infowindow.setContent(context.data);
								} else {
								  j2Query(this).gmap3({
									infowindow:{
									  anchor:marker, 
									  options:{content: context.data}
									}
								  });
								}
							  },
							  mouseout: function(){
								var infowindow = j2Query(this).gmap3({get:{name:"infowindow"}});
								if (infowindow){
								  infowindow.close();
								}
							  }
							}
						  },
						  map:{
							options:{
							  zoom: '.$zoom.',
							  mapTypeId: google.maps.MapTypeId.'.get_map_type().'
							}
						  }
						});';
		
		$gomap_js .= '});';
		
		return '<script type="text/javascript">'.$gomap_js.'</script>';
	}
?>