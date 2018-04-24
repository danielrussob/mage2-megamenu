require([
    'jquery'
], function ($, window, document, undefined) {
    $(document).ready(function(){
        /* toggle menu mobile */
        $(".nav-toggle").off('click').on('click',function(e){
            if(!$("html").hasClass("nav-open")) {
                $("html").addClass("nav-before-open");
                setTimeout(function(){
                    $("html").addClass("nav-open");
                }, 300);
            }
            else {
                $("html").removeClass("nav-open");
                setTimeout(function(){
                    $("html").removeClass("nav-before-open");
                }, 300);
            }
        });

        /* Mobile toggle menu items li */
        $("li.ui-menu-item > .open-children-toggle").off("click").on("click", function(){
            if(!$(this).parent().children(".submenu").hasClass("opened")) {
                $(this).parent().children(".submenu").addClass("opened");
                $(this).parent().children("a").addClass("ui-state-active");
            }
            else {
                $(this).parent().children(".submenu").removeClass("opened");
                $(this).parent().children("a").removeClass("ui-state-active");
            }
        });
    });
});