// JavaScript Document

var Sounds =
{
	notify1 : new Audio("/sounds/notif1.mp3"),
	notify2 : new Audio("/sounds/notif2.mp3"),
	
	PlayNotification1 : function()
	{
		Sounds.notify1.play();
	},
	
	PlayNotification2 : function()
	{
		Sounds.notify2.play();
	}
};