(function( $ ){

  var settings;
  var dom_images;
  var src_images;
  var total_images;
  var error_images;
  var completed;
  var percentage; 

  var methods = {
    init : function( options ) { 
        settings = $.extend({
            'image_selector':'.preloadr',
            'image_list': new Array(),
            'stepin':function(){},
            'completed':function(){}
        },options);

        dom_images = $("img"+settings.image_selector);
        src_images = new Array();
        error_images = new Array();

        //Get image src from dom images with image_selector
        $(dom_images).each(function(i,obj){
            src_images.push(obj.src);
        });

        if(settings.image_list.length > 0){
            $(settings.image_list).each(function(i,obj){
                src_images.push(obj);
            });
        }
        total_images = src_images.length;
        completed = 0;
        percentage = 0;

        $(src_images).each(function(i,obj){
            temp_img = new Image();
            temp_img.src = obj;
            temp_img.onLoad = methods.loaded(temp_img);
            //temp_img.onError = methods.loadError(temp_img);
        });   
    },

    loaded: function(obj){
        completed++;
        percentage = 100 * completed / total_images;
        settings.stepin.apply({percentage:percentage});

        if(percentage == 100){
            settings.completed.apply();
        }
    },

    loadError: function(obj){
        error_images.push(obj.src);
        methods.loaded(obj);
    } 


  };

  $.fn.preloadr = function( method ) {

    // Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply(this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
    }    
  
  };

})( jQuery );