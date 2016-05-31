


var NavigationSystem = 
{
	screenHistory : new Array(),
	currentScreenHistory : 0,
	isLoggedIn : false,
	LoggedIn : function()
	{
		NavigationSystem.isLoggedIn = true;
		NavigationSystem.GiveClass("loggedin");
	},
	
	LoggedOut : function()
	{
		NavigationSystem.isLoggedIn = false;
		NavigationSystem.RemoveClass("loggedin");	
	},
	
	GiveClass : function(className)
	{
		$("body").removeClass(className).addClass(className);
	},
	
	RemoveClass : function(className)
	{
		$("body").removeClass(className);
	},
	
	ChangeClass : function(from, to)
	{
		$("body").removeClass(from).removeClass(to).addClass(to);
	},
	
	HasClass : function(className)
	{
		return $("body").hasClass(className);
	},
	
	Navigate : function(strClasses)
	{
		NavigationSystem.InternalNavigate(strClasses);
		var state = {webbappnav: true, classesStr : strClasses};
		history.pushState(state, "Webbapp", "/user/" + strClasses);
	},
	
	InternalNavigate : function(strClasses)
	{
		$("body").attr("class",MobileSystem.CurrentClass + " " + (NavigationSystem.isLoggedIn ? "loggedin " : "") + strClasses);
		NavigationSystem.AfterNavigate(strClasses);
	},
	
	AfterNavigate : function(){},
};

window.onpopstate = function(event)
{
	if(typeof(event.state) !== "undefined" && event.state != null && typeof(event.state.webbappnav) !== "undefined" && event.state.webbappnav === true)
	{
		NavigationSystem.InternalNavigate(event.state.classesStr);
	}
	else
	{
		NavigationSystem.InternalNavigate("");
	}
};

function setCookie(cname, cvalue, exdays) 
{
	var expires;
	if(typeof exdays !== undefined & exdays !== null)
	{
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		expires = "expires="+d.toUTCString();
	}
	else
	{
		expires = "";
	}
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) 
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}