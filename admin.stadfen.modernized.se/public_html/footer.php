                </div>
            </div>
        </div>
	
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.1.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>
		<script src="js/Chart.min.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
        
        <script>
		function setMainHeight()
		{
			var width = $(window).height();
			var topheight = $(".top-bar").outerHeight()
			var newHeight = width - topheight;
			$(".content").css("height", newHeight  + "px");
		}
		
		$(document).ready(function(e) {
            setMainHeight();
        });
		$(window).resize(function(e) {
            setMainHeight();
        });
		</script>
    </body>
</html>
