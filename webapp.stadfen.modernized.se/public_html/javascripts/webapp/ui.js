window.requestAnimFrame = (function(callback) {
return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
function(callback) {
  window.setTimeout(callback, 1000 / 60);
};
})();

var isInitScreen = true;

function hideInitScreen()
{
	$("#page_init").fadeOut(500);
	CircleLoadingAnimation.Destroy();
	isInitScreen = false;
}

function showInitScreen()
{
	$("#page_init").show();
	var canvas = document.getElementById('bubble-canvas-init');
	CircleLoadingAnimation.Create(canvas);
	CircleLoadingAnimation.BeginAnimation();
	isInitScreen = true;
}

function AnimateConversationScrollToBottom(time)
{
	$('.conversation-thread .messages').animate({scrollTop: $('.conversation-thread .messages')[0].scrollHeight + "px"},time);
}
	
function isConversationScrolledToBottom()
{
	return ($('.conversation-thread .messages').scrollTop() == ($('.conversation-thread .messages')[0].scrollHeight - $('.conversation-thread .messages').height()));
}

function DisableSendInputs()
{
	$('#button-newmessage').attr('disabled', true);
	$('#reply-btn-send').attr('disabled', true);
}

function EnableSendInputs()
{
	$('#button-newmessage').attr('disabled', false);
	$('#reply-btn-send').attr('disabled', false);
}