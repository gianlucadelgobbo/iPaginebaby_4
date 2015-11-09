$(document).ready(function (){
    //PRESET

    var whatsIphoneVisible = false;
    var current_platform = "iPhone";
    var div_intro = $("#intro");
    var cookie_platform = getCookie("platform");
    ua = navigator.userAgent;
    var isiPad = /iPad/i.test(ua);

    //Cookie set
    if(cookie_platform){
        switch (cookie_platform){
            case "ipad":
                $("img."+current_platform).hide();
                $("img.iPad").show();
                $("#intro-iphone").attr("class","iPad");
                $("#whats-iphone").attr("class","iPad");
                $("#first-btn").text("iPhone");
                $(".slide .iPad-slide").show();
                current_platform = "iPad";
            break;
            case "android":
                $("img."+current_platform).hide();
                $("img.Android").show();
                $("#intro-iphone").attr("class","Android");
                $("#whats-iphone").attr("class","Android");
                $("#second-btn").text("iPhone");
                $(".slide .Android-slide").show();
                current_platform = "Android";
            break;
            case "windows":
                $("img."+current_platform).hide();
                $("img.Windows").show();
                $("#intro-iphone").attr("class","Windows");
                $("#whats-iphone").attr("class","Windows");
                $("#second-btn").text("iPhone");
                $("#third-btn").text("Android");
                $(".slide .Windows-slide").show();
                current_platform = "Windows";
            break;
            default:
                $(".slide .iPhone-slide").show();
        }

        //BTN STORE
        switch(current_platform){
            case "Android":
                $("#intro .titles a.iPad").hide();
                $("#intro .titles a.Windows").hide();
                $("#intro .titles a.Android").show();
            break;

            case "Windows":
                $("#intro .titles a.iPad").hide();
                $("#intro .titles a.Windows").show();
                $("#intro .titles a.Android").hide();
            break;

            default:
                $("#intro .titles a.iPad").show();
                $("#intro .titles a.Windows").hide();
                $("#intro .titles a.Android").hide();
        }


    //Cookie not set
    }else{ 

        $(".slide .iPhone-slide").show();
    } 

    if ($(window).width() > 1024){ 

        if ($(window).scrollTop() <= 400){ 
            $("#whats #whats-iphone").css("left","-600px");
            $("#whats #whats-iphone").css("opacity",0);
        }
    }else{
        if ($(window).width() > 960){ 
            animateWhats();                  
        }
    }

    images = new Array(
        template_path+"/img/world.png",
        template_path+"/img/windows.png",
        template_path+"/img/android.png",
        template_path+"/img/iphone.png",
        template_path+"/img/ipad.png",
        template_path+"/img/icon.png",
        template_path+"/img/iphone-whats.png",
        template_path+"/img/android-whats.png",
        template_path+"/img/windows-whats.png",
        template_path+"/img/center_bg.jpg",
        template_path+"/img/more_sprite.png",
        template_path+"/img/social_sprite.png",
        template_path+"/img/store_sprite.png",
        template_path+"/img/white_bg.jpg"       
    );

    $().preloadr({
        'image_list':images,
        completed:function(){
            var t=setTimeout("firstAnimation()",1200);
        }
    });

    //Resize ---------------------------/
    $(window).resize(function(){ //The First resize() is called when contents are loaded 
        if ( $(window).width() > 960){  
            if(!isiPad){
                $("#world").css("position","fixed");
            }
        }else{
            $("#world").css("position","absolute");
        }
        
        animateWhats();
    });      

    //Home Animations ***************************************************/

    //What's - iphone ---------------------------------/

    
    //Intro - World - Iphone - Icon -------------------/
    var o_introIPhoneX = $("#intro-iphone").css("left");
    var o_introIPhoneY = "37px";
                    
    var o_iconX = $("#intro-icon").css("left");
    var o_iconY = "527px";

    var o_iPhoneX = 0
    var o_iPhoneY = $("#whats #whats-iphone").css("top");

    var whatsIphoneVisible = true;

    $(document).scroll(function() {
        
        if ($(window).width() > 1024){ 
            var scrollPosition = $(window).scrollTop();

            var worldLeft = $("#world").css("left");
            var worldTop = $("#world").css("top");
            var worldOpacity = $("world").css("opacity");
            
            var worldPosX = 0;
            var worldPosY = 5;

            var iphonePosX = 0;
            var iphonePosY = 0;

            var iconPosX = 0;
            var iconPosY = 0;

            var whatsIphonePosX = 0;

            switch(true){

                case (0 == scrollPosition):

                    $("#world").css("opacity",1) 
                    $("#world").css("left","0");
                    $("#world").css("top","5");

                    $("#intro-iphone").css("opacity",1) 
                    //$("#intro-iphone").css("left",o_introIPhoneX);
                    $("#intro-iphone").css("top",o_introIPhoneY);

                    $("#intro-icon").css("opacity",1) 
                    //$("#intro-icon").css("left",o_iconX);
                    $("#intro-icon").css("top",o_iconY);                
                 
                break;

                case ((0 < scrollPosition) && (600 > scrollPosition)):

                    /*WORLD TRANSITIONS******************************************/
                    $("#world").css("opacity",1-($(window).scrollTop()/900))
                    
                    worldPosX = expoEasing(scrollPosition,worldPosX,600,450);
                    worldPosY = expoEasing(scrollPosition,worldPosY,460,800);

                    $("#world").css("left",worldPosX+"px");
                    $("#world").css("top",worldPosY+"px");

                    /*IPHONE*****************************************************/
                    $("#intro-iphone").css("opacity",1 - expoEasing(scrollPosition,0,100,400)/1020); 
                                   
                    //iphonePosX = backEasing(scrollPosition,1,1500);
                    iphonePosY = expoEasing(scrollPosition,iphonePosY,-70,400);

                    $("#intro-iphone").css("top",(iphonePosY+37)+"px");

                    /*ICON*****************************************************/
                     
                    $("#intro-icon").css("opacity",1 - expoEasing(scrollPosition,0,100,400)/1020);
                    
                    //iconPosX = backEasing(scrollPosition,1,1500);
                    iconPosY = friction(-scrollPosition, 0.2);
                    
                    $("#intro-icon").css("top",(iconPosY+523)+"px");


                    if(scrollPosition > 400){
                        animateWhats();                
                    }

                break;


                default:
            }
        }

    });

    /*Home carousel *****************************************/

    $('#slides').carousel({
        slider: '.slides_container',
        slide: '.slide',
        nextSlide : '.next',
        prevSlide : '.prev',
        addPagination: true,
        addNav : false
    }).bind({
        'isMoving' : function(e,obj) {
            switch(obj.moveTo){      
                case -100:
                    $("ul.carousel-nav .next").css("display","none");
                    $("ul.carousel-nav .prev").css("display","block");
                break;

                case -0:
                    $("ul.carousel-nav .next").css("display","block");
                    $("ul.carousel-nav .prev").css("display","none");
                break;
            }        
        }
    });
    
    $('.flexslider').flexslider({
        animation: "fade",
        slideshow: true,
        directionNav: false,
        controlNav: false,
        slideshowSpeed: 5000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
        animationDuration: 600 
        }
    );

    /*Header switch *****************************************/

    var platform_list = $(".platform");
    var phone_dom = $("#intro-iphone");
    var what_phone_dom = $("#whats-iphone");
    var active_buttons = true;

    $(".platform a").click(function(e){
        e.preventDefault();

        if(active_buttons){
            active_buttons = false;
            selected_dom = $(this);
            selected_platform = clearWindowsPhoneText(selected_dom.text());

            old_platform = current_platform;
            current_platform = selected_platform; 

            $(what_phone_dom).animate({opacity:"0",left:"-1000px"},600,"easeInExpo");

            $(platform_list).fadeOut("slow",function(){
                selected_dom.text(attachWindowsPhoneText(old_platform));

                $(phone_dom).animate({"top" : "+=500","opacity":"0"},
                    {
                        complete:function(){

                            $(phone_dom).attr("class","");
                            $(phone_dom).addClass(current_platform);

                            $(what_phone_dom).attr("class","");
                            $(what_phone_dom).addClass(current_platform);

                            $("img."+old_platform).hide();
                            $(".slide ."+old_platform+"-slide").hide();
                            $("img."+current_platform).show();
                            $(".slide ."+current_platform+"-slide").show();
                            $(phone_dom).animate({"top" : "-=500","opacity":"1"},400,"easeOutExpo");

                            animateWhats();
                            setCookie(); 
                            setCookie("platform",current_platform,1);

                            active_buttons = true;
                        }
                    }
                );

                $(this).fadeIn("fast");

            });

            $("#intro .titles a.ir").animate({opacity:0},500,function(){

                switch(current_platform){
                    case "Android":
                        $("#intro .titles a.iPad").hide();
                        $("#intro .titles a.Windows").hide();
                        $("#intro .titles a.Android").show();
                        $("#intro .titles a.Android").animate({opacity:1});
                    break;
                    
                    case "Windows":
                        $("#intro .titles a.Android").hide();
                        $("#intro .titles a.Windows").show();
                        $("#intro .titles a.iPad").hide();
                        $("#intro .titles a.Windows").animate({opacity:1});
                    break;
                    
                    default:
                        $("#intro .titles a.Android").hide();
                        $("#intro .titles a.iPad").show();
                        $("#intro .titles a.Windows").hide();
                        $("#intro .titles a.iPad").animate({opacity:1});

                }

                /*DELETE
                if(current_platform == 'Android'){
                    $("#intro .titles a.iPad").hide();
                    $("#intro .titles a.Android").show();
                    $("#intro .titles a.Android").animate({opacity:1});
                }else{
                    $("#intro .titles a.Android").hide();
                    $("#intro .titles a.iPad").show();
                    $("#intro .titles a.iPad").animate({opacity:1});              
                }
                */
            });
        }
    });

    function animateWhats(){
        if(current_platform == "iPad"){
            $("#whats #whats-iphone").stop().animate({
                left: "-505px",
                opacity: 1
            },800,"easeOutExpo");
        }else{
            $("#whats #whats-iphone").stop().animate({
                left: "0px",
                opacity: 1
            },800,"easeOutExpo");   
        }  
    } 
});
//End ready 

function firstAnimation(){
    $("#loading").fadeOut("fast");

     $("#intro-iphone").animate({opacity:1,top:"37px"},700,"easeOutExpo",function(){
         $("#world").animate({opacity:1,top:"5px",left:"0"},500,"easeOutExpo",function(){
            $("#intro-icon").animate({opacity:1},200);   
            if ($("#intro").width() <= 1024){
                $("#world").css("position","absolute");   
            }
         });
    });
}



//Animation function *****************************************/
function friction (time,friction){  
    return time*friction;
}

function backEasing(pos,amp,filter){
    filter = 1500;
    amp = 1;

    return ((pos*pos*pos) - pos * amp * Math.sin(pos * 3.14))/filter;
}

function expoEasing(time,start,end,duration){   
    return end * Math.pow( 2, 10 * (time/duration - 1) ) + start;
}


//Cookies management *****************************************/

function setCookie(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()+"; path=/");
    document.cookie=c_name + "=" + c_value.toLowerCase();
}


function getCookie(c_name)
{
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
      x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
      y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
      x=x.replace(/^\s+|\s+$/g,"");
      if (x==c_name)
        {
        return unescape(y);
        }
    }
}

//Windows text fix 
function clearWindowsPhoneText(theText){ 
    if (theText == "Windows Phone") {
        return "Windows";
    }else{  
        return theText;
    }
}

function attachWindowsPhoneText(theText){  
    if (theText == "Windows") {
        return "Windows Phone";
    }else{  
        return theText;
    }
}

// end
