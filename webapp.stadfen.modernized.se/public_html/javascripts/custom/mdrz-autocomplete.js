;(function ($, window, document, undefined) {

  var pluginName = "autocomplete";

  var AutoComplete = function (element, options) {
	
	// Setting up variables
    this.$element 		= 	$(element);
	this.$element.parent().addClass('autocomplete');
	this.$element.addClass('autocomplete-input');
	this.$element.parent().append('<div class="population"></div>');
	this.$population	=	this.$element.parent().find('.population');
	this.searchArray	= 	new Array();
    this.options  		= 	$.extend( {}, AutoComplete.DEFAULTS, options);
	
	// Initializes the plugin
	this.init();

  }
  
  AutoComplete.VERSION  = '1.0.0'

  AutoComplete.DEFAULTS = {
		data:null,
		itemclick:null,
		keyup:null
  }
  
  AutoComplete.prototype.updateData = function(d) {
	  
	  var plugin = this;
	  
	  plugin.options.data = d;
	  
  }
  
  
  AutoComplete.prototype.init = function() {
	
	var plugin = this;
	
	var clicky;
	
	this.$element.focus(function(e) {
		if (plugin.searchArray.length > 0)
		{
			
			// Update the height value
			plugin.$population.css('top', plugin.$element.parent().outerHeight(false) - 1);
			
			plugin.$population.show();
		}
	});
	
	this.$element.blur(function(e)
	{
		if(!clicky)
			plugin.$population.hide();
	});
	
	this.$element.keydown(function(e) {
		if (plugin.options.keydown != null)
			plugin.options.keydown(e);	
	});

	this.$element.keyup(function(e) {
		
		// Call the custom function if it exists
		if (plugin.options.keyup != null)
		plugin.options.keyup(e);
		
		// Clean the list
		plugin.$population.html('');
		
		// Clear the array since we have a new search
		plugin.searchArray.length = 0;
		
		// Get the string value
		var current_str = plugin.$element.val();
		
		// Update the height value
		plugin.$population.css('top', plugin.$element.parent().outerHeight(false) - 1);
		
		clicky = false;
		
		if (plugin.options.data != null && current_str != "")
		{
			var items = "";
			
			// Loop through our data to see if we have any matches
			for (var i = 0; i < plugin.options.data.length; i++)
			{
				
				var formatted = "0" + plugin.options.data[i].phonenr.slice(2);
				
				if (plugin.options.data[i].phonenr.indexOf(current_str) > -1 || (plugin.options.data[i].name != null && plugin.options.data[i].name.toLowerCase().indexOf(current_str.toLowerCase()) > -1) || formatted.indexOf(current_str.replace("-","")) > -1 || (plugin.options.data[i].nickname != null && plugin.options.data[i].nickname.toLowerCase().indexOf(current_str.toLowerCase()) > -1))
				{
					plugin.searchArray.push(plugin.options.data[i].id);
					var display_str = GetConversationDisplay(plugin.options.data[i].phonenr, plugin.options.data[i].name, plugin.options.data[i].nickname);
					
					items += '<div class="item" data-nr="'+plugin.options.data[i].phonenr+'" '+(plugin.options.data[i].name ? 'data-name="'+plugin.options.data[i].name+'"' : '')+' '+(plugin.options.data[i].nickname ? 'data-nickname="'+plugin.options.data[i].nickname+'"' : '')+'>' + display_str + '</div>';
					
				}
			}
			
			if (plugin.searchArray.length > 0)
			{
				plugin.$population.append(items);
				
				plugin.$population.find('.item').each(function() {
					
					$(this).mousedown(function(e) {
                        clicky = true;
                    });
					$(this).click(plugin.options.itemclick);
					
				});
				
				plugin.$population.show();
			}
			else
			plugin.$population.hide();
			
		}
		else
		{
			plugin.$population.hide();
		}
		
	});
	  
  };
  
  AutoComplete.prototype.clear = function() {
	  
	var plugin = this;  
	
	  // Clean the list
	plugin.$population.html('');
	// Clear the array since we have a new search
	plugin.searchArray.length = 0;
	// Now hide it
	plugin.$population.hide();
  }

  $.fn[pluginName] = function (options)
  {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
				new AutoComplete(this, options));
			}
		});
  }
  
})(jQuery, window, document);