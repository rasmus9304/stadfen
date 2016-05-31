
var SystemSettings = {SIGNATUREGLUE:null,WEBAPP_UPDATE_PERIOD:null,MAX_DESTINATION_COUNT:null,SIGNATURE_MAX_LENGTH:null,MAX_MESSAGE_LENGTH : null,LOADMESSAGES_INITCOUNT:5,LOADMESSAGES_ADDITIONALCOUNT:10};
	var SystemSettingsLoaded = false;
	var Privileges;
	
	var Stuff =
	{
		CompanyName : "",
		DisplayName : "",
		CompanySignature : "",
		CompanySignatureActive : true,
		MySignature : "",
		HaveMySignature : false
	};
	
	function parseDateTime(strDateTime)
	{
		if(strDateTime === null)
			return null;
		var a = strDateTime.split(/[^0-9]/);
		//for (i=0;i<a.length;i++) { alert(a[i]); }
		return new Date (parseInt(a[0]),parseInt(a[1])-1,parseInt(a[2]),parseInt(a[3]),parseInt(a[4]),parseInt(a[5]) );
	}
	
	var LoadSystemSettings = function()
	{
		
		ApplicationLoading.Show('Laddar in konto...');
		$('#main-content-holder').css('display', 'none');
		$('#button-newmessage').attr('disabled', true);
		
		ComSystem.Request("systemsettings",null,function(data)
		{
			//Acquire the time on the server
			SessionTime.setNow(data.ServerTime);
			
			SystemSettings.SIGNATUREGLUE = data.SIGNATUREGLUE;
			SystemSettings.WEBAPP_UPDATE_PERIOD = data.WEBAPP_UPDATE_PERIOD;
			SystemSettings.MAX_DESTINATION_COUNT = data.MAX_DESTINATION_COUNT;
			SystemSettings.SIGNATURE_MAX_LENGTH = data.SIGNATURE_MAX_LENGTH;
			SystemSettings.MAX_MESSAGE_LENGTH = parseInt(data.MAX_MESSAGE_LENGTH);
			SystemSettings.LOADMESSAGES_INITCOUNT = data.LOADMESSAGES_INITCOUNT;
			SystemSettings.LOADMESSAGES_ADDITIONALCOUNT = data.LOADMESSAGES_ADDITIONALCOUNT;
			
			Privileges = data.Privileges;
			
			SystemSettingsLoaded = true;
			
			
			Stuff.CompanyName = data.CompanyName;
			Stuff.DisplayName = data.DisplayName;
			Stuff.CompanySignature = data.CompanySignature;
			Stuff.CompanySignatureActive = data.CompanySignatureActive;
			Stuff.MySignature = data.MySignature;
			Stuff.HaveMySignature = (data.MySignature != null);
			
			$("#sidemenubutton_statistics").css("display",(Privileges[COMPANYTRAFFIC] ? "" : "none"));
			
			$('.popover-newmessage-form .text-length').html("0/" + SystemSettings.MAX_MESSAGE_LENGTH);
			
			
			$('#main-content-holder').fadeIn(200);
			$('#button-newmessage').attr('disabled', false);
			
			if(convToLoad_waitedforsystemsettings != null)
			{
				LoadupConversation(convToLoad_waitedforsystemsettings);
				convToLoad_waitedforsystemsettings = null;
			}
			
			setTimeout(update,SystemSettings.WEBAPP_UPDATE_PERIOD);
			ApplicationLoading.Done();
		});
	};