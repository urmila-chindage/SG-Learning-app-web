
$(document).ready(function () {
    __reviews       = $.parseJSON(__reviews);
    if(Object.keys(__reviews.reviews).length > 0) {
        renderReviews(__reviews.reviews);
    }
    loadCurriculum();
});

function renderReviews(reviews){
    $('#review_list').html(
        `${reviews.map(rv => {
            return `<li class="profilelist-childs">
                        <div class="profile-list-photo">
                            <img src="${rv.cc_user_image === 'default.jpg'?__user_path.default:__user_path.native+rv.cc_user_image}" class="olp-img-rounded img-responsive svg-common profile-pic">
                            <span class="profile-name-text">${rv.cc_user_name}</span>
                            <div class="star-ratings-sprite star-rating-vertical-top-super starr-vertical-top">
                                <span style="width:${rv.cc_rating*20}%" class="star-ratings-sprite-rating"></span>
                            </div>
                            <span class="sub-profile-text">${relative_time_ax(rv.created_date).day}</span>
                        </div><!--profile-list-photo-->
                        <p class="profil-des">
                            ${rv.rv_reviews}
                        </p>
                    </li>`;
        })
        +(+__reviews.count <= __reviews.reviews.length?'':`<a href="javascript:void(0)" onclick="getReviews(${Math.ceil(__reviews.reviews.length/__reviews.limit)+1})" id="show_more_reviews">Show more reviews</a>`)}`
    );
}

function getReviews(offset)
{
    $.ajax({
        url: __site_url+'course/load_reviews/'+__course_id+'/'+offset,
        type: "GET",
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['success'] === true)
            {
                __reviews.reviews = __reviews.reviews.concat(data['reviews']);
                renderReviews(__reviews.reviews);
            }
        }
    });
}

function getIcon(params){
    let className = 'grey';
    let cIcon = {
        1: `<span class="milestone-icon video-${className}"></span>`,
        10:`<span class="milestone-icon drop-${className}"></span>`,
        11: `<span class="milestone-icon video-${className}"></span>`,
        12: `<span class="milestone-icon audio-${className}"></span>`,
        4: `<span class="milestone-icon video-${className}"></span>`,
        6: `<span class="milestone-icon code-${className}"></span>`,
        5: `<span class="milestone-icon code-${className}"></span>`,
        2: `<span class="milestone-icon document-${className}"></span>`,
        7: `<span class="milestone-icon video-${className}"></span>`,
        3: `<span class="milestone-icon quiz-${className}"></span>`,
        9: `<span class="milestone-icon video-${className}"></span>`,
        14: `<span class="milestone-icon certificate-${className}"></span>`,
        8: `<span class="milestone-icon assignment-${className}"></span>`
    }
    var tailHtml = '';
    
    if(!params.first && !params.last){
        tailHtml = '<div class="tail-up"></div><div class="tail-down"></div>';
    }else{
        if(params.last && !params.first){
            tailHtml = '<div class="tail-up"></div>';
        }

        if(params.first && !params.last){
            tailHtml = '<div class="tail-down"></div>';
        }
    }

    return `<div class="milestone-holder">
                <span class="milestone">
                    ${cIcon[+params.type]?cIcon[+params.type]:''}
                </span>
                ${tailHtml}
            </div>`;
}
function getCgst(itemPrice){
    var cgst = (parseFloat(__course.cb_cgst)/100 * parseFloat(itemPrice)).toFixed(2);
    return cgst;
}

function getSgst(itemPrice){
    var sgst = (parseFloat(__course.cb_sgst)/100 * parseFloat(itemPrice)).toFixed(2);
    return sgst;
}
function loadCurriculum() {
    __curriculum.sections = $.parseJSON(__curriculum.sections);
    __curriculum.lectures = $.parseJSON(__curriculum.lectures);

    let __curr;
    __curr = __curriculum.sections.map(sec => {
        return {
            id: sec.id,
            name: sec.s_name,
            lectures: []
        };
    });
    
    __curr = __curr.map(cur => {
        __curriculum.lectures.map(lec => {
            if (cur.id == lec.cl_section_id && +lec.cl_lecture_type !== 13) {
                cur.lectures.push({
                    id: lec.id,
                    name: lec.cl_lecture_name,
                    cl_limited_access : lec.cl_limited_access,
                    unique: lec.unique,
                    cl_total_page: lec.cl_total_page,
                    type: lec.cl_lecture_type
                });
            }
        });
        return cur;
    });
    let __sechtml = '<div class="curriculam-container">';
    __sechtml += __curr.map((sec, sIndex) => {
        if (sec.lectures.length != 0) {
            return `<div class="curriculam-title"><h4>${sec.name}</h4></div>`+
            sec.lectures.map((lec, lIndex) => {
                    let calc = '';
                    switch (+lec.type) {
                        // case 1: calc = lec.unique; break;
                        // case 12: calc = lec.unique; break;
                        // case 2: calc = lec.cl_total_page > 1 ? lec.cl_total_page + ' Pages' : lec.cl_total_page + ' Page'; break;
                        case 7: calc = lec.unique; break;
                        case 3: calc = lec.unique > 1 ? lec.unique + ' Questions' : lec.unique + ' Question'; break;
                        // case 8: calc = lec.unique > 1 ? lec.unique + ' Pages' : lec.unique + ' Page'; break;
                        default: calc = ''; break;
                    }
                    return `<div class="timeline-row">
                                <a href="javascript:void(0)">
                                    ${getIcon({
                                        type : [+lec.type],
                                        first:lIndex==0,
                                        last:(lIndex == sec.lectures.length-1)
                                    })}
                                    <div class="timeline-text text-justify">
                                        <p class="curriculam-info-text col-md-8 col-sm-10 col-xs-9 info-bold">${lec.name}</p>
                                            <p class="curriculam-info-view text-right green-text col-md-2 col-sm-2 col-xs-3 ${+lec.cl_limited_access == 0?'visible-hidden':''}"><span class="green-eye"></span>${+lec.cl_limited_access == 0?'-':lec.cl_limited_access}</p>
                                        <p class="curriculam-info-duration text-right col-md-2">${calc}</p>
                                    </div>
                                </a>
                            </div>`;
                }).join("")
        }
    }).join("");
    __sechtml += '</div>'
    $('#curriculum_div').html('<h3 class="formpage-heading dashboard-curriculam-title">Curriculum</h3>' + __sechtml);
}

function relative_time_ax(date_str){
    var date_time = new Object();
    var d = new Date(date_str);
    var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    var date = d.getDate() + " " + month[d.getMonth()] + " " + d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase();
    date_time.day = date;
    date_time.time = time;
    return date_time;

};
// site_url('/checkout/standard/'.$course['id'].'/'.base64_encode('course'))
function enrollToCourse(){
    resetCoupon();
}

function taxCalculation(){
    
    var click_function            = '';
    console.log(__course);
    if(+__course.cb_is_free == 1)
    {
        
        link            = __site_url+'checkout/standard/'+__course.course_id+'/1';
        location.href   = link;
        // click_function     = 'applyPromo('+__course.course_id+')';

    }
    else
    {
        //link  = __site_url+'checkout/payment_request/'+__course.course_id+'/1';
        click_function     = 'applyPromo('+__course.course_id+')';
        var tax_table ='';
            tax_table +='<div id="tax-table" class="form-group table-holder"><div id="promo-msg"></div>';
            tax_table +='<table class="billing-table" style="width:100%;border:0px">';
            tax_table +='<tbody><tr>';
            tax_table +='<td class="text-left">Course Price</td>';
            tax_table +='<td class="text-right text-blue">&#8377; <span>'+__course.cb_price+'</span></td>';
            tax_table +='</tr>';
            tax_table +='<tr class="promocode-preview" id="promocode_offer" style="display:none;">';
            tax_table +='    <td class="text-left"><span class="promocode"><span id="promocode_text">GET50</span><img src="'+__theme_url+'/images/scisor.svg"></span><a href="#" onClick="resetCoupon();">Remove</a></td>';
            tax_table +='    <td class="text-right text-blue">&#8377; <span class="minus">(-)</span> <span id="promocode_reduction"></span></td>';
            tax_table +='</tr>';
            tax_table +='<tr id="new-price" class="border-row" style="display:none;">';
            tax_table +='<td class="text-left">Price After Discount</td>';
            tax_table +='<td class="text-right text-blue">&#8377; <span id="new-course-price"></span></td>';
            tax_table +='</tr>';
            if(__course.cb_tax_method=='1')
            {
                tax_table +='<tr>';
                tax_table +='    <td class="text-left">CGST('+__course.cb_cgst+'%)</td>';
                tax_table +='    <td class="text-right text-blue">&#8377; <span class="plus">(+)</span> <span id="cgst_price">'+__course.cb_cgst_price+' </span></td>';
                tax_table +='</tr>';
            
                tax_table +='<tr>';
                tax_table +='    <td class="text-left">SGST('+__course.cb_sgst+'%)</td>';
                tax_table +='    <td class="text-right text-blue">&#8377; <span class="plus">(+)</span> <span id="sgst_price">'+__course.cb_sgst_price+' </span></td>';
                tax_table +='</tr>';
            }
            tax_table +='<tr class="total-column">';
            tax_table +='    <td class="text-left"><b>Total</b></td>';
            tax_table +='    <td class="text-right text-blue"><b>&#8377; <span id="net_total">'+__course.cb_total_price+'</span></b></td>';
            tax_table +='</tr>';
            tax_table +='</tbody></table>';
            tax_table +='</div>';
            tax_table +='<div  class="form-group promo-column">';
            tax_table +='<input type="text" class="form-control" style="width:80%;" maxlength="10" id="promo_code" name="promo_code" placeholder="Apply Promo Code">';
            tax_table +='<button id="promo_code_btn" onclick="applyCoupon()" class="custom-btn">Apply</button>';
            tax_table +='</div>';
            showEnrollModal('Do you want to continue?',`${tax_table}`,5,`${click_function}`);  
    }
   
}
function applyPromo() {

    var promo_code = $("#promo_code").val();
    var link            = '';
    if(+__course.cb_is_free == 1)
    {
        link            = __site_url+'checkout/standard/'+__course.course_id+'/1';
    } else {
        link  = __site_url+'checkout/payment_request/'+__course.course_id+'/1';
    }
    if(promo_code!="") {
        msg                          = '<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Please apply coupon code.</div>';  
        $("#promo-msg").html(msg);
    } else {
        $("#promo-msg").html('');
        location.href   = link;
    }
}
function resetCoupon() {
    $.ajax({
        url: __site_url+'checkout/reset_coupon',
        type: "POST",
        data:{"is_ajax":true},
        success: function(response) {
            var data             = $.parseJSON(response);
            if(data['error'] === false){
                taxCalculation();
            } 
        }
    });
}
function applyCoupon()
{
    var promo_code = $("#promo_code").val();
    if(promo_code == ''){
        var msg     = '<div class="alert alert-error alert-danger" id="alert_danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Promocode should not be empty.</div>';  
        $("#promo-msg").html(msg);
        return false;
    } else {
        $.ajax({
            url: __site_url+'checkout/promocode_usage',
            type: "POST",
            data:{"is_ajax":true,'promo_code':promo_code},
            success: function(response) {
                var data             = $.parseJSON(response);
                var msg              = '';
                var discount_rate    = '';
                if(data['header']['success'] === true){
                    msg                          = '<div class="alert alert-success"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Discount Coupon is applied</div>';  
                    $("#promo-msg").html(msg);
                    var discout_type             = data['body']['promocode']['pc_discount_type'];
                    if(discout_type=='1') {
                        discount_rate            = (data['body']['promocode']['pc_discount_rate']!=undefined)?data['body']['promocode']['pc_discount_rate']:'0';
                    } else {
                        var discount_percentage  = (data['body']['promocode']['pc_discount_rate']!=undefined)?data['body']['promocode']['pc_discount_rate']:'0';
                        discount_rate            = ((discount_percentage/100) * __course.cb_price).toFixed(2);
                    }
                    var course_price             = (parseFloat(discount_rate)<parseFloat(__course.cb_price))?(parseFloat(__course.cb_price)-parseFloat(discount_rate)):'0';
                    var discount_amount          = (parseFloat(discount_rate)<parseFloat(__course.cb_price))?parseFloat(discount_rate):parseFloat(__course.cb_price);
                    var course_net_price         = 0;
                    if(__course.cb_tax_method=='1')
                    {
                        var sgst_amount          = getSgst(course_price);
                        var cgst_amount          = getCgst(course_price);
                        course_net_price         = parseFloat(course_price)+parseFloat(sgst_amount)+parseFloat(cgst_amount);
                        $("#cgst_price").html(cgst_amount);
                        $("#sgst_price").html(sgst_amount);
                        $('#new-course-price').html(course_price);
                    }
                    else 
                    {
                      course_net_price          = course_price;
                    }
                    course_net_price = (course_net_price>1)?course_net_price:0;
                    
                    $('.promo-column').hide();
                    $("#promocode_text").html(promo_code);
                    $("#promocode_reduction").html(discount_amount);
                    $("#net_total").html(course_net_price.toFixed(2));
                    $("#promocode_offer").show();
                    $("#new-price").show();
                    $("#promo_code").val('');
                } else {
                    msg     = '<div class="alert alert-error alert-danger" id="alert_danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>'+data['header']['message']+'</div>';  
                    $("#promo-msg").html(msg);
                }
            }
        });
    }
}


function showEnrollModal(heading = '',message = '',type = 0,onclick = null){
    var svg = '';
    $('#enroll_modal_continue').hide();
    $('#enroll_modal_continue').attr('onclick','javascript:void(0)');
    $('#enroll_modal_cancel').addClass('btnorange');
    // $('#enroll_modal_title').removeClass('green-text').removeClass('red-text').removeClass('orange-text');
    switch(type){
        case 1:  //Success
            if(heading == ''){
                heading = 'Success';
            }
            svg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#00aa47" xml:space="preserve"><g><g><path d="M85.263,46.49L54.485,77.267L42.804,65.584c-0.781-0.782-2.047-0.782-2.828-0.002c-0.781,0.782-0.781,2.048,0,2.829    l14.51,14.513l33.605-33.607c0.781-0.779,0.781-2.046,0-2.827C87.31,45.708,86.044,45.708,85.263,46.49z M64.032,13.871    c-27.642,0-50.129,22.488-50.129,50.126c0.002,27.642,22.49,50.131,50.131,50.131h0.004c27.638,0,50.123-22.489,50.123-50.131    C114.161,36.358,91.674,13.871,64.032,13.871z M64.038,110.128h-0.004c-25.435,0-46.129-20.694-46.131-46.131    c0-25.434,20.693-46.126,46.129-46.126s46.129,20.693,46.129,46.126C110.161,89.434,89.471,110.128,64.038,110.128z"/></g></g></svg>`;
            $('#enroll_modal_title').addClass('green-text');
        break;

        case 2:  //Error
            if(heading == ''){
                heading = 'Error';
            }
            svg = `<svg enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#f44" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M84.815,43.399c-0.781-0.782-2.047-0.782-2.828,0L64.032,61.356L46.077,43.399c-0.781-0.782-2.047-0.782-2.828,0    c-0.781,0.781-0.781,2.047,0,2.828l17.955,17.957L43.249,82.141c-0.781,0.78-0.781,2.047,0,2.828    c0.391,0.39,0.902,0.585,1.414,0.585s1.023-0.195,1.414-0.585l17.955-17.956l17.955,17.956c0.391,0.39,0.902,0.585,1.414,0.585    s1.023-0.195,1.414-0.585c0.781-0.781,0.781-2.048,0-2.828L66.86,64.184l17.955-17.957C85.597,45.447,85.597,44.18,84.815,43.399z     M64.032,14.054c-27.642,0-50.129,22.487-50.129,50.127c0.002,27.643,22.491,50.131,50.133,50.131    c27.639,0,50.125-22.489,50.125-50.131C114.161,36.541,91.674,14.054,64.032,14.054z M64.036,110.313h-0.002    c-25.435,0-46.129-20.695-46.131-46.131c0-25.435,20.693-46.127,46.129-46.127s46.129,20.693,46.129,46.127    C110.161,89.617,89.47,110.313,64.036,110.313z"/></g></g></svg>`;
            $('#enroll_modal_cancel').removeClass('btnorange');
            $('#enroll_modal_cancel').html('Ok');
            $('#enroll_modal_title').addClass('btnorange');
        break;

        case 5:  //Error
            $('#enroll_modal_cancel').removeClass('btnorange');
            $('#enroll_modal_cancel').html('Ok');
            $('#enroll_modal_title').addClass('btnorange');
            $('#enroll_modal_cancel').addClass('btnorange');
            $('#enroll_modal_img').hide();
        break;

        default : //Info
            if(heading == ''){
                heading = 'Warning';
            }
            svg = `<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            width="60px" height="60px" fill="#f78700" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                    <circle fill="none" stroke="#f78700" stroke-width="5" stroke-miterlimit="10" cx="64" cy="64" r="47.304"/>
                    <path d="M67.375,80.041c0,1.496-1.287,2.709-2.875,2.709l0,0c-1.588,0-2.875-1.213-2.875-2.709V37.876
                        c0-1.496,1.287-2.709,2.875-2.709l0,0c1.588,0,2.875,1.213,2.875,2.709V80.041z"/>
                    <path d="M67.542,91.382c0,1.681-1.362,3.042-3.042,3.042l0,0c-1.68,0-3.042-1.361-3.042-3.042v-0.264
                        c0-1.681,1.362-3.042,3.042-3.042l0,0c1.68,0,3.042,1.361,3.042,3.042V91.382z"/>
                    </svg>`;
            $('#enroll_modal_title').addClass('orange-text');
        break;
    }
    //$('#enroll_modal_cancel').html('Close');
    if(onclick != null && onclick != 'null'){
        $('#enroll_modal_continue').attr('onclick',onclick);
        $('#enroll_modal_continue').show();
        $('#enroll_modal_cancel').html('Cancel');
    }
    $('#enroll_modal_img').html(svg);
    //$('#enroll_modal_title').html(heading);
    $('#enroll_modal_title').hide();
    $('#enroll_modal_content').html(message);
    $('#enroll_modal').modal('show');
}