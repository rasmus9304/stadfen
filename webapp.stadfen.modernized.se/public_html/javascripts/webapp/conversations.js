var Conversations =
{
	Current : 0,
	OldestMessage : null,
	NewestMessage : null,
	TotalConversationMessages : 0,
	CurrentOpenTime : null,
	
	ConvListItemID : function(ConvID)
	{
		return "convlistitem_"+ConvID;
	},
	GetConvListItem : function(ConvID)
	{
		return document.getElementById(Conversations.ConvListItemID(ConvID));
	},
	LoadLists : function()
	{
		LOAD(true,null,null); 
	},
	Inactivate : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		if(!$listElement.data("archived"))
			$listElement.detach().appendTo("#convlist_inactive");
		$listElement.data("active",false);
		Conversations.RequestSetActivation(ConvID, false);
		
		Conversations.CheckAllConversationLists();
		
		if (Conversations.Current == ConvID)
		{
			Conversations.CloseCurrent();	
		}
	},
	Activate : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		if(!$listElement.data("archived"))
			$listElement.detach().appendTo("#convlist_active");
		$listElement.data("active",true);
		Conversations.RequestSetActivation(ConvID, true);
		
		Conversations.CheckAllConversationLists();
		
		if (Conversations.Current == ConvID)
		{
			Conversations.CloseCurrent();	
		}
	},
	
	Archive : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		$listElement.detach().appendTo("#convlist_archived");
		$listElement.data("archived",true);
		Conversations.RequestSetArchived(ConvID, true);
		
		Conversations.CheckAllConversationLists();
		
		if (Conversations.Current == ConvID)
		{
			Conversations.CloseCurrent();	
		}
	},
	Unarchive : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		$listElement.detach().appendTo($listElement.data("active") ? "#convlist_active" : "#convlist_inactive");
		$listElement.data("archived",false);
		Conversations.RequestSetArchived(ConvID, false);
		
		Conversations.CheckAllConversationLists();
		
		if (Conversations.Current == ConvID)
		{
			Conversations.CloseCurrent();	
		}
	},
	
	RequestSetActivation : function(ConvID, active)
	{
		ComSystem.Request("setconvactivation",{convid: ConvID, active : (active ? 1 : 0)},function(data)
		{
			
		});
	},
	
	RequestSetArchived : function(ConvID, archived)
	{
		ComSystem.Request("setconvarchive",{convid: ConvID, archived : (archived ? 1 : 0)},function(data)
		{
			
		});
	},
	
	RequestSetNickname : function(ConvID, nickname)
	{
		ComSystem.Request("setconvnickname",{convid: ConvID, nickname: nickname},function(data)
		{
			
		});
	},
	
	SetNickname : function(ConvID, nickname)
	{
		var $element = $(Conversations.GetConvListItem(ConvID));
		$element.data("convnickname",nickname);
		var disp = GetConversationDisplay($element.data("convnumber"), $element.data("convname"), nickname);
		$element.find(".title").html(disp);
		if(Conversations.Current == ConvID)
			$("#convname_disp").html(disp)
		Conversations.RequestSetNickname(ConvID, nickname);
	},
	
	RequestSetName : function(ConvID, name)
	{
		ComSystem.Request("setconvname",{convid: ConvID, name: name},function(data)
		{
			
		});
	},
	
	SetName : function(ConvID, name)
	{
		var $element = $(Conversations.GetConvListItem(ConvID));
		$element.data("convname",name);
		var disp = GetConversationDisplay($element.data("convnumber"), name, $element.data("convnickname"));
		$element.find(".title").html(disp);
		if(Conversations.Current == ConvID)
			$("#convname_disp").html(disp)
		Conversations.RequestSetName(ConvID, name);
	},
	
	RequestSetFavorite : function(ConvID, favorite)
	{
		ComSystem.Request("setconvfavorite",{convid: ConvID, favorite : (favorite ? 1 : 0)},function(data)
		{
			
		});
	},
	
	SetFavorite : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		$listElement.data("favorite",true);
		$listElement.setOrder(Conversations.GetConvListOrderValue($listElement.data("lastmessageid"),true));
		Conversations.RequestSetFavorite(ConvID, true);
		$listElement.addClass("favorite");
		$listElement.find('.right-bar img.favorite').attr('src', ICON_FAVORITE);
		$listElement.find('.right-bar img.favorite').addClass("selected");
	},
	SetUnfavorite : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		$listElement.data("favorite",false);
		$listElement.setOrder(Conversations.GetConvListOrderValue($listElement.data("lastmessageid"),false));
		Conversations.RequestSetFavorite(ConvID, false);
		$listElement.removeClass("favorite");
		$listElement.find('.right-bar img.favorite').attr('src', ICON_NOTFAVORITE);
		$listElement.find('.right-bar img.favorite').removeClass("selected");
	},
	ToggleFavorite : function(ConvID)
	{
		var $listElement = $(Conversations.GetConvListItem(ConvID));
		var isFav = $listElement.data("favorite");
		if(isFav)
		{
			Conversations.SetUnfavorite(ConvID);
		}
		else
		{
			Conversations.SetFavorite(ConvID);
		}
	},
	
	GetConvListOrderValue : function(LastMessageID, IsFavorite)
	{
		var wrongway = -sort_base_val + parseInt(LastMessageID) + (IsFavorite ? sort_base_val : 0);
		return sort_base_val-wrongway;
	},
	
	GetMessageOrderValue : function(MessageID)
	{
		return parseInt(MessageID) - sort_base_val;
	},
	
	SearchChange : function()
	{
		var text = document.getElementById('conv_search_textfield').value.toLowerCase().replace("-","").replace(" ","");
		if(text == "")
			$(".conversation-item").removeClass("searchhide");
		else
			$(".conversation-item").each(function(index, element) {
				var $element = $(element);
				
				var number = $element.data("convnumber");
				var name = $element.data("convname");
				var nickname = $element.data("convnickname");
				var number2 = "0" + number.slice(2);

				if(typeof(number) == "string" && number.toLowerCase().indexOf(text) != -1)
					$element.removeClass("searchhide");
				if(typeof(number2) == "string" && number2.toLowerCase().indexOf(text) != -1)
					$element.removeClass("searchhide");
				else if(typeof(number) == "string" && Phonenumber.GetDisplayStyle(number).toLowerCase().indexOf(text) != -1)
					$element.removeClass("searchhide");
				else if(typeof(name) == "string" && name.toLowerCase().indexOf(text) != -1)
					$element.removeClass("searchhide");
				else if(typeof(nickname) == "string" && nickname.toLowerCase().indexOf(text) != -1)
					$element.removeClass("searchhide");
				else
					$element.addClass("searchhide");
			});
	},
	
	CloseCurrent : function()
	{
		$("#current_conversation_main").html('');
		$(".conversation-item.selected").removeClass("selected");
		$("#convname_disp").html("");
		Conversations.Current = 0;
	},
	
	InternalCloseCurrent : function()
	{
		if(NavigationSystem.HasClass("module_conversations"))
		{
			var currentListClass = Conversations.GetCurrentListClass();
			if(currentListClass == "")
				currentListClass = "convlist_active";
			
			//NavigationSystem.Navigate("module_conversations " + currentListClass);
		}
	},
	
	LoadFurtherMessages : function()
	{
		var preScroll = $("#messages_scroll").scrollTop();
		var preHeight = $("#messages_scroll")[0].scrollHeight;
		LOAD(false,Conversations.Current,function()
		{
			var newNeight = $("#messages_scroll")[0].scrollHeight;
			var deltaHeight = newNeight - preHeight;
			var scrollTop = $("#messages_scroll")[0].scrollTop;
			var newScrollTop = scrollTop + deltaHeight;
			document.getElementById('messages_scroll').scrollTop = newScrollTop;
		},SystemSettings.LOADMESSAGES_ADDITIONALCOUNT, Conversations.OldestMessage);
	},
	
	Delete : function(ConvID)
	{
		if(Conversations.Current == ConvID)
			Conversations.CloseCurrent();
		ComSystem.Request("removeconv",{convid: ConvID},null);
		$(Conversations.GetConvListItem(ConvID)).remove();
	},
	
	DeleteMessage : function(MessageID)
	{
		ComSystem.Request("removemessage",{msgid: MessageID},null);
		$("#message_"+MessageID).remove();
	},
	
	CurrentLoadedMessages : function()
	{
		return $("#current_conversation_main .messagerow").length;
	},
	
	GetCurrentListClass : function()
	{
		if(NavigationSystem.HasClass("convlist_inactive"))
			return "convlist_inactive";
		else if(NavigationSystem.HasClass("convlist_active"))
			return "convlist_active";
		else if(NavigationSystem.HasClass("convlist_archived"))
			return "convlist_archived";
		else
			"";
	},
	
	CheckConversationList : function($list)
	{
		const LABELCLASS = "conversation-noitems-label";
		
		var empty = ($list.find(".conversation-item").size() == 0);
		var $labelElement = $list.find("." + LABELCLASS)
		if($labelElement.size() > 0 && !empty)
			$labelElement.remove();
		else if($labelElement.size() == 0 && empty)
			$list.append('<div class="'+ LABELCLASS +'"><p>Det finns inga konversationer i denna kategori.</p></div>');
	},
	
	CheckAllConversationLists : function()
	{
		Conversations.CheckConversationList($("#convlist_active"));
		Conversations.CheckConversationList($("#convlist_inactive"));
		Conversations.CheckConversationList($("#convlist_archived"));
	},
};

function AddMessageToCurrentConv(MessageInfo)
{
	var elementID = "message_" +MessageInfo.ID;
	// Create time
	var dateFormat = new Date(MessageInfo.CreateTime);
	var dateString = formatDate(dateFormat);
	// Delivery time
	if (MessageInfo.DeliveryTime != null)
	{
		dateFormat = new Date(	MessageInfo.DeliveryTime);
		dateString += '<br><span class="faded">' + formatDate(dateFormat) + "</style>";
	}
	
	if(Conversations.OldestMessage == null || parseInt(MessageInfo.ID) < Conversations.OldestMessage)
			Conversations.OldestMessage = parseInt(MessageInfo.ID);
			
	//New incoming message?
	if(MessageInfo.Direction == MESSAGEDIRECTION_IN)
	{
		var parsedDate = parseDateTime(MessageInfo.DeliveryTime);
		if(parsedDate > Conversations.CurrentOpenTime)
		{
			//Is a new message
			Sounds.PlayNotification2();
			var $conv = $(Conversations.GetConvListItem(Conversations.Current));
			
			//Conversations.g
			LocalNotifications.Display(GetConversationDisplay($conv.data("convnumber"),$conv.data("convname"),$conv.data("convnickname")) + ": " + MessageInfo.Content,true);
		}
	}
	
	
	var d_message = '<div class="message"><p>'+ escapeHtml(MessageInfo.Content) +'</p></div>';
	var d_timelabel = '<div class="timelabel"><p></p><img class="message-status-icon message-error" src="/images/icon-alert.svg" onclick="askDeleteMessage('+MessageInfo.ID+');"><img class="message-status-icon message-buffer" src="/images/icon-buffer.svg"></div>';
	
	$convmain.append(
'<div id="'+elementID+'" class="messagerow '+(MessageInfo.Direction == MESSAGEDIRECTION_OUT ? "out" : "in")+'" style="order: '+ MessageInfo.ID +';">'+d_timelabel+d_message+'</div>'
	);
	
	return document.getElementById(elementID);
	
	
}

function CreateMessageInfoObject(ID, Content, CreateTime, DeliveryTime, Direction)
{
	var ret =
	{
		ID: ID,
		Content: Content,
		CreateTime: CreateTime,
		DeliveryTime: DeliveryTime,
		Direction: Direction
	};
	
	return ret;
}

function LoadupConversation(ConvID)
{
	$("#current_conversation_main").html('<div style="display:flex;order:99999999;width:100%;"><div style="height:64px; width:100%;" id="conv-bottom-space"></div></div>'); //Almost empty, just a filler at bottom
	Conversations.Current = ConvID;
	Conversations.OldestMessage = null;
	
	//var currentDate = new Date();
	Conversations.CurrentOpenTime = new Date();
	
	LOAD(false,ConvID,function()
	{
		var $cm = $("#current_conversation_main");
		$cm.scrollTop($cm.height());
	});

	//Remove "newmessage"
	$("#convlistitem_" + ConvID).removeClass("newinc");
}
var convToLoad_waitedforsystemsettings = null;
function LoadupConversation_WaitForSystemSettings(ConvID)
{
	convToLoad_waitedforsystemsettings = ConvID;
}

function SendMessageRequest(isConv, message, signature, convID, strNumber,callback)
{
	
	ApplicationLoading.Show("Skickar...");
	DisableSendInputs();
	
	ComSystem.Request("sendmessage",
	{
		isconv : (isConv ? 1 : 0),
		message : message,
		signature : signature,
		convid : convID,
		numbers : strNumber
	},
	function(data)
	{
		var fullMessage = data.FullMessage;
		var sendSuccess = false;
		if(isConv)
		{
			var messageID = data.MessageID;
			var sendTime = data.SendTime;
			sendSuccess = data.SendSuccess;
			var isError = !(sendTime);
			
			if (sendSuccess)
			{
				ApplicationLoading.Success("Meddelande skickat till " + Phonenumber.GetDisplayStyle(data.Number));
				//if current conv
				if(convID == Conversations.Current)
				{
					var messageInfo = CreateMessageInfoObject(messageID, fullMessage, sendTime, null, MESSAGEDIRECTION_OUT);
					
					var isPreScrollBottom = isConversationScrolledToBottom();
					AddMessageToCurrentConv(messageInfo);
					
					if(isPreScrollBottom)
						AnimateConversationScrollToBottom();
				}
			}
			else
				ApplicationLoading.Error("Meddelandet kunde inte skickas");
		
			ApplicationLoading.Done();
			
			//Update list item
			var listItem = Conversations.GetConvListItem(convID);
			var $element = $(listItem);
			
			$element.find(".lastmessage").html(messageID);
			$element.find(".lastmessagetime").html(formatDate(new Date(sendTime)));
			$element.setOrder(Conversations.GetConvListOrderValue(messageID, $element.data("favorite")));
		}
		else
		{
			var successCount = 0;
		
			for(var i = 0; i < data.MessageReturns.length; i++)
			{
				var number = data.MessageReturns[i].Number;
				var sendSuccess = data.MessageReturns[i].Success;
				var messageID = data.MessageReturns[i].messageID;
				if(sendSuccess)
				{
					successCount++;
				}
			}
			
			
			if (successCount == data.MessageReturns.length)
				ApplicationLoading.Success("Meddelande skickat");
			else
				ApplicationLoading.Error("Meddelandet kunde inte skickas till " + (data.MessageReturns.length - successCount) + " nummer");
				
			
			ApplicationLoading.Done();
		}
		
		EnableSendInputs();
		
		if(typeof(callback) != "undefined" && callback != null)
			callback(data);
	});
}



function openContextMenu(id)
{

	var $this = $(Conversations.GetConvListItem(id));
	var context = [];
	var active = $this.data('active');
	var removable = $this.hasClass('removable');
	
	var archived = $this.data('archived');
	
	var favorite = $this.data("favorite");
	
	context[context.length] = { id:id, title: "Information", callback: ContextMenu.ViewInfo };
	
	if(favorite)
	{
		context[context.length] = { id:id, title: "Avmarkera favorit", callback: ContextMenu.Unfavorite }
	}
	else
	{
		context[context.length] = { id:id, title: "Favoritmarkera", callback: ContextMenu.Favorite };
	}
	
	
	if (Privileges[ALLCONVERSATIONS])
	{
		context[context.length] = { id:id, title: "Byt namn på konversation", callback: ContextMenu.SetConversationName };

		if (archived)
		{
			context[context.length] = { id:id, title: "Flytta från papperskorg", callback: ContextMenu.Unarchive };
		}
		else
		{
			context[context.length] = { id:id, title: "Flytta till papperskorg", callback: ContextMenu.Archive };
		}
	}
	
	if (active)
	{
		context[context.length] = { id:id, title: "Inaktivera", callback: ContextMenu.Inactivate };
	}
	else
	{
		context[context.length] = { id:id, title: "Aktivera", callback: ContextMenu.Activate };
	}
	
	context[context.length] = { id:id, title: "Ge smeknamn", callback: ContextMenu.SetNickName };
	
	if (removable || (archived && Privileges[ALLCONVERSATIONS]))
	{
		context[context.length] = { id:id, title: "Ta bort fullständigt", callback: ContextMenu.RemoveConversation };
	}
	
	ContextMenu.Show($this.find('.dropdown'), context);
}

var ContextMenu =
{
	Instance : null,
	Show : function(element, contextmenu)
	{
		element.dynamicmenu({menu:contextmenu});
	},
	Favorite : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.SetFavorite(id);
	},
	Unfavorite : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.SetUnfavorite(id);
	},
	RemoveConversation : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		askDeleteThread(id);
	},
	Activate : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.Activate(id);
	},
	Inactivate : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.Inactivate(id);
	},
	Archive : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.Archive(id);
	},
	Unarchive : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		Conversations.Unarchive(id);
	},
	SetNickName : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		popupbox.openPopup(420, "Ge ett smeknamn", "<p class='popup-p'>Ge konversationen ett smeknamn som du lätt kan komma ihåg.</p><input class='form-control input-change-nickname' type='text' style='margin-top: 15px;'>", new OkCancelDialog(function() {
			var nickname = popupbox.getPopup().find('.input-change-nickname').val();
			Conversations.SetNickname(id, nickname);
		}, null));
		
	},
	SetConversationName : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		popupbox.openPopup(420, "Ge ett namn till konversationen", "<p class='popup-p'>Genom att ge konversationen ett namn kan du lättare komma ihåg kunden.</p><input class='form-control input-change-conversationname' type='text' style='margin-top: 15px;'>", new OkCancelDialog(function() {
			var name = popupbox.getPopup().find('.input-change-conversationname').val();
			Conversations.SetName(id, name);
		}, null));
	},
	
	ViewInfo : function()
	{
		var i = $(this);
		var id = i.data('id');
		
		var $ConvElem = $(Conversations.GetConvListItem(id));
		
		var convNumber = $ConvElem.data("convnumber");
		var convName = $ConvElem.data("convname");
		var convNickname = $ConvElem.data("convnickname");
		
		var isArchived = $ConvElem.data("archived");
		var isInactive = $ConvElem.data("inactive");
		
		popupbox.openPopup(420, "Information", 
		"<p class='popup-p'>Telefonnummer: "+ Phonenumber.GetDisplayStyle(convNumber) +"</p>" +
		"<p class='popup-p'>Namn: "+ (convName ? convName : '<i>Inget</i>') +"</p>" +
		"<p class='popup-p'>Smeknamn: "+ (convNickname ? convNickname : '<i>Inget</i>') +"</p>" +
		"<p class='popup-p'>Inaktiverad: "+ (isInactive ? '<i>Ja</i>' : '<i>Nej</i>') +"</p>" +
		"<p class='popup-p'>Arkiverad: "+ (isArchived ? '<i>Ja</i>' : '<i>Nej</i>') +"</p>"
		, new OkDialog(function() {
			//No event when clicking ok
		}, null));
	},
	
	
}

function GetConversationDisplay(number, convname, nickname)
{
	if(convname && nickname)
		return nickname + " ("+ convname +")";
	else if(nickname)
		return nickname;
	else if(convname)
		return convname;
	else
		return Phonenumber.GetDisplayStyle(number);
}