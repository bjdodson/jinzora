// JavaScript Document
/*************************************************
Star Rating System
First Version: 21 November, 2006
Author: Ritesh Agrawal
Inspriation: Will Stuckey's star rating system (http://sandbox.wilstuckey.com/jquery-ratings/)
Demonstration: http://php.scripts.psu.edu/rja171/widgets/rating.php
Usage: $('#rating').rating('www.url.to.post.com', {maxvalue:5, curvalue:0});

arguments
url : required -- post changes to 
options
	maxvalue: number of stars
	curvalue: number of selected stars
	
************************************************/

jQuery.fn.rating = function(url, options) {
	
	if(url == null) return;
	
	var settings = {
        url       : url, // post changes to 
        maxvalue  : 5,   // max number of stars
        curvalue  : 0    // number of selected stars
    };
	
    if(options) {
       jQuery.extend(settings, options);
    };
   jQuery.extend(settings, {cancel: (settings.maxvalue > 1) ? true : false});
   
   
   var container = jQuery(this);
	
	jQuery.extend(container, {
            averageRating: settings.curvalue,
            url: settings.url
        });

	for(var i= 0; i <= settings.maxvalue ; i++){
		var size = i
        if (i == 0) {
			if(settings.cancel == true){
	             var div = '<div class="cancel"><a href="#0" title="Cancel Rating">Cancel Rating</a></div>';
				 container.append(div);
			}
        } 
		else {
             var div = '<div class="star"><a href="#'+i+'" title="Give it '+i+'/'+size+'">'+i+'</a></div>';
			 container.append(div);

        }
 
		

	}
	
	var stars = jQuery(container).children('.star');
    var cancel = jQuery(container).children('.cancel');
	
    stars
	        .mouseover(function(){
                event.drain();
                event.fill(this);
            })
            .mouseout(function(){
                event.drain();
                event.reset();
            })
            .focus(function(){
                event.drain();
                event.fill(this)
            })
            .blur(function(){
                event.drain();
                event.reset();
            });

    stars.click(function(){
		if(settings.cancel == true){
            settings.curvalue = stars.index(this) + 1;
            jQuery.post(container.url, {
                "rating": jQuery(this).children('a')[0].href.split('#')[1] 
            }, function(a) { alert(a) });
			return false;
		}
		else if(settings.maxvalue == 1){
			settings.curvalue = (settings.curvalue == 0) ? 1 : 0;
			$(this).toggleClass('on');
			jQuery.post(container.url, {
                "rating": jQuery(this).children('a')[0].href.split('#')[1] 
            });
			return false;
		}
		return true;
			
    });

        // cancel button events
	if(cancel){
        cancel
            .mouseover(function(){
                event.drain();
                jQuery(this).addClass('on')
            })
            .mouseout(function(){
                event.reset();
                jQuery(this).removeClass('on')
            })
            .focus(function(){
                event.drain();
                jQuery(this).addClass('on')
            })
            .blur(function(){
                event.reset();
                jQuery(this).removeClass('on')
            });
        
        // click events.
        cancel.click(function(){
            event.drain();
			settings.curvalue = 0;
            jQuery.post(container.url, {
                "rating": jQuery(this).children('a')[0].href.split('#')[1] 
            });
            return false;
        });
	}
        
	var event = {
		fill: function(el){ // fill to the current mouse position.
			var index = stars.index(el) + 1;
			stars
				.children('a').css('width', '100%').end()
				.lt(index).addClass('hover').end();
		},
		drain: function() { // drain all the stars.
			stars
				.filter('.on').removeClass('on').end()
				.filter('.hover').removeClass('hover').end();
		},
		reset: function(){ // Reset the stars to the default index.
			stars.lt(settings.curvalue).addClass('on').end();
		}
	}        
	event.reset();
	
	return(this);	

}