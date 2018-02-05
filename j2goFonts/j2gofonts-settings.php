<?php

/*
	*
	* settings:
	* j2gofonts
	*
	*/
	
	class wp_j2gofonts{
	
		var $mid = array();
	
		public function __construct(){
			if(is_admin()){
			add_action('admin_menu', array($this, 'add_plugin_page'));
			add_action('admin_init', array($this, 'page_init'));
			}
		}
		
		public function add_plugin_page(){
		
			// This page will be under "Settings"
			
			add_options_page('Admin Options', 'j2gofonts', 'manage_options', 'j2gofonts-setting-admin', array($this, 'create_admin_page'));
		}

		public function create_admin_page(){
			?>
		<script>
		jQuery(document).ready(function($){
			$('.my-color-field').wpColorPicker();
			
			var myOptions = {
				// you can declare a default color here,
				// or in the data-default-color attribute on the input
				defaultColor: false,
				// a callback to fire whenever the color changes to a valid color
				change: function(event, ui){},
				// a callback to fire when the input is emptied or an invalid color
				clear: function() {},
				// hide the color picker controls on load
				hide: true,
				// show a group of common colors beneath the square
				// or, supply an array of colors to customize further
				palettes: true
			};
			 
			$('.my-color-field').wpColorPicker(myOptions);
			
			$('#font_list').on('change',function(e){
				if($(this).val() == 'custom'){
					$('#input_cf').show();
				}else{
					$('#input_cf').hide();
				}
			});
			if($('#font_list').val() == 'custom'){
					$('#input_cf').show();
				}else{
					$('#input_cf').hide();
			}
		});
		</script>
		<style>table.form-table th{display:none;}table.form-table tr td{vertical-align:top;}</style>
		<div class="wrap">
		
			<?php screen_icon(); ?>
			<h2>Your j2Gofonts Settings</h2>			
			<form method="post" action="options.php">
				<?php
				
				// This prints out all hidden setting fields
				
				settings_fields('j2gofonts_option_group');	
				do_settings_sections('j2gofonts-setting-admin');
				
				?>
				<?php submit_button(); ?>
				
			</form>
			
		</div>
		<?php
		}
		
		public function page_init(){
		register_setting('j2gofonts_option_group', 'array_key', array($this, 'check_ID'));
			
		add_settings_section(
		
			'setting_section_id',
			'Setting',
			array($this, 'print_section_info'),
			'j2gofonts-setting-admin'
			
		);	
			
		add_settings_field(
		
			'wp_gofonts_id', 
			' ', 
			array($this, 'create_wp_gofonts_field'), 
			'j2gofonts-setting-admin',
			'setting_section_id'
			
		);
		
		}
		
		public function check_ID($input = array()){
		
			if(!empty($_POST['array_key'])){
			
				$mid = $_POST['array_key'];
				if(get_option('wp_j2gofonts_opts') === FALSE){
				
					add_option('wp_j2gofonts_opts', $mid);
					
				}else{
				
					update_option('wp_j2gofonts_opts', $mid);
					
				}
			
			}else{
			
				$mid = null;
			}
			
			
			return $mid;
			
		}
		
		public function print_section_info(){
		
		print 'Enter your settings below:';
		
		}
		
		public function create_wp_gofonts_field(){
/**
 * Returns an array of system fonts
 * Feel free to edit this, update the font fallbacks, etc.
 */

function get_os_fonts() {
	// OS Font Defaults
	$os_faces = array(
		'Arial, sans-serif' => 'Arial',
		'Avant Garde, sans-serif' => 'Avant Garde',
		'Cambria, Georgia, serif' => 'Cambria',
		'Copse, sans-serif' => 'Copse',
		'Garamond, Hoefler Text, Times New Roman, Times, serif' => 'Garamond',
		'Georgia, serif' => 'Georgia',
		'Helvetica Neue, Helvetica, sans-serif' => 'Helvetica Neue',
		'Tahoma, Geneva, sans-serif' => 'Tahoma'
	);
	return $os_faces;
}

/**
 * Returns a select list of Google fonts
 * Feel free to edit this,add your own choice from http://www.google.com/fonts(key sensitive), update the fallbacks, etc.
 */

function get_google_fonts() {
	// Google Font Defaults
	$google_faces = array(
		'Arvo, serif' => 'Arvo',
		'Copse, sans-serif' => 'Copse',
		'Droid Sans, sans-serif' => 'Droid Sans',
		'Droid Serif, serif' => 'Droid Serif',
		'Lobster, cursive' => 'Lobster',
		'Nobile, sans-serif' => 'Nobile',
		'Open Sans, sans-serif' => 'Open Sans',
		'Oswald, sans-serif' => 'Oswald',
		'Quattrocento, serif' => 'Quattrocento',
		'Raleway, cursive' => 'Raleway',
		'Ubuntu, sans-serif' => 'Ubuntu',
		'Yanone Kaffeesatz, sans-serif' => 'Yanone Kaffeesatz'
	);
	return $google_faces;
}
			$gofonts_opt = get_option('wp_j2gofonts_opts');
			if (!empty($gofonts_opt)) {
			
				foreach ($gofonts_opt as $key => $option)
					$options[$key] = $option;

			}
			$merge_fonts = array( 'System fonts' => get_os_fonts() , 'Google fonts' => get_google_fonts() );
			// $os_fonts = get_os_fonts();
			// $google_fonts = get_google_fonts();
			?>
			
				<table style="width:100%;">
					<tr>
						<td>Select Font : </td>
						<td>
							<select name="array_key[font_name]" id="font_list">
							
							<?php
								//$post_types = get_post_types( '', 'names' );
								$ncf = true;
								foreach ($merge_fonts as $k => $v) {
									echo '<optgroup label="'.$k.'">';
									foreach($v as $k2 => $v2){
										if(strtolower($v2) == strtolower($options['font_name'])){
											echo '<option value="'.$v2.'" selected="selected">'.$v2.'</option>';
											$ncf=false;
										}else{
											echo '<option value="'.$v2.'">'.$v2.'</option>';
										}
									}
									echo '</optgroup>';
								}
							?>
							<optgroup label="Custom Fonts">
								<option value="custom" <?php if($ncf == true) echo 'selected="selected"'; ?>>Custom font</option>
							</optgroup>
							</select>
							<br>
							<div  id="input_cf" style="display:none;"><input type="text" class="text" name="array_key[cf]" placeholder="Font name" value="<?php	echo $options['cf'];?>" /><em> key sensitive</em><br>
							<em>Choose your font <a href="http://www.google.com/fonts" target="_blank">here</a>.</em></div>
						</td>
					</tr>
					<tr>
						<td>Font color: </td>
						<td>
							<input type="text" id="input_color" class="text my-color-field" name="array_key[color]" value="<?php	echo $options['color'];?>" data-default-color="#324154" />
						</td>
					</tr>
					<tr>
						<td>Font size: </td>
						<td>
							<input type="text" id="input_size" class="text" name="array_key[size]" value="<?php	echo $options['size'];?>" />
						</td>
					</tr>
					<tr>
						<td> </td>
						<td>
							<input type="checkbox" id="input_overrides" class="text" name="array_key[overrides]" <?php	if($options['overrides'] == "1"){ echo 'checked="checked"'; } ?> value="1" /> <label for="input_overrides">Set as !important:</label>
							<br><em>The !important rule overrides all property.</em>
						</td>
					</tr>
					<tr>
						<td>Target html tags: </td>
						<td>
							<textarea cols="60" rows="5" id="input_target" class="text textarea" name="array_key[target]"><?php	echo $options['target'];?></textarea>
							<br><em>list all html tags you want to be affected by this fonts,separated by comma(,).</em>
						</td>
					</tr>
					<tr>
						<td>Advance: </td>
						<td>
							<textarea cols="60" rows="8" id="input_custom_css" class="text textarea" name="array_key[custom]"><?php	echo $options['custom'];?></textarea>
							<br><em>Use this field for custom inline css.</em>
						</td>
					</tr>
				</table>
				<div class="message"><p><em></em></p></div>
				
			<?php
			//echo var_dump($gofonts_opt);
			
		}
		
	}

	$wp_j2gofonts = new wp_j2gofonts();
	
	
	

?>