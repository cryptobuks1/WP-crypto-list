/* debouncedresize: special jQuery event that happens once after a window resize
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery-smartresize
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 */
(function($){var $event=$.event,$special,resizeTimeout;$special=$event.special.debouncedresize={setup:function(){$(this).on("resize",$special.handler);},teardown:function(){$(this).off("resize",$special.handler);},handler:function(event,execAsap){var context=this,args=arguments,dispatch=function(){event.type="debouncedresize";$event.dispatch.apply(context,args);};if(resizeTimeout){clearTimeout(resizeTimeout);}execAsap?dispatch():resizeTimeout=setTimeout(dispatch,$special.threshold);},threshold:150};})(jQuery);

/* throttledresize: special jQuery event that happens at a reduced rate compared to "resize"
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery-smartresize
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 */
(function($){var $event=$.event,$special,dummy={_:0},frame=0,wasResized,animRunning;$special=$event.special.throttledresize={setup:function(){$(this).on("resize",$special.handler);},teardown:function(){$(this).off("resize",$special.handler);},handler:function(event,execAsap){var context=this,args=arguments;wasResized=true;if(!animRunning){setInterval(function(){frame++;if(frame>$special.threshold&&wasResized||execAsap){event.type="throttledresize";$event.dispatch.apply(context,args);wasResized=false;frame=0;}if(frame>9){$(dummy).stop();animRunning=false;frame=0;}},30);animRunning=true;}},threshold:0};})(jQuery);