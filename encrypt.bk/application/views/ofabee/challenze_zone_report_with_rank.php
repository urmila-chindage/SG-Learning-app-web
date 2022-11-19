<?php include_once 'header.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/chrome-css.css">
<section>
    <div class="fundamentals">
        <div class="container">
            <div class="container-reduce-width">
                <span class="funda-date" id="attempt_date"><?php echo $my_attempt_date ?></span>
                <h2 class="funda-head"><?php echo $challenge_zone_report['cz_title'] ?></h2>
                <ul class="funda-rank clearfix">
                    <li class="funda-rank-list"><span class="funda-rank-orange span-mam">Your rank<span class="number-orange-bold" id="attempt_rank"><?php echo (($my_rank)?$my_rank:'0') ?></span></span></li>
                    <li class="funda-rank-list"><span class="funda-rank-dark span-mam">Mark scored <span class="mark-funda-number" id="attempt_mark"><?php echo (($my_mark)?$my_mark:'0') ?></span></span></li>
                    <li class="funda-rank-list"><span class="funda-rank-dark span-mam">Students attended<span class="mark-funda-number" id="attempt_total"><?php echo (($total_attempt)?$total_attempt:'0') ?></span></span></li>
                    <li class="view-rank-list-wraper-alterd">
                        <?php if($my_attempt_id>0): ?>
                        <ul class="view-mark-list-ul">
                            <li><a href="<?php echo site_url('material/challenge_zone_report_item/'.$my_attempt_id) ?>" class="orange-flat-btn orange-btn-alterd-for-next-page">Challenge Zone report</a></li>
                        </ul>
                        <?php endif; ?>
                    </li>
                </ul>          
            </div><!--container-reduce-width-->
        </div><!--container-->    
    </div><!--fundamentals-->
</section>   

<section>
    <div class="filter-area">
        <div class="container">
            <div class="container-reduce-width">
                <span class="filter-label">Filter by</span>
                <span class="filter-dropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-second-page dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">States
                            <span class="dropdown-arrow-down">
                                <svg version="1.1" x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                <g>
                                <g>
                                <path fill="#808080" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                </g>
                                </g>
                                </svg>
                            </span></button>
                        <ul class="dropdown-menu dropdown-menu-width">
                            <li><a href="#">Keralam</a></li>
                            <li><a href="#">Andhra pradesh</a></li>
                            <li><a href="#">Karnataka</a></li>
                        </ul>
                    </div>
                </span><!--filter-dropdown-->


                <span class="filter-dropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-second-page btn-outline-second-page-second dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">City
                            <span class="dropdown-arrow-down">
                                <svg version="1.1" x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                <g>
                                <g>
                                <path fill="#808080" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                </g>
                                </g>
                                </svg>
                            </span></button>
                        <ul class="dropdown-menu dropdown-menu-width">
                            <li><a href="#">Thiruvananthapuram</a></li>
                            <li><a href="#">Kollam</a></li>
                            <li><a href="#">Pathanamthitta</a></li>
                        </ul>
                    </div>
                </span><!--filter-dropdown-->

                <?php /* ?><span class="chkbox-span">
                    <input type="checkbox" id="c1" name="cc" />
                    <label class="label-narrow" for="c1"><span></span>Show only my friends</label>
                </span><!--chkbox-span--><?php */ ?>

                <span class="compare-btn-container">
                    <a href="javascript:void(0)" id="submit" onclick="compareResult()" class="btn grey-flat-btn">Compare selected</a>
                </span><!--compare-btn-container-->
            </div><!--container-reduce-width-->
        </div><!--container-->	
    </div><!--filter-area-->
</section> 

<section>
    <div class="rank-list">
        <div class="container">
            <div class="container-reduce-width">
                <div class="table-wrapper">
                    <div class="table-responsive">          
                        <table class="table">
                            <thead>
                                <tr class="table-black-head">
                                    <th class="table-th">Rank</th>
                                    <th class="table-th">Student name</th>
                                    <th class="table-th">Marks scored</th>
                                    <th class="table-th">Time taken</th>
                                    <th class="table-th">Compare</th>
                                </tr>
                            </thead>
                            <tbody class="tbody-border" id="rank_wrapper">
                            </tbody>
                        </table>
                    </div>
                </div><!--table-wrapper-->
                <div class="text-center" id="load_more_btn"><a href="javascript:void(0)" onclick="loadMoreAttempts()" class="btn btn-black">Load more</a></div><!--text-center-->
            </div><!--container-reduce-width-->
        </div><!--container-->
    </div><!--rank-list-->	
</section>

<script>
    //focused-row
    var __site_url              = '<?php echo site_url() ?>/';
    var __challengeZoneObject   = $.parseJSON(atob('<?php echo base64_encode(json_encode($challenge_zone_report)) ?>'));
    var __attemptObject         = $.parseJSON(atob('<?php echo base64_encode(json_encode($attempts)) ?>'));
    var __userId                = '<?php echo $session['id']; ?>';
    var __checked               = 'checked="checked"';
    var __disabled              = 'disabled="disabled"';
    var __awardMedals           = new Object;
        __awardMedals[1]        = 'award-gold.svg';
        __awardMedals[2]        = 'award-silver.svg';
        __awardMedals[3]        = 'award-brouns.svg';
        
    var __default_user_path = '<?php echo default_user_path() ?>';
    var __user_path         = '<?php echo user_path() ?>';
    var __theme_img         = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';

    var __offset          = 2;
    var __perPage         = 3;
    var __start           = false;
    var __requestTimeOut  = null;
    var __rank            = 1;
    var __compareIds      = new Object;
        
    $(document).ready(function () {
        $('#rank_wrapper').html(renderRankHtml(__attemptObject));
    });

    function renderRankHtml(attempts)
    { 
        $('#load_more_btn a').html('Load More').css('visibility', 'hidden');
        var ranksHtml  = '';
        var medalHtml  = '';
        var userImg    = '';
        if(Object.keys(attempts).length > 0 )
        {
            $.each(attempts, function(attemptKey, attempt )
            {
                userImg      = '';    
                medalHtml    = '';
                if(typeof __awardMedals[__rank] != 'undefined')
                {
                    medalHtml = '<img class="svg-badge-size" src="'+__theme_img+'/images/'+__awardMedals[__rank]+'">';
                }
                userImg      = ((attempt['us_image'] == 'default.jpg')?__default_user_path:__user_path); 
                ranksHtml += '<tr class="tabel-white-strip tabel-white-strip-left-text '+((__userId == attempt['cza_user_id'])?'focused-row':'')+'" id="rank_wrapper_'+attempt['cza_user_id']+'">';
                ranksHtml += '    <td class="table-sub-rows table-sub-rows-altr"><span class="rank-level text-middle">'+__rank+'</span> <span class="svg-rank text-middle">'+medalHtml+'</span></td>';
                ranksHtml += '    <td class="table-sub-rows table-sub-rows-altr"><img class="rank-holder-image" src="'+userImg+''+attempt['us_image']+'"><span class="text-middle">'+attempt['us_name']+'</span></td>';
                ranksHtml += '    <td class="table-sub-rows table-sub-rows-altr text-center"><span class="text-middle">'+((attempt['total_mark']!=null)?attempt['total_mark']:'')+'</span></td>';
                ranksHtml += '    <td class="table-sub-rows table-sub-rows-altr text-center"><span class="text-middle">'+secondsToHms(attempt['cza_duration'])+'</span></td>';
                ranksHtml += '    <td class="table-sub-rows table-sub-rows-altr text-center"><span class="text-middle"><input '+((__userId == attempt['cza_user_id'])?__checked+' '+__disabled:'')+' id="user_'+attempt['id']+'" value="'+attempt['id']+'" class="user-rank-selected" name="user_'+attempt['id']+'" type="checkbox"><label class="label-narrow label-narrow-altr" for="user_'+attempt['id']+'"><span></span></label></span></td>';
                ranksHtml += '</tr>';
                if(__userId == attempt['cza_user_id'])
                {
                    __compareIds[attempt['id']] = attempt['id'];
                }

                __rank++;
            });
            if( Object.keys(attempts).length == __perPage)
            {
                $('#load_more_btn a').css('visibilty', 'visible');                
            }
        }
        return ranksHtml;
    }
    
    function secondsToHms(d) 
    {
        d = Number(d);
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);

        var minuteClock = '';
        if(h > 0)
        {
            m = m+(h*60);
        }
        if(m > 0)
        {
            if(m > 9)
            {
                minuteClock += m+':';           
            }
            else
            {
                minuteClock += '0'+m+':'; 
            }
        }
        else
        {
           minuteClock += '00:'; 
        }
        if(s > 0)
        {
            if(s > 9)
            {
                minuteClock += s;       
            }
            else
            {
                minuteClock += '0'+s;                 
            }
        }
        else
        {
           minuteClock += '00'; 
        }
        //var hDisplay = (h > 0) ? (h + "h,"):("");
        //var mDisplay = (m > 0) ? (m+"m,"): ("0m,");
        //var sDisplay = (s > 0) ? s+'s' : "0s";
        //return hDisplay + mDisplay + sDisplay; 
        return minuteClock;
    }

    function getAttempts()
    {
        $.ajax({
            url: __site_url+'report/challenge_zone_json',
            type: "POST",
            data:{"is_ajax":true, "offset":__offset},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#rank_wrapper').html('');
                        $('#rank_wrapper').html(renderRankHtml(data['attempts']));
                    }
                    else
                    {
                        $('#rank_wrapper').append(renderRankHtml(data['attempts']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        });
    }
    
    $(document).on('change', '.user-rank-selected', function(){
        var selectorValue = $(this).val();
        if($(this).prop('checked') == true)
        {
            __compareIds[selectorValue] = selectorValue;
        }
        else
        {
            removeArrayIndex(__compareIds, selectorValue)
        }
        
        $('.user-rank-selected').removeAttr('disabled');
        if( Object.keys(__compareIds).length < 5  )
        {
            if(Object.keys(__compareIds).length == 4)
            {
                $('.user-rank-selected').attr('disabled', 'disabled');
            }
            $.each(__compareIds, function(id, value ){
                $('#user_'+id).removeAttr('disabled');
            });
        }
        
        $('.grey-flat-btn').removeClass("grey-flat-btn-to-orange");
        if(Object.keys(__compareIds).length > 1 && Object.keys(__compareIds).length < 5)
        {
            $('.grey-flat-btn').addClass("grey-flat-btn-to-orange");
        }
    });

    function removeArrayIndex(array, index) 
    {
        if(typeof array == 'object')
        {
            delete array[index];
        }
        else
        {
          for(var i = array.length; i--;) {
              if(array[i] === index) {
                  array.splice(i, 1);
              }
          }        
        }
    }
    
    function compareResult()
    {
        if(Object.keys(__compareIds).length > 1 && Object.keys(__compareIds).length < 5)
        {
           $('body').append('<a id="assessment_compare_btn_link" href="'+__site_url+'report/challenge_zone_compare/'+btoa(JSON.stringify(__compareIds))+'"></a>');
           $("#assessment_compare_btn_link")[0].click();
        }
    }
    
    function loadMoreAttempts()
    {
        $('#load_more_btn a').html('Loading...');
        getAttempts();
    }
</script>
<?php include_once 'footer.php'; ?>