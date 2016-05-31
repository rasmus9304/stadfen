// JavaScript Document

var CircleLoadingAnimation = 
{
	circle1 : {
		x:0,
		y:0,
		size:0,
		r:99,
		g:219,
		b:255,
		a:1,	
	},
	circle2 : {
		x:0,
		y:0,
		size:0,
		r:51,
		g:240,
		b:255,
		a:1,	
	},
	circle3 : {
		x:0,
		y:0,
		size:0,
		r:51,
		g:194,
		b:255,
		a:1,		
	},
	circle4 : {
		x:0,
		y:0,
		size:0,
		r:60,
		g:249,
		b:212,
		a:1,	
	},
	canvas : null,
	context : null,
	timer : null,
	circleCount : 4,
	max_size : 40,
	animation_speed : 1000,
	scale : 0.07,
	delay : 600,
	loadingScreenAnimationDuration : 1000,
	DrawCircle : function(circle, context)
	{
		circle.size += this.scale;
		circle.size += this.scale;
		circle.x = this.canvas.width/2;
		circle.y = this.canvas.height/2;
		circle.a -= 0.004;
		
		if (circle.size > this.max_size)
		{
			circle.size = 0;
			circle.a = 1;	
		}
		
		context.beginPath();
		context.arc(circle.x,circle.y,circle.size,0,2*Math.PI);
		context.fillStyle = 'rgba('+circle.r+','+circle.g+','+circle.b+','+circle.a+')';
		context.fill();
	},
	animate : function(canvas, context, startTime)
	{
		var time = (new Date()).getTime() - startTime;
		
		if (this.canvas == null || this.context == null)
		{
			this.Destroy();
			return;	
		}
		
		context.clearRect(0, 0, canvas.width, canvas.height);
		
		if (time > this.delay*0)
		this.DrawCircle(this.circle1, context);
		if (time > this.delay*1)
		this.DrawCircle(this.circle2, context);
		if (time > this.delay*2)
		this.DrawCircle(this.circle3, context);
		if (time > this.delay*3)
		this.DrawCircle(this.circle4, context);
		
		requestAnimFrame(function() {
			  CircleLoadingAnimation.animate(canvas, context, startTime);
		});
	},
	Create : function(c)
	{
		this.canvas = c;
		this.context = c.getContext('2d');
	},
	Destroy : function()
	{
		this.EndAnimation();
		this.canvas = null;
		this.context = null;
	},
	BeginAnimation : function()
	{
		CircleLoadingAnimation.timer = setTimeout(function() {
        var startTime = (new Date()).getTime();
        CircleLoadingAnimation.animate(CircleLoadingAnimation.canvas, CircleLoadingAnimation.context, startTime);
      }, 0);
	},
	EndAnimation : function()
	{
		clearTimeout(this.timer);
		this.ResetCircle(this.circle1);
		this.ResetCircle(this.circle2);
		this.ResetCircle(this.circle3);
		this.ResetCircle(this.circle4);
	},
	ResetCircle : function(circle)
	{
		circle.x = 0;
		circle.y = 0;
		circle.size = 0;
		circle.a = 1;
	}
};