function PopupDialogButton(handler, title, result)
{
	this.handler = handler;
	this.title = title;
	this.result = result;
}

function YesNoDialog(yes, no)
{
	this.handler = 1;
	this.buttons = [
	new PopupDialogButton('popup-button-yes', "Ja", yes),
	new PopupDialogButton('popup-button-no', "Nej", no)
	]
}

function YesNoCancelDialog(yes, no, cancel)
{
	this.handler = 2;
	this.buttons = [
	new PopupDialogButton('popup-button-yes', "Ja", yes),
	new PopupDialogButton('popup-button-no', "Nej", no),
	new PopupDialogButton('popup-button-cancel', "Avbryt", cancel)
	]
}

function OkDialog(ok)
{
	this.handler = 3;
	this.buttons = [
	new PopupDialogButton('popup-button-ok', "Ok", ok)
	]
}

function OkCancelDialog(ok, cancel)
{
	this.handler = 4;
	this.buttons = [
	new PopupDialogButton('popup-button-ok', "Ok", ok),
	new PopupDialogButton('popup-button-cancel', "Avbryt", cancel),
	]
}

function PopupWindow(parent) {
	
	this.parent = parent;
	this.init = function() {};
	this.title = "Popup window";
	this.moveable = false;
	this.resizable = false;
	this.handler = "popup-window";
	this.dialog = null;
	this.language = "eng";
	this.default_w = 350;
	this.w = this.default_w; // Default
	this.h = 0; // Dynamic
	
	// Add the popup window
	$(parent).prepend('<div id="'+ this.handler +'"><div class="absolute-center popup-box clearfix"><div class="popup-windowbar"></div><h1 class="popup-title"></h1><div class="popup-content clearfix"></div><div class="popup-dialog clearfix"></div></div></div>');
	
	this.getHeight = function()
	{
        this.h = $('#' + this.handler + ' .popup-box .popup-windowbar').outerHeight(true) + $('#' + this.handler + ' .popup-box .popup-content').outerHeight(true) + $('#' + this.handler + ' .popup-box .popup-title').outerHeight(true) + $('#' + this.handler + ' .popup-box .popup-dialog').outerHeight(true);
		
		$('#' + this.handler + ' .popup-box').height(this.h);
	}
	
	this.getPopup = function()
	{
		return $('#' + this.handler);	
	}

	this.getDialog = function(dialog)
	{
		var html = '';
		
		for (var i = 0; i < dialog.buttons.length; i++)
		{
			html += '<a role="button" class="btn btn-default" id="' + dialog.buttons[i].handler + '">'+ dialog.buttons[i].title +'</a>';
		}
		
		return html;
	}
	
	this.initDialog = function(dialog)
	{
		var box = this;
		
		for (var i = 0; i < dialog.buttons.length; i++)
		{
			var btn = dialog.buttons[i];
			
			$('#' + btn.handler).click({button:btn}, function(e) {
				
				// If we have a callback
				if (e.data.button.result != null)
				e.data.button.result();
				
				// The action is done, close the popup box
				box.closePopup();
				
			});
		}
	}
	
	this.openPopup = function(w, title, content, dialog)
	{
		$('#' + this.handler).css('visibility','hidden');
		$('#' + this.handler).css('display','block');

		$('#' + this.handler + ' .popup-box').css('width',w);
		this.w = w;
		
		$('#' + this.handler + ' .popup-box .popup-content').html(content);
		$('#' + this.handler + ' .popup-box .popup-title').html(title);
		$('#' + this.handler + ' .popup-box .popup-dialog').html(this.getDialog(dialog));
		
		this.init();
		this.initDialog(dialog);
		this.getHeight();

		$('#' + this.handler).css('display','none');
		$('#' + this.handler).css('visibility','visible');

		$('#' + this.handler).fadeIn(150, function() {
			
		});
	}

	this.closePopup = function()
	{
		$('#' + this.handler).fadeOut(150, function() {
			$('#' + this.handler + ' .popup-box').attr('style', '');
		});
	}
	
	$('#' + this.handler).click({obj:this}, function(e) {
		if(e.target != this) return;
		e.data.obj.closePopup();	
	});
	
}