<?php

	add_action( 'add_meta_boxes', 'atb_meta_box_add' );
	function atb_meta_box_add()
	{
		add_meta_box('atb_meta_box_cb', 'ATB Meta Box', 'atb_meta_box_cb', 'affiliate', 'normal', 'default');
	}
	function atb_meta_box_cb($post)
	{
		$values = get_post_custom( $post->ID );
		$atb_name = isset( $values['atb_name'] ) ? esc_attr( $values['atb_name'][0] ) : '';
		$atb_market = isset( $values['atb_market'] ) ? esc_attr( $values['atb_market'][0] ) : '';
		$atb_vendor = isset( $values['atb_vendor'] ) ? esc_attr( $values['atb_vendor'][0] ) : '';
		$atb_link = isset( $values['atb_link'] ) ? esc_attr( $values['atb_link'][0] ) : 'http://[affid].johndoe.hop.clickbank.net';
		
		$e_subject = get_post_meta( $post->ID, 'email_subject', true );
		$e_instruc = get_post_meta( $post->ID, 'email_instruc', true );
		$e_content= get_post_meta( $post->ID, 'email_content', true );
		
		$atb_twt= get_post_meta( $post->ID, 'atb_twt', true );
		
		$imgid = isset( $values['imgid'] ) ? esc_attr( $values['imgid'][0] ) : '';
		
		$mpa = array("ClickBank","JVZoo","Custom");
		
		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'wp_atb_nonce', 'atb_nonce' );
	?>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
			<table class="atb_admin_fields">
				<tr>
					<td>
						<p>
							<label for="atb_name">Project Name</label>
							<input type="text" name="atb_name" required value="<?php echo $atb_name; ?>" id="atb_name" class="widefat" placeholder="My Project Name" /><br>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<p>
							<label for="atb_market">Choose Your Affiliate Marketplace</label>
							<select name="atb_market" id="atb_market" class="widefat">
								<?php
								foreach($mpa as $mv){ 
									if($atb_market == strtolower($mv)){
										echo '<option  selected="selected" value="'.strtolower($mv).'">'.$mv.'</option>';
									}else{
										echo '<option value="'.strtolower($mv).'">'.$mv.'</option>';
									}
									
								}?>								
							</select><br>
						</p>
					</td>
				</tr>
				<tr class="trlinks">
					<td>
						<p>
							<label for="atb_vendor">Enter Your Vendor ID / Seller ID / Product ID</label>
							<input type="text" name="atb_vendor" required value="<?php echo $atb_vendor; ?>" id="atb_vendor" class="widefat" placeholder="VendorID / SellerID / ProductID" /><br>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<p>
							<label for="atb_link">Affiliate Link Preview<span class="help"><em>Your affiliates will be able to rebrand any link that you provide with their own unique affiliate code. Simple insert the tag[affid] where you would like the affliate's ID to be substitute in your links.<br>Example : http://your-site.com?product=12&ref=[affid]&tid=mylink</em></span></label>
							<input type="text" name="atb_link" required value="<?php echo $atb_link; ?>" id="atb_link" class="widefat" placeholder="http://[affid].johndoe.hop.clickbank.net" /><br>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<div class="hdr-separator"><h4>Emails</h4></div>
						<p class="email-desc">Simply use the "Insert Link" button to automatically add your brandible affiliate link into your emails (wherever you'd like the affiliate link to appear).</p>
						
						<div class="email-wrap">
							
								<?php
									$ecnt = count($e_subject);
									
								if($e_subject){
									for($i = 0; $i < $ecnt; $i++){
								?>
									<div class="email-item">
										<input type="text" name="email_subject[]" value="<?php echo $e_subject[$i]; ?>" id="email_subject" class="widefat" placeholder="Subject title" /><br><br>
										<input type="text" name="email_instruc[]" value="<?php echo $e_instruc[$i]; ?>" id="email_instruc" class="widefat" placeholder="Special Instructions. OPTIONAL. (Example: Email #1,Send 2 hours before launch.)" /><br><br>
										<textarea name="email_content[]" rows="6" id="txtarea<?php echo $i; ?>"><?php echo $e_content[$i];	?></textarea><a href="#" class="x-twt">remove</a>
										<a href="javascript:void(0);" class="itoken" data="txtarea<?php echo $i; ?>" title="Insert affiliate link to your email content">insert link</a>
									</div>
								<?php	}
								}else{?>
									<div class="email-item">
										<input type="text" name="email_subject[]" value="" id="email_subject" class="widefat" placeholder="Subject title" /><br><br>
										<input type="text" name="email_instruc[]" value="" id="email_instruc" class="widefat" placeholder="Special Instructions. OPTIONAL. (Example: Email #1,Send 2 hours before launch.)" /><br><br>
										<textarea name="email_content[]" rows="6" id="txtarea<?php echo $i; ?>"></textarea><a href="#" class="x-twt">remove</a>
										<a href="javascript:void(0);" class="itoken" data="txtarea<?php echo $i; ?>" title="Insert affiliate link to your email content">Insert Link</a>
									</div>
								<?php	}	?>
								
							
						</div>
						<div class="email-action"><a href="#" class="btn">Add Email</a></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="hdr-separator"><h4>Twitter Tweets</h4></div>
						<p class="twt-desc">Just add your tweet text. Your link will automatically be added at the end.(Max: 100 characters per tweet)</p>
						<div class="twt-wrap">
							<?php
								if($atb_twt){
									//echo var_dump($atb_twt);
									foreach ($atb_twt as $twtv) {
										if(!empty($twtv)){
							?>
							
									<div class="twt-item"><textarea name="atb_twt[]"><?php echo $twtv;	?></textarea><a href="#" class="x-twt">remove</a></div>
						
							<?php		}else{
											echo '<div class="twt-item"><textarea name="atb_twt[]"></textarea><a href="#" class="x-twt">remove</a></div>';
										}
									}?>
							
							<?php	}else{	?>
								<div class="twt-item"><textarea name="atb_twt[]"></textarea><a href="#" class="x-twt">remove</a></div>
							<?php	}?>
							
						</div>
						<div class="twt-action"><a href="#" class="btn">Add Tweet</a></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="hdr-separator"><h4>Affliate Banners</h4></div>
						<p>
							<?php  
							// adjust values here
							$id = "imgid";
							$svalue = $imgid;
							$multiple = true;
							$width = null;
							$height = null;
							?>
				
							<div class="atb_thumbnails" id="imgiddrag-drop-area" class="imgiddrag-drop-area">
							
									<label for="imgidplupload-browse-button">Drop files here<br><span>or</span></label>
									<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />  
									<div class="plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-upload-ui">  
										<input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" />
										<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
										<?php if ($width && $height): ?>
												<span class="plupload-resize"></span><span class="plupload-width" id="plupload-width<?php echo $width; ?>"></span>
												<span class="plupload-height" id="plupload-height<?php echo $height; ?>"></span>
										<?php endif; ?>
										<div class="filelist"></div>
									</div>
							
							</div>
							<div class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-thumbs">  
							</div>  
							<div class="clear"></div>
						</p>
					</td>
				</tr>
			</table>
<script>
	var lp;
	var vv;
	jQuery("body").on( "change", "#atb_vendor", function() {
	
		if(jQuery("#atb_market option:selected").text() == "ClickBank"){
			if(jQuery(this).val() != ""){	vv = jQuery(this).val();	}else{	vv="vendorID";	}
			lp = "http://[affid]." + vv + ".hop.clickbank.net";
			jQuery("#atb_link").val(lp);
		}else if(jQuery("#atb_market option:selected").text() == "JVZoo"){
			if(jQuery(this).val() != ""){	vv = jQuery(this).val();	}else{	vv="vendorID";	}
			lp = "http://jvzoo.com/c/[affid]/" + vv;
			jQuery("#atb_link").val(lp);
		}
	
	});
				
	jQuery(document).ready(function(){
	if(jQuery("#atb_market option:selected").text() == "Custom"){
		jQuery('#atb_vendor').removeAttr('required');
		jQuery('tr.trlinks p').hide();
	}
	});
	var ms;
	jQuery("#atb_market").change(function(){
	ms = jQuery("#atb_market option:selected").text();
	if(ms == "Custom"){
		jQuery('tr.trlinks p').slideUp();
		jQuery("#atb_link").attr("placeholder","Enter your custom link");
		jQuery("#atb_link").val("");
		jQuery('#atb_vendor').removeAttr('required');
	}else if(ms == "ClickBank"){
		if(jQuery("#atb_vendor").val() != ""){	vv = jQuery("#atb_vendor").val();	}else{	vv="vendorID";	}
		lp = "http://[affid]." + vv + ".hop.clickbank.net";
		jQuery("#atb_link").val(lp);
		jQuery('tr.trlinks p').slideDown();
	}else if(ms == "JVZoo"){
		if(jQuery("#atb_vendor").val() != ""){	vv = jQuery("#atb_vendor").val();	}else{	vv="vendorID";	}
		lp = "http://jvzoo.com/c/[affid]/" + vv;
		jQuery("#atb_link").val(lp);
		jQuery('tr.trlinks p').slideDown();
	}
	});
	jQuery("#atb_name").change(function(){
	var atbn = jQuery(this).val();
	jQuery("#titlewrap #title").val(atbn);
	});
	
	// adding and removing email items
	jQuery('body').on('click', '.email-action .btn', function(event) {
		event.preventDefault();
		var eItems = jQuery('.email-item').length + 1;
		var clonetwt = '<div class="email-item"><input type="text" name="email_subject[]" value="" id="email_subject" class="widefat" placeholder="Subject title" /><br><br><input type="text" name="email_instruc[]" value="" id="email_instruc" class="widefat" placeholder="Special Instructions. OPTIONAL. (Example: Email #1,Send 2 hours before launch.)" /><br><br><textarea name="email_content[]" rows="6" id="txtarea'+ eItems + '"></textarea><a href="#" class="x-twt">remove</a><a href="javascript:void(0);" class="itoken" data="txtarea'+ eItems + '" title="Insert affiliate link to your email content">insert link</a></div>';
		jQuery(".email-wrap").append(clonetwt);

		$(".twtbackup").remove();
	});
	jQuery('body').on('click', '.email-item a.x-twt', function(event) {
	event.preventDefault();

	var idx = jQuery(".email-item").length;
	if(idx <= 1){
		jQuery(".email-wrap").append('<input type="hidden" name="email_subject[] rows="6"" class="twtbackup" value="" /><input type="hidden" name="email_instruc[] rows="6"" class="twtbackup" value="" /><input type="hidden" name="email_content[] rows="6"" class="twtbackup" value="" />');
	}
	jQuery(this).parents(".email-item").remove();
	});
	
	// adding and removing twitter items
	jQuery('body').on('click', '.twt-action .btn', function(event) {
		event.preventDefault();
		var clonetwt = '<div class="twt-item"><textarea name="atb_twt[]"></textarea><a href="#" class="x-twt">remove</a></div>';
		jQuery(".twt-wrap").append(clonetwt);

		jQuery(".twtbackup").remove();
	});
	jQuery('body').on('click', '.twt-item a.x-twt', function(event) {
	event.preventDefault();

	var idx = jQuery(".twt-item").length;
	if(idx <= 1){
		jQuery(".twt-wrap").append('<input type="hidden" name="atb_twt[]" class="twtbackup" value="" />');
	}
	jQuery(this).parents(".twt-item").remove();
	});
	
	jQuery('body').on('click', '.itoken', function() {
		var txtid = jQuery(this).attr("data");
		insertAtCaret(txtid, "[affiliate_link]")
	});
	function insertAtCaret(txtid, text) {
			var txtarea = document.getElementById(txtid);
			var scrollPos = txtarea.scrollTop;
			var caretPos = txtarea.selectionStart;

			var front = (txtarea.value).substring(0, caretPos);
			var back = (txtarea.value).substring(txtarea.selectionEnd, txtarea.value.length);
			txtarea.value = front + text + back;
			caretPos = caretPos + text.length;
			txtarea.selectionStart = caretPos;
			txtarea.selectionEnd = caretPos;
			txtarea.focus();
			txtarea.scrollTop = scrollPos;
	}
</script>
	<?php
	}
	
	
	add_action( 'save_post', 'atb_meta_box_save' );
	function atb_meta_box_save( $post_id ){
		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		 
		// if our nonce isn't there, or we can't verify it, bail
		if( !isset( $_POST['atb_nonce'] ) || !wp_verify_nonce( $_POST['atb_nonce'], 'wp_atb_nonce' ) ) return;
		 
		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' ) ) return;
		 
		// now we can actually save the data
		$allowed = array( 
			'a' => array( // on allow a tags
				'href' => array() // and those anchors can only have href attribute
			)
		);
		
		if(isset($_POST['atb_name'])){	$atb_name = $_POST['atb_name'];	}else{	$atb_name = " ";	}
		if(isset($_POST['atb_market'])){	$atb_market = $_POST['atb_market'];	}else{	$atb_name = " ";	}
		if(isset($_POST['atb_vendor'])){	$atb_vendor = $_POST['atb_vendor'];	}else{	$atb_name = " ";	}
		if(isset($_POST['atb_link'])){	$atb_link = $_POST['atb_link'];	}else{	$atb_name = " ";	}
		if(isset($_POST['atb_twt'])){	$atb_twt = $_POST['atb_twt'];	}else{	$atb_name = " ";	}
		
		if(isset($_POST['email_subject'])){	$email_subject = $_POST['email_subject'];	}else{	$email_subject = " ";	}
		if(isset($_POST['email_instruc'])){	$email_instruc = $_POST['email_instruc'];	}else{	$email_instruc = " ";	}
		if(isset($_POST['email_content'])){	$email_content = $_POST['email_content'];	}else{	$email_content = " ";	}
		 
		// Make sure your data is set before trying to save it
		if( isset( $_POST['atb_name'] ) )
			update_post_meta( $post_id, 'atb_name', wp_kses( $atb_name, $allowed ) );
			
		if( isset( $_POST['atb_market'] ) )
			update_post_meta( $post_id, 'atb_market', wp_kses( $atb_market, $allowed ) );
			
		if( isset( $_POST['atb_vendor'] ) )
			update_post_meta( $post_id, 'atb_vendor', wp_kses( $atb_vendor, $allowed ) );
			
		if( isset( $_POST['atb_link'] ) )
			update_post_meta( $post_id, 'atb_link', wp_kses( $atb_link, $allowed ) );
			
		if( isset( $_POST['atb_twt'] ) )
			update_post_meta( $post_id, 'atb_twt', wp_kses( $atb_twt, $allowed ) );
		
		if( isset( $_POST['email_subject'] ) )
			update_post_meta( $post_id, 'email_subject', wp_kses( $email_subject, $allowed ) );
			
		if( isset( $_POST['email_instruc'] ) )
			update_post_meta( $post_id, 'email_instruc', wp_kses( $email_instruc, $allowed ) );
			
		if( isset( $_POST['email_content'] ) )
			update_post_meta( $post_id, 'email_content', wp_kses( $email_content, $allowed ) );
			
		if( isset( $_POST['imgid'] ) )
			update_post_meta( $post_id, 'imgid', wp_kses( $_POST['imgid'], $allowed ) );
		
	}
?>