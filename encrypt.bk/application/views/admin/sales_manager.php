<?php include_once "header.php"; ?>
<script src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<style type="text/css" media="screen">
 .rTableCell label.manage-stud-list{
     display: flex !important;
     } 
 .batch-carrot{
     top: 20px !important;
     }  
 /*added css*/
 #user_bulk{
     width:96px !important;
     }
 .tooltip-inner {
     max-width: unset !important;
     }
 .d-none { 
     display:none;
     }
 .unselectable {
    pointer-events: none;
  }
 /* .suggestion > ul {width:100%;} */
</style>

<section class="content-wrap base-cont-top" style="padding-right:0px;">
            <!-- Nav section inside this wrap  --> <!-- START -->
            <!-- =========================== -->
            <div class="container-fluid nav-content nav-course-content">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow flex-space" style="justify-content: flex-end;">

                            <div class="rTableCell" style="width: 60%;border-left: 1px solid #ccc;">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="search_keyword" placeholder="Search">
                                    <span id="searchclear" style="display: none;">×</span>
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div>
                                <div class='input-group suggestion d-none'>
                                    <ul id='suggestion-list'>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- =========================== -->
            <!-- Nav section inside this wrap  --> <!-- END -->
            <!-- LEFT CONTENT --> <!-- STARTS -->
            <!-- ===========================  -->
            <div class="left-wrap col-sm-12 pad0">



                <!-- Sales management starts here -->
                <div class="sales-manage-wrapper">
                    <div class="row">
                        <div class="col-sm-12 course-cont-wrap" id="show_message_div">
                        </div>
                    </div>
                    <ul class="sales-manage-list list-group" id='sortables'>
                       
                    </ul>
                </div>
                <!-- Sales management ends -->



            </div>
            <!-- ==========================  -->
            <!--  LEFT CONTENT--> <!-- ENDS -->
        </section>




<script>
    const __adminUrl    = '<?php echo admin_url() ?>';
    let _items          = atob('<?php echo base64_encode(json_encode($items)); ?>');
    _items              = $.parseJSON(_items);
    let activeItemId    = '';
    let __popularStatusInProgress = false;
    let __featureStatusInProgress = false;

    $(document).ready(function(){
        $('#search_keyword').keyup(function(){
            let keyword      = ($(this).val()).trim().toLowerCase();
            let searchResult = [];
            $('#suggestion-list').html('');
            if(keyword.length > 0){
                $('div.suggestion').removeClass('d-none');
                $('#suggestion-list').removeClass('d-none');
                $.each(_items, function(key, item){
                    if((item['iso_item_name'].toLowerCase()).search(keyword) != -1){
                        searchResult.push(item['id']);
                        $('#suggestion-list').append(`<li id='l-${item['id']}'>${item['iso_item_name']}</li>`);
                    }
                });
            } else {
                $('div.suggestion').addClass('d-none');
            }
        });
    });

    $('#searchclear').click(function(){
        $('#suggestion-list').addClass('d-none');
    });

    $(document).on("click", function(e) {
        if ($(e.target).is("#suggestion-list li") === false) {
            $("#suggestion-list").addClass("d-none");
        }
    });

    $('#suggestion-list').on('click','li', function(e){
        let suggestionId             = $(this).prop('id');
        if(suggestionId.length > 0 && suggestionId.includes("-")) {
            let suggestionIdSplitted = suggestionId.split("-");
            let courseName           = $(this).text();
            let itemId               = suggestionIdSplitted[1];
            activeItemId             = itemId;
            let id                   = '#item_'+itemId;
            $('#sortables > li').removeClass('active');
            $(id).addClass('active');
            $('#search_keyword').val(courseName);
            $('html,body').animate({
                scrollTop: $(id).offset().top-300
            }, 2000);
        }
        $('div.suggestion').addClass('d-none');
    });
        
    $(document).ready(function(){
        $('#sortables').html(renderItems(_items));
        $('#sortables').sortable({
            connectWith: "#sortables",
            scroll: true,
            axis  :'y',
            update:function(event,ui){
                var itemList = $(this).sortable(
                        "serialize", {
                        attribute: "id",
                        key: 'item_id[]'
                    });
                $.ajax({
                    url    : __adminUrl+'sales_manager/update_item_position',
                    type   : "POST",
                    data   :{"is_ajax":true, 'item_position':itemList},
                    success: function(response) {
                        //console.log(response);
                        var data = $.parseJSON(response);
                    }
                });
            }
        });
        $("#sortables").disableSelection();
    });
    let renderItems = function(items) {
        let totalItems                    = items.length;
        let itemHtml                      = '';
        let classNames                    = '';
        let discount                      = 0;
        let discountPercentage            = 0;
        let checkedPopular                = '';
        let checkedFeatured               = '';
        let ratingPercentage              = 0;
        if(Object.keys(items).length > 0 ) {
            $.each(items, function(itemKey, item){
                
                ratingPercentage          = 0;
                discount                  = 0;
                discountPercentage        = 0;
                classNames                = '';
                ratingPercentage          = (item['iso_item_rating'] / 5 ) * 100;
                if(item['iso_item_type'] == 'bundle'){
                    classNames            = "bundle";
                }
                if(activeItemId == item['id']){
                    classNames           += ' active';
                }
                if(item['iso_item_popular'] == 1) {
                    checkedPopular        = 'checked="checked"';
                } else {
                    checkedPopular        = '';
                }
                if(item['iso_item_featured'] == 1) {
                    checkedFeatured       = 'checked="checked"';
                } else {
                    checkedFeatured       = '';
                }
                if(item['iso_item_price'] != 0){
                    discountPercentage    = Math.ceil(( (item['iso_item_price'] - item['iso_item_discount_price']) / item['iso_item_price'] ) * 100 );
                }
                 
                
                itemHtml += `<li class="d-flex align-center justify-between ${classNames}" id="item_${item['id']}">`;
                itemHtml += `<div class="course-title d-flex">`;
                itemHtml += `<div class="course-title-icon bundle"></div>`;
                itemHtml += `<div class="course-name">${item["iso_item_name"]}</div>`;
                itemHtml += `</div>`;
                itemHtml += `<div class="course-rating d-flex align-center">`;
                itemHtml += `<div class="star-ratings-sprite star-ratings-sprite-block">`;
                itemHtml += `<span style="width:${ratingPercentage}%" class="star-ratings-sprite-rating"></span>`;
                itemHtml += `</div>`;
                itemHtml += `</div>`;
                itemHtml += `<div class="course-type-column"><div class="course-type">Combo</div></div>`;
                itemHtml += `<div class="course-pricing-column d-flex align-center">`;
                itemHtml += `<div class="selling-price">`;
                    if(item['iso_item_is_free'] == 1){
                        itemHtml += `Free</div>`;
                    }else if(item['iso_item_discount_price'] == 0){
                        itemHtml += `<span class="rupee-unicode">&#8377;</span>${item["iso_item_price"]}</div>`;
                    } else {
                        itemHtml += `<span class="rupee-unicode">&#8377;</span>${item["iso_item_discount_price"]}</div>`;
                        itemHtml += `<div class="mrp">`;
                        itemHtml += `<span class="rupee-unicode">&#8377;</span>`;
                        itemHtml += `<span class="mrp-amount">${item["iso_item_price"]}</span>`;
                        itemHtml += `</div>`;
                        itemHtml += `<div class="discount-tag-column"><div class="discount-tag">${discountPercentage}% OFF</div></div>`;
                    }
                itemHtml += `</div>`;
                itemHtml += `<div class="popularity-holder"><label class="popularity custom-checker">`;
                itemHtml += `<span class="checkbox-title">Popular</span>`;
                itemHtml += `<input type="checkbox" class='popularCheckbox' onchange='changePopularStatus(this)' id='popular-${item['id']}' ${checkedPopular}>`;
                itemHtml += `<span class="checkmark"></span>`;
                itemHtml += `</label></div>`;
                itemHtml += `<div class="featured-holder"><label class="featured custom-checker">`;
                itemHtml += `<span class="checkbox-title">Featured</span>`;
                itemHtml += `<input type="checkbox" class='featuredCheckbox' onchange='changeFeaturedStatus(this)' id='featured-${item['id']}' ${checkedFeatured}>`;
                itemHtml += `<span class="checkmark"></span>`;
                itemHtml += `</label></div>`;
                itemHtml += `<div class="arrange">`;
                itemHtml += `<span class="up" aria-hidden="true" onclick="upArrow(${item["id"]}, ${itemKey})"></span>`;
                itemHtml += `<span class="down" aria-hidden="true" onclick="downArrow(${item['id']}, ${itemKey},${totalItems})"></span>`;
                itemHtml += `</div>`;
                itemHtml += `<div class="drag">`;
                itemHtml += `<span class="drag-icon">......</span>`;
                itemHtml += `</div>`;
                itemHtml += `</li>`;

            });
        } else {
            itemHtml += '<div class="alert alert-danger unselectable">No Report Found</div>';
        }
        
        return itemHtml;

    }
    
    let downArrow = function(itemId, position, totalItems){
        if(position < (totalItems - 1) ) {
            let targetPosition       = position + 1;
            let currentId            = '#item_'+itemId;
            let targetId             = $(currentId).next().attr('id');
            if((targetId != undefined) && (targetId.length > 0) && (targetId.includes("_"))){
                let targetIdSplitted = targetId.split("_");
                let targetItemId     = targetIdSplitted[1];
                $.ajax({
                    url  : __adminUrl+"sales_manager/update_item_position_swap",
                    type : 'post',
                    data : {'itemId': itemId, 'itemPosition' : targetPosition, 'targetItemId' : targetItemId, 'targetItemPosition' : position },
                    success : function(response) {
                        let data     = $.parseJSON(response);
                        if(data['error'] != true) {
                            $('#sortables').html('');
                            $('#sortables').html(renderItems(data['items']));
                        }           
                    }
                });
            } 
        } 
    }

    let upArrow = function(itemId, position){
        if(position != 0) {
            let targetPosition       = position - 1;
            let currentId            = '#item_'+itemId;
            let targetId             = $(currentId).prev().attr('id');
            if(targetId != undefined && targetId.length >0 && targetId.includes("_")) {
                let targetIdSplitted = targetId.split("_");
                let targetItemId     = targetIdSplitted[1];
                $.ajax({
                    url     : __adminUrl+"sales_manager/update_item_position_swap",
                    type    : 'post',
                    data    : {'itemId': itemId, 'itemPosition' : targetPosition, 'targetItemId' : targetItemId, 'targetItemPosition' : position },
                    success : function(response) {
                        let data     = $.parseJSON(response);
                        //console.log(data);
                        if(data['error'] != true){
                            $('#sortables').html('');
                            $('#sortables').html(renderItems(data['items']));
                        }
                    }
                });
            }
        } 
    }

    
    let changePopularStatus = function(el) {
        let status                     = 2; //false flag;
        if($(el).prop("checked") == true){
            status                     = 1; // checked
        }
        else if($(el).prop("checked") == false){
            status                     = 0; //unchecked
        }
        if(__popularStatusInProgress == true){
            return false;
        }
        __popularStatusInProgress = true;
        let maxPopular                = $('.popularCheckbox:checked').length;
        if(maxPopular > 5){
            $(el).prop('checked',false);
            let msgObj = {
                'body': 'Popular Item limit exceeded. Please unselect popular items and try again',
                'button_yes':'OK',
                'prevent_button_no': false
            };
            callback_danger_modal(msgObj);
            __popularStatusInProgress = false;
            return false;
        }
        let popularId                  = $(el).prop('id');
        if(popularId != undefined && popularId.length > 0 && popularId.includes("-")) {
            let popularIdSplitted      = popularId.split("-");
            let itemId                 = popularIdSplitted[1];
            if(status !== 2){
                $.ajax({
                    url     : __adminUrl+"sales_manager/update_item_popular_status",
                    type    : 'post',
                    data    : {'popularStatus' : status, 'itemId' : itemId },
                    success : function(response){
                        let data       = JSON.parse(response);
                        if(data['error'] == true){
                            $(el).prop('checked',false);
                            let msgObj = {
                                'body': data['message'],
                                'button_yes':'OK',
                                'prevent_button_no': false
                            };
                            callback_danger_modal(msgObj);
                        }
                        __popularStatusInProgress = false;
                    }
                });
            }
        }
    }
    
    let changeFeaturedStatus = function(el) {
        let status                     = 2; //false flag
        if($(el).prop("checked") == true){
            status                     = 1; //checked
        }
        else if($(el).prop("checked") == false){
            status                     = 0; //unchecked
        }
        if(__featureStatusInProgress == true)
        {
            return false;
        }
        __featureStatusInProgress = true;
        let maxFeatured                = $('.featuredCheckbox:checked').length;
        if(maxFeatured > 5){
            $(el).prop('checked',false);
            let msgObj = {
                'body': 'Featured Item limit exceeded. Please unselect featured items and try again',
                'button_yes':'OK',
                'prevent_button_no': false
            };
            callback_danger_modal(msgObj);
            __featureStatusInProgress = false;
            return false;
        }
        let featuredId                 = $(el).prop('id');
        if(featuredId != undefined && featuredId.length > 0 && featuredId.includes("-")) {
            let featuredIdSplitted     = featuredId.split("-");
            let itemId                 = featuredIdSplitted[1];
            if(status !== 2) {
                $.ajax({
                    url     : __adminUrl+"sales_manager/update_item_featured_status",
                    type    : 'post',
                    data    : {'featuredStatus' : status, 'itemId' : itemId },
                    success : function(response){
                        let data       = JSON.parse(response);
                        if(data['error']==true){
                            $(el).prop('checked',false);
                            let msgObj = {
                                'body': data['message'],
                                'button_yes':'OK',
                                'prevent_button_no': false
                            };
                            callback_danger_modal(msgObj);
                        }
                        __featureStatusInProgress = false;
                    }
                });
            }
        } else {
            console.log('error');
        }
    }
</script>

<?php include_once 'footer.php';?>