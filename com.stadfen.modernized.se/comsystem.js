

var ComSystem =
{
	BaseURI : "http://com.stadfen.modernized.se",
	StatusCodes : 
	{
		OK : 0,
		MISSINGDATA : 1,
		INVALIDDATA : 2,
		NOTLOGGEDIN : 3,
		SYSTEMOFF : 4,
	},
	Request : function(com_item, data, callback)
	{
		var uri = ComSystem.BaseURI + "/" + com_item;
		
		$.modernizedPOST(uri, data, function(_data, _data2, _success)
		{
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
		OnUnknownError : null,
	}
};