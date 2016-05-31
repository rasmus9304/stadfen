function parseConversationList(arrayList)
{
	for(var i = 0; i < arrayList.length; i++)
	{
		var ConvID = arrayList[i].ConversationID;
		var elementID = Conversations.ConvListItemID(ConvID);
		
		
		//Figure out
		var elementList, listname;
		if(parseInt(arrayList[i].Archived))
		{
			elementList = document.getElementById('convlist_archived');
			listname = "archived";
		}
		else if(parseInt(arrayList[i].Active))
		{
			elementList = document.getElementById('convlist_active');
			listname = "active";
		}
		else
		{
			elementList = document.getElementById('convlist_inactive');
			listname = "inactive";
		}
		var $eList = $(elementList);
		
		var element = document.getElementById(elementID);
		var $element = $(element);
		
		var isFavorite = (parseInt(arrayList[i].Favorite) > 0);
		var fav_img = '';
		var fav_selected = '';
		
		if (isFavorite)
		{
			fav_img = ICON_FAVORITE;
			fav_selected = 'selected';
		}
		else
			fav_img = ICON_NOTFAVORITE;
		
		if(element === null)
		{
			//New, has to be created
$eList.append('<div class="conversation-item" id="'+ elementID +'" onclick="onSelectConversation('+ConvID+')"> <div class="right-bar"><img class="favorite '+fav_selected+' no-nav" src="'+fav_img+'" onClick="Conversations.ToggleFavorite('+ConvID+');"><img class="thread-error-icon no-nav" src="/images/icon-alert.svg" onclick="askDeleteThread('+ConvID+');"></div><div class="number"><h2 class="title"></h2><div class="dropdown no-nav" onclick="openContextMenu('+ConvID+');"><img src="/images/dropdown-arrow.png"></div><p class="lastmessage"></p><p class="lastmessagetime" style="margin: 0; font-size: 1em;"></p></div></div>');
			
			element = document.getElementById(elementID);
			$element = $(element);
			
			
			$('.no-nav').click(function(e) {
				e.stopPropagation();
			});

		}
		else
		{
			//May have been moved to different list
			if($element.data("listname") != listname)
			{
				$element.detach().appendTo(elementList);
			}
		}
		$element.data("listname",listname);
		

		$element.find('.right-bar img.favorite').attr('src', fav_img);
		
		var newincoming = (arrayList[i].NewMessageCount > 0);
		
		$element.find(".title").html(escapeHtml(GetConversationDisplay(arrayList[i].Number, arrayList[i].ConversationName, arrayList[i].Nickname)));
		$element.find(".lastmessage").html(escapeHtml(arrayList[i].LastMessage));
		$element.find(".lastmessagetime").html(formatDate(parseDateTime(arrayList[i].LastMessageTime)));
		$element.setOrder(Conversations.GetConvListOrderValue(arrayList[i].LastMessageID, (parseInt(arrayList[i].Favorite) ? true : false)));
		
		
		$element.toggleClass("favorite",isFavorite);
		$element.toggleClass("thread-error",(parseInt(arrayList[i].ErrorCount) > 0));
		$element.toggleClass("removable",(arrayList[i].MessageCount == arrayList[i].ErrorCount));
		
			
		var prevNewMessageCount = parseInt($element.data("newmessagecount")); //From before this load
		
		//Store data in element
		$element.data("convid",arrayList[i].ConvID);
		$element.data("convname",arrayList[i].ConversationName);
		$element.data("convnumber",arrayList[i].Number);
		$element.data("convnickname",arrayList[i].Nickname);
		$element.data("archived",parseInt(arrayList[i].Archived) ? true : false);
		$element.data("active",parseInt(arrayList[i].Active) ? true : false);
		$element.data("favorite",(isFavorite ? true : false));
		$element.data("lastmessageid",(arrayList[i].LastMessageID ? parseInt(arrayList[i].LastMessageID) : 0));
		$element.data("newmessagecount",parseInt(arrayList[i].NewMessageCount));
		
		if(newincoming && ConvID != Conversations.Current)
		{
			if((!$element.hasClass("newinc") || (arrayList[i].NewMessageCount > prevNewMessageCount)) && parseDateTime(arrayList[i].LastMessageTime) > startTime)
			{
				Sounds.PlayNotification1();
				LocalNotifications.Display(GetConversationDisplay(arrayList[i].Number,arrayList[i].ConversationName,arrayList[i].Nickname) + ": " + arrayList[i].LastMessage,false);
			}
			$element.addClass("newinc");
		}
		else
			$element.removeClass("newinc");

		$element.data("convData",arrayList[i]);
	}
	
	Conversations.CheckAllConversationLists();
}

function parseDeletedConversationList(arrayList)
{
	for(var i = 0; i < arrayList.length; i++)
	{
		var ConvID = arrayList[i];
		var elementID = Conversations.ConvListItemID(ConvID);
		
		if(elementID != null)
		{
			$(elementID).remove();
		}
	}
}

function parseOpenConversation(data)
{
	Conversations.TotalConversationMessages = parseInt(data.Conversation.TotalMessageCount);
	$("#convname_disp").html(escapeHtml(GetConversationDisplay(data.Conversation.Number,data.Conversation.Name,data.Conversation.Nickname)));
	$convmain = $("#current_conversation_main");
	//$convmain.html("");
	
	var preLoadedMessageCount = Conversations.CurrentLoadedMessages();
	
	var newReadMessages = new Array();
	
	var addedMessages = false;
	var isPreScrollBottom = isConversationScrolledToBottom();
	
	var dateFormat;
	
	for(var i = 0; i < data.Conversation.Messages.length; i++)
	{
		var elementID = "message_" +data.Conversation.Messages[i].ID;
		var element = document.getElementById(elementID);
		
		if(element === null)
		{
			addedMessages = true;
			
			
			
			//if(Conversations.NewestMessage == null || parseInt(data.Conversation.Messages[i].ID) > Conversations.NewestMessage)
				//Conversations.NewestMessage = parseInt(data.Conversation.Messages[i].ID);

			element = AddMessageToCurrentConv(data.Conversation.Messages[i]);
			
			if(data.Conversation.Messages[i].ReadTime === null && data.Conversation.Messages[i].Direction == MESSAGEDIRECTION_IN)
				newReadMessages[newReadMessages.length] = data.Conversation.Messages[i].ID;
		}
		
		var $element = $(element);
		var messageStatus = parseInt(data.Conversation.Messages[i].Status);
		
		// Create time
		var dateFormat = parseDateTime(data.Conversation.Messages[i].CreateTime);
		var dateString = formatDate(dateFormat);
		
		// Delivery time
		if (data.Conversation.Messages[i].DeliveryTime != null)
		{
			dateFormat = parseDateTime(	data.Conversation.Messages[i].DeliveryTime);
			dateString += '<br><span class="faded">' + formatDate(dateFormat) + "</style>";
		}
		$element.find(".timelabel p").html(dateString);
		
		switch(messageStatus)
		{
			case MESSAGESTATUS_SENT:
				$element.removeClass("error").removeClass("buffered").addClass("sent");
				break;
				
			case MESSAGESTATUS_RECEIVED:
				$element.removeClass("error").removeClass("buffered").removeClass("sent").addClass("received");
				break;
				
			case MESSAGESTATUS_SENDFAIL:
			case MESSAGESTATUS_DELIVERYFAILED:
			case MESSAGESTATUS_DELIVERY_UNKNOWN:
			case MESSAGESTATUS_CREATED:
				$element.removeClass("received").removeClass("buffered").removeClass("sent").addClass("error");
				break;
			
			case MESSAGESTATUS_DELIVERYBUFFERED:
				$element.removeClass("error").removeClass("sent").addClass("buffered");
				break;
		}
	}
	
	//If any new messages have been read
	if(newReadMessages.length > 0)
	{
		ComSystem.Request(ComSystem.Items.READMESSAGES, {msgs : JSON.stringify(newReadMessages),convid : data.Conversation.ID}, function(data){
			var x = 3;
			});
		//Decrease notifier
		var $notifier = $(".new-incoming-messages-box p");
		if($notifier.length > 0)
		{
			try
			{
				var old = parseInt($notifier.html());
				var _new = old - newReadMessages.length;
				if(_new > 0)
					$notifier.html(_new);
				else
					$notifier.parent().html("").css('display', 'none');
			}
			catch(exc)
			{
				
			}
		}
		
	}
	
	//All messages loaded?
	var loadedCount = $("#current_conversation_main .messagerow").length;
	if(loadedCount >= Conversations.TotalConversationMessages)
		$("#btn_furthermsgs").hide();
	else
		$("#btn_furthermsgs").show();
	resize_stuff();
	
	if(addedMessages && isPreScrollBottom)
	{
		if(preLoadedMessageCount > 0)
			AnimateConversationScrollToBottom(300);
		else
			$('.conversation-thread .messages').scrollTop($('.conversation-thread .messages')[0].scrollHeight);
	}
}

var hasLoadedConvLists = false;

var lastLoadServerTime = null;

function LOAD(loadConvs, convID, callback, loadMessageCount, aboveID)
{
	if(convID === null)
		convID = 0;
		
	if(typeof(loadMessageCount) == "undefined")
		loadMessageCount = SystemSettings.LOADMESSAGES_INITCOUNT;
	if(typeof(aboveID) == "undefined")
		aboveID = 0;
		
	var isFirstLoad = false;
	
	if(!hasLoadedConvLists && loadConvs)
	{
		isFirstLoad = true;
		hasLoadedConvLists = true;
	}
	
	var requestParameters =
	{
		getconvs : (loadConvs ? "1" : "0"),
		convid: convID,
		messagecount : loadMessageCount,
		aboveid : aboveID,
		firstlistload : (isFirstLoad ? "1" : "0"),
	};
	
	if(lastLoadServerTime != null)
		requestParameters.servertime = lastLoadServerTime;
		
	ComSystem.Request(ComSystem.Items.LOAD,requestParameters,
	function(data,data2)
	{
		var unreadCount_active = parseInt(data.NewCounts.Active);
		var unreadCount_inactive = parseInt(data.NewCounts.Inactive);
		var unreadCount_archived = parseInt(data.NewCounts.Archived);
		
		document.getElementById('unreadcount_active').innerHTML = unreadCount_active;
		document.getElementById('unreadcount_inactive').innerHTML = unreadCount_inactive;
		
		if (unreadCount_active > 0)
		{
			$('.new-incoming-messages-box').css('display', 'block');
			$('.new-incoming-messages-box').html('<p>' + unreadCount_active + '</p>');
		}
		else
		{
			$('.new-incoming-messages-box').css('display', 'none');
			$('.new-incoming-messages-box').html('');
		}
		
		
		if(loadConvs)
		{
			parseConversationList(data.ConversationLists.Conversations);
			parseDeletedConversationList(data.ConversationLists.DeletedConversations);
		}
			
		
		if(convID)
		{
			if(convID == Conversations.Current)
			{
				if(data.Conversation.Exists)
				{
					parseOpenConversation(data);
				}
				else
				{
					Conversations.CloseCurrent();
				}
			}
		}
		
		Stuff.CompanyName = data.SystemSettings.CompanyName;
		Stuff.DisplayName = data.SystemSettings.DisplayName;
		Stuff.CompanySignature = data.SystemSettings.CompanySignature;
		Stuff.CompanySignatureActive = data.SystemSettings.CompanySignatureActive;
		Stuff.MySignature = data.SystemSettings.MySignature;
		Stuff.HaveMySignature = (data.SystemSettings.MySignature != null);
			
		if(typeof callback != "undefined" && callback !== null)
			callback();
			
		if(isInitScreen)
		{
			//Should hide when stuff is loaded
			if(Conversations.Current == 0 || convID)
			{
				hideInitScreen();
			}
		}
	});
	
	//Store last load time locally if available
	if(SessionTime.hasTime())
		lastLoadServerTime = SessionTime.getServerTime();
}