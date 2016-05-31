;(function ($, window, document, undefined) {

var pluginName = "slider";

  var SliderMenu = function (element, options) {
	
	// Setting up variables
    this.$element 		= 	$(element);
	this.$container 	= 	$(element).find('.active-container');
	this.$menu 			= 	$(element).find('.menu');
	this.activeIndex 	= 	0;
	this.totalTabs 		= 	0;
    this.options  		= 	$.extend( {}, SliderMenu.DEFAULTS, options);
	
	// Initializes the plugin
	this.init();

  }
  
  SliderMenu.VERSION  = '1.0.0'
  
  SliderMenu.ANIMATION_TYPE_SLIDE = 0;
  SliderMenu.ANIMATION_TYPE_FADE = 1;

  SliderMenu.DEFAULTS = {
    inactiveOpacity: 0.5,
	animationSpeed: 175,
	animationType: SliderMenu.ANIMATION_TYPE_SLIDE,
  }
  
  SliderMenu.prototype.init = function() {
	
	var menu_index = 0;
	var plugin = this;
	
	plugin.$menu.find('a').each(function(index, element) {
			
			$(element).attr('data-index', menu_index++);
			
			$(element).click(function() {
				
				var i = $(element).attr('data-index');

				plugin.slideTo(parseInt(i), $(element).attr('data-nav'));
			});

	});
	
	plugin.totalTabs = menu_index;

	plugin.$element.on('swipeleft', { plugin: this }, plugin.slideLeft);
	plugin.$element.on('swiperight', { plugin: this }, plugin.slideRight);

	plugin.setActiveMenu(plugin.activeIndex);
	  
  };
  
  SliderMenu.prototype.setActiveMenu = function(index)
  {
		
		var activeMenu = this.$menu.find('a[data-index="'+this.activeIndex+'"]');
		activeMenu.removeClass('active');
		
		this.activeIndex = index;
		
		activeMenu = this.$menu.find('a[data-index="'+this.activeIndex+'"]');
		activeMenu.addClass('active');
		 
  };
  
  SliderMenu.prototype.slideLeft = function(event) {
		
		var plugin = event.data.plugin;
		
		var nav = plugin.$menu.find('a[data-index="'+(plugin.activeIndex+1)+'"]').attr('data-nav');
		plugin.slideTo(plugin.activeIndex+1, nav);
		
  };
  
  SliderMenu.prototype.slideRight = function(event) {

		var plugin = event.data.plugin;

		var nav = plugin.$menu.find('a[data-index="'+(plugin.activeIndex-1)+'"]').attr('data-nav');
		plugin.slideTo(plugin.activeIndex-1, nav);
		
  };
  
  SliderMenu.prototype.slideTo = function(index, navigation) {
	
	var plugin = this;
	
	if (plugin.activeIndex == index)
	return;
	
	if (index >= 0 && index <= plugin.totalTabs-1)
	{
		
		switch (plugin.options.animationType)
		{
			
			case SliderMenu.ANIMATION_TYPE_SLIDE:
				var sliding_delta = index - plugin.activeIndex;
				var slideRight = (sliding_delta < 0);
				
				plugin.setActiveMenu(index);
				
				if (slideRight)
				{
					plugin.$container.stop().animate({left: plugin.$container.width()}, plugin.options.animationSpeed, function() {
						
						plugin.$container.css('left', -plugin.$container.width());

						NavigationSystem.Navigate(navigation);
						
						plugin.$container.stop().animate({left: 0}, plugin.options.animationSpeed, function() {
							
						});
						
					});
				}
				else
				{
					
					plugin.$container.stop().animate({left: -plugin.$container.width()}, plugin.options.animationSpeed, function() {
						
						plugin.$container.css('left', plugin.$container.width());
						
						NavigationSystem.Navigate(navigation);
						
						plugin.$container.stop().animate({left: 0}, plugin.options.animationSpeed, function() {
							
						});
						
					});
				}
			break;
			
			case SliderMenu.ANIMATION_TYPE_FADE:
			
			plugin.setActiveMenu(index);
			
			plugin.$container.stop().fadeOut(plugin.options.animationSpeed, function() {
				
				NavigationSystem.Navigate(navigation);
				
				plugin.$container.stop().fadeIn(plugin.options.animationSpeed, function() {
					
				});
				
			});
			
			break;
			
		}
	}
  }
  
  $.fn[pluginName] = function (options)
  {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
				new SliderMenu(this, options));
			}
		});
  }
  
})(jQuery, window, document);