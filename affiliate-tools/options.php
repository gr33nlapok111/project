<?php
add_action( 'admin_enqueue_scripts', 'at_add_color_picker' );
function at_add_color_picker( $hook ) {
 
    if( is_admin() ) { 
     
        // Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
         
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'custom-script-handle', plugins_url( '/js/at-custom.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    }
}

add_action('admin_menu' , 'services_opt');

function services_opt() {
add_submenu_page('edit.php?post_type=affiliate', 'Custom Post Type Admin', 'Settings', 'edit_posts', basename(__FILE__), 'at_submenu_page_callback');
}
function at_submenu_page_callback() {

	$opt = array();

	if (isset($_POST["update_settings"])) {
		$opt = $_POST["at_val"];
		
		if(get_option('at_val') === FALSE){
				
			add_option('at_val', $opt);
					
		}else{
				
			update_option('at_val', $opt);
					
		}
		
		echo '<div id="message" class="updated">Settings saved</div>';
	}
	$at_opt_a = get_option("at_val");
	if (!empty($at_opt_a)) {
	
		foreach ($at_opt_a as $key => $option)
			$options[$key] = $option;

	}
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		?>
		
		<form method="POST" action="" class="form-setting">
            <table class="form-table">
                <tr valign="top" class="opt_field" >
                    <td>
						<label>Text Color :</label>
                        <input type="text" name="at_val[at_text_color]" value="<?php echo empty($options['at_text_color'])? '#000' : $options['at_text_color']; ?>" id="at_text_color" class="sts-color-picker" >
                    </td>
                </tr>
				<tr valign="top" class="opt_field">
					<td>
						<label id="preview-head">Preview</label>
						<p id="preview-content">Simply enter your affiliate ID into field below and click the "Submit" button to automatically brand all of the affiliate links, tools and banners below with your affiliate ID</p>
						<input type="hidden" name="update_settings" value="Y" />
                    </td>
                </tr>
            </table>
			<p>
				<input type="submit" value="Save settings" class="button-primary"/>
			</p>
        </form>
		<script>
			jQuery(function() {
				jQuery('.sts-color-picker').wpColorPicker();
			});
			
			jQuery(document).ready(function(){
				var tc = jQuery('#at_text_color').val();
				jQuery("#preview-content").css({'color':tc});
			});
		</script>
	<?php
	echo '</div>';

}
?>