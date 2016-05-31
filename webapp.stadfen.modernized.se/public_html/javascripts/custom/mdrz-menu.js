;(function ($, window, document, undefined) {

var pluginName = "dynamicmenu";

  var DynamicMenu = function (element, options) {
	
	// Setting up variables
    this.$element 		= 	$(element);
	this.$menu			=	null;
	this.options  		= 	$.extend( {}, DynamicMenu.DEFAULTS, options);
	
	// Initializes the plugin
	this.init();

  }
  
  DynamicMenu.VERSION  = '1.0.0'

  DynamicMenu.DEFAULTS = {
    menu:null
  }
  
  DynamicMenu.prototype.destroy = function() {
	  $.removeData(this.$element.get(0));
	  $('.menu-context').remove();
	  this.$element.off('plugin_' + pluginName);
	  this.$element.unbind('.plugin_' + pluginName);
	  this.options.menu = null;
  }
  
  DynamicMenu.prototype.init = function() {
	
		var plugin = this;

		if (this.options.menu == null)
		{
			alert("Måste initiera en meny.");
			return;	
		}
	
		var items = '<div class="menu-context">';
		var menu = this.options.menu;
		
		for (var i = 0; i < menu.length; i++)
		{
			var title = menu[i].title;
			
			items += '<div class="menu-item">'+title+'</div>';
		}
		
		items += '</div>';
		
		$('body').append(items);
		this.$menu = $('body').find('.menu-context');
		
		var length = 0;
		$('body').find('.menu-context .menu-item').each(function(index, element) {
			
			$(this).data('id', menu[length].id);
			$(this).click(menu[length].callback);
			
			length++;
		});
		
		$(document).not(this.$menu).click(function(e) {
			plugin.destroy();
		});

		/*this.$element.click(function(e) {
			e.stopPropagation();
			plugin.show();
		});*/
		
		plugin.show();
		
		//this.hide();
	
  };
  
  DynamicMenu.prototype.show = function() {
		
		this.$menu.css('visibility','hidden');
		this.$menu.show();
		var height = this.$menu.outerHeight();
		var width = this.$menu.outerWidth();

		var window_width = $(window).width();
		var window_height = $(window).height();
		
		var el_pos = this.$element.offset();
		var el_width = this.$element.width();
		var el_height = this.$element.height();
		
		this.$menu.css('top', el_pos.top + el_height + 'px');
		this.$menu.css('left', el_pos.left + 'px');
		this.$menu.css('height', height + 'px');
		
		var current_pos_top = this.$menu.position().top;
		var current_pos_left = this.$menu.position().left;
		
		if (current_pos_top + height >= window_height)
		{
			this.$menu.css('top', 'auto');
			this.$menu.css('bottom', (window_height - el_pos.top) + 'px');
		}
		else if (current_pos_top - height <= 0)
		{
			this.$menu.css('top', el_pos.top + 'px');
		}
		
		if (current_pos_left - width <= 0)
		{
			this.$menu.css('left', el_pos.left + 'px');
		}
		else if (current_pos_left + width >= window_width)
		{
			this.$menu.css('right', (window_width - el_pos.left) + 'px');
			this.$menu.css('left', 'auto');
		}
		
		this.$menu.css('visibility', 'visible');
	  
  }
    
  DynamicMenu.prototype.hide = function() {
	
		this.$menu.hide();
	  
  }
  
  DynamicMenu.prototype.toggle = function() {
	
		if (this.$menu.css('display') == 'block')
		this.hide();
		else
		this.show();
	  
  }
  
  $.fn[pluginName] = function (options)
  {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
				new DynamicMenu(this, options));
			}
		});
  }
  
})(jQuery, window, document);