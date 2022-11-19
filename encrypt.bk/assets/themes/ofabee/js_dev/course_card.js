
function __courseCard(course)
{ console.log('course card js');
    var coursesHtml  = '';

    var __img_path          = ((course['cb_image'] == 'default.jpg')?__default_course_path:__course_path);
    var image_first_name    = course['cb_image'];
        image_first_name    = image_first_name.slice(0,-4);
    var image_dimension     = '_300x160.jpg';
    var __image_new_name    = image_first_name+''+image_dimension;
    
    var course_rate = "width:0%";
    if(course['ratting'] !== undefined && course['ratting'] != 0){
        var percentage  = 20*course['ratting'];
        var course_rate = 'width:'+percentage+'%';
    } 
    var course_slug     = (course['cb_slug'] !== undefined)? course['cb_slug'] : '';
    var course_title    = (course['cb_title'] !== undefined)? course['cb_title'] : '';
    
    var tutor_names = new Array();
    if(course['assigned_tutors'] !== undefined){
        $.each(course['assigned_tutors'], function(tutorKey, course_tutor ){
            tutor_names.push(course_tutor['us_name']);
        });
    }
    
    var by_tutor = (tutor_names.length == 0)?__admin_name:tutor_names.join();
    var header_url = __site_url+course_slug;
    var footer_url = __site_url+course_slug;
    var onclick = 'javascript:void(0)';

    if(course['enrolled']){
        if(+course['cs_approved']){
            header_url = __site_url+'materials/course/'+course['id']+'#'+course['cs_last_played_lecture'];
            footer_url = __site_url+'course/dashboard/'+course['id'];
        }else{
            header_url = 'javascript:void(0)';
            footer_url = 'javascript:void(0)';
            onclick = 'showCommonModal(\'\',\'Subsciption is waiting for approoval by admin.\',\'\')';
        }
    }

    coursesHtml += '    <div class="col-md-3 col-sm-3 xs-replacer">';
    coursesHtml += '            <div class="course-block-1">';
    coursesHtml += '                <div class="course-top-half course-top-sm-alter">';
    coursesHtml += '                    <a onclick="'+onclick+'" href="'+header_url+'">';
    coursesHtml += '                        <img src="'+__img_path+__image_new_name+'" class="card-img-fit">';
    if(course['enrolled']){
        coursesHtml += '                    <div class="play-btn"></div>';
    }
    coursesHtml += '                    </a>';
    coursesHtml += '                </div>';
    if(course['enrolled']){
        course['percentage'] = Math.round(+course['percentage']);
        coursesHtml += '<div class="courser-bottom-half">';
        coursesHtml += '    <a onclick="'+onclick+'" href="'+footer_url+'">';
        coursesHtml += '        <label class="block-head">'+course_title+'</label>';
        coursesHtml += '        <p class="sub-head-des-pre">'+by_tutor+'</p>';

        if(+course['cs_approved']){
            coursesHtml += '        <div class="progress_main">';
            coursesHtml += '            <div class="progress">';
            coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
            coursesHtml += '            </div>';
            coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
            coursesHtml += '        </div>';
            switch(+course['cs_course_validity_status']){
                case 0:
                    coursesHtml += '    <span class="course-status course-green">Lifetime Validity</span>';
                break;
                default:
                    if(+course['expire_in_days'] > 0){
                        switch(+course['expire_in_days']){
                            case 0:
                                coursesHtml += '    <span class="course-status course-green">Expires today</span>';
                            break;
                            case 1:
                                coursesHtml += '    <span class="course-status course-green">Expires tomorrow</span>';
                            break;
                            default:
                                coursesHtml += '    <span class="course-status course-green">Expires in '+course['expire_in_days']+' days</span>';
                            break;
                        }
                    }else{
                        coursesHtml += '    <span class="course-status course-red">Expired on '+course['validity_format_date']+'</span>';
                    }
                break;
            }
        }else{
            coursesHtml += '        <div class="progress_main">';
            coursesHtml += '            <div class="progress">';
            coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
            coursesHtml += '            </div>';
            coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
            coursesHtml += '        </div>';
            coursesHtml += '        <span class="course-status course-red">Pending Approval</span>';
        }        

        coursesHtml += '    </a>';
        coursesHtml += '</div>';
    }else{
        coursesHtml += '        <div class="courser-bottom-half">';
        coursesHtml += '            <a onclick="'+onclick+'" href="'+footer_url+'">';
        coursesHtml += '                <label class="block-head">'+course_title+'</label>';
        coursesHtml += '                <p class="sub-head-des">'+by_tutor+'</p>';
        coursesHtml += '                <div class="star-ratings-sprite star-ratings-sprite-block"><span style="'+course_rate+'" class="star-ratings-sprite-rating"></span></div>';
        if(course['cb_is_free'] == '1'){

            coursesHtml += '                <label class="amount"><strong>FREE</strong></label>';
        }else{

            if(course['cb_discount']>0){
                coursesHtml += '                <label class="amount"><span style="font-family: Roboto, sans-serif;">&#8377;</span>. '+course['cb_discount']+'</label>';
                coursesHtml += '                <label class="discount"><span style="font-family: Roboto, sans-serif;">&#8377;</span>. '+course['cb_price']+'</label>';
            }else{
                coursesHtml += '                <label class="amount"><span style="font-family: Roboto, sans-serif;">&#8377;</span>. '+course['cb_price']+'</label>';
            }
            
        }
        coursesHtml += '            </a>';
        coursesHtml += '        </div>';
    }
    coursesHtml += '            </div>';
    coursesHtml += '    </div>';

    return coursesHtml;
}

function stringToDate(s) {
    var dateParts = s.split(' ')[0].split('-'); 
    var timeParts = s.split(' ')[1].split(':');
    var d = new Date(dateParts[0], --dateParts[1], dateParts[2]);
    d.setHours(timeParts[0], timeParts[1], timeParts[2])
    return d;
}