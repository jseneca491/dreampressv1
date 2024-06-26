(function($){
    $(function($, undefined){
        //--------------- Data Attribute Driven Swiper Slider ---------------//
        //Ready Made Swiper
        $('[data-carousel="swiper"]').each( function() {

            var container		= $(this).find('[data-swiper="container"]').attr('id');
            var pagination		= $(this).find('[data-swiper="pagination"]').attr('id');
            var prev		    = $(this).find('[data-swiper="prev"]').attr('id');
            var next		    = $(this).find('[data-swiper="next"]').attr('id');
            var items		    = $(this).data('items');
            var autoplay		= $(this).data('autoplay');
            var iSlide		    = $(this).data('initial');
            var loop		    = $(this).data('loop');
            var center		    = $(this).data('center');
            var effect		    = $(this).data('effect');
            var parallax	    = $(this).data('parallax');
            var direction	    = $(this).data('direction');
        
            // Configuration
            var conf 	= {};
        
            if ( items ) {
                conf.slidesPerView = items
            };
            if ( autoplay ) {
                conf.autoplay = autoplay
            };
            if ( iSlide ) {
                conf.initialSlide = iSlide
            };
            if ( center ) {
                conf.centeredSlides = center
            };
            if ( loop ) {
                conf.loop = loop
            };
            if ( effect ) {
                conf.effect = effect
            };
            if ( direction ) {
                conf.direction = direction
            };
            if ( parallax ) {
                conf.parallax = parallax
            };
            if ( prev ) {
                conf.prevButton = '#' + prev
            };
            if ( next ) {
                conf.nextButton = '#' + next
            };
            if ( pagination ) {
                conf.pagination = {
                    el : '#' + pagination,
                    clickable: true
                }
            };
        
            // Initialization
            if (container) {
                var initID = '#' + container;
                var init = new Swiper( initID, conf);
            };
        });


        //GLightbox
        
        //-------- START DATA ATTRIBUTE DRIVEN [GLightbox] ---------//
        $('[data-glightbox="imagebox"]').each( function() {
            
            if($(this).is('[id]') == true){
                var container = $(this).attr('id');
                console.log(container);
                var data_imagebox = GLightbox({
                    selector: '#' + container
                });
            }
            
        });

        $('[data-glightbox="htmlbox"]').each( function() {
            if($(this).is('[id]') == true){
                var container = $(this).attr('id');
                var data_htmlbox = GLightbox({
                    selector: '#' + container
                });
            }
        });

        $('[data-glightbox="videobox"]').each( function() {
            if($(this).is('[id]') == true){
                var container = $(this).attr('id');
                var data_videobox = GLightbox({
                    selector: '#' + container
                });
            }
        });
        //-------- END DATA ATTRIBUTE DRIVEN [GLightbox] ---------//
        
        //-------- START CLASS DRIVEN [GLightbox] ---------//
        var imagebox = GLightbox({
            selector: '.dp-image'
        });

        var htmlbox = GLightbox({
            selector: '.dp-html'
        });

        var videobox = GLightbox({
            selector: '.dp-video'
        });
        //-------- END CLASS DRIVEN [GLightbox] ---------//

        //-------- START CLASS DRIVEN [MatchHeight JS] ---------//
            // EXAMPLE
            // $('.block').matchHeight();
        //-------- END CLASS DRIVEN [MatchHeight JS] ---------//

        //-------- START CLASS DRIVEN [Rellax JS] ---------//
        // var rellax = new Rellax('.rellax', {
        //     speed: 5,
        // });
        //-------- END CLASS DRIVEN [Rellax JS] ---------//

        // ------------- Live Search Ajax  ------------- //
        // Delay function for trigger events
        // function delay(callback, ms) {
        //     var timer = 0;
        //     return function() {
        //         var context = this, args = arguments;
        //         clearTimeout(timer);
        //         timer = setTimeout(function () {
        //         callback.apply(context, args);
        //         }, ms || 0);
        //     };
        // }
        
        //Search event on word typed
        // $('#live_search').keyup(delay(function(){
            
        //     var live_search = $(this).val();
        //     if(live_search){
        //         $('.livesearch-item').remove();
        //         $('.livesearch-loader').css('display','block');
        //         $.ajax({
        //             type: 'POST',
        //             url: et_pb_custom.ajaxurl,
        //             dataType: "json",
        //             data: { action : 'knowledge_search', live_search : live_search },
        //             success: function( response ) {
        //                 $('.livesearch-wrap').append(response.data_html);
        //                 $('.livesearch-wrap').css('display','block');
        //                 $('.livesearch-loader').hide();
        //             }
        //         });
        //     }else{
        //         $('.livesearch-item').remove();
        //         $('.livesearch-wrap').css('display','none');
        //     }
            

        // }, 1000));

        // $('.ds-search').submit(function(){
        //     return false;
        // });


        // ------------- Live Login Ajax  ------------- //
        //FORM SUBMIT
        // $('#login-modal form#login').on('submit', function(e){
            
        //     $('form#login p.status').show().html($.parseHTML(ds_login_object.loadingmessage));
        //     $.ajax({
        //         type: 'POST',
        //         dataType: 'json',
        //         url: ds_login_object.ajaxurl,
        //         data: {
        //             'action': 'ajaxlogin',
        //             'username': $('form#login #username').val(),
        //             'password': $('form#login #password').val(),
        //             'security': $('form#login #security').val() },
        //         success: function(data){
        //             $('form#login p.status').html($.parseHTML(data.message));
        //             if (data.loggedin === true){
        //                 document.location.href = ds_login_object.redirecturl;
        //             }
        //         }
        //     });
        //     e.preventDefault();
        // });

        //-------- START CLASS DRIVEN [One Page JS] ---------//
        // var load_Scroll = $(window).scrollTop() + 1;
        // var load_home_scroll = $('#banner').offset().top;
        // var _menu = $(".menu-item");

        // if (load_Scroll >= load_home_scroll) {
        //     $("#menu-main-menu .menu-item").removeClass("current-menu-item");
        //     $("#menu-main-menu .menu_banner").addClass("current-menu-item");

        //     $("#mobile_menu1 .menu-item").removeClass("current-menu-item");
        //     $("#mobile_menu1 .menu_banner").removeClass("current-menu-item");
        // }

        // for(let i = 0; i < _menu.length; i++){
        //     $(window).scroll(function() {
        //         var _element = $(".menu-item").eq(i);                     
        //         var att_id = _element.find('a').attr("href");
        //         var attr_menuid = att_id.replace('#', '');
        //         var Scroll = $(window).scrollTop();
        //         var current_pos = $(att_id).offset().top;
        //         if(Scroll > $(att_id).offset().top){
        //             $("#menu-main-menu .menu-item").removeClass("current-menu-item");
        //             $("#menu-main-menu .menu-"+ attr_menuid).addClass("current-menu-item");                    
        //             $("#mobile_menu1 .menu-item").removeClass("current-menu-item");
        //             $("#mobile_menu1 .menu-"+ att_id).addClass("current-menu-item");
        //         }
        //     });
        // }
        //-------- END CLASS DRIVEN [One Page JS] ---------//
    });
})(jQuery);
