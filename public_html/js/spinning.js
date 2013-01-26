/**
 jQuery Spinning Numbers v. 0.8
 by Sarp Erdag
*/
var spinnercount=0;
(function($) {
	$.extend($.fx.step,{
	    backgroundPosition: function(fx) {
            if (fx.state === 0 && typeof fx.end == 'string') {
                var start = $.curCSS(fx.elem,'backgroundPosition');
                start = toArray(start);
                fx.start = [start[0],start[2]];
                var end = toArray(fx.end);
                fx.end = [end[0],end[2]];
                fx.unit = [end[1],end[3]];
			}
            var nowPosX = [];
            nowPosX[0] = ((fx.end[0] - fx.start[0]) * fx.pos) + fx.start[0] + fx.unit[0];
            nowPosX[1] = ((fx.end[1] - fx.start[1]) * fx.pos) + fx.start[1] + fx.unit[1];
            fx.elem.style.backgroundPosition = nowPosX[0]+' '+nowPosX[1];

           function toArray(strg){
               strg = strg.replace(/left|top/g,'0px');
               strg = strg.replace(/right|bottom/g,'100%');
               strg = strg.replace(/([0-9\.]+)(\s|\)|$)/g,"$1px$2");
               var res = strg.match(/(-?[0-9\.]+)(px|\%|em|pt)\s(-?[0-9\.]+)(px|\%|em|pt)/);
               return [parseFloat(res[1],10),res[2],parseFloat(res[3],10),res[4]];
           }
        }
	});

	var Spinning = function(element, options)
    {
		spinnercount++;
        var elem = $(element);
        var obj = this;
		
        var settings = $.extend({
            initial_value: 0
        }, options || { });

        var final_value;
        var digit_count = 0;
		
		var rate = settings["rate"];
        var spinnerID = settings["ID"];
        
        var initial_value = settings["initial_value"].toString();
        var i = 0;
        digit_count = initial_value.length;
        for (i=0;i<digit_count;i++){
            $(elem).append("<div class='s"+spinnerID+"_digit_" + i + " digit' ></div>");
            $(".s"+spinnerID+"_digit_" + i).css({backgroundPosition: "0px -" + initial_value[i] * 12 + "px"});
        }
        final_value = parseInt(initial_value);
		
        
        this.increment = function(amount,spinnerID) {
            final_value = final_value + amount;
            final_value = final_value.toString();

            new_digit_count = final_value.length;
            if(new_digit_count>digit_count){
                var pos = new_digit_count-1;
                $('.s'+spinnerID+'_digit_0').parent().append("<div class='s"+spinnerID+"_digit_" + pos + " digit'></div>");
            }
            for (i=0;i<new_digit_count;i++){
                $(".s"+spinnerID+"_digit_" + i).animate({'background-position-y':"-" + (final_value[i] * 12) + "px"},{duration:800});
			}

            final_value = parseInt(final_value);
            digit_count = new_digit_count;
        };
		
		
    };

    $.fn.spinning = function(options)
    {
        return this.each(function()
        {
            var element = $(this);
            if (element.data('spinning')) return;
            var plugin = new Spinning(this, options);
            element.data('spinning', plugin);
        });
    };
})(jQuery);