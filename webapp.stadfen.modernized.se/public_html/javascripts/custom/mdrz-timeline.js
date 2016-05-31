;(function ($, window, document, undefined) {

var pluginName = "timeline";

  var Timeline = function (element, options) {
	
	// Setting up variables
    this.$element 		= 	$(element);
	this.context		= 	$(element)[0].getContext('2d');
	this.options  		= 	$.extend( {}, Timeline.DEFAULTS, options);
	
	// Initializes the plugin
	this.init();
	
  }
  
  Timeline.VERSION  = '1.0.0'

  Timeline.DEFAULTS = {
	data:null,
	weekdays_array:["Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag", "Söndag", ""],
  }

  
  Timeline.prototype.updateTimeline = function(d) {
	  
	  var plugin = this;
	  
	  plugin.options.data = d;
	  
  }
  
  Timeline.prototype.init = function() {
	
		var plugin = this;
		var parent_width = plugin.$element.parent();
		plugin.context.canvas.width = parent_width.width();
		
		var parent = plugin.$element.parent();
		parent.append("<div class='time-tooltip'></div>");
		
		var ctx = plugin.context;
		
		var weekdays_array = plugin.options.weekdays_array;
		
		var weekdays = weekdays_array.length;
		var line_width = 3;
		
		var radius = 8;
		var outline_size = 3;
		
		var circle_width = ((radius*2) + (outline_size*2));
		
		var marginX = 20;
		
		var canvasWidth = ctx.canvas.width;
		var canvasHeight = ctx.canvas.height;
		
		var workableWidth = canvasWidth - marginX*2;
		
		var centerX = ctx.canvas.width / 2;
		var centerY = ctx.canvas.height / 2;
		
		var color_line = '#1bd3f9';
		var color_circle = '#1bd3f9';
		var outline_circle = '#FFF';
		var text_color = '#A0A0A0';
		
		var color_line_picker = '#F5DC32';
		var color_line_chosen = '#000';
		var dayStep = workableWidth / (weekdays-1);
		
		var pendingIntervalStart = null;
		var startingX = null;
		
		var getHoverInfo = function(mouseX,mouseY)
		{
			var realX = mouseX - marginX;
			var rX = realX/(workableWidth + dayStep);
			
			var secondPerDay = 60*60*24
			var secondPerWeek = secondPerDay*weekdays;
			
			var seconds = rX*secondPerWeek;
			var day = Math.floor(seconds / secondPerDay) + 1;
			
			var daySeconds = seconds- ((day-1)*secondPerDay);
			var hours = Math.floor(daySeconds/(60*60));
			var secondsLeft = daySeconds-hours*60*60;
			//var minutes = Math.floor(Math.floor(secondsLeft/(60)) / 30) * 30;
			
			$('.time-tooltip').css('left', mouseX - $('.time-tooltip').width() / 2);
			$('.time-tooltip').css('top', mouseY);
			$('.time-tooltip').html(weekdays_array[day-1] + " kl " + hours + ":00");
			
			var ret = {};
			
			ret.Day = day;
			ret.Hours = hours;
			
			return ret;
		};
		
		var beginTimePick = function(hoverInfo)
		{
			pendingIntervalStart = hoverInfo;
			
			
		};
		
		var endTimePick = function(hoverInfo)
		{
			pendingIntervalStart = null;
		};
		
		plugin.$element.mousedown(function(e) {
			
			var parentOffset = $(this).parent().offset();
			
			//var mouseX = e.pageX - parentOffset.left;
			//var mouseY = e.pageY - parentOffset.top - 40;
			
			startingX = mouseX;
			
			beginTimePick(getHoverInfo(mouseX,mouseY));
			
		});
		
		plugin.$element.mouseup(function(e) {
			
			endTimePick();
			
		});
		
		plugin.$element.mouseleave(function(e) {
			
			$('.time-tooltip').addClass('hidden');
			
		});
		
		plugin.$element.mouseenter(function(e) {
			
			$('.time-tooltip').removeClass('hidden');
			
		});
		
		var mouseX, mouseY;
		
		plugin.$element.mousemove(function(e) {
			
			var parentOffset = $(this).parent().offset();
			
			mouseX = e.pageX - parentOffset.left;
			mouseY = e.pageY - parentOffset.top - 40;
			
			getHoverInfo(mouseX,mouseY);
			
			if (pendingIntervalStart != null)
			{
				render(mouseX, mouseY);
			}
		});

		var redrawTimeline = function()
		{
			
			for (var i = 0; i < weekdays; i++)
			{
				var currentX = marginX + (dayStep * i);
				
				ctx.beginPath();
				ctx.arc(currentX, centerY, radius, 0, 2 * Math.PI, false);
				ctx.fillStyle = color_circle;
				ctx.fill();
				ctx.lineWidth = outline_size;
				ctx.strokeStyle = outline_circle;
				ctx.stroke();
				
				ctx.fillStyle = text_color;
				ctx.font = '10pt open-sans';
	
				ctx.fillText(weekdays_array[i], currentX - (ctx.measureText(weekdays_array[i]).width / 2) + dayStep / 2, centerY + 40);
				
			}
			
			ctx.beginPath();
			ctx.moveTo(marginX, centerY);
			ctx.lineWidth = line_width;
			ctx.lineTo(workableWidth + marginX, centerY);
			ctx.strokeStyle = color_line;
			ctx.stroke();
			
			if (plugin.options.data.length > 0)
			{
				var startX = marginX;
				var xPerDay = dayStep;
				var xPerHour = xPerDay/24;
				for(var i = 0; i < plugin.options.data.length; i++)
				{
					var StartDay = plugin.options.data[i].StartDay;
					var StartTime = plugin.options.data[i].StartTime;
					var EndDay = plugin.options.data[i].EndDay;
					var EndTime = plugin.options.data[i].EndTime;
					
					var starthour = StartTime.split(":")[0];
					var endhour = EndTime.split(":")[0];
					
					var X1 = startX + xPerDay*(StartDay-1) + xPerHour*starthour;
					var X2 = startX + xPerDay*(EndDay-1) + xPerHour*endhour;
					
					
					ctx.beginPath();
					ctx.moveTo(X1, centerY);
					ctx.lineWidth = line_width;
					ctx.lineTo(X2, centerY);
					ctx.strokeStyle = color_line_chosen;
					ctx.stroke();
				}
			}
		};
		
		var redrawPicker = function()
		{
			ctx.beginPath();
			ctx.moveTo(startingX, centerY);
			ctx.lineWidth = line_width;
			ctx.lineTo(mouseX, centerY);
			ctx.strokeStyle = color_line_picker;
			ctx.stroke();
		}
		
		var render = function()
		{
			ctx.clearRect(0,0,canvasWidth, canvasHeight);
			redrawTimeline();
			redrawPicker();
			requestAnimationFrame(render);
		}
		
		redrawTimeline();
		
  };
  
  
  $.fn[pluginName] = function (options)
  {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
				new Timeline(this, options));
			}
		});
  }
  
})(jQuery, window, document);