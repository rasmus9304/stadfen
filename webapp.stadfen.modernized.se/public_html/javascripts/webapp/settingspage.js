// JavaScript Document

var settinsgpage_loaded = false;
var settingspage_slider;

function onSettingsEnter()
{
	
	settingspage_slider = $('#settings-tabs-container').slider( { animationType: 1, animationSpeed: 100 } ).data("plugin_slider");
	
	$("#accountlist_table tbody").html("");
	$("#companytemplate_table tbody").html("");
	$("#mytemplate_table tbody").html("");
	$("#editacc_convlist_list tbody").html("");
	$("#intervallist_table tbody").html("");
	SettingsFunc.HideTemplatePane();
	SettingsFunc.EndEditTemplate();
		ComSystem.Request("settingspage", null, function(data)
		{	
			var priv_editaccounts = data.Privileges[EDITACCOUNTS];
			var priv_allconversations = data.Privileges[ALLCONVERSATIONS];
			var priv_companytemplate = data.Privileges[COMPANYTEMPLATE];
			var priv_companysignature = data.Privileges[COMPANYSIGNATURE];
			var priv_companytraffic = data.Privileges[COMPANYTRAFFIC];
			
			if (priv_editaccounts)
			{
				$('#mypriv_editaccount').addClass('yes');
			}
			if (priv_allconversations)
			{
				$('#mypriv_allconvs').addClass('yes');
			}
			if (priv_companytemplate)
			{
				$('#mypriv_companytemplate').addClass('yes');

			}
			if (priv_companysignature)
			{
				$('#mypriv_companysignature').addClass('yes');

			}
			if (priv_companytraffic)
			{
				$('#mypriv_companytraffic').addClass('yes');

			}
			
			$("#settings_accountlist").css("display",(priv_editaccounts ? "" : "none"));
			
			$('#mypriv_editaccount h1').html(priv_editaccounts ? "Ja" : "Nej");
			$('#mypriv_allconvs h1').html(priv_allconversations ? "Ja" : "Nej");
			$('#mypriv_companytemplate h1').html(priv_companytemplate ? "Ja" : "Nej");
			$('#mypriv_companysignature h1').html(priv_companysignature ? "Ja" : "Nej");
			$('#mypriv_companytraffic h1').html(priv_companytraffic ? "Ja" : "Nej");
			
			document.getElementById('mydn_edit').value = (data.DisplayName ? data.DisplayName : "");

			if(data.AccountTemplates.length > 0)
			{
				var accTemplateTable = document.getElementById('mytemplate_table');
				accTemplateTable.style.display = "table";
				document.getElementById('mytemplate_none').style.display = "none";
				
				var $tablebody = $("#mytemplate_table tbody");
				for(var i = 0; i < data.AccountTemplates.length; i++)
				{
					$tablebody.append('<tr id="templaterow_'+data.AccountTemplates[i].ID+'"><td class="img"><img class="table-image-btn" src="/images/send4.svg" style="width: 14px;"></td><td class="name">'+data.AccountTemplates[i].Title+'</td><td class="content">'+data.AccountTemplates[i].Text+'</td><td class="right"><span class="defaultbuttons"><button type="button" class="btn btn-default" onclick="SettingsFunc.StartEditTemplate('+data.AccountTemplates[i].ID+');return false;">Redigera</button>&nbsp;<button type="button" class="btn btn-default" onclick="SettingsFunc.ClickRemoveTemplate('+data.AccountTemplates[i].ID+');return false;">Ta bort</button></span></td></tr>');
				}
			}
			else
			{
				//document.getElementById('mytemplate_table').style.display = "";
				//document.getElementById('mytemplate_none').style.display = "block";
			}
			
			if(data.Privileges[COMPANYTEMPLATE])
			{
				document.getElementById('companytemplate_settings').style.display = "block";
				if(data.CustomerTemplates.length > 0)
				{
					var custTemplateTable = document.getElementById('companytemplate_table');
					custTemplateTable.style.display = "table";
					document.getElementById('companytemplate_none').style.display = "none";
					
					var $tablebody = $("#companytemplate_table tbody");
					for(var i = 0; i < data.CustomerTemplates.length; i++)
					{
						//$tablebody.append('<tr id="templaterow_'+data.AccountTemplates[i].ID+'"><td>'+data.CustomerTemplates[i].Title+'</td><td>'+data.CustomerTemplates[i].Text+'</td></tr>');
						
						$tablebody.append('<tr id="templaterow_'+data.CustomerTemplates[i].ID+'"><td class="img"><img style="width: 14px;" src="/images/send4.svg"></td><td class="name">'+data.CustomerTemplates[i].Title+'</td><td class="content">'+data.CustomerTemplates[i].Text+'</td><td class="right"><span class="defaultbuttons"><button type="button" class="btn btn-default" onclick="SettingsFunc.StartEditTemplate('+data.CustomerTemplates[i].ID+');return false;">Redigera</button>&nbsp;<button type="button" class="btn btn-default" onclick="SettingsFunc.ClickRemoveTemplate('+data.CustomerTemplates[i].ID+');return false;">Ta bort</button></span></td></tr>');
					}
				}
				else
				{
					//document.getElementById('companytemplate_table').style.display = "";
					//document.getElementById('companytemplate_none').style.display = "block";
				}
			}
			else
			{
				document.getElementById('companytemplate_settings').style.display = "none";
			}
			
			SettingsFunc.ToggleTemplatePanels();

			document.getElementById('mysignature_edit').innerHTML = (data.AccountSignature ? data.AccountSignature : "");
			
			
			$("#companysignature_notification").toggle(data.CompanySignatureEnabled);
			
			if(data.Privileges[COMPANYSIGNATURE])
			{
				document.getElementById('companysignature_settings').style.display = "block";
				document.getElementById('compsignature_edit').innerHTML = (data.CompanySignature ? data.CompanySignature : "");
				document.getElementById('compsign_enabled').checked = data.CompanySignatureEnabled;
			}
			else
			{
				document.getElementById('companysignature_settings').style.display = "none";
			}
			
			if(data.Privileges[EDITACCOUNTS])
			{
				document.getElementById('companytemplate_settings').style.display = "block";
				var $tablebody = $("#accountlist_table tbody");
				for(var i = 0; i < data.AccountList.length; i++)
				{
					$tablebody.append('<tr><td>'+(data.AccountList[i].DisplayName ? data.AccountList[i].DisplayName : "<i>Inget</i>")+'</td><td>'+data.AccountList[i].EmailAddress+'</td><td>'+(data.AccountList[i].IsMainAccount ? "Ja" : "Nej")+'</td><td class="right"><button class="btn btn-default" onclick="SettingsFunc.StartEditAccount('+ data.AccountList[i].ID +');return false;">Redigera</button></td></tr>');
				}
				
				document.getElementById('settings_acc_c').innerHTML = data.AccountCount;
				document.getElementById('settings_acc_m').innerHTML = data.AccountMax;
				if(data.AccountCount < data.AccountMax)
				{
					$("#buttonbar_createaccount").show();
					$("#setting_acc_nomore").hide();
				}
				else
				{
					$("#buttonbar_createaccount").hide();
					$("#setting_acc_nomore").show();
				}
			}
			else
			{
				document.getElementById('companytemplate_settings').style.display = "none";
			}
			
			var manualSilentMode = data.ManualSilentMode;
			
			if(manualSilentMode)
			{
				document.getElementById('settings_manualsilent_status').innerHTML = "Status: Aktiverat";
				document.getElementById('btn_manual_silentmode_activate').style.display = "none";
				document.getElementById('btn_manual_silentmode_deactivate').style.display = "";
			}
			else
			{
				document.getElementById('settings_manualsilent_status').innerHTML = "Status: Ej aktiverat";
				document.getElementById('btn_manual_silentmode_activate').style.display = "";
				document.getElementById('btn_manual_silentmode_deactivate').style.display = "none";
			}
			
			
			
			var $tbody_intervals = $("#intervallist_table tbody");
			for(var i = 0; i < data.SilentModeIntervals.length; i++)
			{
				var StartDay = data.SilentModeIntervals[i].StartDay;
				var StartTime = data.SilentModeIntervals[i].StartTime;
				var EndDay = data.SilentModeIntervals[i].EndDay;
				var EndTime = data.SilentModeIntervals[i].EndTime;
				
				var starthour = parseInt(StartTime.split(":")[0]);
				var endhour = parseInt(EndTime.split(":")[0]);
				
				var row = '<tr id="interval_'+i+'">';
				row += '<td><select class="startday">'+ getDaySelectContent(StartDay) +'</select></td>';
				row += '<td><select class="starttime">'+ getTimeSelectContent(starthour) +'</select></td>';
				row += '<td><select class="endday">'+ getDaySelectContent(EndDay) +'</select></td>';
				row += '<td><select class="endtime">'+ getTimeSelectContent(endhour) +'</select></td>';
				row += '<td><button type="button" class="btn btn-default btn_silentmode_save" onClick="SettingsFunc.RemoveSilentInterval('+i+');return false;">Ta bort</button></td>';
				
				$tbody_intervals.append(row);
				
				SettingsFunc.IntervalCount = i;
			}

			// Loading is finished
			$('#settings-tabs-container').removeClass('loading');
			
			//SettingsFunc.InitializeSilentTimeline(data.SilentModeIntervals);
		});
}

var getDaySelectContent = function(selected)
{
	var ret = new Array();
	ret[ret.length] = 'Måndag';
	ret[ret.length] = 'Tisdag';
	ret[ret.length] = 'Onsdag';
	ret[ret.length] = 'Torsdag';
	ret[ret.length] = 'Fredag';
	ret[ret.length] = 'Lördag';
	ret[ret.length] = 'Söndag';
	
	var retstr = "";
	
	for(var i = 0; i < ret.length; i++)
	{
		retstr += '<option value="'+(i+1)+'" '+ (selected==(i+1) ? ' selected="selected" ' : '') +'>'+ ret[i] +'</option>';
	}
	
	return retstr;
};

var getTimeSelectContent = function(selected)
{
	var retstr = "";
	
	for(var i = 1; i <= 24; i++)
	{
		retstr += '<option value="'+i+'" '+ (selected==i ? ' selected="selected" ' : '') +'>'+ i +':00</option>';
	}
	
	return retstr;
};

const COM_SS = "savesettings";

const TEMPLATE_TYPE_ACCOUNT = 1;
const TEMPLATE_TYPE_CUSTOMER = 2;

var SettingsFunc =
{
	RemoveSilentInterval : function(index)
	{
		$("#interval_"+index).remove();
	},
	IntervalCount : 0,
	NewSilentInterval : function()
	{
		var i = SettingsFunc.IntervalCount++;
		var $tbody_intervals = $("#intervallist_table tbody");
		var row = '<tr id="interval_'+i+'">';
		row += '<td><select class="startday">'+ getDaySelectContent(1) +'</select></td>';
		row += '<td><select class="starttime">'+ getTimeSelectContent(12) +'</select></td>';
		row += '<td><select class="endday">'+ getDaySelectContent(1) +'</select></td>';
		row += '<td><select class="endtime">'+ getTimeSelectContent(13) +'</select></td>';
		row += '<td><button type="button" class="btn btn-default btn_silentmode_save" onClick="SettingsFunc.RemoveSilentInterval('+i+');return false;">Ta bort</button></td>';
		
		$tbody_intervals.append(row);
	},
	SaveIntervals : function()
	{
		var $tbody_intervals = $("#intervallist_table tbody");
		var array = [];
		$tbody_intervals.find("tr").each(function(index, element) {
			var $element = $(element);
            var obj =
			{
				StartDay : $element.find(".startday").val(),
				StartTime : $element.find(".starttime").val() + ":00:00",
				EndDay :$element.find(".endday").val(),
				EndTime: $element.find(".endtime").val() + ":00:00"
			};
			array[array.length] = obj;
        });
		
		
		
		
		SettingsFunc.RequestSaveIntervals(JSON.stringify(array));
	},
	isDisabled : false,
	Disable : function()
	{
		$("#module_settings").find("input, button").prop("disabled",true);
		SettingsFunc.isDisabled = true;
	},
	Enable : function()
	{
		$("#module_settings").find("input, button").prop("disabled",false);
		SettingsFunc.isDisabled = false;
	},
	ClickSaveNewPass : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		var $newpass = $("#newpassword");
		var $repeat = $("#repeatpassword");
		
		var newpass = $newpass.val();
		var repeat = $repeat.val();
		
		$newpass.modernizedSetErrorLabel("");
		$repeat.modernizedSetErrorLabel("");
		var is_req = false;
		if(!newpass)
			$newpass.modernizedSetErrorLabel("Ange ett lösenord");
		else if(newpass != repeat)
			$repeat.modernizedSetErrorLabel("Lösenorden stämmer inte överens");
		else
		{
			is_req = true;
			ComSystem.Request(COM_SS,{x:"newpw",password: newpass},function(data)
			{
				SettingsFunc.Enable();
				if(data.Success)
					alert("Sparat"); //TODO
			});
		}
		
		if(!is_req)
			SettingsFunc.Enable();
	},
	
	StartMyDisplayNameEdit : function()
	{
		$("#mydn_edit").html($("#mydn_display").html());
		$("#mydn_display_pane").hide();
		$("#mydn_edit_pane").show();
	},
	
	EndMyDisplayNameEdit : function()
	{
		$("#mydn_edit_pane").hide();
		$("#mydn_display_pane").show();
	},
	
	SaveDisplayNameSign : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		var name = $("#mydn_edit").val();
		Stuff.DisplayName = name;
		
		ComSystem.Request(COM_SS,{x:"setdisplayname",name: name},function(data)
		{
			$("#mydn_display").html(name);
			SettingsFunc.EndMyDisplayNameEdit();
			SettingsFunc.Enable();
		});
	},
	
	HideTemplatePane : function()
	{
		$("#newatemplate,#newctemplate").hide();
		$("#btn_createat,#btn_createct").show();
	},
	
	ShowTemplatePane : function(ttype)
	{
		//Hide and show
		if(ttype == TEMPLATE_TYPE_CUSTOMER)
		{
			$("#btn_createat").show();
			$("#newatemplate").hide();
			$("#btn_createct").hide();
			$("#newctemplate").show();
		}
		else
		{
			$("#btn_createat").hide();
			$("#newatemplate").show();
			$("#btn_createct").show();
			$("#newctemplate").hide();
		}
	},
	
	ClickSaveTemplate : function(ttype)
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		
		var $namefield, $contentfield;
		if(ttype == TEMPLATE_TYPE_CUSTOMER)
		{
			$namefield = $("#tname_c");
			$contentfield = $("#tcontent_c");
		}
		else
		{
			$namefield = $("#tname_a");
			$contentfield = $("#tcontent_a");
		}
		
		var name = $namefield.val();
		var content = $contentfield.val();
		
		$namefield.modernizedSetErrorLabel("");
		$contentfield.modernizedSetErrorLabel("");
		
		var is_req = false;
		
		if(!name)
			$namefield.modernizedSetErrorLabel("Ange ett namn");
		else if(!content)
			$contentfield.modernizedSetErrorLabel("Ange innehåll");
		else
		{
			is_req = true;
			ComSystem.Request(COM_SS,{x:"addtemplate",ttype: ttype, name : name, content: content},function(data)
			{
				var newTemplateID = data.TemplateID;
				SettingsFunc.Enable();
				SettingsFunc.HideTemplatePane();
				
				var $tablebody = (ttype == TEMPLATE_TYPE_CUSTOMER) ? $("#companytemplate_table tbody") : $("#mytemplate_table tbody");
				
				$tablebody.append('<tr id="templaterow_'+newTemplateID+'"><td class="img"><img style="width: 14px;" src="/images/send4.svg"></td><td class="name">'+name+'</td><td class="content">'+content+'</td><td class="right"><span class="defaultbuttons"><button type="button" class="btn btn-default" onclick="SettingsFunc.StartEditTemplate('+newTemplateID+');return false;">Redigera</button>&nbsp;<button type="button" class="btn btn-default" onclick="SettingsFunc.ClickRemoveTemplate('+newTemplateID+');return false;">Ta bort</button></span></td></tr>');
				
				SettingsFunc.ToggleTemplatePanels();
			});
		}
		
		if(!is_req)
			SettingsFunc.Enable();
	},
	
	StartMySignEdit : function()
	{
		$("#mysignature_edit").html($("#mysignature_display").html());
		$("#mysignature_display_pane").hide();
		$("#mysignature_edit_pane").show();
	},
	
	EndMySignEdit : function()
	{
		$("#mysignature_edit_pane").hide();
		$("#mysignature_display_pane").show();
	},
	
	SaveMySign : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		var signature = $("#mysignature_edit").val();
		
		Stuff.MySignature = signature;
		Stuff.HaveMySignature = signature != "";
		
		ComSystem.Request(COM_SS,{x:"setmysign",sign: signature},function(data)
		{
			$("#mysignature_display").html(signature);
			$("#mysignature_none").css("display",(signature ? "none" :""));
			SettingsFunc.EndMySignEdit();
			SettingsFunc.Enable();
		});
	},
	
	StartCompSignEdit : function()
	{
		$("#compsignature_edit").html($("#compsignature_display").html());
		$("#compsignature_display_pane").hide();
		$("#compsignature_edit_pane").show();
	},
	
	EndCompSignEdit : function()
	{
		$("#compsignature_edit_pane").hide();
		$("#compsignature_display_pane").show();
	},
	
	SaveCompSign : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		var signature = $("#compsignature_edit").val();
		
		Stuff.CompanySignature = signature;
		
		ComSystem.Request(COM_SS,{x:"setcustsign",sign: signature},function(data)
		{
			$("#compsignature_display").html(signature);
			$("#compsignature_none").css("display",(signature ? "none" :""));
			SettingsFunc.EndCompSignEdit();
			SettingsFunc.Enable();
		});
	},
	
	
	
	ClickCompSignCheckbox : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		var checked = document.getElementById('compsign_enabled').checked;
		
		$("#companysignature_notification").toggle(checked);
		
		Stuff.CompanySignatureActive = checked;
		Stuff.CompanySignature = $("#compsignature_edit").val();
		ComSystem.Request(COM_SS,{x:"setcustsignactivation",active: (checked ? 1 : 0)},function(data)
		{
			$("#compsign_not").css("display",(checked ? "block" : "none"));
			SettingsFunc.Enable();
		});
	},
	
	ClickRemoveTable: function(id)
	{
		alert("TODO: Confirmbox");
	},
	
	currentEditTemplate : {ID: 0, Name: null, Content: null},
	
	EndEditTemplate : function()
	{
		$("div.active-container").css("display","");
		$("#edittemplateform").css("display","none");
	},
	
	StartEditTemplate : function(id)
	{
		var $trow = $("#templaterow_"+id);
		var $nameCell = $trow.find(".name");
		var $contentCell = $trow.find(".content");
		
		$("div.active-container").css("display","none");
		$("#edittemplateform").css("display","");
		
		
		SettingsFunc.currentEditTemplate.ID = id;
		SettingsFunc.currentEditTemplate.Name = $nameCell.html();
		SettingsFunc.currentEditTemplate.Content = $contentCell.html();
		
		$("#tname_e").val(SettingsFunc.currentEditTemplate.Name);
		$("#tcontent_e").val(SettingsFunc.currentEditTemplate.Content);
	},
	
	SaveTemplateEdit : function()
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		
		var is_req = false;
		
		var $nameField = $("#tname_e");
		var $contentField = $("#tcontent_e");
		
		
		var newName = $nameField.val();
		var newContent = $contentField.val();
		
		ComSystem.Request(COM_SS,{x:"edittemplate",id: SettingsFunc.currentEditTemplate.ID, "delete" : 0, tname_e : newName, tcontent_e : newContent},function(data)
		{
			if(data.Success)
			{
				SettingsFunc.currentEditTemplate.Name = newName;
				SettingsFunc.currentEditTemplate.Content = newContent;
				
				var $trow = $("#templaterow_"+SettingsFunc.currentEditTemplate.ID);
				var $nameCell = $trow.find(".name");
				var $contentCell = $trow.find(".content");
				
				$nameCell.html(newName);
				$contentCell.html(newContent);
			}
			SettingsFunc.EndEditTemplate();
			SettingsFunc.Enable();
		});
		
		if(!is_req)
			SettingsFunc.Enable();
	},
	
	ClickRemoveTemplate : function(id)
	{
		if(confirm("Är du säker?")) //TODO
		{
			
			ComSystem.Request(COM_SS,{x:"edittemplate",id: id, "delete" : 1},function(data)
			{
				if(data.Success)
				{
					$("#templaterow_"+id).remove();
					SettingsFunc.ToggleTemplatePanels();
				}
				SettingsFunc.EndEditTemplate();
				SettingsFunc.Enable();
			});
		}
	},
	
	ToggleTemplatePanels : function()
	{
		var haveMyTemplates = $("#mytemplate_table tbody tr").length > 0;
		$("#mytemplate_table").toggle(haveMyTemplates);
		$("#mytemplate_none").toggle(!haveMyTemplates);
		
		var haveCompanyTemplates = $("#companytemplate_table tbody tr").length > 0;
		$("#companytemplate_table").toggle(haveCompanyTemplates);
		$("#companytemplate_none").toggle(!haveCompanyTemplates);
	},
	
	StartCreateAccount : function()
	{
		NavigationSystem.Navigate("module_editaccount newaccount");
	},
	
	StartEditAccount : function(id)
	{
		NavigationSystem.Navigate("module_editaccount acc"+id);
	},
	
	ActivateManualSilentMode : function()
	{
		ComSystem.Request(COM_SS,{x:"setmanualsilentmodestatus",active: 1},function(data)
		{
			if(data.Success)
			{
				document.getElementById('settings_manualsilent_status').innerHTML = "Status: Aktiverat";
				document.getElementById('btn_manual_silentmode_activate').style.display = "none";
				document.getElementById('btn_manual_silentmode_deactivate').style.display = "";
			}
			SettingsFunc.EndEditTemplate();
			SettingsFunc.Enable();
		});
	},
	DeactivateManualSilentMode : function()
	{
		ComSystem.Request(COM_SS,{x:"setmanualsilentmodestatus",active: 0},function(data)
		{
			if(data.Success)
			{
				document.getElementById('settings_manualsilent_status').innerHTML = "Status: Ej aktiverat";
				document.getElementById('btn_manual_silentmode_activate').style.display = "";
				document.getElementById('btn_manual_silentmode_deactivate').style.display = "none";
			}
			SettingsFunc.EndEditTemplate();
			SettingsFunc.Enable();
		});
	},
	
	InitializeSilentTimeline : function(intervals)
	{
		$('#silent-mode-timeline').timeline({data:intervals});
	},
	
	RequestSaveIntervals : function(jsonstring)
	{
		if(SettingsFunc.isDisabled)
			return;
		SettingsFunc.Disable();
		ComSystem.Request("savecalintervals",{data:jsonstring},function(data)
		{
			if(data.Success)
			{
				
			}
			SettingsFunc.EndEditTemplate();
			SettingsFunc.Enable();
		});
	},
	
	

};


function onEditAccountEnter(accountID)
{
	var isNewAccount = (accountID == null);
	ComSystem.Request("editaccountpage", {accountid:(accountID ? accountID : 0), newaccount : (accountID ? 0 : 1)}, function(data)
	{		
				var isNew = data.IsNewAccount;
				
				document.getElementById('ea_email').innerHTML = data.AccountData.EmailAddress;
				
				document.getElementById('ea_password_generated').style.display = "none";
				
				$("#editacc_convlist_list tbody").html("");
				
				if(isNew)
				{
					$("#displayname_part").css("display","none");
					$("#eac_email").css("display","");
					$("#ea_email").css("display","none");
				}
				else
				{
					$("#displayname_part").css("display","");
					$("#eac_email").css("display","none");
					$("#ea_email").css("display","");
					$("#ea_displayname").html(data.AccountData.DisplayName ? data.AccountData.DisplayName : '<i>N/A</i>');
				}
				
				document.getElementById('editacc_buttonbar1').style.display = isNew ? "none" :"";
				
				
				document.getElementById('ea_email').innerHTML = data.AccountData.EmailAddress;
				
				document.getElementById('eapriv_editaccount').checked 		= (data.AccountData.Privileges[EDITACCOUNTS] ? true : false);
				document.getElementById('eapriv_allconvs').checked 			= (data.AccountData.Privileges[ALLCONVERSATIONS] ? true : false);
				document.getElementById('eapriv_companytemplate').checked 	= (data.AccountData.Privileges[COMPANYTEMPLATE] ? true : false);
				document.getElementById('eapriv_companysignature').checked 	= (data.AccountData.Privileges[COMPANYSIGNATURE] ? true : false);
				document.getElementById('eapriv_companytraffic').checked 	= (data.AccountData.Privileges[COMPANYTRAFFIC] ? true : false);
				
				
				$(".editpriv_checkbox").prop("disabled",!data.CanEditPrivileges);
				$(".editpriv_checkbox_label").attr("disabled",data.CanEditPrivileges ? "disabled" : "");
				document.getElementById('savepriv_button').disabled = !data.CanEditPrivileges;
				
				document.getElementById('buttonbar_savepriv').style.display = isNew ? "none" :"";
				document.getElementById('buttonbar_saveconvpriv').style.display = isNew ? "none" :""; 
				
				document.getElementById('ea_section_traffic').style.display = isNew ? "none" :""; 
				
				document.getElementById('ea_cancelaccount').style.display = data.CanCancelAccount ? "" : "none";
				
				
				var $convlist = $("#editacc_convlist_list tbody");
				for(var i = 0; i < data.Conversations.length; i++)
				{
					var id = 'convprivcb'+ data.Conversations[i].ID
					$convlist.append('<tr><td>'+Phonenumber.GetDisplayStyle(data.Conversations[i].Number)+'</td><td>'+(data.Conversations[i].ConversationName ? data.Conversations[i].ConversationName : "<i>Inget namn</i>")+'</td><td class="right"><div class="checkbox-default convaccess_checkbox"><input type="checkbox" id="' + id +'" name="' + id +'" '+(data.CanEditConvPrivileges ? '' : 'disabled="disabled"')+'/><label for="' + id +'" '+(data.CanEditConvPrivileges ? '' : 'disabled="disabled"')+'></label></div>');
					$("#" + id).data("convid", data.Conversations[i].ID).prop("checked",!data.Conversations[i].Blocked);
				}
				document.getElementById('saveconvpriv_button').disabled = !data.CanEditConvPrivileges;
				document.getElementById('buttonbar_savenewaccount').style.display = (!isNew) ? "none" :"";
				
				EAFunc.currentAccount = (accountID ? accountID : 0);
				
				initCheckboxes();
				// Loading is finished
				
	});
}

var EAFunc =
{
	currentAccount : 0,
	isDisabled : false,
	Disable : function()
	{
		$("#module_editaccount").find("input, button").prop("disabled",true);
		SettingsFunc.isDisabled = true;
	},
	Enable : function()
	{
		$("#module_editaccount").find("input, button").prop("disabled",false);
		SettingsFunc.isDisabled = false;
	},
	ParsePrivileges : function()
	{
		var ret = new Array();
		ret[ret.length] = {Priv :EDITACCOUNTS, Val: document.getElementById('eapriv_editaccount').checked};
		ret[ret.length] = {Priv :ALLCONVERSATIONS, Val: document.getElementById('eapriv_allconvs').checked};
		ret[ret.length] = {Priv :COMPANYTEMPLATE, Val: document.getElementById('eapriv_companytemplate').checked};
		ret[ret.length] = {Priv :COMPANYSIGNATURE, Val: document.getElementById('eapriv_companysignature').checked};
		ret[ret.length] = {Priv :COMPANYTRAFFIC, Val: document.getElementById('eapriv_companytraffic').checked};
		
		return ret;
	},
	ParseConversationPrivileges : function()
	{
		var convPrivData = new Array();
		
		$(".convaccess_checkbox input").each(function(index, element) {
            var $element = $(element);
			convPrivData[convPrivData.length] = { ConversationID: $element.data("convid"), Blocked :  (! $element.prop("checked"))};
        });
		
		return convPrivData;
	},
	
	ButtonSaveNewAccount : function()
	{
		if(EAFunc.isDisabled)
			return;
		EAFunc.Disable();
		var $emailField = $("#eac_email");
		var email = $emailField.val();
		$emailField.modernizedSetErrorLabel("");
		
		var is_req = false;
		
		if(email)
		{
			var privData = EAFunc.ParsePrivileges();
			var convprivData = EAFunc.ParseConversationPrivileges();
			
			is_req = true;
			ComSystem.Request("eanewaccount", {eac_email : email, priv : JSON.stringify(privData), convpriv : JSON.stringify(convprivData)}, function(data)
			{
				if(data.Success)
				{
					var newID = data.NewAccountID;
					NavigationSystem.Navigate("module_editaccount acc" + newID + " newcreatedacc");
				}
				EAFunc.Enable();
			});
		}
		else
		{
			$emailField.modernizedSetErrorLabel("Ange en E-postadress");
		}
		
		if(!is_req)
			EAFunc.Enable();
	},
	
	SavePrivEdit : function()
	{
		if(EAFunc.isDisabled)
			return;
		EAFunc.Disable();
		
		var privData = EAFunc.ParsePrivileges();
		
		ComSystem.Request("easave", {x: "editpriv", acc: EAFunc.currentAccount, priv : JSON.stringify(privData)}, function(data)
		{
			if(data.Success)
			{
				
			}
			EAFunc.Enable();
		});
	},
	
	SaveConvPrivEdit : function()
	{
		if(EAFunc.isDisabled)
			return;
		EAFunc.Disable();
		
		var convPrivData = EAFunc.ParseConversationPrivileges();
		
		ComSystem.Request("easave", {x: "editconvpriv", acc: EAFunc.currentAccount, convpriv : JSON.stringify(convPrivData)}, function(data)
		{
			if(data.Success)
			{
				
			}
			EAFunc.Enable();
		});
	},
	
	GeneratePassword : function()
	{
		if(EAFunc.isDisabled)
			return;
		EAFunc.Disable();
		
		var convPrivData = EAFunc.ParseConversationPrivileges();
		
		ComSystem.Request("easave", {x: "generatepass", acc: EAFunc.currentAccount}, function(data)
		{
			if(data.Success)
			{
				document.getElementById('ea_password_generated').style.display = "";
			}
			EAFunc.Enable();
		});
	},
	
	ButtonGeneratePassword : function()
	{
		if(confirm("Vill du generera ett nytt lösenord?"))
			EAFunc.GeneratePassword();
	},
	
	OnSearchEdit : function()
	{
		setTimeout(EAFunc.OnSearchEdit2,100);
	},
	
	OnSearchEdit2 : function()
	{
		var s = document.getElementById('accounts_search_textfield').value.toLowerCase();
		var $tb = $("#accountlist_table tbody");
		var $cells;
		if(s == "")
			$tb.find("tr").show();
		else
		{
			$tb.find("tr").each(function(index, element) {
                $cells = $(element).find("td");
				
				if($cells[0].innerHTML.toLowerCase().indexOf(s) != -1 || $cells[1].innerHTML.toLowerCase().indexOf(s) != -1)
					$(this).show();
				else
					$(this).hide();
            });
		}
	},
	
	CancelAccount : function()
	{
		if(EAFunc.isDisabled)
			return;
		EAFunc.Disable();
		
		var convPrivData = EAFunc.ParseConversationPrivileges();
		
		ComSystem.Request("cancelaccount", {acc : EAFunc.currentAccount}, function(data,data2)
		{
			if(data.Success)
			{
				NavigationSystem.Navigate("module_settings account_accountlist settingspage");
			}
			EAFunc.Enable();
		});
	},
	
	ButtonCancelAccount : function()
	{
		if(confirm("Är du säker på att du vill avsluta kontot"))
		{
			EAFunc.CancelAccount();
		}
	}
};

function showNewPWPane()
{
	$("#changepw_pane").fadeIn(300);
	$("#btnchangepass_1").prop("disabled",true);
}