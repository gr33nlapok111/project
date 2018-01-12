<?php
/*
Plugin Name: Affiliate Tools
Plugin URI: 
Description: Affiliate Tools by Stark. Generate brandable promotional tools and banners for your affiliates.
Version: 1.0.1
Author: StarkExpressions
Author URI: http://starkexpressions.com/
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
	
		echo 'we are the starkexpressions';
		exit;
		
	}
	
	
	include_once dirname( __FILE__ ) . '/affiliate-tools-shortcode.php';
	
	include_once dirname( __FILE__ ) . '/affiliate-tools-metabox.php';
	
	include_once dirname( __FILE__ ) . '/options.php';
	
	//set text domain
	load_textdomain('affiliate-tool', false, basename( dirname( __file__ ) ) . '/lang' );
	
	
	/*if (!is_admin()) 
	add_action("wp_enqueue_scripts", "atb_jquery_enqueue", 11);
	
	function atb_jquery_enqueue() {
	   wp_register_script('jquerylib', plugins_url( '/js/jquery.1.9.0.js', __FILE__ ), array('jquery'));
	   wp_enqueue_script('jquerylib');
	}*/
	
	function atb_style()
	{
		// Register the style like this for a plugin:  
		wp_register_style( 'atb_style', plugins_url( '/css/ui-form.css', __FILE__ ), array(), '', 'all' ); 
		wp_enqueue_style( 'atb_style' );
	}
	add_action( 'wp_enqueue_scripts', 'atb_style' );
	
	
	//add inline css on admin page for ATB table list
	add_action('admin_head', 'atb_manage_list_css');
	function atb_manage_list_css() {
	  echo '<link rel="stylesheet" href="'.plugins_url( '/css/ui-form.css', __FILE__ ).'" type="text/css" media="all" />';
	}
	
	
	function plu_admin_enqueue() {
    wp_register_script('myplupload', plugins_url( '/js/myplupload.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('myplupload');
 
    wp_register_style('myplupload', plugins_url( '/css/myplupload.css', __FILE__ ));
    wp_enqueue_style('myplupload');
	}
	add_action( 'admin_enqueue_scripts', 'plu_admin_enqueue' );
	
	function plu2_admin_enqueue() {
	wp_register_script('pluploadui', plugins_url( '/js/jquery.ui.plupload.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('pluploadui');
	}
	add_action( 'admin_enqueue_scripts', 'plu2_admin_enqueue' );
	
	function plu3_admin_enqueue() {
	wp_register_script('pluploadjs', plugins_url( '/js/plupload.full.min.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('pluploadjs');
	}
	add_action( 'admin_enqueue_scripts', 'plu3_admin_enqueue' );

	
	function plupload_admin_head() {  
		// place js config array for plupload
			$plupload_init = array(
				'runtimes' => 'html5,silverlight,flash,html4',
				'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
				'container' => 'plupload-upload-ui', // will be adjusted per uploader
				'dragdrop' => true,
				'drop_element' => 'drag-drop-area', // will be adjusted per uploader
				'file_data_name' => 'async-upload', // will be adjusted per uploader
				'multiple_queues' => true,
				'max_file_size' => wp_max_upload_size() . 'b',
				'url' => admin_url('admin-ajax.php'),
				'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				'filters' => array(array('title' => __('Allowed Files'), 'extensions' => 'jpg,gif,png')),
				'multipart' => true,
				'urlstream_upload' => true,
				'multi_selection' => false, // will be added per uploader
				 // additional post data to send to our ajax hook
				'multipart_params' => array(
					'_ajax_nonce' => "", // will be added per uploader
					'action' => 'plupload_action', // the ajax action name
					'imgid' => 0 // will be added per uploader
				)
			);
		?>
		<script type="text/javascript">  
			var base_plupload_config=<?php echo json_encode($plupload_init); ?>;
		</script>  
		<?php  
		}
		add_action("admin_head", "plupload_admin_head"); 
	
	function g_plupload_action() {
 
    // check ajax noonce
    $imgid = $_POST["imgid"];
    check_ajax_referer($imgid . 'pluploadan');
 
    // handle file upload
    $status = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'plupload_action'));
 
    // send the uploaded file url in response
    echo $status['url'];
    exit;
}
add_action('wp_ajax_plupload_action', "g_plupload_action"); 
	
	/*
	*
	*create custom post type
	*
	*/
	
	add_action( 'init', 'atb_post_type' );
	function atb_post_type() {
		
		$labels = array(
			'name' => "AT Projects",
			'singular_name' => "AT Project",
			'add_new' => 'Add Project',
			'add_new_item' => 'Add New Affiliate Tools Project',
			'edit_item' => 'Edit Affiliate Tools Project',
			'new_item' => 'New Affiliate Tools Project',
			'all_items' => 'Manage AT Projects',
			'view_item' => 'View AT Project',
			'search_items' => 'Search AT Project',
			'not_found' =>  'No AT Project found',
			'not_found_in_trash' => 'No AT Project found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'AT Projects'
			
		  );

		  $args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => 'affiliate' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 55,
			'menu_icon' => 'dashicons-admin-generic',
			'supports' => array( 'title','content' )
		  ); 

		  register_post_type( 'affiliate', $args );
	}
// disable the autosave for our plugin
add_action( 'admin_enqueue_scripts', 'my_admin_enqueue_scripts' );
function my_admin_enqueue_scripts() {
    if ( 'affiliate' == get_post_type() )
        wp_dequeue_script( 'autosave' );
}

// change publish button to add new
add_filter( 'gettext', 'change_publish_button', 10, 2 );

function change_publish_button( $translation, $text ) {
if ( 'affiliate' == get_post_type())
if ( $text == 'Publish' )
    return 'Add Project';

return $translation;
}

//change ATM messages
function atb_set_messages($messages) {
global $post, $post_ID;
$post_type = 'affiliate';

$obj = get_post_type_object($post_type);
$singular = 'Affiliate Project';

$messages[$post_type] = array(
0 => '', // Unused. Messages start at index 1.
1 => sprintf( __($singular.' updated. The shortcode is [ATB pageid="'.$post_ID.'"]'), esc_url( get_permalink($post_ID) ) ),
2 => __('Custom field updated.'),
3 => __('Custom field deleted.'),
4 => __($singular.' updated.'),
5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
6 => sprintf( __($singular.' published. The shortcode is [ATB pageid="'.$post_ID.'"]'), esc_url( get_permalink($post_ID) ) ),
7 => __('Page saved.'),
8 => sprintf( __($singular.' submitted. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
10 => sprintf( __($singular.' draft updated. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);
return $messages;
}

add_filter('post_updated_messages', 'atb_set_messages' );


add_filter( 'manage_edit-affiliate_columns', 'my_edit_atb_columns' ) ;

function my_edit_atb_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'atbname' => __( 'AT Project Name' ),
		'atbshortcode' => __( 'Shortcode' ),
		'atbedit' => __( 'Edit' ),
		'atbdelete' => __( 'Delete' )
	);

	return $columns;
}

add_action( 'manage_affiliate_posts_custom_column', 'my_manage_atb_columns', 10, 2 );

function my_manage_atb_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'duration' column. */
		case 'atbname' :

			/* Get the post meta. */
			$atb_name = get_post_meta( $post_id, 'atb_name', true );

			/* If no atb_name is found, output a default message. */
			if ( empty( $atb_name ) )
				echo __( get_the_titile($post_id) );

			/* If there is a atb_name to the text string. */
			else
				echo '<a href="'.get_edit_post_link( $post->ID, '', true ).'">'.$atb_name.'</a>';

			break;

		/* If displaying the 'genre' column. */
		case 'atbshortcode' :

			/* Get the shortcode for the post. */
			$shortcode = '[ATB pageid="'.$post_id.'"]';

			if ( !empty( $shortcode ) ) {

				printf( __( '%s' ), $shortcode );
			}else {
				_e( 'No Shortcode' );
			}

			break;
			
		case 'atbedit' :

			/* Get the shortcode for the post. */
			$atbedit = '<a href="'.get_edit_post_link( $post->ID, '', true ).'" class="dashicons-before dashicons-welcome-write-blog"><span class="txt">Edit</span></a>';

			if ( !empty( $atbedit ) ) {

				printf( __( '%s' ), $atbedit );
			}

			break;
		
		case 'atbdelete' :

			/* Get the shortcode for the post. */
			$atbdelete = '<a href="'.get_delete_post_link( $post->ID, '', true ).'" class="dashicons-before dashicons-no"><span class="txt">Delete</span></a>';

			if ( !empty( $atbdelete ) ) {

				printf( __( '%s' ), $atbdelete );
			}

			break;
			
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}