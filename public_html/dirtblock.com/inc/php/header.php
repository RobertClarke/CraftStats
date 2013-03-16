<head>
	<title>Dirt Block</title>
	<link rel="shortcut icon" href="inc/img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="screen"/>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript">
		var toUpdate; //= {"#bbroken": {'rate':286,'value':1000}};
		$(document).ready(function() {
			
			getStats(true);
		});
		
		var getStats = function(first){
			$.ajax({					
				type: "POST",
				url: "http://craftstats.com/api",
				data: "req=m08",
				async: true,
				cache: false,
				dataType: "jsonp",
				
				success: function(data){
					$.each(data, function(key, obj) {
						var prev = parseInt($(key).data('previous'));
						if(!isNaN(prev))countTo($(key),addThousandSeparator(parseInt($(key).data('previous'))));
						var incr = (parseInt(obj.value)-parseInt(prev))/15;
						obj.rate = 0;
						if(!isNaN(incr))obj.rate=incr;						
						$(key).data('previous',obj.value);
						if(!isNaN(prev))obj.value -= incr*15;
					});
					toUpdate = data;
					if(first){
						setInterval(updateStats, 1000);
						setInterval(getStats,15000);
					}
				}
			});
		}
		
		var updateStats = function() {
			$.each(toUpdate, function(key, obj) {
				if(obj.value+obj.rate < $(key).data('previous'))obj.value += obj.rate;
				countTo($(key),addThousandSeparator(parseInt(obj.value)));
			});
		}
		var addThousandSeparator = function(nStr) {
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1;
		}
		var countTo = function(el, val) {
			if (el.text().length != val.length) {
				el.text(val);
				el.css('width', el.width() + 'px').css('display', 'inline-block');
				return false;
			}
			var digits = el.text().split('');
			el.css('width', el.width() + 'px').css('display', 'inline-block');
			el.html("");
			var offset = new Array();
			var digitEles = new Array();
			for (i in digits) {
				var digit = $("<span></span>").text(digits[i]).appendTo(el);
				offset.push(digit.position().left);
				digitEles.push(digit);
			}
			for (i in digitEles) {
				digitEles[i].css({
					top: 0,
					left: offset[i] + "px",
					position: 'absolute'
				})
			}

			var newDigits = val.split('');
			for (i in newDigits) {
				if (newDigits[i] != digits[i]) {
					var newDigit = $('<span></span>').text(newDigits[i]).appendTo(el);
					newDigit.css({
						top: "-10px",
						left: offset[i] + "px",
						position: 'absolute'
					});
					newDigit.animate({
						top: '+=10',
						opacity: 1.0
					}, 200), function() {
						el.html(val)
					};
					digitEles[i].animate({
						top: '+=15',
						opacity: 0.0,
						color: "rgba(0,0,0,0.3)"
					}, 200, function(){
						$(this).remove()
					});
				}
			}
		}
	</script>
</head>
