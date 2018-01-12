<?php

	add_action( 'add_meta_boxes', 'j2gomap_meta_box_add' );
	function j2gomap_meta_box_add()
	{
		add_meta_box( 'j2gomap-meta-box-id', 'j2Gomap Meta Box', 'j2gomap_meta_box_cb', 'post', 'normal', 'high' );
	}
	function j2gomap_meta_box_cb($post)
	{
		$values = get_post_custom( $post->ID );
		$address = isset( $values['j2gomap_address'] ) ? esc_attr( $values['j2gomap_address'][0] ) : '';
		$zoom = isset( $values['j2gomap_zoom'] ) ? esc_attr( $values['j2gomap_zoom'][0] ) : '14';
		$markers = get_post_meta($post->ID,"j2gomap",true);
		print_r($markers);
		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'wp_gomap_nonce', 'gomap_nonce' );
	?>
			<div class="j2gomap_admin_fields">
				<div>
					<p>
						<label for="j2gomap_address"  class="txt">Address</label>
						<input type="text" name="j2gomap_address" id="j2gomap_address" value="<?php echo $address; ?>" />
					</p>
					<p>
						<label for="j2gomap_zoom"  class="txt">Zoom</label>
						<input type="text" name="j2gomap_zoom" id="j2gomap_zoom" value="<?php echo $zoom; ?>" />
					</p>
				</div>
				
				<?php
				$cnt = 0;
				if($markers){
				foreach ($markers['key'] as $marker => $v) {
				?>
					<div class="repeater green">
					<a href="javascript:void(0);" class="rmv"><span class="dashicons dashicons-no"></span></a>
					<div class="selecto-pokemon"><a href="javascript:void(0);" class="getpokemon" data="midx<?php echo $cnt; ?>">Pokemon</a></div>
					<div>
						<img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/pokemons/<?php echo $v['y']; ?>.png" class="pokesrc" />
						<input type="hidden" class="pokemon midx<?php echo $cnt; ?>" value="<?php echo $v['y']; ?>" name="j2gomap[key][<?php echo $cnt; ?>][y]" />
						<p>
							<label for="j2gomap_marker" class="txt">Marker</label>
							<input type="text" name="j2gomap[key][<?php echo $cnt; ?>][a]" id="j2gomap_marker" value="<?php echo $v['a']; ?>" /><br/>
							<em><span>if empty address will be use as marker</span></em>
						</p>
					</div>
					<div>
						<p>
							<label for="marker_desc">Marker description</label><br>
							<textarea name="j2gomap[key][<?php echo $cnt; ?>][b]" id="marker_desc" rows="4" ><?php echo $v['b']; ?></textarea>
						</p>
					</div>
					</div>
				<?php
				$cnt++;
				}
				}else{	?>
				
				<div class="repeater green">
				<a href="javascript:void(0);" class="rmv"><span class="dashicons dashicons-no"></span></a>
				<div class="selecto-pokemon"><a href="javascript:void(0);" class="getpokemon" data="midx<?php echo $cnt; ?>">Pokemon</a></div>
				<div>
						<img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/pokemons/pokemon.png" class="pokesrc" />
						<input type="hidden" class="pokemon midx<?php echo $cnt; ?>" value="" name="j2gomap[key][0][y]" />
					<p>
						<label for="j2gomap_marker" class="txt">Marker</label>
						<input type="text" name="j2gomap[key][0][a]" id="j2gomap_marker" value="" /><br/>
						<em><span>if empty address will be use as marker</span></em>
					</p>
				</div>
				<div>
					<p>
						<label for="marker_desc">Marker description</label><br>
						<textarea name="j2gomap[key][0][b]" id="marker_desc" rows="4" ></textarea>
					</p>
				</div>
				</div>
				<?php	}	?>

			</div>
			<div id="newMarker"><a href="javascript:void(0);" class="button button-primary button-large">Add Marker</a></div>
			<div class="pokedex" style="display:none;">
			<div class="filter"><input type="text" class="filter-pokemon" placeholder="Search by pokemon ID" /></div>
			<ul>
			<?php
			$pokepath = plugin_dir_url( __FILE__ )."/img/pokemons/";
			$pdex_dir = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/j2gomap/img/pokemons";
			$pokemons = array_diff(scandir($pdex_dir), array('pokeball.png', '..', '.'));
			foreach($pokemons as &$pokemon){
				$pokemon_id = str_replace('.png','',$pokemon);
				echo "<li style='float:left;'><a href='javascript:void(0);' pokemon-id='".$pokemon_id."' pokesrc='".$pokepath.$pokemon."'><img src='".$pokepath.$pokemon."'></a></li>";
				}				?>				</ul>			</div>
			<script>
					jQuery('body').on('click', '#newMarker a', function(event) {
						var idx = jQuery('.green').length;
						var clone = '<a href="javascript:void(0);" class="rmv"><span class="dashicons dashicons-no"></span></a><div class="selecto-pokemon"><a href="javascript:void(0);" class="getpokemon" data="midx'+ idx +'" >Pokemon</a></div><div><input type="hidden" class="pokemon midx'+ idx +'" name="j2gomap[key][0][y]" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/pokemons/pokeball.png" class="pokesrc" /><p><label for="j2gomap_marker" class="txt">Marker</label><input type="text" name="j2gomap[key]['+ idx +'][a]" id="j2gomap_marker" value="" /><br/><em><span>if empty address will be use as marker</span></em></p></div><div><p><label for="marker_desc">Marker description</label><br><textarea name="j2gomap[key]['+ idx +'][b]" id="marker_desc" rows="4" ></textarea></p></div>';
						jQuery('.j2gomap_admin_fields').append( "<div class='new green'>" + clone + "</div>");
					});
					
					jQuery('body').on('click', '.rmv', function(event) {
						var ng = jQuery('.green').length;
						if(ng > 1){
						jQuery(this).parents('.green').remove();
						}
					});
					jQuery('body').on('click', '.getpokemon', function(event) {
						jQuery('.pokedex').show();
						jQuery('.pokedex').attr("data",jQuery(this).attr("data"));
					});
					jQuery('body').on('click', '.pokedex a', function() {
						var field = jQuery(".pokedex").attr("data");
						var pokemon = jQuery(this).attr("pokemon-id");
						var pokesrc = jQuery(this).attr("pokesrc");
						//alert(field);
						jQuery("."+field).val(pokemon);
						jQuery("."+field).siblings('.pokesrc').attr("src",pokesrc);
						jQuery('.pokedex').fadeOut(200);
					});
			</script>
	<?php
	}
	
	
	add_action( 'save_post', 'gomap_meta_box_save' );
	function gomap_meta_box_save( $post_id ){
		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		 
		// if our nonce isn't there, or we can't verify it, bail
		if( !isset( $_POST['gomap_nonce'] ) || !wp_verify_nonce( $_POST['gomap_nonce'], 'wp_gomap_nonce' ) ) return;
		 
		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' ) ) return;
		 
		// now we can actually save the data
		$allowed = array( 
			'a' => array( // on allow a tags
				'href' => array() // and those anchors can only have href attribute
			)
		);
		 
		// Make sure your data is set before trying to save it
		if( isset( $_POST['j2gomap_address'] ) )
			update_post_meta( $post_id, 'j2gomap_address', wp_kses( $_POST['j2gomap_address'], $allowed ) );
		
		if( isset( $_POST['j2gomap_zoom'] ) )
			update_post_meta( $post_id, 'j2gomap_zoom', $_POST['j2gomap_zoom'] );
		
		if( isset( $_POST['j2gomap'] ) )
			update_post_meta( $post_id, 'j2gomap', $_POST['j2gomap'] );
		
	}
?>