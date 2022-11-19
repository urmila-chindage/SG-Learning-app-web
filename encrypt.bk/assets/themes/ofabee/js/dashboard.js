
$( ".block-load-in" ).click(function( event ) {
    event.preventDefault();
});

function renderMyCourseCategories(categories){
    var renderHtml = '<li><a href="javascript:void(0);" onclick="changeCourseCategory(0)">All Categories</a></li>';

    $.each(categories, function(key, category )
    {
        if(key != 0){
            renderHtml += '<li><a href="javascript:void(0);" onclick="changeCourseCategory('+key+')">'+category['title']+'</a></li>';
        }
    });

    return renderHtml;
}

function changeCourseCategory(category_id){
    $('.hiding-course').hide();
    if(category_id != 0){
        $('#my_courses_category_name').html(__my_course_categories[category_id]['title']);
        $('.hiding-course-'+category_id).show();
    }else{
        $('#my_courses_category_name').html('All categories');
        $('.hiding-course').show();
    }
}