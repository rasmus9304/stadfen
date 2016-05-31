// JavaScript Document

(function( $ ) {
 
    $.fn.setOrder = function( orderValue ) {
 
        $(this).css("-webkit-box-ordinal-group", orderValue+1);
 		$(this).css("-webkit-order", orderValue);
		$(this).css("-moz-order", orderValue);
		$(this).css("-ms-flex-order", orderValue);
		$(this).css("order", orderValue);
		
        return this;
 
    };
 
}( jQuery ));