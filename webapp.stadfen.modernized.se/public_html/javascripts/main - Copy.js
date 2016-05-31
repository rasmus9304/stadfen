// JavaScript Document

var popupbox;
var currentEditMax;
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
			$('.popover-newmessage-form .text-length').html(count + "/" + currentEditMax); 
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
			
			/*if (Stuff.HaveMySignature == true && Stuff.CompanySignatureActive == false)
			{
				signature += SystemSettings.SIGNATUREGLUE + Stuff.MySignature;
			}*/
			
			$('.reply-container #reply-message-text').val(Text + signature);
			
			var count = Text.length + signature.length;
			//$('.popover-newmessage-form .text-length').html(count + "/" + currentEditMax);
		}
		
	});
}

$(document).ready(function(e) {

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
	
});

function showReplyBox()
{
	var currentCount = $('.reply-container #reply-message-text').val().length;
	
	calculateMaxMessageLength();
	$('.reply-container').animate({ height: '160px' }, 300, function() {
		$('.reply-container .reply-character-count').html(currentCount + "/" + currentEditMax);
		$('.reply-container #reply-message-text').attr('maxlength', currentEditMax);
		
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
			
			ContextMenu.Show($('.reply-container .attach-template'), menu);
		}
		
	});
}

function initReplyBox()
{
	reloadReplyBoxTemplates();
	
	
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
		
		if (count <= currentEditMax)
		{
			$('.reply-container .reply-character-count').html(count + "/" + currentEditMax);
			
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
			
			SendMessageRequest(true, msg, null, parseInt(convID), phone_nr, function(data)
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
/*
function calculateMaxMessageLength()
{
	if(Stuff.CompanySignatureActive)
	{
		currentEditMax = SystemSettings.MAX_MESSAGE_LENGTH - Stuff.CompanySignature.length - SystemSettings.SIGNATUREGLUE.length;

	}
	else if(Stuff.HaveMySignature)
	{
		currentEditMax = SystemSettings.MAX_MESSAGE_LENGTH;
	}
	else
	{
		currentEditMax = SystemSettings.MAX_MESSAGE_LENGTH;
	}
}*/



function reloadNewMessageBox()
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
			
			if(Templates.length > 0)
				ContextMenu.Show($('.popover .open-templates'), menu);
		}
		
	});
	
	var sent = false;

	var currentCount = 0;
	
	calculateMaxMessageLength();
	
	
	$(".popover .form-control.message-text-container .company-signature").remove();
	if(Stuff.CompanySignatureActive)
	{
		/*var preText = $(".popover .send-message-text").text();
		if( StringOperations.endsWith(preText,SystemSettings.SIGNATUREGLUE + Stuff.MySignature)) // In case settings were just changed to enable companysign, clear the personal signature
		{
			var trimmedText = StringOperations.removeFromEnd($(".popover .send-message-text").text(), SystemSettings.SIGNATUREGLUE + Stuff.MySignature);
			$(".popover .send-message-text").text(trimmedText);
		}*/
		/*$(".popover .form-control.message-text-container").append('<div class="company-signature" onclick="$( \'#send-message-text\').focus();" style="cursor:text;">'+_nl2br(Stuff.CompanySignature)+'</div>');
		currentCount = $(".popover .send-message-text").val().length;*/
	}
	else if(Stuff.HaveMySignature)
	{
		/*$(".popover .send-message-text").text(SystemSettings.SIGNATUREGLUE + Stuff.MySignature)
		currentCount = $(".popover .send-message-text").val().length;*/
	}
	
	var signature = Signature.GetCurrentWithGlue();
	
	if(signature != null)
	{
		(".popover .form-control.message-text-container").append('<div class="company-signature" onclick="$( \'#send-message-text\').focus();" style="cursor:text;">'+_nl2br(SystemSettings.SIGNATUREGLUE + signature)+'</div>');
	}
	
	$('.popover-newmessage-form .text-length').html(currentCount + "/" + currentEditMax);
	$('.popover .send-message-text').attr('maxlength', currentEditMax);
	
	
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
	
	c.find('.popover-newmessage-form #send-message-phonenr').autocomplete({data:data, itemclick:autoCompleteItemClick, keyup:autoCompleteKeyUp});
	
	autocomplete_form = c.find('.popover-newmessage-form #send-message-phonenr').data()["plugin_autocomplete"];
	
	c.find('.popover-newmessage-form .btn-send-message').click(function(e) {

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
					
					SendMessageRequest(false, msg, null, null, phone_nr, function(data)
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
	});
	
	c.find('.popover-newmessage-form .send-message-text').flexText();
	
	c.find('.popover-newmessage-form .send-message-text').keyup(function(e) {
		var count = $(this).val().length;
		
		if (count <= currentEditMax)
		{
			$('.popover-newmessage-form .text-length').html(count + "/" + currentEditMax);
			
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

function userInputNumber()
{
	preHistoricalString();
	var input_val = $('.autocomplete .autocomplete-input').val();
	
	if (input_val.trim() != "" && isValidNumberString(input_val))
	addNumber(null);
	else
	{
		alert("Du måste skriva in ett giltigt nummer.");
	}
}

function autoCompleteKeyUp(e) {
	
	var code = e.keyCode || e.which;
	var ret = true;
	switch (code)
	{
		case 13: // [enter]
			 event.preventDefault();
     		 ret = false;
		case 32: // Spacebar
		case 188: // ,
			userInputNumber();
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
        
		if (this.checked)
		{
			$(this).parent().removeClass('checked-style');
		}
		else
		{
			$(this).parent().addClass('checked-style');
		}
		
		if ($(this).is(':disabled'))
		{
			$(this).parent().addClass('disabled');	
		} 
		
    });

	$('.checkbox-default input').change(function(e) {
        
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

function initMessageBox()
{
	popupbox = new PopupWindow('body');
}

function initSliders()
{
	$('#conversation-list-slider').slider();
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