<?php
/*
Plugin Name: WooCommerce Advance Filter
Plugin URI: 
Description: advance filter for your woocommerce product with on sale bulk functionality 
Version: 1.0
Author: SkyRockIT
Author URI: skyrockit.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

function load_custom_wp_admin_style() {
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'jquery-ui-css' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


add_action( 'admin_menu', 'advance_filter_menu', 99 );

function advance_filter_menu() {
	add_submenu_page( 'woocommerce', 'Advance Filter', 'Advance Filter', 'manage_options', 'wc-advance-filter', 'wc_advance_filter' );
}

function wc_advance_filter(){

	$pstock = isset($_GET['stock'])? $_GET['stock'] : '';
	$per_page = isset($_GET['perpage'])? $_GET['perpage'] : 20;
	$order = isset($_GET['orderby'])? $_GET['orderby'] : 'ASC';
	$paged = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
	$p_paged = ($paged-0);
	$n_paged = ($paged+1);
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var dateToday = new Date();
		$('.onsale_date').datepicker({
		dateFormat : 'yy-mm-dd',
		minDate: dateToday
		});
			
		jQuery("#select_all").change(function () {
			$("#put_on_sale table td input:checkbox").prop('checked', jQuery(this).prop("checked"));
		});
		
		
	});

   jQuery(window).scroll(function(){
		var sh = jQuery(this).scrollTop();
		var dh = jQuery(document).height() - 1000;
		if(sh > dh){
			jQuery('.actions').css({'position':'relative','bottom':'0px'});
		}else{
			jQuery('.actions').css({'position':'fixed','bottom':'30px'});
		}
	})

	</script>
	<div id="waf-wrap">
	<h2>WooCommerce Advance Filter</h2>
	
	<?php
	
	if(isset($_POST['action'])){
		
		$pids = $_POST['fpos'];
		$sprice = $_POST['sale_price'];
		$date_from = strtotime( $_POST['sale_start'], current_time( 'timestamp' ));
		$date_to = strtotime( $_POST['sale_end'], current_time( 'timestamp' ));
		
		foreach ($pids as &$pid) {
			
			update_post_meta( $pid, '_sale_price_dates_from', $date_from );
			update_post_meta( $pid, '_sale_price_dates_to', $date_to );
			update_post_meta($pid,'_sale_price',$sprice);
			
		}
		
		echo '<div id="message" class="updated notice notice-success"><p>Product(s) successfully put on sale from <strong>'.$_POST['sale_start'].'</strong> to <strong>'.$_POST['sale_end'].'</strong>.</p></div>';
		
	}
	?>
	
	<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="get" class="onsale_filter">
		<input type="hidden" name="page" value="wc-advance-filter">
		<div class="item-fields">
			<select name="stock">							<option value="" >Select</option>				
				<option value="instock" <?php selected( $pstock, 'instock' ); ?> >Stock</option>
				<option value="outofstock" <?php selected( $pstock, 'outofstock' ); ?>>Out Stock</option>
			</select>
		</div>
		<div class="item-fields">
			<select name="perpage">
				<option value="10" <?php selected( $per_page, 10 ); ?>>10 per page</option>
				<option value="20" <?php selected( $per_page, 20 ); ?>>20 per page</option>
				<option value="30" <?php selected( $per_page, 30 ); ?>>30 per page</option>
				<option value="50" <?php selected( $per_page, 50 ); ?>>50 per page</option>
				<option value="100" <?php selected( $per_page, 100 ); ?>>100 per page</option>
			</select>
		</div>
		<div class="item-fields">
			<select name="orderby">
				<option value="ASC" <?php selected( $order, 'ASC' ); ?>>ASC</option>
				<option value="DESC" <?php selected( $order, 'DESC' ); ?>>DESC</option>
			</select>
		</div>
		<input type="submit" value="Filter" name="filter" />
		<div style="clear:both;"></div>
	</form>
	
	<?php
		$post_type = 'product';
		$post_status = 'publish';
		if ($pstock != '')
		{
			$waf_query = new WP_Query(array('post_type' => $post_type, 'post_status' => $post_status, 'posts_per_page' => $per_page, 'orderby' => 'date', 'order' => $order, 'paged' => $paged, 'meta_query' => array(array('key' => '_stock_status', 'value' => $pstock, 'compare' => '='), array('key' => '_sale_price_dates_from', 'value' => '', 'compare' => '=='))));
		
		}
		else
		{
			$waf_query = new WP_Query(array('post_type' => $post_type, 'post_status' => $post_status, 'posts_per_page' => $per_page, 'orderby' => 'date', 'order' => $order, 'paged' => $paged, 'meta_query' => array(array('key' => '_sale_price_dates_from', 'value' => '', 'compare' => '=='))));
		
		}
	?>
	<div class="top-nav tablenav-pages">
		<span class="pagination-links">
		<a class="prev-page" href="<?php echo $_SERVER["REQUEST_URI"].'&paged='.$p_paged; ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
		<a class="next-page" href="<?php echo $_SERVER["REQUEST_URI"].'&paged='.$n_paged; ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
		</span>
	</div>
	<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" id="put_on_sale" class="products-list">
	<table>
	<tr>
		<th>Thumbnail</th><th>Product Name</th><th>Stock</th><th><input type="checkbox" value="" id="select_all" /><label for="select_all"> Select All</label></th>
	</tr>
		<?php
		
		if ( $waf_query->have_posts() ) :
		while ( $waf_query->have_posts() ) : $waf_query->the_post();

			$wcpfg_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'thumbnail' );
			$wcpfg_thumb = $wcpfg_thumb['0'];
			
			$sale_price = get_post_meta( get_the_ID(), '_sale_price', true);
			
			global $woocommerce, $product;
			
			
			if($product->get_stock_quantity() <= 0){
				$stck = '<span class="os">Out stock</span> <br>x'.$product->get_stock_quantity();
			}else{
				$stck = '<span class="is"> In stock</span> <br>x'.$product->get_stock_quantity();
			}
			if($product->is_on_sale() || $sale_price > 0){
				$onsale = '<span class="onsale">On sale</span>';				$slct = 'checked';
			}else{
				$onsale ='<span class="not-onsale">On sale</span>';				$slct = '';
			}
			
			echo '<tr>';
			echo '<td class="wcpfg-product-thumb"><img src="'.$wcpfg_thumb.'" /></td>';
			echo '<td class="wcpfg-product-title"><div><a href="https://kenzepanne.com/k3ndev/wp-admin/post.php?post='.get_the_ID().'&action=edit" target="_blank">'.get_the_title().'</a></div></td>';
			echo '<td class="wcpfg-product-stock">'.$stck.'</td>';
			echo '<td class="wcpfg-product-onsale"><input type="checkbox" name="fpos[]" value="'.get_the_ID().'" id="id'.get_the_ID().'" '.$slct.'><label for="id'.get_the_ID().'">'.$onsale.'</label></td>';
			echo '</tr>';
			
			
			//echo var_dump($product);
		endwhile;
			
			?>
			<tr>
				<td colspan="4" style="padding: 0px;">
				<div class="actions">
				<input type="text" name="sale_price" value="0.00" class="sale-price" />
				<input type="text" name="sale_start" value="<?php echo date('Y-m-d'); ?>" class="onsale_date date-start" />
				<input type="text" name="sale_end" value="" class="onsale_date date-end" />
				<input type="submit" id="push_onsale" name="action" value="Submit" />
				</div>
				</td>
			</tr>
			<tr><td colspan="4">
			<div class="nav-previous alignleft"><a href="<?php echo $_SERVER["REQUEST_URI"].'&paged='.$p_paged; ?>">‹ <span>Back</span></a></div>
			<div class="nav-next alignright"><a href="<?php echo $_SERVER["REQUEST_URI"].'&paged='.$n_paged; ?>"><span>Onward</span> ›</a></div>
			</td></tr>
			<?php
			
			
		wp_reset_query();
		else:
			echo '<tr><td colspan="4">Sorry, no products matched your criteria.</td></tr>';
		endif;
		
		?>
	</table>
	</form>
	</div>
<?php
}


function wpdocs_enqueue_waf_admin_style() {
	wp_register_style( 'waf_admin_css', plugin_dir_url( __FILE__ ) . '/css/admin-waf.css' );
	wp_enqueue_style( 'waf_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_waf_admin_style' );