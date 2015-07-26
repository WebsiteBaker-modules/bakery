(function($){
/*
 * includeMany 1.2.2
 *
 * Copyright (c) 2009 Arash Karimzadeh (arashkarimzadeh.com)
 * Licensed under the MIT (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.arashkarimzadeh.com/jquery/17-includemany-jquery-include-many.html
 *
 * Date: Dec 03 2009
 */
$.chainclude = function(urls,finaly){
	var onload = function(callback,data){
						if(typeof urls.length!='undefined'){
							if(urls.length==0)
								return $.isFunction(finaly)
											?finaly(data)
											:null;
							urls.shift();
							return $.chainclude.load(urls,onload);
						}
						for(var item in urls){
							urls[item](data);
							delete urls[item];
							var count = 0;
							for(var i in urls)
								count++;
							return (count==0)
										?$.isFunction(finaly)?finaly(data):null
										:$.chainclude.load(urls,onload);
						}
					}
	$.chainclude.load(urls,onload);
};
$.chainclude.load = function(urls,onload){
	if(typeof urls=='object' && typeof urls.length=='undefined')
		for(var item in urls)
			return $.include.load(item,onload,urls[item].callback);
	urls = $.makeArray(urls);
	$.include.load(urls[0],onload,null);
};
$.include = function(urls,finaly){
	var luid = $.include.luid++;
	var onload = function(callback,data){
						if($.isFunction(callback))
							callback(data);
						if(--$.include.counter[luid]==0&&$.isFunction(finaly))
							finaly();
					}
	if(typeof urls=='object' && typeof urls.length=='undefined'){
		$.include.counter[luid] = 0;
		for(var item in urls)
			$.include.counter[luid]++;
		return $.each(urls,function(url,callback){$.include.load(url,onload,callback);});
	}
	urls = $.makeArray(urls);
	$.include.counter[luid] = urls.length;
	$.each(urls,function(){$.include.load(this,onload,null);});
}
$.extend(
	$.include,
	{
		luid: 0,
		counter: [],
		load: function(url,onload,callback){
			url = url.toString();
			if($.include.exist(url))
				return onload(callback);
			if(/.css$/.test(url))
				$.include.loadCSS(url,onload,callback);
			else if(/.js$/.test(url))
				$.include.loadJS(url,onload,callback);
			else
				$.get(url,function(data){onload(callback,data)});
		},
		loadCSS: function(url,onload,callback){
			var css=document.createElement('link');
			css.setAttribute('type','text/css');
			css.setAttribute('rel','stylesheet');
			css.setAttribute('href',''+url);
			$('head').get(0).appendChild(css);
			$.browser.msie
				?$.include.IEonload(css,onload,callback)
				:onload(callback);//other browsers do not support it
		},
		loadJS: function(url,onload,callback){
			var js=document.createElement('script');
			js.setAttribute('type','text/javascript');
			js.setAttribute('src',''+url);
			$.browser.msie
				?$.include.IEonload(js,onload,callback)
				:js.onload = function(){onload(callback)};
			$('head').get(0).appendChild(js);
		},
		IEonload: function(elm,onload,callback){
			elm.onreadystatechange = 
					function(){
						if(this.readyState=='loaded'||this.readyState=='complete')
							onload(callback);
					}
		},
		exist: function(url){
			var fresh = false;
			$('head script').each(
								function(){
									if(/.css$/.test(url)&&this.href==url)
											return fresh=true;
									else if(/.js$/.test(url)&&this.src==url)
											return fresh=true;
								}
							);
			return fresh;
		}
	}
);
//
})(jQuery);





if(!jQuery().sortable){ 
	// load_jQ_UI;			
	$.include('../../include/jquery/jquery-ui-min.js');
}

$(window).load(function(){
	
	// load jQuery UI if sortable not loaded

	if(jQuery().sortable){
				
	 	
	 	/** 
			Drag&Drop 
		*/	
		$(function() { 
			// This class="dragdrop_bakery" will result in class="dragdrop_bakery dragdrop_handle"
			$('.dragdrop_bakery').addClass('dragdrop_handle');
			// Remove up/down icons (we have drag&drop therefore we don't need them)
			$(".move_up a,.move_down a").fadeOut(1); 
			
			$("#dragBakeryTable").sortable({ 
				appendTo: 'body',
				handle:  '.dragdrop_handle',
				opacity: 0.8, 
				cursor: 'move', 
				update: function() { 
					var order = $(this).sortable("serialize") + '&action=updatePosition'; 
					$.post("../../modules/bakery/move_dragdrop.php", order, function(acknowledgement){
						//$("#dragBakeryTable tr").removeClass('hilite');
						$("#dragBakeryResult").html(acknowledgement).fadeIn("slow");	
						$("#dragBakeryResult").fadeOut(3500);						
					}); 	
				}				
			})
		}); 
		
	}//endif
	
	/*
		hover effect for item rows 	
	*/
	$("tr.irow")
		.mouseover(function(){
			$(this).addClass("irow_hover");
		})
		.mouseout(function(){
			$(this).removeClass("irow_hover");
		});
	
}); //window.load





/**
	Image preview script 
	written by Alen Grakalic (http://cssglobe.com)
	for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
*/
 
this.imagePreview = function(){	
	$('div.mod_bakery_thumbnail_b a.internal').addClass('preview'); 
	$("a.preview").hover(function(e){
			this.t = this.title;
			this.title = "";	
			var c = (this.t != "") ? "<br/>" + this.t : "";
			$("body").append("<p id='mod_bakery_preview_b'><img src='"+ this.href +"' alt='Image preview' />"+ c +"</p>");								 
			$("#mod_bakery_preview_b")
				.css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px").fadeIn("slow");
	},
	function(){
			this.title = this.t;	
			$("#mod_bakery_preview_b").remove();
	});	
	$("a.preview").mousemove(function(e){
		$("#mod_bakery_preview_b")
			.css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px");
	});	
		
};

$(window).load(function(){	
// initialize Image preview script 
	xOffset = 100;
	yOffset = 40;
	imagePreview();
}); //window.load