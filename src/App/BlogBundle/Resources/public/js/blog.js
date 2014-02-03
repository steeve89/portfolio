var current_page = 0;
!function ($) {
    $(function(){

        var $window = $(window);        
        // make code pretty
        window.prettyPrint && prettyPrint();
        //  Collections d'objets
        var $classItem = ".collections-items";
        var $collectionsItems = $($classItem);        
        if($collectionsItems.size())
        {
            var number_of_pages = Math.ceil($collectionsItems.find('.post').size() / show_per_page);
            if( number_of_pages > 0 ) 
            {
                var nav = '<div class="pagination pagination-centered">';
                nav += '<ul>';
                nav += '<li class="previous_link">';
                nav += '<a href="javascript:pagination_previous(\'' + $classItem + '\');"><<</a>';
                nav += '';
                var i = -1;
                while(number_of_pages > ++i){
                    nav += '<li class="page_link';
                    if(!i) nav += ' active';
                    nav += '" id="id' + i +'">';
                    nav += '<a href="javascript:pagination_go_to_page(\'' + $classItem + '\', ' + i + ')">'+ (i + 1)
                    +'</a>';
                    nav += '';
                }
                nav += '<li class="next_link" >';
                nav += '<a href="javascript:pagination_next(\'' + $classItem + '\');">>></a>';
                nav += '';
                nav += '</ul>';
                nav += '</div>';
                $('#page_navigation').html(nav);
                set_display($collectionsItems, 0, show_per_page);
            }
        }
    });
}(window.jQuery);

function set_display($collectionsItems, first, last) {
    $collectionsItems.find('.post').css('display', 'none');
    $collectionsItems.find('.post').slice(first, last).css('display',
    'block');
}

function pagination_previous($classItem){
    var $collectionsItems = $($classItem);
    if($( '#page_navigation .active').prev('.page_link').length)
    pagination_go_to_page($collectionsItems, current_page - 1);
}

function pagination_next($classItem){
    var $collectionsItems = $($classItem);
    if($( '#page_navigation .active').next('.page_link').length)
    pagination_go_to_page($collectionsItems, current_page + 1);
}

function pagination_go_to_page($classItem, page_num){
    var $collectionsItems = $($classItem);
    current_page = page_num;
    var start_from = current_page * show_per_page;
    var end_on = start_from + show_per_page;
    set_display($collectionsItems, start_from, end_on);
    $('#page_navigation .active').removeClass('active');
    $('#page_navigation #id' + page_num).addClass('active');
}