const INPUTSTATUS_GOOD = 0;
const INPUTSTATUS_ERROR = 1;

(function ( $ ) 	
{
	const CLASSNAME_ERROR = "modernizedformerror";
	var __modernized_manage_inputreturns = function(data)
	{
		var success = true;
		for(var i = 0; i < data.length; i++)
		{
			var info = data[i];
			
			var $element = $("#" + info.ElementID);
			if($element.length > 0)
			{
				$element.modernizedSetInputStatus(info.Status, info.Message, info.Value);
				
				if(success && info.Status == INPUTSTATUS_ERROR)
					success = false;
			}
			else
			{
				document.write("AJAXENGINE ERROR_2, unable to select element with ID '"+ info.ElementID +"'");
			}
		}
		
		return success;
	}
	
	
	var exectuteJQAjax = function(func, url, data, success)
	{
		return func(url,data,function(retdata)
		{
			//alert("callback for post");
			var pdata
			try
			{
				pdata = JSON.parse(retdata);
			}
			catch(e)
			{
				document.write("AJAXENGINE ERROR_1 for url \""+ url +"\": " + retdata);
				return;
			}
			
			if(pdata.Javascript !== undefined && pdata.Javascript !== null)
				eval(pdata.Javascript);
			
			var formularsuccess = __modernized_manage_inputreturns(pdata.FormData);
			
			if(pdata.EchoManual == 1 || (pdata.EchoManual == 2 && pdata.ManualData))
				alert(pdata.ManualData);
				
				
			if(pdata.DebugMessage)
				alert(pdata.DebugMessage);
				
				
			if(success !== undefined && success !== null)
				success(pdata.Data,pdata.ManualData,formularsuccess);
				
		});
	}
	
	$.modernizedGET = function(url, data, success)
	{
		return exectuteJQAjax($.get, url, data, success);
	}
	
	$.modernizedPOST = function(url, data, success)
	{
		//alert("calling post");
		return exectuteJQAjax($.post, url, data, success);
	}
	
	$.fn.modernizedSetInputStatus = function(Status, Message, Value)
	{
		if(Value !== undefined && Value !== null)
			this.val(Value);
			
		this.modernizedSetErrorLabel(Message);
			
		if(Status !== undefined && Status !== null)
		{
			if(Status == INPUTSTATUS_GOOD)
				this.removeClass(CLASSNAME_ERROR);
			else if(Status = INPUTSTATUS_ERROR)
				this.addClass(CLASSNAME_ERROR);
		}
		else
		{
			this.removeClass(CLASSNAME_ERROR);
		}
		
		return this;
	}
	
	$.fn.modernizedSubmitForm = function(additionalValues,callback,precheck)
	{
		var $form = this;
		
		
		var $controls = $form.find("input,select,textarea");
		
		$controls.each(function(index, element) {
			var $element = $(element);
            var name = $element.attr('name');
			var id = $element.attr('id');
			if((typeof name === 'undefined' || name === false) && !(typeof id === 'undefined' || id === false))
				$element.attr('name',id);
        });
		
		var formdata = $form.serializeArray();
		var formdata2 = {};
		//Reorganize
		$.each(formdata,function(index,value)
		{
			formdata2[value.name] = value.value;
		});
		
		//For checkboxes
		$controls.each(function(index, element) {
			if(element.type == "checkbox")
			{
				var index = element.name;
				if(typeof index !== "undefined")
					formdata2[index] = element.checked;
			}
        });
		formdata = formdata2;
		
		var beginsubmit = function()
		{
			$controls.prop('disabled', true);
		};
		var endsubmit = function()
		{
			$controls.prop('disabled', false);
		};
		
		beginsubmit();
		
		if(additionalValues !== undefined && additionalValues !== null)
		{
			formdata = $.extend(formdata,additionalValues);
		}
			
		if(precheck === undefined || precheck === null || precheck(formdata) == true)
		{
			var callback2 = function(data,manualdata,formularsuccess)
			{
				if(callback !== undefined && callback !== null)
					callback(data, manualdata, formularsuccess);
				endsubmit();
			};
			/*submit*/
			$.modernizedPOST($form.attr("action"),formdata, callback2);
		}
		else
		{
			callback(null, null,false);
			endsubmit();
		}
		
		return this;
	}
	
	var modernizedButtonAjax = function(element, func, url, data, success)
	{
		if(element.data("modernizedButton") !== true)
		{
			element.data("modernizedButton",true);
			
			return exectuteJQAjax(func, url, data, function(data,data2,formularsuccess)
			{
				element.data("modernizedButton",false);
				
				if(typeof success !== "undefined" && success !== null) 
					success(data,data2,formularsuccess);
			});
		}
		
		return element;
	}
	
	$.fn.modernizedButtonGET = function(url, data, success)
	{
		return modernizedButtonAjax(this,$.get, url, data, success);
	}
	
	$.fn.modernizedButtonPOST = function(url, data, success)
	{
		return modernizedButtonAjax(this,$.post, url, data, success);
	}
	
	$.fn.modernizedButtonActive = function()
	{
		return this.data("modernizedButton") === true;
	}
	
	
	
	$.fn.modernizedSetErrorLabel = function(Text)
	{
		if(Text == null || Text == "")
		{
			this.modernizedRemoveErrorLabel();
		}
		else if(this.data("modernizederrorlabel") !== undefined && this.data("modernizederrorlabel") !== null)
		{
			var label = this.data("modernizederrorlabel");
			label.innerHTML = Text;
		}
		else
		{
			var $form = this.closest("form");
			$form.css("position","relative");
			
			var label = document.createElement("div");
			var $label = $(label);
			
			$label.addClass("modernizederrorlabel");
			
			$label.css("position","absolute");

			$form.append(label);
			
			$label.html('<p>'+Text+'</p>');
			
			var inputOffset = this.offset();
			var inputW = this.outerWidth();
			var inputH = this.outerHeight();
			
			var formOffset = $form.offset();

			var posX = inputOffset.left + inputW - formOffset.left + 2;
			var posY = inputOffset.top + inputH / 2 - $label.height() / 2 - formOffset.top;
			
			$label.css("left", posX + "px");
			$label.css("top", posY + "px");
			
			$label.stop().fadeIn(200, function() {
			
				$label.delay(10000).fadeOut(200);
				
			});
			
			$(this).data("modernizederrorlabel", label);
		}
	}
	
	$.fn.modernizedRemoveErrorLabel = function()
	{
		var label = this.data("modernizederrorlabel");
		var $label = $(label);
		this.data("modernizederrorlabel",null);
		$label.remove();
	}
} (jQuery)); 

