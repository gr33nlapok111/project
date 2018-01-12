<?php

//[ATB]
function at_func( $atts ){
	$a = shortcode_atts( array(
        'pageid' => 'something'
    ), $atts );
	
	function getBrowser(){
		static $browser;
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$agent = $_SERVER['HTTP_USER_AGENT'];
		}
		if (strlen(strstr($agent, 'Firefox')) > 0) {
			$browser = 'firefox';
		}else{
			$browser = 'unknowed';
		}
		return $browser;
	}
	
	if(!empty($a['pageid'])){
		$pid = $a['pageid'];
		$values = get_post_custom( $pid );
		$atb_market = isset( $values['atb_market'] ) ? esc_attr( $values['atb_market'][0] ) : '';
		$atb_vendor = isset( $values['atb_vendor'] ) ? esc_attr( $values['atb_vendor'][0] ) : '';
		$atb_link = isset( $values['atb_link'] ) ? esc_attr( $values['atb_link'][0] ) : 'http://[affid].johndoe.hop.clickbank.net';
		$urls = isset( $values['imgid'] ) ? esc_attr( $values['imgid'][0] ) : '';
		
		if(isset($_POST['atb_id'])){	$fid = $_POST['atb_id']; $aid = $_POST['atb_id'];	}else{	$fid = "[affid]"; $aid = ''; }
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		if($atb_market == "clickbank"){	
			$ff =  'http://'.$fid.'.'.$atb_vendor.'.hop.clickbank.net';
		}else if($atb_market == "jvzoo"){
			$ff =  'http://jvzoo.com/c/'.$fid.'/'.$atb_vendor;
		}else{
			$ff = str_replace('[affid]',$fid,$atb_link);
		}
		//$surl = isset($_POST['surl'])?	$_POST['surl'] :	"#badTinyURLRequest";
		$paths = explode(',',$urls);
		
		ob_start();
?>
<?php
if(isset($_POST['atb_id'])){

	include_once dirname( __FILE__ ) . '/affiliate-tools-tiny-url.php';
	
	$tinyurl = fetchTinyUrl(''.$ff.'');
}else{
	$zdx = 'nfid';
}	?>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<?php
$at_opt_a = get_option("at_val");
	if (!empty($at_opt_a)) {
		foreach ($at_opt_a as $key => $option)
			$options[$key] = $option;
	}
?>
<style>
#affiliate-tool-wrap p{color:<?php echo empty($options['at_text_color'])? '#000' : $options['at_text_color']; ?>;}
.atb-thumbnails h2{color:<?php echo empty($options['at_text_color'])? '#000' : $options['at_text_color']; ?>;}
</style>

<?php	$ctr = 1;$ti = 1;	?>
<div id="affiliate-tool-wrap">
<p>Simply enter your affiliate ID into field below and click the "Submit" button to automatically brand all of the affiliate links, tools and banners below with your affiliate ID</p>
<form id="ufid" method="post" action="<?php	echo $pageURL; ?>">
<input type="text" name="atb_id" id="atb_id" value="<?php echo $aid; ?>" placeholder="Your Affiliate ID" /><input type="submit" value="Submit" /><div style="clear:both;"></div>
</form>

<div class="atb-thumbnails">
<h2>Facebook</h2>
<?php
	if(isset($_POST['atb_id'])){
		$fbt = 'target="_blank"';
		$fb = 'http://www.facebook.com/sharer.php?u='.$tinyurl;
	}else{
		$fbt = "";
		$fb = "javascript:void(0);";
	}
?>
<div class="fb-share-btn">
<a href="<?php echo $fb; ?>" <?php echo $fbt; ?> class="atb-btn <?php echo $zdx.'xx'; ?>">
<img src="<?php echo plugins_url( '/img/fb-share-btn.png', __FILE__ );	?>" alt="Facebook" />
</a>
</div>
<?php
//Emails start here
$esubject = get_post_meta( $pid, 'email_subject', true );
$einstruc = get_post_meta( $pid, 'email_instruc', true );
$econtent = get_post_meta( $pid, 'email_content', true );
$e_cnt = count($esubject);
if( ! empty( $esubject ) && $esubject[0] != "") { ?>
<h2>Emails <i class="aticker">open</i></h2>
<div class="lapok">
<?php	for($e = 0; $e < $e_cnt; $e++){	?>
	<div class="atb-thumbnail-link">
<?php
if(isset($_POST['atb_id'])){
$econ_tu = str_replace('[affiliate_link]',$tinyurl,$econtent[$e]);
}
$econ = str_replace('[affiliate_link]',$ff,$econtent[$e]);
?>
<textarea class="email-body copy-email<?php echo $e; ?>" wrap="off" readonly>
<?php	echo $einstruc[$e]; ?>


<?php	echo "SUBJECT";	?>

<?php	echo $esubject[$e];	?>


<?php	echo "BODY";	?>

<?php	echo $econ;	?>

</textarea>
<pre class="plain-email" style="display:none;">
<?php	echo $einstruc[$e]; ?>


<?php	echo "SUBJECT";	?>

<?php	echo $esubject[$e];	?>


<?php	echo "BODY";	?>

<?php	echo $econ;	?>
</pre>
<pre class="cloak-email" style="display:none;"><?php
if($tinyurl != ""){	?>
<?php	echo $einstruc[$e]; ?>


<?php	echo "SUBJECT";	?>

<?php	echo $esubject[$e];	?>


<?php	echo "BODY";	?>

<?php	echo $econ_tu;	?>
<?php	}	?>
</pre>
<div class="atb-mssg atb-email<?php echo $e; ?>" style="display:none;"><?php if(getBrowser() == "firefox"){	echo 'Hit CTRL + C to copy';}else{	echo 'Copied Successfully';} ?></div>
<a href="javascript:void(0);" class="atb-btn <?php echo $zdx.'xx'; ?> copy-email-btn<?php echo $e.' '.getBrowser(); ?>" >
<?php if(getBrowser() == "firefox"){	echo 'Select';}else{	echo 'Copy';} ?> Email
</a>
<a href="javascript:void(0);" class="atb-btn atb-email-convesion atb-plain-txt <?php echo $zdx.'xx'; ?>">Cloak link</a>
<?php if(isset($_POST['atb_id'])){	?>
<script type="text/javascript">
	var copyTextareaBtn = document.querySelector('.copy-email-btn<?php echo $e; ?>');

	copyTextareaBtn.addEventListener('click', function(event) {
	  var copyTextarea = document.querySelector('.copy-email<?php echo $e; ?>');
	  copyTextarea.select();

	  try {
		var successful = document.execCommand('copy');
		$(".atb-email<?php echo $e; ?>").fadeIn(300,function(){ $(this).fadeOut(2000); });
	  } catch (err) {
		console.log('Oops, unable to copy');
	  }
	});
</script>
<?php	}	?>
	</div>
<?php	} ?>
</div>
<?php }	?>



<?php 
//twitter start here
$atb_twt= get_post_meta( $pid, 'atb_twt', true );
if( ! empty( $atb_twt ) && $atb_twt[0] != "") { ?>
<h2>Twitter Tweets <i class="aticker">open</i></h2>
<div class="lapok">
<?php 
foreach($atb_twt as $twt){
?>
<?php if(!empty($twt)){	
$twt = str_replace('"','&quot;',$twt);
?>
<div class="atb-thumbnail-link tweet-content-text">
<textarea class="copy-twt<?php echo $ti; ?>" wrap="off" readonly>
<?php	echo $twt." ".$ff;	?>
</textarea>
	<?php
		if(isset($_POST['atb_id'])){
			$clntxt = str_replace(" ","+",$twt);
			$twtpath = "https://twitter.com/intent/tweet?url=".$ff."&text=".$clntxt."&hashtags=";
			$twtcloak = "https://twitter.com/intent/tweet?url=".$tinyurl."&text=".$clntxt."&hashtags=";
			$target = 'target="_blank"';
		}else{
			$twtpath = "javascript:void(0);";
			$target = "";
		}
	?>
<div class="atb-mssg atb-mssg-ti<?php echo $ti; ?>" style="display:none;"><?php if(getBrowser() == "firefox"){	echo 'Hit CTRL + C to copy';}else{	echo 'Copied Successfully';} ?></div>
<a href="javascript:void(0);" class="atb-btn <?php echo $zdx.'xx'; ?> copy-twt-btn<?php echo $ti.' '.getBrowser(); ?>"><?php if(getBrowser() == "firefox"){	echo 'Select';}else{	echo 'Copy';} ?> Tweet</a>
<a href="<?php echo $twtpath; ?>" class="atb-btn atb-twt-now <?php echo $zdx.'xx'; ?>" <?php echo $target;?> link1="<?php echo $twtpath; ?>" link2="<?php echo $twtcloak; ?>">Tweet Now</a>
<a href="javascript:void(0);" class="atb-btn atb-text-convesion atb-plain-txt <?php echo $zdx.'xx'; ?>" data-plain="<?php	echo $twt." ".$ff;	?>" data-cloak="<?php	echo $twt." ".$tinyurl;	?>" >Cloak link</a>
<?php if(isset($_POST['atb_id'])){	?>
<script type="text/javascript">
	var copyTextareaBtn = document.querySelector('.copy-twt-btn<?php echo $ti; ?>');

	copyTextareaBtn.addEventListener('click', function(event) {
	  var copyTextarea = document.querySelector('.copy-twt<?php echo $ti; ?>');
	  copyTextarea.select();

	  try {
		var successful = document.execCommand('copy');
		$(".atb-mssg-ti<?php echo $ti; ?>").fadeIn(300,function(){ $(this).fadeOut(2000); });
	  } catch (err) {
		console.log('Oops, unable to copy');
	  }
	});
</script>
<?php	}	?>
	</div>
<?php	}	?>
<?php	$ti++; } ?>
</div>
<?php }	?>

<?php 
//Affiliate banners start here
if(!empty($paths[0])){	?>
<h2>Affiliate Banners <i class="aticker">open</i></h2>
<div class="lapok">
<?php foreach($paths as $path){	?>
<?php $clean_url = preg_replace( "/\r|\n/", "", $path );?>
<img src="<?php	echo $clean_url; ?>" class="atb-thumbnail-img" />
<div class="atb-thumbnail-link affiliate-content-text">
<textarea class="copy-fid<?php echo $ctr; ?>" wrap="off" readonly>&lt;a href="<?php echo $ff; ?>"&gt;&lt;img src="<?php echo $clean_url; ?>" target="_blank" border="0" /&gt;&lt;/a&gt;</textarea>
<div class="atb-mssg atb-mssg-i<?php echo $ctr; ?>" style="display:none;"><?php if(getBrowser() == "firefox"){	echo 'Hit CTRL + C to copy';}else{	echo 'Copied Successfully';} ?></div>
<a href="javascript:void(0);" class="atb-btn <?php echo $zdx.'xx'; ?> copy-fid-btn<?php echo $ctr.' '.getBrowser(); ?>" ><?php if(getBrowser() == "firefox"){	echo 'Select';}else{	echo 'Copy';} ?> Embed Code</a>
<a href="javascript:void(0);" <?php echo $goodAPI; ?> class="atb-btn atb-text-convesion atb-plain-txt <?php echo $zdx.'xx'; ?>" data-plain='&lt;a href="<?php echo $ff; ?>"&gt;&lt;img src="<?php echo $clean_url; ?>" target="_blank" border="0" /&gt;&lt;/a&gt;' data-cloak='&lt;a href="<?php echo $tinyurl; ?>"&gt;&lt;img src="<?php echo $clean_url; ?>" target="_blank" border="0" /&gt;&lt;/a&gt;'>Cloak link</a>
<div class="clr"></div>
<?php if(isset($_POST['atb_id'])){	?>
<script type="text/javascript">
	var copyTextareaBtn = document.querySelector('.copy-fid-btn<?php echo $ctr; ?>');

	copyTextareaBtn.addEventListener('click', function(event) {
	  var copyTextarea = document.querySelector('.copy-fid<?php echo $ctr; ?>');
	  copyTextarea.select();

	  try {
		var successful = document.execCommand('copy');
		$(".atb-mssg-i<?php echo $ctr; ?>").fadeIn(300,function(){ $(this).fadeOut(2000); });
	  } catch (err) {
		console.log('Oops, unable to copy');
	  }
	});
</script>
<?php }	?>
</div>
<?php	$ctr++;	}	?>
</div>
<?php }	?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		
		var allPanels = $('.atb-thumbnails > .lapok').hide();
		
	  $('.atb-thumbnails > h2 > i').click(function() {
		if($(this).parent().next().css("display") == 'none'){
			allPanels.slideUp();
			$(this).parent().next().slideDown();
			$('.atb-thumbnail-link textarea').each(function() {
				var txtheight = null;
				txtheight = this.scrollHeight;
				$(this).height(txtheight);
			});
		}		
		return false;
	  });
		
		$('.atb-thumbnail-link textarea').each(function() {
			var txtheight = null;
			txtheight = this.scrollHeight;
			$(this).height(txtheight);
		});
		
		$('.atb-btn.firefox').click(function(){
		if($(this).hasClass("nfidxx") == false){
		$(this).siblings(".atb-mssg").fadeIn(300,function(){ $(this).fadeOut(10000); });
		}
		});
		
		$(".atb-btn").click(function(e){	if($(this).hasClass("nfidxx") == true){ $("#atb_id").addClass("error"); $("html, body").animate({ scrollTop: $('#atb_id').offset().top-150 }, 1000);	}});
		var cpath,twtnow;
		$('body').on('click', '.atb-text-convesion', function(event) {
			if($(this).hasClass("nfidxx") == false){
				if($(this).hasClass( "atb-cloak-txt" ) == true){
					$(this).text("Plain URL");
					$(this).removeClass('atb-cloak-txt').addClass('atb-plain-txt');
					cpath = $(this).attr("data-plain");
					$(this).siblings('textarea').text(cpath);
					
					twtnow = $(this).siblings('.atb-twt-now').attr("link1");
					$(this).siblings('.atb-twt-now').attr("href",twtnow);
				}else{
					$(this).text("Cloak URL");
					$(this).removeClass('atb-plain-txt').addClass('atb-cloak-txt');
					cpath = $(this).attr("data-cloak");		
					$(this).siblings('textarea').text(cpath);
					
					twtnow = $(this).siblings('.atb-twt-now').attr("link2");
					$(this).siblings('.atb-twt-now').attr("href",twtnow);
				}
				event.preventDefault();
			}
		});
		$('body').on('click', '.atb-email-convesion', function(event) {
			if($(this).hasClass("nfidxx") == false){
				if($(this).hasClass( "atb-cloak-txt" ) == true){
					$(this).text("Plain URL");
					$(this).removeClass('atb-cloak-txt').addClass('atb-plain-txt');
					cpath = $(this).siblings(".plain-email").html();
					$(this).siblings('textarea.email-body').html(cpath);
				}else{
					$(this).text("Cloak URL");
					$(this).removeClass('atb-plain-txt').addClass('atb-cloak-txt');
					cpath = $(this).siblings(".cloak-email").html();
					$(this).siblings('textarea.email-body').html(cpath);
				}
				event.preventDefault();
			}
		});
	});
</script>
</div>
	<?php
		$output_string = ob_get_contents();
		ob_end_clean();
	}else{
		$output_string = '<p class="AF_error">Something is wrong, Please contact the administrator.</p>';
	}
	return $output_string;
}
add_shortcode( 'ATB', 'at_func' );