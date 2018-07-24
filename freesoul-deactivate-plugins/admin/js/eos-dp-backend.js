jQuery(document).ready(function($){
	var right = eos_dp_js.is_rtl ? 'left' : 'right';
	$('.eos-dp-preview').on('click',function(){
		var a = this;
		var plugin_path = '';
		var row_class = $(a).hasClass('eos-dp-archive-preview') ? '.eos-dp-archive-row' : '.eos-dp-post-row';
		var row = $(this).closest(row_class);
		var colN = 0;
		var chk;
		row.find('.eos-dp-td-chk-wrp input[type=checkbox]').each(function(){
			chk = $(this);
			if(chk.is(':checked') && !chk.hasClass('eos-dp-global-chk-row')){
				colN = $(this).index();
				plugin_path += ';pn:' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path');
			}
			else{
				plugin_path += ';pn:';
			}
		});
		var newHref = a.href.split('&paths=')[0] + '&paths=' + plugin_path.replace(';pn:','');
		if(newHref.length < 2000){
			a.href = newHref;
		}
		else{
			alert( 'Too many changes for the preview!' );
			return false;
		}	
	});
	$('#eos-dp-setts input[type=checkbox]').on('click',function(){
		$(this).closest('td').toggleClass('eos-dp-active');
	});
	$('.eos-dp-global-chk-col').on('click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		eos_dp_update_chk_wrp(chk,checked);
		$('.eos-dp-col-' + chk.attr('data-col')).attr('checked',checked);
		eos_dp_update_chks($('.eos-dp-col-' + chk.attr('data-col')));
	});	
	$('.eos-dp-global-chk-row').on('click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		chk.closest('.eos-dp-post-row').find('input[type=checkbox]').attr('checked',checked);
		eos_dp_update_chks(chk.closest('.eos-dp-post-row').find('input[type=checkbox]'));
		eos_dp_update_chk_wrp(chk,checked);
	});	
	$('.eos-dp-global-posts-reset').on('click',function(){
		$('.eos-dp-col-' + $(this).attr('data-col') + '-' + $(this).attr('data-post_type')).each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
		$(this).closest('.eos-dp-global-posts').find('.eos-dp-global-posts-chk').attr('checked',false);
		eos_dp_update_chk_wrp($(this),checked);
	});
	$('.eos-dp-reset-col').on('click',function(){
		$('.eos-dp-col-' + $(this).attr('data-col')).each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
		$(this).closest('.eos-dp-global-chk-col-wrp').find('.eos-dp-global-chk-col').attr('checked',false);
		eos_dp_update_chk_wrp($(this),checked);
	});	
	$('.eos-dp-reset-row').on('click',function(){
		$(this).closest('.eos-dp-post-row').find('input[type=checkbox]').each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
		$(this).closest('td').find('.eos-dp-global-chk-row').attr('checked',false);
		eos_dp_update_chk_wrp($(this),checked);
	});
	$('.eos-dp-global-posts-chk').on('click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		$('.eos-dp-col-' + chk.attr('data-col') + '-' + chk.attr('data-post_type')).filter(':visible').attr('checked',checked);
		eos_dp_update_chks($('.eos-dp-col-' + chk.attr('data-col') + '-' + chk.attr('data-post_type')).filter(':visible'));
		eos_dp_update_chk_wrp(chk,checked);
	});
	$('.eos-dp-global-chk-post_type').on('click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		$('.eos-dp-post-' + chk.attr('data-post_type')).find('input[type=checkbox]').attr('checked',checked);
		eos_dp_update_chks($('.eos-dp-post-' + chk.attr('data-post_type')).find('input[type=checkbox]'));
		eos_dp_update_chk_wrp(chk,checked);
	});		
	$('.eos-dp-reset-post_type').on('click',function(){
		$('.eos-dp-post-' + $(this).attr('data-post_type') + ' input[type=checkbox]').each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
	});
	$('.eos-dp-plugin-name').each(function(){
		var name_wrp = $(this);
		if(name_wrp.text().length > 27){
			name_wrp.text(name_wrp.text().substring(0,24) + ' ...');
		}
	});
	$('.eos-dp-title').each(function(){
		var name_wrp = $(this);
		if(name_wrp.text().length > 60){
			name_wrp.text(name_wrp.text().substring(0,57) + ' ...');
		}
	});
	$('.eos-dp-filter-terms').on('change',function(){
		var sel = $(this);
		if(sel.val() !== 'all'){
			$('.eos-dp-post-' + sel.attr('data-post_type')).closest('.eos-dp-post-row').css('display','none');
			$('.eos-dp-term-' + sel.val()).closest('.eos-dp-post-row').css('display','table-row');
		}
		else{
			$('.eos-dp-post-' + sel.attr('data-post_type')).closest('.eos-dp-post-row').css('display','table-row');
		}
		sel.closest('tr').find('select').not(sel).val('all');
	});
	$(".eos-dp-setts-menu-item").on("click", function () {
		var tab = $(this);
		$('#eos-dp-setts').removeClass('fixed');
		$(".eos-dp-setts-menu-item").removeClass('eos-active');
		tab.addClass('eos-active');
		$('.eos-dp-section').addClass('eos-hidden');
		$('#' + tab.attr('data-section')).removeClass('eos-hidden');
	});	
	$(".submit-dp-opts").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var data_checked = 'not checked';
		var actual_checked = '';
		var chk;
		var post_id = '';
		var dataArchives = {};
		var data = {};
		var str = '';
		var modified = [];
		var bit = 0;
		var archiveRow;
		var row;
		$('.eos-dp-archive-row').each(function(){
			str = '';
			row = $(this);
			row.find('input[type=checkbox]').filter(':visible').not('.eos-dp-global-chk-row').each(function(){
				chk = $(this);
				str += chk.is(':checked') ? ',' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path') : ',';
			});			
			dataArchives[row.find('.eos-dp-view').attr('href')] = str;
		});
		$('.eos-dp-post-row').each(function(){
			row = $(this);
			modified = [];
			str = '';
			post_id = row.attr('data-post-id');
			if('undefined' !== typeof(post_id)){
				row.find('input[type=checkbox]').filter(':visible').not('.eos-dp-global-chk-row').each(function(){
					chk = $(this);
					data_checked = chk.attr('data-checked');
					actual_checked = chk.is(':checked') ? 'checked' : 'not-checked';
					bit = actual_checked !== data_checked ? '1' : '0';
					modified.push(bit);
					str += actual_checked === 'checked' ? ',' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path') : ',';
				});
				if(modified.indexOf('1') > -1){
					data['post_id_' + post_id] = str.substring(1,(str.length));
				}
			}	
		});
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_setts").val(),
				"eos_dp_setts" : data,			
				"eos_dp_setts_archives" : dataArchives,			
				"action" : 'eos_dp_save_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
					var checked = '';
					$('#eos-dp-setts input[type=checkbox]').each(function(){
						checked = $(this).is(':checked') ? 'checked' : 'not-checked';
						$(this).attr('data-checked',checked);
					});
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$('#eos-dp-sidebar-ctrl span').on('click',function(){
		var arrow = $('#eos-dp-sidebar-ctrl span');
		var ctrlWrp = arrow.parent();
		var sidebar = ctrlWrp.closest('.eos-dp-sidebar');			
		arrow.removeClass('eos-hidden');
		$(this).addClass('eos-hidden');
		sidebar.toggleClass('open').toggleClass('close');
		if(sidebar.hasClass('open')){
			sidebar.animate({right:30 + sidebar.width() + 'px'});
		}
		else{
			sidebar.animate({right: '0'});
		}
	});
	$('#eos-dp-go-to-wrp span').on('click',function(){
		eos_dp_go_to_post_type($(this).prev().val());
	});
	var availableOptions = [];
	$('.eos-dp-post-name').each(function(){
		availableOptions.push($(this).text());
	})
    $( "#eos-dp-go-to-posttype" ).autocomplete({
		source: availableOptions
    });	
	window.first_scroll = document.body.scrollTop + document.documentElement.scrollTop;
	window.onscroll = eos_move_table_head;
});
jQuery.fn.eos_dp_isInViewport = function() {
	var elementTop = jQuery(this).offset().top;
	var elementBottom = elementTop + jQuery(this).outerHeight();
	var viewportTop = jQuery(window).scrollTop();
	var viewportBottom = viewportTop + jQuery(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};
function eos_dp_update_chks(chk){
	if(!chk.is(':checked')){
		chk.closest('td').addClass('eos-dp-active');
	}
	else{
		chk.closest('td').removeClass('eos-dp-active');
	}
}
function eos_move_table_head(){
	var table = document.getElementById('eos-dp-setts');
	if(Math.pow(table.offsetWidth - table.scrollWidth,2) > 25) return;
	if(!jQuery('.eos-pre-nav').first().eos_dp_isInViewport()){
		var ofs = jQuery('.eos-dp-post-row td').outerWidth();
		ofs = eos_dp_js.is_rtl !== '1' ? ofs : - ofs;
		jQuery('#eos-dp-table-head').css('transform','translateX(' + ofs + 'px)');
		table.className = 'fixed';
	}
	else{
		table.className = '';
		jQuery('#eos-dp-table-head').css('transform','none');
	}
}
function eos_dp_update_chk_wrp(chk,checked){
	if(true === checked){
		chk.parent().removeClass('eos-dp-active-wrp').addClass('eos-dp-not-active-wrp');
	}
	else{
		chk.parent().addClass('eos-dp-active-wrp').removeClass('eos-dp-not-active-wrp');
	}
}
function eos_dp_go_to_post_type(post_type){
	var tableHead = jQuery('#eos-dp-setts.fixed');
	var offs = tableHead.length < 1 ? parseInt(jQuery('#eos-dp-setts').offset().top) : 0;
	jQuery('.eos-dp-post-name').each(function(){
		if(jQuery(this).text().toLowerCase().split(' ').join('-')  === post_type.toLowerCase().split(' ').join('-') ){
			var el = jQuery(this).closest('.eos-dp-filters-table');
			if('undefined' !== typeof(el)){
				jQuery('html,body').animate({
					scrollTop: parseInt(el.offset().top) - offs - 40
				},500);
			}
			return false;
		}
	});
}	