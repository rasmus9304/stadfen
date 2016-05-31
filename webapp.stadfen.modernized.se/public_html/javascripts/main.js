// JavaScript Document

var popupbox;
var autocomplete_form;

function parseSignature(text)
{
	return text.replace("{COMPANYNAME}",Stuff.CompanyName).replace("{DISPLAYNAME}",Stuff.DisplayName);
}

function appendTemplateToNewMessage()
{
	var i = $(this);
	var id = i.data('id');
	
	Templates.RequestTemplate(id, function(data) {
		
		if (data.Success)
		{
			var Title = data.Template.Title;
			var Text = data.Template.Text;
			var signature = '';
			
			/*if (Stuff.HaveMySignature == true && Stuff.CompanySignatureActive == false)
			{
				signature += SystemSettings.SIGNATUREGLUE + Stuff.MySignature;
			}*/
			
			$('.popover-newmessage-form .send-message-text').val(Text + signature);
			
			var count = Text.length + signature.length;
			$('.popover-newmessage-form .text-length').html(count + "/" + Signature.GetMaxMessageLength()); 
		}
		
	});
	
}

function askDeleteMessage(id)
{
	popupbox.openPopup(500, "Ta bort meddelande", "<p class='popup-p'>Vill du ta bort det här meddelandet?</p>", new YesNoDialog(function() {
		Conversations.DeleteMessage(id);
	}, null));
	
}

function askDeleteThread(id)
{
	popupbox.openPopup(500, "Ta bort konversation", "<p class='popup-p'>Vill du ta bort den här konversationen?</p>", new YesNoDialog(function() {
		Conversations.Delete(id);
	}, null));
}

function appendTemplateToReplyMessage()
{
	var i = $(this);
	var id = i.data('id');
	
	Templates.RequestTemplate(id, function(data) {
		
		if (data.Success)
		{
			var Title = data.Template.Title;
			var Text = data.Template.Text;
			var signature = '';
			
			
			$('.reply-container #reply-message-text').val(Text + signature);
			
			var count = Text.length + signature.length;
		}
		
	});
}

$(document).ready(function(e) {
	
	showInitScreen();

	$(document).mouseup(function (e)
	{
		var container = $(".reply-container");
		var container2 = $(".menu-context");
		
		if ((!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) && (!container2.is(e.target)
			&& container2.has(e.target).length === 0)) // ... nor a descendant of the container
		{
			
			$('.reply-character-count').addClass('hidden');
			$('.reply-container').animate({ height: '68px' }, 300, function() {
			
			});
			$("#conv-bottom-space").animate({height: '68px'},300);
		}
	});
	
	$('.attach-template').click(function(e) {
        reloadReplyBoxTemplates();
    });

});

function showReplyBox()
{
	var currentCount = $('.reply-container #reply-message-text').val().length;
	
	//calculateMaxMessageLength();
	$('.reply-container').animate({ height: '160px' }, 300, function() {
		$('.reply-container .reply-character-count').html(currentCount + "/" + Signature.GetMaxMessageLength());
		$('.reply-container #reply-message-text').attr('maxlength', Signature.GetMaxMessageLength());
		
		$('.reply-container .reply-character-count').removeClass('hidden');
	});
	$("#conv-bottom-space").animate({height: '160px'},300, function()
	{
		//$('.conversation-thread .messages').scrollTop($('.conversation-thread .messages')[0].scrollHeight);
		
	});
	
	AnimateConversationScrollToBottom(300);
}

function reloadReplyBoxTemplates()
{
	Templates.RequestList(function(data) {
		
		if (data.Success)
		{
			var menu = [];
			var Templates = data.Templates;
			for (var i = 0; i < Templates.length; i++)
			{
				var ID = Templates[i].ID;
				var Title = Templates[i].Title;
				var IsCustomerTemplate = Templates[i].IsCustomerTemplate;
				
				menu[menu.length] = { id: ID, title: Title, personalTemplate: IsCustomerTemplate, callback:appendTemplateToReplyMessage };
			}
			
			if(Templates.length > 0)
			ContextMenu.Show($('.reply-container .attach-template'), menu);
		}
		
	});
}

function initReplyBox()
{
	
	$('.conversation-thread .reply-button').click(function(e) {
		
		$('#reply-message-text').focus();
		
		showReplyBox();
    });
	
	$('.reply-container .message-text-container').focusin(function(e)
	{
		showReplyBox();
	});
	
	$('.reply-container #reply-message-text').keyup(function(e) {
		var count = $(this).val().length;
		
		if (count <= Signature.GetMaxMessageLength())
		{
			$('.reply-container .reply-character-count').html(count + "/" + Signature.GetMaxMessageLength());
			
			if ($('.reply-container .reply-character-count').hasClass('warning'))
				$('.reply-container .reply-character-count').removeClass('warning');
		}
		else 
		{
			if (!$('.reply-container .reply-character-count').hasClass('warning'))
				$('.reply-container .reply-character-count').addClass('warning');
		}
		
	});
	
	$('.reply-container #reply-btn-send').click(function(e) {
        
		var msg = $('.reply-container #reply-message-text').val();
		
		if (msg != "" && msg != null)
		{
		
			var current_conv = $(Conversations.GetConvListItem(Conversations.Current));
			var convID = Conversations.Current;
			var nr = current_conv.data('convnumber');
			var phone_nr = Phonenumber.GetDisplayStyle(nr);
			
			SendMessageRequest(true, msg, Signature.GetCurrent(), parseInt(convID), phone_nr, function(data)
			{
				$('.reply-container #reply-message-text').val('');
			});
			
			$('[data-toggle="popover"]').popover('hide');
		
		}
		else
		{
			alert("Meddelandefältet får ej vara tomt.");	
		}
		
    });
	
}

function loadNewMessageBoxTemplates()
{
	Templates.RequestList(function(data) {
		
		if (data.Success)
		{
			var menu = [];
			var Templates = data.Templates;
			for (var i = 0; i < Templates.length; i++)
			{
				var ID = Templates[i].ID;
				var Title = Templates[i].Title;
				var IsCustomerTemplate = Templates[i].IsCustomerTemplate;
				
				menu[menu.length] = { id: ID, title: Title, personalTemplate: IsCustomerTemplate, callback:appendTemplateToNewMessage };
			}

			ContextMenu.Show($('.popover .open-templates'), menu);
		}
		
	});
}

function reloadNewMessageBox()
{

	var sent = false;
	var currentCount = 0;

	$(".popover .open-templates").click(function(e) {
        loadNewMessageBoxTemplates();
    });

	$(".popover .form-control.message-text-container .company-signature").remove();
	var signature = Signature.GetCurrentWithGlue();
	if(signature != null)
	{
		$(".popover .form-control.message-text-container").append('<div class="company-signature" onclick="$( \'#send-message-text\').focus();" style="cursor:text;">'+_nl2br(signature));
	}
	
	$('.popover-newmessage-form .text-length').html(currentCount + "/" + Signature.GetMaxMessageLength());
	$('.popover .send-message-text').attr('maxlength', Signature.GetMaxMessageLength());
	
	
/*		var data = [ 
	{phonenr: "0737330426", name:"Rasmus Berg-Lundfeldt"},
	{phonenr: "0730521321", name:"Erik Johansson"},
	{phonenr: "0764214124"} ];*/
	
	var data = [];
	
	$("#conversation_list .conversation-item").each(function(index, element) {
		
		var element = $(this);
		
		data[data.length] = { phonenr: element.data("convnumber"), name: element.data("convname"), nickname: element.data("convnickname") };
		
	});

	autocomplete_form.updateData(data);
}


function initNewMessageBox()
{
	
	var c = $('#popover_content_wrapper').clone();
	$('#popover_content_wrapper').remove();
	
	c.css('display', 'block');
	
	var $popup = $('[data-toggle="popover"]').popover({html: true, content: c});
	
	var data = [];
	
	c.find('.popover-newmessage-form #send-message-phonenr').autocomplete({data:data, itemclick:autoCompleteItemClick, keyup:autoCompleteKeyUp, keydown:autoCompleteKeyDown});
	
	autocomplete_form = c.find('.popover-newmessage-form #send-message-phonenr').data()["plugin_autocomplete"];
	
	c.find('.popover-newmessage-form .btn-send-message').click(function(e) {
		
		if($('.autocomplete .autocomplete-input').val() == "" || userInputNumber(false))
		{
			var msg = $('.popover-newmessage-form .send-message-text').val();
			
			if (msg != "" && msg != null)
			{
				
				if (msg.length <= SystemSettings.MAX_MESSAGE_LENGTH)
				{
					var phone_nr = "";
					
					$('.popover-newmessage-form .autocomplete .added-number').each(function() {
						phone_nr += Phonenumber.GetDisplayStyle($(this).attr('data-nr')) + ',';
					});
					
					if (phone_nr != "")
					{
						phone_nr = phone_nr.substring(0, phone_nr.length - 1);
						
						SendMessageRequest(false, msg, Signature.GetCurrent(), null, phone_nr, function(data)
						{
								
						});
						
						$('[data-toggle="popover"]').popover('hide');
					}
					else
					{
						alert("Du måste skicka meddelandet till minst en mottagare.");	
					}
				}
				else
				{
					alert("Du har överskridit meddelandegränsen för det här meddelandet.");
				}
			}
			else
			{
				alert("Meddelandefältet får ej vara tomt.");
			}
		}
	});
	
	c.find('.popover-newmessage-form .send-message-text').flexText();
	
	c.find('.popover-newmessage-form .send-message-text').keyup(function(e) {
		var count = $(this).val().length;
		
		if (count <= Signature.GetMaxMessageLength())
		{
			$('.popover-newmessage-form .text-length').html(count + "/" + Signature.GetMaxMessageLength());
			
			if ($('.popover-newmessage-form .text-length').hasClass('warning'))
			$('.popover-newmessage-form .text-length').removeClass('warning');
		}
		else 
		{
			if (!$('.popover-newmessage-form .text-length').hasClass('warning'))
			$('.popover-newmessage-form .text-length').addClass('warning');
		}
	});
	
	$('[data-toggle="popover"]').on('shown.bs.popover', function() {
		
		reloadNewMessageBox();

	});

	$('[data-toggle="popover"]').on('show.bs.popover', function() {
		$('.bg').removeClass().addClass('bg');
		$('.bg').addClass('bg faded').fadeIn(100);
	});
	
	$('[data-toggle="popover"]').on('hide.bs.popover', function() {
		$('.bg').fadeOut(100, function() {
			clearNewMessage(); 
		});
	});
		
	$('.bg').click(function(e) {

		$('[data-toggle="popover"]').popover('hide');
		
    });
}

function clearNewMessage()
{
 	clearAutoCompleteInput();
	clearNumbers();
}

function clearNumbers()
{
	$('.autocomplete .added-number').remove();	
}

function clearAutoCompleteInput()
{
	$('.autocomplete .autocomplete-input').val('');
	autocomplete_form.clear();
}

function addNumber(el)
{

	if ($('.autocomplete .added-number').length >= SystemSettings.MAX_DESTINATION_COUNT)
	{
		// Clear the search and input data
		clearAutoCompleteInput();
		// Alert the user
		alert("Du kan inte skicka till fler nummer.");
		return;
	}
	
	if (el != null)
	{
		var element = $(el);
		var str;
		
		// Check if it has a valid name, otherwise just print the number
		str = GetConversationDisplay(element.attr('data-nr'), element.attr('data-name'), element.attr('data-nickname'));
		
		// If we already have the number in the list, dont add it again
		if ($('.autocomplete .added-number[data-nr="'+element.attr('data-nr')+'"]').length > 0)
		{
			alert("Du har redan lagt till " +element.attr('data-nr')+ ".");
			clearAutoCompleteInput();
			return;
		}
		
		// Add an send object to the popover
		$('.autocomplete').prepend('<div class="added-number" data-nr="'+element.attr('data-nr')+'">'+str+'<img style="width: 8px; height: 8px; margin: 0px 6px; cursor: pointer;" src="/images/icon-delete.svg" class="remove"></div>');
		
		$('.autocomplete .added-number[data-nr="'+element.attr('data-nr')+'"] img.remove').click(removeNumber);
	}
	else
	{
		// Get the undentified number written by the user
		var number = $('.autocomplete .autocomplete-input').val();
		var formatted = Phonenumber.ParseToStandard(number);
		
		// If we already have the number in the list, dont add it again
		if ($('.autocomplete .added-number[data-nr="'+formatted+'"]').length > 0)
		{
			alert("Du har redan lagt till " +Phonenumber.GetDisplayStyle(formatted)+ ".");
			clearAutoCompleteInput();
			return;
		}
		
		// Add an send object to the popover
		$('.autocomplete').prepend('<div class="added-number" data-nr="'+formatted+'">'+Phonenumber.GetDisplayStyle(formatted)+'<img style="width: 8px; height: 8px; margin: 0px 6px; cursor: pointer;" src="/images/icon-delete.svg" class="remove"></div>');
		
		$('.autocomplete .added-number[data-nr="'+formatted+'"] img.remove').click(removeNumber);
	}
	
	clearAutoCompleteInput();
}

function removeNumber(e)
{
	$(this).parent().remove();
}

function preHistoricalString()
{
	var preHistoricalString = $('.autocomplete .autocomplete-input').val();
	preHistoricalString = preHistoricalString.substring(0, preHistoricalString.length - 1);
	$('.autocomplete .autocomplete-input').val(preHistoricalString);	
}

function isValidNumberString(str)
{
	var regexp = /^\+?\d+$/;
	return regexp.test(str);
}

function userInputNumber(useprehistorical)
{
	if(useprehistorical)
		preHistoricalString();
	var input_val = $('.autocomplete .autocomplete-input').val();
	
	if (input_val.trim() != "" && isValidNumberString(input_val))
	{
		addNumber(null);
		return true;
	}
	else
	{
		alert("Du måste skriva in ett giltigt nummer.");
		return false;
	}
}

function autoCompleteRemoveLatest()
{
	$('.autocomplete .added-number:last').remove();
}

function autoCompleteKeyUp(e) {
	
	var code = e.keyCode || e.which;
	var ret = true;
	var $inputField = $('.popover-newmessage-form #send-message-phonenr');
	switch (code)
	{
		case 13: // [enter]
			 event.preventDefault();
     		 ret = false;
			 userInputNumber(false);
			 break;
		case 32: // Spacebar
		case 188: // ,
			userInputNumber(true);
		break;
	}
	return ret;
}

function autoCompleteKeyDown(e)
{
	var code = e.keyCode || e.which;
	var ret = true;
	var $inputField = $('.popover-newmessage-form #send-message-phonenr');
	switch (code)
	{
		case 8: // Backspace
			if($inputField.val() == "")
				autoCompleteRemoveLatest();
			ret = true;
			break;
	}
	return ret;
}

// When we press a search item
function autoCompleteItemClick(e) {
	
	addNumber(this);
	
}

function initPage()
{
	initUI();
}

function initUI()
{
	initSliders();
	initNewMessageBox();
	initSearchbar();
	initMessageBox();
	initReplyBox();
	initCheckboxes();
}

function initCheckboxes()
{
	$('.checkbox-default input').each(function(index, element) {
        
		if (this.checked && !this.disabled)
		{
			$(this).parent().addClass('checked-style');
		}
		else
		{
			$(this).parent().removeClass('checked-style');
		}
		
		if ($(this).is(':disabled'))
		{
			$(this).parent().addClass('disabled');	
		} 
		
    });
	
	$('.checkbox-default input').unbind("change").change(function(e) {
        
		if (this.checked)
		{
			$(this).parent().addClass('checked-style');
		}
		else
		{
			$(this).parent().removeClass('checked-style');
		}
		
		if ($(this).is(':disabled'))
		{
			$(this).parent().addClass('disabled');	
		}
		else
		{
			$(this).parent().removeClass('disabled');	
		}
		
    });	
}

var conversationSlider;

function initMessageBox()
{
	popupbox = new PopupWindow('body');
}

function initSliders()
{
	conversationSlider = $('#conversation-list-slider').slider().data("plugin_slider");
}

function initSearchbar()
{
	$('.searchbar input').focusin(function(e) {
        $(this).parent().width(200);
    });
	
	$('.searchbar input').focusout(function(e) {
        $(this).parent().css('width', '');
    });
}

function addZero(i)
{
	if (i < 10)
	{
		return "0"+i;	
	}
	
	return i;
}

function formatDate(d)
{
	return addZero(d.getHours()) + ":" + addZero(d.getMinutes());
}

function updateMenuItem()
{
	var $body = $('body');
	$('.menu-items .item').removeClass('selected');
	
	$('.menu-items .item#sidemenubutton_conversations img').attr('src', '/images/icon-menu-inbox.svg');
	$('.menu-items .item#sidemenubutton_statistics img').attr('src', '/images/icon-menu-datatraffic.svg');
	$('.menu-items .item#sidemenubutton_settings img').attr('src', '/images/icon-menu-settings.svg');
	
	var $element;
	
	// Check which module we are in
	if ($body.hasClass('module_conversations'))
	{
		$element = $('#sidemenubutton_conversations');
		$element.addClass('selected');
		$element.find('img').attr('src', '/images/icon-menu-inbox-selected.svg');
	}
	else if ($body.hasClass('module_statistics'))
	{
		$element = $('#sidemenubutton_statistics');
		$element.addClass('selected');
		$element.find('img').attr('src', '/images/icon-menu-datatraffic-selected.svg');
	}
	else if ($body.hasClass('module_settings') || $body.hasClass('module_newaccount') || $body.hasClass('module_editaccount'))
	{
		$element = $('#sidemenubutton_settings');
		$element.addClass('selected');
		$element.find('img').attr('src', '/images/icon-menu-settings-selected.svg');
	}

}