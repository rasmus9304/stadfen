

var ComSystem =
{
	BaseURI : "http://com.stadfen.modernized.se", /*ex: http://com.server.system.something.com */
	StatusCodes : 
	{
		OK : 0,
		MISSINGDATA : 1,
		INVALIDDATA : 2,
		NOTLOGGEDIN : 3,
		SYSTEMOFF : 4,
		KICK : 5,
	},
	Request : function(com_item, data, callback, button)
	{
		var isButton = (typeof button !== "undefined" && button !== null);
		if(isButton)
		{
			var $button = $(button);
			var button_data = $button.data("comsys_button_state");
			if(button_data === true)
			{
				return;
			}
			else
			{
				$button.data("comsys_button_state",true);
			}
		}
		
		var uri = ComSystem.BaseURI + "/" + com_item;
		
		$.modernizedPOST(uri, data, function(_data, _data2, _success)
		{
			if(isButton)
				$button.data("comsys_button_state",false);
				
			if(_data.EM !== null)
				alert(_data.EM);
			
			var statusCode = _data.SC;
			if(statusCode == ComSystem.StatusCodes.OK)
			{
				var comData = _data.CD;
				
				if(typeof callback !== "undefined" && callback !== null)
					callback(comData,_data2,_success);
			}
			else
			{
				switch(statusCode)
				{
					case ComSystem.StatusCodes.MISSINGDATA:
						if(ComSystem.Events.OnMissingData !== null)
							ComSystem.Events.OnMissingData();
						break;
						
					case ComSystem.StatusCodes.INVALIDDATA:
						if(ComSystem.Events.OnInvalidData !== null)
							ComSystem.Events.OnInvalidData();
						break;
						
					case ComSystem.StatusCodes.NOTLOGGEDIN:
						if(ComSystem.Events.OnNotLoggedIn !== null)
							ComSystem.Events.OnNotLoggedIn();
						break;
						
					case ComSystem.StatusCodes.SYSTEMOFF:
						if(ComSystem.Events.OnSystemOff !== null)
							ComSystem.Events.OnSystemOff();
						break;
						
					case ComSystem.StatusCodes.KICK:
						if(ComSystem.Events.OnKick !== null)
							ComSystem.Events.OnKick();
						break;
						
					default:
						if(ComSystem.Events.OnUnknownError !== null)
							ComSystem.Events.OnUnknownError();
						break;
				}
			}
		});
	},
	
	Events :
	{
		OnMissingData : null,
		OnInvalidData : null,
		OnNotLoggedIn : null,
		OnSystemOff : null,
		OnKick : null,
		OnUnknownError : null,
	},
	
	Items :
	{
		CONVERSATIONLIST : "conversationlist",
		LOAD : "load",
		READMESSAGES : "readmessages",
		LOGOUT: "logout"
	}
};