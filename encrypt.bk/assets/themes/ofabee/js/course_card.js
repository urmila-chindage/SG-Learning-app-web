console.log('sdpk js');
function __courseCard(course)
{
    // console.log(course);
    var item_type           = course['item_type'];
    var coursesHtml         = '';
    var __img_path          = '';
    var __image_new_name    = '';
    var image_dimension     = '_300x160.jpg';
    var course_slug         = '';
    var course_title        = '';

    if(item_type == 'bundle'){

        __img_path              = ((course['c_image'] == 'default.jpg')?__default_catalog_path:(__catalog_path.replace("/catalog/", "/catalog/"+course['id']+"/catalog/")));
        var image_first_name    = course['c_image'];
        image_first_name        = image_first_name.slice(0,-4);
        __image_new_name        = image_first_name+''+image_dimension;
        course_slug             = (course['c_slug'] !== undefined)? course['c_slug'] : '';
        course_title            = (course['c_title'] !== undefined)? course['c_title'] : '';
    }else{
        
        __img_path              = ((course['cb_image'] == 'default.jpg')?__default_course_path:(__course_path.replace("/course/", "/course/"+course['id']+"/course/")));
        var image_first_name    = course['cb_image'];
        image_first_name        = image_first_name.slice(0,-4);
        __image_new_name        = image_first_name+''+image_dimension;
        var course_rate         = "width:0%";
        if(course['ratting'] !== undefined && course['ratting'] != 0){
            var percentage      = 20*course['ratting'];
            var course_rate     = 'width:'+percentage+'%';
        } 
        course_slug             = (course['cb_slug'] !== undefined)? course['cb_slug'] : '';
        course_title            = (course['cb_title'] !== undefined)? course['cb_title'] : '';
    
        var tutor_names = new Array();
        if(course['assigned_tutors'] !== undefined){
            $.each(course['assigned_tutors'], function(tutorKey, course_tutor ){
                tutor_names.push(course_tutor['us_name']);
            });
        }
        var by_tutor = (tutor_names.length == 0)?__admin_name:tutor_names.join(', ');
    
    }
    
    var header_url = __site_url+course_slug;
    var footer_url = __site_url+course_slug;
    var onclick = 'javascript:void(0)';

    if(item_type == 'bundle'){

        if(course['enrolled']){
            switch(+course['bs_approved']){
                case 1:
                header_url = __site_url+course_slug;
                footer_url = __site_url+course_slug;
                break;
                case 2:
                header_url = 'javascript:void(0)';
                footer_url = 'javascript:void(0)';
                onclick = 'showCommonModal(\'\',\'Subsciption is waiting for approval by admin.\',\'\')';
                break;
                default:
                header_url = 'javascript:void(0)';
                footer_url = 'javascript:void(0)';
                onclick = 'showCommonModal(\'\',\'Your subsciption is suspended by admin.\',\'\')';
                break;
            }
        }
        coursesHtml += '    <div class="col-md-3 col-sm-3 xs-replacer">';
        coursesHtml += '            <div class="course-block-1">';
        coursesHtml += '                <div class="course-top-half course-top-sm-alter">';
        coursesHtml += '                    <a onclick="'+onclick+'" href="'+header_url+'">';
        coursesHtml += '                        <img src="'+__img_path+__image_new_name+'" class="card-img-fit">';
        // if(course['enrolled']){
        //     coursesHtml += '                    <div class="play-btn"></div>';
        // }
        coursesHtml += '                    </a>';
        coursesHtml += '                </div>';
        coursesHtml += '                <div class="bundle-label">';
        coursesHtml += '                    <div class="bundle-icon">';
        coursesHtml += '                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 296.999 296.999" style="enable-background:new 0 0 296.999 296.999;fill: #fff;width: 30px;height: 22px;" xml:space="preserve">';
        coursesHtml += '                      <g><g><g><path d="M45.432,35.049c-0.008,0-0.017,0-0.025,0c-2.809,0-5.451,1.095-7.446,3.085c-2.017,2.012-3.128,4.691-3.128,7.543     v159.365c0,5.844,4.773,10.61,10.641,10.625c24.738,0.059,66.184,5.215,94.776,35.136V84.023c0-1.981-0.506-3.842-1.461-5.382     C115.322,40.849,70.226,35.107,45.432,35.049z"></path>';
        coursesHtml += '                      <path d="M262.167,205.042V45.676c0-2.852-1.111-5.531-3.128-7.543c-1.995-1.99-4.639-3.085-7.445-3.085c-0.009,0-0.018,0-0.026,0     c-24.793,0.059-69.889,5.801-93.357,43.593c-0.955,1.54-1.46,3.401-1.46,5.382v166.779     c28.592-29.921,70.038-35.077,94.776-35.136C257.394,215.651,262.167,210.885,262.167,205.042z"></path>';
        coursesHtml += '                      <path d="M286.373,71.801h-7.706v133.241c0,14.921-12.157,27.088-27.101,27.125c-20.983,0.05-55.581,4.153-80.084,27.344     c42.378-10.376,87.052-3.631,112.512,2.171c3.179,0.724,6.464-0.024,9.011-2.054c2.538-2.025,3.994-5.052,3.994-8.301V82.427     C297,76.568,292.232,71.801,286.373,71.801z"></path>';
        coursesHtml += '                      <path d="M18.332,205.042V71.801h-7.706C4.768,71.801,0,76.568,0,82.427v168.897c0,3.25,1.456,6.276,3.994,8.301     c2.545,2.029,5.827,2.78,9.011,2.054c25.46-5.803,70.135-12.547,112.511-2.171c-24.502-23.19-59.1-27.292-80.083-27.342     C30.49,232.13,18.332,219.963,18.332,205.042z"></path></g></g></g><g></g><g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g>';
        coursesHtml += '                      </svg>';
        coursesHtml += '                    </div>';
        coursesHtml += '                    <div class="bundle-count">';
        coursesHtml += '                       <span>'+course['bundle_length']+'</span>';
        coursesHtml += '                       <span class="in">in</span>';
        coursesHtml += '                       <span>1</span>';
        coursesHtml += '                    </div>';
        coursesHtml += '                </div>';
        if(course['enrolled']){
            // course['percentage'] = Math.round(+course['percentage']);
            coursesHtml += '<a onclick="'+onclick+'" href="'+footer_url+'">';
            coursesHtml += '    <div class="courser-bottom-half">';
            // coursesHtml += '    <a onclick="'+onclick+'" href="'+footer_url+'">';
            coursesHtml += '        <label class="block-head">'+course_title+'</label>';
            // coursesHtml += '        <p class="sub-head-des-pre">'+by_tutor+'</p>';

            switch(+course['bs_approved']){
                case 0:
                    // coursesHtml += '        <div class="progress_main">';
                    // coursesHtml += '            <div class="progress">';
                    // coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
                    // coursesHtml += '            </div>';
                    // coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
                    // coursesHtml += '        </div>';
                    coursesHtml += '        <span class="course-status course-red">Suspended</span>';
                break;
                case 2:
                    // coursesHtml += '        <div class="progress_main">';
                    // coursesHtml += '            <div class="progress">';
                    // coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
                    // coursesHtml += '            </div>';
                    // coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
                    // coursesHtml += '        </div>';
                    coursesHtml += '        <span class="course-status course-red">Pending Approval</span>';
                break;
                default:
                    coursesHtml += '        <span class="course-status course-green subscribed-course">Already Subscribed</span>';
                    // coursesHtml += '        <div class="progress_main">';
                    // coursesHtml += '            <div class="progress">';
                    // coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
                    // coursesHtml += '            </div>';
                    // coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
                    // coursesHtml += '        </div>';
                    // switch(+course['cs_course_validity_status']){
                    //     case 0:
                    //         coursesHtml += '    <span class="course-status course-green">Lifetime Validity</span>';
                    //     break;
                    //     default:
                    //         if(!course['expired']){
                    //             switch(+course['expire_in_days']){
                    //                 case 0:
                    //                     coursesHtml += '    <span class="course-status course-green">Expire today</span>';
                    //                 break;
                    //                 case 1:
                    //                     coursesHtml += '    <span class="course-status course-green">Expire tomorrow</span>';
                    //                 break;
                    //                 default:
                    //                     coursesHtml += '    <span class="course-status course-green">Expires in '+course['expire_in_days']+' days</span>';
                    //                 break;
                    //             }
                    //         }else{
                    //             coursesHtml += '    <span class="course-status course-red">Expired on '+course['validity_format_date']+'</span>';
                    //         }
                    //     break;
                    // }
                break;
            }    
            coursesHtml += '</div>';
            coursesHtml += '</a>';
            
        }else{

        coursesHtml += '     <a onclick="'+onclick+'" href="'+footer_url+'">';
        coursesHtml += '        <div class="courser-bottom-half">';
        
        coursesHtml += '                <label class="block-head">'+course_title+'</label>';
        // coursesHtml += '                <p class="sub-head-des">'+by_tutor+'</p>';
        // coursesHtml += '                <div class="star-ratings-sprite star-ratings-sprite-block"><span style="'+course_rate+'" class="star-ratings-sprite-rating"></span></div>';
        coursesHtml += '                    <div class="card-pricing-row">'; //card-pricing-row
            if(course['c_is_free'] == '1'){

                coursesHtml += '                <div class="free-course">FREE</div>';
            }else{
 
                if(course['c_discount']>0){
                    var discount_persentage = (Math.round((1- (course['c_discount']/course['c_price']))*100));
                    coursesHtml += '              <div class="selling-price-column"><span class="rupee-unicode">&#8377;</span><span class="selling-price"> '+course['c_discount']+' </span></div> <div class="real-price-column"> <div class="real-price-info"><span class="rupee-unicode">&#8377;</span> <span class="real-price">'+course['c_price']+'</span></div>';
                    if(discount_persentage > 0 && (Math.round((1- (course['c_discount']/course['c_price']))*100))){
                        coursesHtml += ' <div class="offer-tag">'+(Math.round((1- (course['c_discount']/course['c_price']))*100))+'% OFF</div>';
                    }
                    coursesHtml += ' </div>';
                }else{
                    coursesHtml += '              <div class="selling-price-column"><span class="rupee-unicode">&#8377;</span> <span class="selling-price">'+course['c_price']+' </span> </div>';
                }
                
            }
            coursesHtml += '                </div>'; //card-pricing-row ends
            coursesHtml += '        </div>';            
            coursesHtml += '   </a>';
        }
        coursesHtml += '            </div>';
        coursesHtml += '    </div>';

    }else{
        if(course['enrolled']){
            console.log(course['cs_approved'],'cs_approved');
            switch(+course['cs_approved']){
                case 1:

                    header_url = __site_url+'materials/course/'+course['id'];
                    footer_url = __site_url+'course/dashboard/'+course['id'];

                    if(course['cb_status'] == '0'){
                        header_url = __site_url+course_slug;
                        footer_url = __site_url+course_slug;
                    }

                    if(typeof course['display_inside_bundle'] != 'undefined' && course['display_inside_bundle'] == true) {
                        header_url = __site_url+'materials/course/'+course['id'];
                        footer_url = __site_url+'materials/course/'+course['id'];
    
                        }
                
                
                break;
                case 2:
                header_url = 'javascript:void(0)';
                footer_url = 'javascript:void(0)';
                onclick = 'showCommonModal(\'\',\'Subsciption is waiting for approval by admin.\',\'\')';
                break;
                default:
                header_url = 'javascript:void(0)';
                footer_url = 'javascript:void(0)';
                onclick = 'showCommonModal(\'\',\'Your subsciption is suspended by admin.\',\'\')';
                break;
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

            switch(+course['cs_approved']){
                case 0:
                coursesHtml += '        <div class="progress_main">';
                coursesHtml += '            <div class="progress">';
                coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
                coursesHtml += '            </div>';
                coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
                coursesHtml += '        </div>';
                coursesHtml += '        <span class="course-status course-red">Suspended</span>';
                break;
                case 2:
                coursesHtml += '        <div class="progress_main">';
                coursesHtml += '            <div class="progress">';
                coursesHtml += '                <div class="progress-bar" role="progressbar" aria-valuenow="'+course['percentage']+'" aria-valuemin="0" aria-valuemax="100" style="width:'+course['percentage']+'%"></div>';
                coursesHtml += '            </div>';
                coursesHtml += '            <span class="sr-only">'+course['percentage']+'% Complete</span>';
                coursesHtml += '        </div>';
                coursesHtml += '        <span class="course-status course-red">Pending Approval</span>';
                break;
                default:
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
                        if(!course['expired']){
                            switch(+course['expire_in_days']){
                                case 0:
                                    coursesHtml += '   <span class="course-status course-green">Expires today</span>';
                                break;
                                case 1:
                                    coursesHtml += '    <span class="course-status course-green">Expires tomorrow</span>';
                                break;
                                default:
                                    coursesHtml += '    <span class="course-status course-green">Expires in '+(parseInt(course['expire_in_days'])+1)+' days</span>';
                                break;
                            }
                        }else{
                            coursesHtml += '    <span class="course-status course-red">Expired on '+course['validity_format_date']+'</span>';
                        }
                    break;
                }
                break;
            }    
            coursesHtml += '    </a>';
            coursesHtml += '</div>';

        }else{
            coursesHtml += '   <a onclick="'+onclick+'" href="'+footer_url+'">';
            coursesHtml += '        <div class="courser-bottom-half">';
            coursesHtml += '                <label class="block-head">'+course_title+'</label>';
            coursesHtml += '                <p class="sub-head-des">'+by_tutor+'</p>';
            coursesHtml += '                <div class="star-ratings-sprite star-ratings-sprite-block"><span style="'+course_rate+'" class="star-ratings-sprite-rating"></span></div>';
            coursesHtml += '                    <div class="card-pricing-row">'; //card-pricing-row
            if(course['cb_is_free'] == '1'){

                coursesHtml += '                    <div class="free-course">FREE</div>';
            }else{

                if(course['cb_discount']>0){
                    var discount_persentage = (Math.round((1- (course['cb_discount']/course['cb_price']))*100));
                    coursesHtml += '              <div class="selling-price-column"><span class="rupee-unicode">&#8377;</span><span class="selling-price"> '+course['cb_discount']+' </span></div> <div class="real-price-column"> <div class="real-price-info"><span class="rupee-unicode">&#8377;</span> <span class="real-price">'+course['cb_price']+'</span></div>';
                    if((discount_persentage > 0) && (Math.round((1- (course['cb_discount']/course['cb_price']))*100) > 0)){
                        coursesHtml += ' <div class="offer-tag">'+(Math.round((1- (course['cb_discount']/course['cb_price']))*100))+'% OFF</div>';
                    }
                    coursesHtml += ' </div>';
                } 
                else
                {
                    coursesHtml += '                 <div class="selling-price-column"><span class="rupee-unicode">&#8377;</span> <span class="selling-price">'+course['cb_price']+' </span> </div>';
                }
                
            }
            coursesHtml += '                    </div>';//card-pricing-row
            
            
            coursesHtml += '        </div>';
            coursesHtml += '  </a>';
        }
        coursesHtml += '            </div>';
        coursesHtml += '    </div>';
    }
    
    return coursesHtml;
}

function stringToDate(s) {
    var dateParts = s.split(' ')[0].split('-'); 
    var timeParts = s.split(' ')[1].split(':');
    var d = new Date(dateParts[0], --dateParts[1], dateParts[2]);
    d.setHours(timeParts[0], timeParts[1], timeParts[2])
    return d;
}