
var LocalNotifications =
{
	Display : function(Text, RequireWindowBlurred)
	{
		//Ignore if no support for notifications
		if (!("Notification" in window))
    		return;
		//Or if notifications are denied
		if(Notification.permission === "denied")
			return;
			
			
		var _doDisplay = function()
		{
			//TODO: Add Icon
			if(document.hidden || !RequireWindowBlurred)
				var notification = new Notification(Text);
		};
		
		
		if(Notification.permission === "granted")
		{
			_doDisplay();
		}
		else
		{
			Notification.requestPermission(function (permission)
			{
		  		if (permission === "granted") 
					_doDisplay();
			});
		}
		
	}
	
	
};