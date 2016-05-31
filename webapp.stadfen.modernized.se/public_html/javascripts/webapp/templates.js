var Templates =
{
	RequestList : function(callback)
	{
		ComSystem.Request("templatelist",null,callback);
	},
	RequestTemplate : function(id, callback)
	{
		ComSystem.Request("template",{tid: id},callback);
	},
};