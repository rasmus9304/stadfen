var MobileSystem =
{
	CurrentClass : null,
	Execute : function()
	{
		var w = window.innerWidth;
		//Determine mobile device
		if (w < 1024 && w >= 768)
		{
			MobileSystem.CurrentClass = "tablet";
			$("body:not(.tablet)").removeClass("desktop").removeClass("mobile").addClass("tablet");
		}
		else if (w < 768)
		{
			MobileSystem.CurrentClass = "mobile";
			$("body:not(.mobile)").removeClass("desktop").removeClass("tablet").addClass("mobile");
		}
		else
		{
			MobileSystem.CurrentClass = "desktop";
			$("body:not(.desktop)").removeClass("mobile").removeClass("tablet").addClass("desktop");
		}
	},
	
	filledScenes: new Array(),
	
	Initialize : function()
	{
		$(window).resize(function(e) {
        	MobileSystem.Execute();
    	});
		this.Execute();
	}
};

$(document).ready(function(e) {
    MobileSystem.Initialize();
});