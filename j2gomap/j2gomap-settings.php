<?php

/*
	*
	* settings:
	* j2goMap
	*
	*/
	
	class wp_j2gomap{
	
		var $mid = array();
	
		public function __construct(){
			if(is_admin()){
			add_action('admin_menu', array($this, 'add_plugin_page'));
			add_action('admin_init', array($this, 'page_init'));
			}
		}
		
		public function add_plugin_page(){
		
			// This page will be under "Settings"
			
			add_options_page('Settings Admin', 'j2gomap', 'manage_options', 'j2gomap-setting-admin', array($this, 'create_admin_page'));
		}

		public function create_admin_page(){
			?>
		<div class="wrap">
		
			<?php screen_icon(); ?>
			<h2>Your j2Gomap Settings</h2>			
			<form method="post" action="options.php">
				<?php
				
				// This prints out all hidden setting fields
				
				settings_fields('j2gomap_option_group');	
				do_settings_sections('j2gomap-setting-admin');
				
				?>
				<?php submit_button(); ?>
				
			</form>
			
		</div>
		<?php
		}
		
		public function page_init(){
		register_setting('j2gomap_option_group', 'array_key', array($this, 'check_ID'));
			
		add_settings_section(
		
			'setting_section_id',
			'Setting',
			array($this, 'print_section_info'),
			'j2gomap-setting-admin'
			
		);	
			
		add_settings_field(
		
			'wp_gomap_id', 
			' ', 
			array($this, 'create_wp_gomap_field'), 
			'j2gomap-setting-admin',
			'setting_section_id'
			
		);
		
		}
		
		public function check_ID($input = array()){
		
			if(!empty($_POST['array_key'])){
			
				$mid = $_POST['array_key'];
				if(get_option('wp_j2gomap_opts') === FALSE){
				
					add_option('wp_j2gomap_opts', $mid);
					
				}else{
				
					update_option('wp_j2gomap_opts', $mid);
					
				}
			
			}else{
			
				$mid = null;
			}
			
			
			return $mid;
			
		}
		
		public function print_section_info(){
		
		print 'Enter your settings below:';
		
		}
		
		
		public function create_wp_gomap_field(){
		
			$gomap_opt = get_option('wp_j2gomap_opts');
			if (!empty($gomap_opt)) {
			
				foreach ($gomap_opt as $key => $option)
					$options[$key] = $option;

			}

			?>
			
				<table style="width:100%;">					<tr>						<td>Marker icon url:</td>						<td>							<input type="text" id="input_api" class="text" name="array_key[marker_api]" value="<?php	echo $options['marker_api'];?>" /><br>							<em><span>Get your google map API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>.</span></em>						</td>					</tr>
					<tr>
						<td>Select post type : </td>
						<td>
							<select name="array_key[post_type]">
							<?php
								$post_types = get_post_types( '', 'names' );
								foreach ( $post_types as $post_type ) {
									if(($post_type != "attachment") && ($post_type != "revision") && ($post_type != "nav_menu_item")){
										echo '<option value="' . $post_type . '">' . $post_type . '</option>';
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Marker icon url:</td>
						<td>
							<input type="text" id="input_slug" class="text" name="array_key[marker_icon]" value="<?php	echo $options['marker_icon'];?>" /><br>
							<em><span>set your default marker icon on google map</span></em>
						</td>
					</tr>
					<tr>
						<td>Zoom:</td>
						<td>
							<input type="text" id="input_slug" class="int" name="array_key[zoom]" value="<?php	echo $options['zoom'];?>" /><br>
							<em><span>set your default zoom level on map</span></em>
						</td>
					</tr>
					<tr>
						<td>Map type :</td>
						<td>
							<select name="array_key[m_type]">
							<?php
								$map_option_list = array("ROADMAP","TERRAIN","SATELLITE","HYBRID");
								$ctr = count($map_option_list);
								for($i = 0; $i < $ctr; $i++){
									if($map_option_list[$i] == $options['m_type']){
										print '<option selected="selected" value="'.$map_option_list[$i].'">'.$map_option_list[$i].'</option>';
									}
									else{
										print '<option value="'.$map_option_list[$i].'">'.$map_option_list[$i].'</option>';
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Display as :</td>
						<td>
							<input type="radio" onclick="locked();" name="array_key[display_as]" id="popup" <?php	if($options['display_as'] == "popup"){ echo 'checked="checked"'; } ?>class="custom_field1" value="popup"><label for="popup">Popup</label> <br>
							<input type="radio" onclick="locked();" name="array_key[display_as]" id="single" <?php	if($options['display_as'] == "single"){ echo 'checked="checked"'; } ?> class="custom_field1" value="single"><label for="single">Single post</label><br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="array_key[ba_display]" <?php	if($options['ba_display'] == "before"){ echo 'checked="checked"'; } ?> id="before" value="before"> <label for="before">before post</label> <input type="radio" name="array_key[ba_display]" <?php	if($options['ba_display'] == "after"){ echo 'checked="checked"'; } ?> id="after" value="after"> <label for="after">after post</label>
							<br><input type="radio" onclick="locked();"name="array_key[display_as]" id="shortcode" <?php	if($options['display_as'] == "shortcode"){ echo 'checked="checked"'; } ?> class="custom_field1" value="shortcode"><label for="shortcode">Shortcode only</label>
						</td>
					</tr>
					<tr>
						<td>j2goMap Style</td>
					</tr>
					<tr>
						<td>Width</td>
						<td><input type="text" name="array_key[wcss]" class="int" value="<?php	echo $options['wcss'];?>"> <input type="radio" name="array_key[wtcss]" <?php	if($options['wtcss'] == "px"){ echo 'checked="checked"'; } ?> value="px">px <input type="radio" name="array_key[wtcss]" <?php	if($options['wtcss'] == "pt"){ echo 'checked="checked"'; } ?> value="pt">% </td>
					</tr>
					<tr>		
						<td>Height</td>
						<td><input type="text" name="array_key[hcss]" class="int" value="<?php	echo $options['hcss'];?>"> <input type="radio" name="array_key[htcss]" <?php	if($options['htcss'] == "px"){ echo 'checked="checked"'; } ?> value="px">px <input type="radio" name="array_key[htcss]" <?php	if($options['htcss'] == "pt"){ echo 'checked="checked"'; } ?> value="pt">% </td>
					</tr>
				</table>
				<div class="message"><p><em>Note: when using multiple shortcodes in page or post use unique marker_id value for each shortcode to avoid conflict.</em></p></div>
				<script>
					function locked(){
						if(document.getElementById("single").checked == false){
							document.getElementById("before").disabled = true;
							document.getElementById("after").disabled = true;
						}else{
							document.getElementById("before").disabled = false;
							document.getElementById("after").disabled = false;
						}
					}
					window.onload =locked();
					
				</script>
				
			<?php
			//echo var_dump($gomap_opt);
			
		}
		
	}

	$wp_j2gomap = new wp_j2gomap();
	
	
	

?>