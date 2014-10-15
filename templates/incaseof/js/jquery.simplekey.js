/**
 * Created by ThemeVan.
 * SimpleKey Jquery functions.
 */
jQuery(function ($) { 
  /*Loading HomePage*/
  //var MobileDetect_loading = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/);
  if(isLoad==1){
    $('body').jpreLoader({
		loaderVPos: '50%'
		
	});
  } 
});
 
jQuery(document).ready(function($){
	
  function initPrimaryNavi(){
	   /*Fix the primary navi when scrolling*/
	   $("#primary-menu").sticky({topSpacing:0});
	   
       /*Sub menu*/
	   menulink=$('#primary-menu-container li');
	   menulink.each(function(){
			$(this).mouseover(function(){
			 $(this).children('ul.sub-menu').show();
			 $(this).children('ul.sub-menu li a').animate({paddingLeft:'18px'},'fast','linear');
			});
			$(this).mouseout(function(){
			 $(this).children('ul.sub-menu').hide();
			 $(this).children('ul.sub-menu li a').animate({paddingLeft:'15px'},'fast','linear');
			});
	   });
	   
	   /*Mobile menu*/
	   /*$('#mobile-menu').click(function(){
		 var menulist=$(this).children('ul#menulist');
		 if(menulist.css('display')=='none'){
			menulist.show();
			menulist.html($('#primary-menu-container ul').html());
		 }else{
			menulist.hide();
		 }
	   });
	   $('#mobile-menu li a').click(function(){
	        $('ul#menulist').hide();
	   });*/
	   /*Mobile menu*/
	   /*$('#mobileMenu').html($('#primary-menu-container').html());
	   $('#mobileMenu').mobileMenu({
				defaultText: 'Navigate to...',
				className: 'select-menu',
				subMenuDash: '&nbsp;&nbsp;'
	   });
	   $(".select-menu").each(function(){  
			$(this).wrap('<div class="css3-selectbox">');
	   });
	   $('#primary-menu-container li').each(function() {
			var i=1;
			if($(this).hasClass('none')) {
			  $(this).remove();
			}
		});*/
	   
   }initPrimaryNavi();
   
   /*Init Portfolio block*/
   $('.overlay').hide();
   function initPortfolioBlocks(){
       var MobileDetect = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/);
	   if(MobileDetect) {
	      $(window).load(function(){
	           $('.portfolio-item').fadeIn();
	      });
	      $('.portfolio-item').click(function(){
	           var permalink=$(this).find('.info').attr('href');
	           $(this).attr('href',permalink);
	           location.href=permalink;
	      });
	   }else{
		   /*Show Portfolios detail*/
		   function portfolioHoverIn(){
			   $(this).children('.overlay').fadeIn(200);
			   $(this).children('.tools').fadeIn(200);
		   }
		   /*Hide Portfolios detail*/
		   function portfolioHoverOut(){
			   $(this).children('.overlay').fadeOut();
			   $(this).children('.tools').fadeOut();
		   }
	           $('.portfolio-item').hoverIntent({
				 sensitivity: 2,
				 interval: 20,
				 over: portfolioHoverIn,
				 timeout: 0,
				 out: portfolioHoverOut
		   });
	   }
	   
	   //init isotope
	   $('.portfolios').isotope({ 
          itemSelector: '.portfolio-item',
		  animationEngine: 'css'
	   });
	   $('#filter a').click(function(){
		  var selector = $(this).attr('data-filter');
		  $('.portfolios').isotope({ 
		    filter: selector
		  });
		  $(this).parent().attr('class','filter_current');
		  $(this).parent().siblings().removeAttr('class');
		  return false;
		});
		
		//Set Portfolio Height on Mobile
		if(MobileDetect) {
		var portfolioWidth=$('.portfolio-item').width();
		$('.portfolio-item').css('height',portfolioWidth+'px')
		}
		
		//Ajax load content
		if(MobileDetect) {
			//do something for mobile
			/*V!11052013*/
			$('.portfolio-item').each(function(){
				var myhref = $(this).attr('data-urlnoajax');
				$(this).find('a.overlay').attr('href', myhref );
				$(this).find('a.overlay').removeClass('ajax');
				$(this).find('.tools span .zoomin').css({'display':'none'})
			});
		}else{
			$('.portfolio-item a.ajax').add('.zoomin').click(function(){
			 var url=$(this).parent().attr('data-url');
			 if(url!==''){
				 $("body").getNiceScroll().hide();
				 portfolioTop = $(this).parent().offset().top;
				$("#ajax-load").slideDown();
				ajaxload('#ajax-content',url,'#portfolio-single');
				//Load effects
				//$('.flexslider').flexslider();
			 }
			});
		}
			$("#ajax-load #close").click(function(){
				$('html,body').animate({scrollTop:portfolioTop-100},'slow');
				$("#ajax-load").slideUp();
				$('#ajax-content').html('');
				$("body").getNiceScroll().show();
			});
		
   }initPortfolioBlocks();
   
   /*Init Team block*/
   function initTeamBlocks(){
	   /*Show Portfolio's detail*/
	   function TeamHoverIn(){
		   $(this).children('.overlay').fadeIn();
	   }
	   /*Hide Portfolio's detail*/
	   function TeamHoverOut(){
		   $(this).children('.overlay').fadeOut();
	   }
	   $('.member .avatar').hoverIntent({
			 sensitivity: 2,
			 interval: 100,
			 over: TeamHoverIn,
			 timeout: 0,
			 out: TeamHoverOut
	   })
   }initTeamBlocks();
	
   function initPageScroll(){
	   /*Smooth Scroll to section*/
	   $.localScroll({
		target:'body',
		offset: {left: 0, top: -65}
	   });
	   
	 //Detecting page scroll and set the navigation link active status
	$(window).scroll(function() {

		var currentNode = null;
		$('.page-area').each(function(){
			var currentId = $(this).attr('id');	
			if($(window).scrollTop() >= $('#'+currentId).offset().top - 79)
			{
				currentNode = currentId;
			}
		});
		$('#primary-menu li').removeClass('current-menu-item').find('a[href="#'+currentNode+'"]').parent().addClass('current-menu-item');
	});
	   
	   /*Smooth scroll event*/
	   $("body").niceScroll({
		   cursorcolor:"#000",
		   scrollspeed:70,
		   horizrailenabled:false,
		   autohidemode:false
	   });
   }initPageScroll();
   
   /*Flex slider*/
   /*$('.flexslider').flexslider();*/
   
   /*Display the slider background on mobile & tablet*/
   $(window).load(function() {
	   if($(window).width() <= 1024 && $(window).width() >= 768) {
		 replaceSliderBg('data-ipad');
	   }
	   if($(window).width() <= 640) {
		 replaceSliderBg('data-mobile');
	   }
   });
   
   function replaceSliderBg(data){
     $('.slide_bg').each(function() {
         var newSrc=$(this).children('img').attr(data);
	     if(newSrc!==''){
           $(this).children('img').attr('src',newSrc);
	     }
     });
   }
   
   /*Lightbox*/
   $('a.lightbox').colorbox({
	  maxWidth:"98%"
   });
   $('.attachment a').colorbox({
	  maxWidth:"98%"
   });
   $('.gallery-icon a').colorbox({
	  maxWidth:"98%"
   });
   //added for handmade photos
   $('a.link').colorbox({
	   inline:true, width:"50%",opacity:0
   });
   $('a.biglink').colorbox({
	   inline:true, width:"50%",opacity:0, returnFocus:false 
	   
	   });
   
   /*Lazyload*/
   if (navigator.platform == "iPad") return;
   $("img").lazyload({
       effect:"fadeIn",
       placeholder: pixel
   });
   
   /*Placeholder for IE*/
   $("input, textarea").placeholder();
   
   /*Display back to top button*/
	$(window).scroll(function(){
	  if($(document).scrollTop()==0){
		  $('#backtoTop').hide();
	  }else{
	      $('#backtoTop').show();
	  }
	});
	/*Back to Top*/
	$('#backtoTop').click(function(){
		$('html,body').animate({scrollTop:0},'slow');
		return false;

	});
   
   /*Ajax load*/
   function ajaxload(id,url,object) { 
	$(id).addClass("loader"); 
	$.ajax({ 
		type: "get", 
		url: url, 
		cache: false, 
		error: function() {(id).html('Loading error!');}, 
		success: function(data) { 
			$(id).removeClass("loader"); 
            $("body").css({"overflow":"hidden"});
			$("#ajax-load").css({"overflow":"auto"});
			//$content=$(data).find(object).html();
			//$(id).append($content);
			
			//alert(data);
			$('#ajax-content').html(data);
			
			$('.attachment a').colorbox({maxWidth:"98%"});
            $('#ajax-content .gallery-icon a').colorbox({ maxWidth:"98%"});
            $('#ajax-content a.lightbox').colorbox({ maxWidth:"98%"});
		}
	}); 
   }
   
   //adds br to shipment and payment method
   $( "<br/>" ).insertAfter( $( ".vmshipment_name" ) );
   $( "<br/>" ).insertAfter( $( ".vmCartPaymentLogo" ) );
   
   /*$(document).on('click','.overlay',function(){
   	window.addEvent('domready', function() {
   	     SqueezeBox.fromElement('ajax-load');
   	  });
   	return false;
   }); */
})