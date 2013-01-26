jQuery(document).ready(function($) {
	var currentTab = $('.stabs li.active').index()+1;
	$('.stabs li').click(function(){
		$(this).siblings().attr('class','');
		$(this).attr('class','active');
		if(currentTab != $(this).index()+1){
			currentTab = $(this).index()+1;
			$('#tabs .tab').each(function(index) {
				$(this).fadeOut("fast");
			});
			$('#tabs .tab:nth-child('+currentTab+')').delay(200).fadeIn("fast");
		}
	});
	
	$('img[mcuser]').each(function(){
		var src = $(this).attr('mcuser');
		var size = $(this).attr('mcsize');
		$(this).hide();
		$(this).attr('title',src).addClass('ttip').addClass('mcuser').wrap('<a href="/player/'+src+'"></a>').attr('src','/skins.php?size='+size+'&head&user='+src);
		$(this).on('load', function () {
			$(this).fadeIn(1200);
		});
	});
	
	$('.lazyload img').hide();
	$('.lazyload img').each( function(){
		$(this).on('load', function () {
			$(this).fadeIn(1200);
		});
	});
	
	$('img[title].ttip').tooltip();
	$('img[title].pover').popover();
	
	$('.bannerchange a').click(function(){
		var clickval = $(this).html().toLowerCase();
		
		$('.bannerpost').each(function(){
			$(this).html("/"+clickval);
		});
		
		$('.bannertarget').each(function(){
			$(this).attr('src',$(this).attr('data-bbase')+"/"+clickval);
			
			$(this).fadeOut();
		});
	});
	
	$('.banner').load(function(){
		$(this).fadeIn();
	});
});

function addvote(sid){
	var votebtn = $('.votebtn');
	votebtn.addClass('disabled').html('Processing..');
	$.ajax({					
		type: "POST",
		url: "/api.php",
		data: "req=m05&id="+sid+"&usr="+$('.mcuservote').val(),
		async: true,
		cache: false,
		dataType: "json",
		
		success: function(data){
			window.location.reload(true);
		}
		
	});
}

var substage = 0;
function advanceLookup(){
	if(substage == 0){
		$(".stg1").animate({ width: 'toggle'}, 400);
		$(".stg2").delay(400).animate({ width: 'toggle'}, 400);
		$('.ipinput').attr('class','ipinput');
		$('.emsg').fadeOut('fast',function(){
			$('.emsg').html('');
		});
		$.ajax({
								
			type: "POST",
			url: "/api.php",
			data: "req=m01&ip="+$(".ipinput").val(),
			async: true,
			cache: false,
			dataType: "json",
			
			error: function(xhr,status,error){
				$('.emsg').html("Error Status "+xhr.status+": "+xhr.statusText);
				$(".stg1").delay(1300).animate({ width: 'toggle'}, 400, function(){
					substage = 0;
					$('.ipinput').attr('class','ipinput error');
					$('.emsg').fadeIn('fast');
				});
				$(".stg2").delay(500).animate({ width: 'toggle'}, 400);
			},
			
			success: function(data){
				$('.emsg').html(data.info);
				
				$(".stg1").delay(1300).animate({ width: 'toggle'}, 400, function(){
					substage = 0;
					if(data.status == 'error'){
						$('.ipinput').attr('class','ipinput error');
					}else{
						$('.emsg').attr('class','emsg success');
						window.location.href = "/server/"+data.extra;
					}
					$('.emsg').fadeIn('fast');
				});
				$(".stg2").delay(500).animate({ width: 'toggle'}, 400);
			}
			
		});
	}
	substage = 1;
}

window.setInterval(rotateServers, 8000);

function rotateServers() { 
	$('.rotate div:nth-child(1)').each(function(){
		$(this).animate({ height: '0', padding: '0',opacity: 'toggle' }, 1300,function(){
			$('.rotate').append("<div style=\"display:none;\" class=\"box boxbottom\">"+$(this).html()+"</div>");
			$(this).remove();
			$('.rotate div:nth-child(1)').each(function(){
				$(this).attr('class','box boxtop');
			});
		});
	});
	
	$('.rotate div:nth-child(7)').each(function(){
		$(this).animate({ height: 'toggle', opacity: 'toggle' }, 1300);
	});
	
	$('.rotate div:nth-child(6)').each(function(){
		$(this).attr('class','box boxmid');
	});
}

!function(a){var b=function(a,b){this.init("tooltip",a,b)};b.prototype={constructor:b,init:function(b,c,d){var e,f;this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.enabled=!0,this.options.trigger=="click"?this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this)):this.options.trigger!="manual"&&(e=this.options.trigger=="hover"?"mouseenter":"focus",f=this.options.trigger=="hover"?"mouseleave":"blur",this.$element.on(e+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(f+"."+this.type,this.options.selector,a.proxy(this.leave,this))),this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},getOptions:function(b){return b=a.extend({},a.fn[this.type].defaults,b,this.$element.data()),b.delay&&typeof b.delay=="number"&&(b.delay={show:b.delay,hide:b.delay}),b},enter:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);if(!c.options.delay||!c.options.delay.show)return c.show();clearTimeout(this.timeout),c.hoverState="in",this.timeout=setTimeout(function(){c.hoverState=="in"&&c.show()},c.options.delay.show)},leave:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);this.timeout&&clearTimeout(this.timeout);if(!c.options.delay||!c.options.delay.hide)return c.hide();c.hoverState="out",this.timeout=setTimeout(function(){c.hoverState=="out"&&c.hide()},c.options.delay.hide)},show:function(){var a,b,c,d,e,f,g;if(this.hasContent()&&this.enabled){a=this.tip(),this.setContent(),this.options.animation&&a.addClass("fade"),f=typeof this.options.placement=="function"?this.options.placement.call(this,a[0],this.$element[0]):this.options.placement,b=/in/.test(f),a.remove().css({top:0,left:0,display:"block"}).appendTo(b?this.$element:document.body),c=this.getPosition(b),d=a[0].offsetWidth,e=a[0].offsetHeight;switch(b?f.split(" ")[1]:f){case"bottom":g={top:c.top+c.height,left:c.left+c.width/2-d/2};break;case"top":g={top:c.top-e,left:c.left+c.width/2-d/2};break;case"left":g={top:c.top+c.height/2-e/2,left:c.left-d};break;case"right":g={top:c.top+c.height/2-e/2,left:c.left+c.width}}a.css(g).addClass(f).addClass("in")}},setContent:function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},hide:function(){function d(){var b=setTimeout(function(){c.off(a.support.transition.end).remove()},500);c.one(a.support.transition.end,function(){clearTimeout(b),c.remove()})}var b=this,c=this.tip();return c.removeClass("in"),a.support.transition&&this.$tip.hasClass("fade")?d():c.remove(),this},fixTitle:function(){var a=this.$element;(a.attr("title")||typeof a.attr("data-original-title")!="string")&&a.attr("data-original-title",a.attr("title")||"").removeAttr("title")},hasContent:function(){return this.getTitle()},getPosition:function(b){return a.extend({},b?{top:0,left:0}:this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight})},getTitle:function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||(typeof c.title=="function"?c.title.call(b[0]):c.title),a},tip:function(){return this.$tip=this.$tip||a(this.options.template)},validate:function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},enable:function(){this.enabled=!0},disable:function(){this.enabled=!1},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(){this[this.tip().hasClass("in")?"hide":"show"]()},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}},a.fn.tooltip=function(c){return this.each(function(){var d=a(this),e=d.data("tooltip"),f=typeof c=="object"&&c;e||d.data("tooltip",e=new b(this,f)),typeof c=="string"&&e[c]()})},a.fn.tooltip.Constructor=b,a.fn.tooltip.defaults={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover",title:"",delay:0,html:!0}}(window.jQuery)

!function(a){var b=function(a,b){this.init("popover",a,b)};b.prototype=a.extend({},a.fn.tooltip.Constructor.prototype,{constructor:b,setContent:function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content > *")[this.options.html?"html":"text"](c),a.removeClass("fade top bottom left right in")},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var a,b=this.$element,c=this.options;return a=b.attr("data-content")||(typeof c.content=="function"?c.content.call(b[0]):c.content),a},tip:function(){return this.$tip||(this.$tip=a(this.options.template)),this.$tip},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}}),a.fn.popover=function(c){return this.each(function(){var d=a(this),e=d.data("popover"),f=typeof c=="object"&&c;e||d.data("popover",e=new b(this,f)),typeof c=="string"&&e[c]()})},a.fn.popover.Constructor=b,a.fn.popover.defaults=a.extend({},a.fn.tooltip.defaults,{placement:"right",trigger:"click",content:"",template:'<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'})}(window.jQuery)