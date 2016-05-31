
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

var arr = ["Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec"];

function getMonthInSwedish(n)
{
	return arr[n];
}

function getDateArray(past_days)
{
	var today = new Date();
	var arr = new Array();
	
	for (var i = 0; i < past_days;i++)
	{
		if (d < 2)
		{
			today.setMonth(today.getMonth()-1);
			today.setDate(31);
		}
		
		var d = today.getDate() - i;
		
		arr[i] = d + " " + getMonthInSwedish(today.getMonth());
	}
	
	return arr;
}

function randomData(num)
{
	var arr = new Array();
	
	for (var i = 0; i < num; i++)
	{
		arr[i] = getRandomInt(250, 500);
	}
	
	return arr;
}

function showAccountMenu()
{
	$('.background-fade').fadeIn(200);
	$('.account-menu').animate({ 'left':'0px' }, 200);
}

function hideAccountMenu()
{
	$('.background-fade').fadeOut(200);
	$('.account-menu').animate({ 'left':'-500px' }, 200);
}

$(document).ready(function(e) {
	
	jQuery('img.svg').each(function(){
    var $img = jQuery(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgURL = $img.attr('src');

    jQuery.get(imgURL, function(data) {
        // Get the SVG tag, ignore the rest
        var $svg = jQuery(data).find('svg');

        // Add replaced image's ID to the new SVG
        if(typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if(typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass+' replaced-svg');
        }

        // Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');

        // Replace image with new SVG
        $img.replaceWith($svg);

    }, 'xml');

});
	
});
