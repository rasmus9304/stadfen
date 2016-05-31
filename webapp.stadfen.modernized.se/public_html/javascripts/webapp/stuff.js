
var Phonenumber =
{
	DEFAULT_CONTRYCODE : 46,
	GetDisplayStyle : function(standardNumber)
	{
		return "+" + standardNumber;
	},
	ParseToStandard : function(number)
	{
		number = number.replace(" ","").replace("-","");
		//Check if it starts with countycode
		if(number.indexOf("00") == 0)
		{
			//Remove leading zeroes
			return number.slice(2);
		}
		else if(number.indexOf("+") == 0)
		{
			//Remove leading zeroes
			return number.slice(1);
		}
		else
		{
			while(number.indexOf("0") == 0)
				number = number.slice(1);
			//If it didn't start with countrycode, remove leading zeroes and dd default contrycode
			return Phonenumber.DEFAULT_CONTRYCODE + number;
		}
	}
};

function _nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function escapeHtml(text) {
	if(text === null)
		return null;
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

function parseDateTime(strDateTime)
{
	var a = strDateTime.split(/[^0-9]/);
	return new Date (parseInt(a[0]),parseInt(a[1])-1,parseInt(a[2]),parseInt(a[3]),parseInt(a[4]),parseInt(a[5]) );
}
var StringOperations =
{
	endsWith : function(str, suffix) {
		return str.indexOf(suffix, str.length - suffix.length) !== -1;
	},
	
	removeFromEnd : function(str, remove)
	{
		return str.substring(0, str.length - remove.length);
	}
};


function isnum(val)
{
	return /^\d+$/.test(val);
}
