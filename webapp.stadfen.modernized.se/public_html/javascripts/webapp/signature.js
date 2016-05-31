

var Signature =
{
	GetCurrent : function()
	{
		if(Stuff.CompanySignatureActive)
			return Stuff.CompanySignature;
		else if(Stuff.HaveMySignature)
			return Stuff.MySignature;
		else
			return null;
	},
	
	GetCurrentWithGlue : function()
	{
		return SystemSettings.SIGNATUREGLUE + Signature.GetCurrent();
	},
	
	GetMaxMessageLength : function()
	{
		var sign = Signature.GetCurrent();
		
		if(sign == null)
			return SystemSettings.MAX_MESSAGE_LENGTH;
		else
			return (SystemSettings.MAX_MESSAGE_LENGTH - SystemSettings.SIGNATUREGLUE.length - Signature.GetCurrent().length);
	}
};