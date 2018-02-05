jQuery(document).ready(function($)
	{
		
		
	$(document).on('click', '.wcpfg-grid-load-more', function()
		{
			$(this).addClass('loaidng');
			
			var offset = parseInt($(this).attr('offset'));
			
			var val_sort = $('.wcpfg_sort:checked').val();
			
			var val_orderby = $('.wcpfg_orderby:checked').val();
			var val_date = $('.wcpfg_date:checked').val();
		
			var val_per_page = parseInt($('.wcpfg_per_page:checked').val());			
			var val_order = $('.wcpfg_order:checked').val();
			
			var offset = val_per_page+offset;
			$(this).attr('offset',offset);
			
			$.ajax(
				{
			type: 'POST',
			url:wcpfg_ajax.wcpfg_ajaxurl,
			data: {"action": "wcpfg_product_list_ajax", 'val_sort':val_sort, 'val_orderby':val_orderby,  'val_date':val_date,  'val_order':val_order, 'val_per_page':val_per_page , 'offset':offset },
			success: function(data)
					{	

						$('.wcpfg-product-list').append(data);
						$('.wcpfg-grid-load-more').removeClass('loaidng');

					}
				});
			
			
			
			
			
			
		})
		
		
	$(document).on('click', '.wcpfg-menu-submit', function()
		{
			$(this).addClass('loaidng');
			
			var val_sort = $('.wcpfg_sort:checked').val();			
			
			var val_orderby = $('.wcpfg_orderby:checked').val();
			var val_date = $('.wcpfg_date:checked').val();		
			var val_per_page = $('.wcpfg_per_page:checked').val();			
			var val_order = $('.wcpfg_order:checked').val();
				

				
			
			$.ajax(
				{
			type: 'POST',
			url:wcpfg_ajax.wcpfg_ajaxurl,
			data: {"action": "wcpfg_product_list_ajax", 'val_sort':val_sort, 'val_orderby':val_orderby,  'val_date':val_date,  'val_order':val_order, 'val_per_page':val_per_page },
			success: function(data)
					{	

						$('.wcpfg-product-list').html(data);
						$('.wcpfg-menu-submit').removeClass('loaidng');

					}
				});
		})
		
	});	