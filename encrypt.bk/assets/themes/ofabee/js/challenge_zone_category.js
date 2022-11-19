    $(document).ready(function () {
        //console.log(__challenges);
        __challenges      = $.parseJSON(__challenges);
        //console.log(render_chellenges(__challenges));
        $('#content').html(render_chellenges(__challenges));
    });

    
    function render_chellenges(challenges){
        var rendersOutput = '';
        $.each(challenges, function(ratingkey, challenge )
        {
            rendersOutput += render_single_challenge(challenge);
        });

        return rendersOutput;
    }

    function render_single_challenge(challenge){
        var rendersHtml = '';
        rendersHtml += '<div class="col-xs-4 col-sm-4 col-md-4 mb30 table-challenge mobile-challenge"><div class="challenge-inside-block shadow-box">';
        rendersHtml += '<span class="challenge-block-head">';
        rendersHtml += '<span class="challege-inside-area">'+challenge['cz_title']+'</span>';
        rendersHtml += '</span><span class="challenge-body"><span class="circles-wrap">';
        rendersHtml += generate_challenge_stat(challenge['date_status'],challenge['status']);
        rendersHtml += '</span><span class="ends-details">';
        rendersHtml += generate_date_area(challenge['date_status'],challenge['date_on'],challenge['time']);
        rendersHtml += '</span></span><span class="grey-line"></span>';
        rendersHtml += '<div class="challenge-footer">'+generate_footer(challenge['date_status'],challenge['status'],challenge['id']);
        rendersHtml += '</div></div></div>';
        
        return rendersHtml;
    }

    function generate_challenge_stat(date_stat,attend_stat){
        var return_html = '';

        if(date_stat == 0&&attend_stat == 0){
            if(__user_id == 0){
                return_html = '<span class="ongoing-left ongpoing-red">Ended</span><span class="ongoing-left hidden">Unattended</span>';
            }else{
                return_html = '<span class="ongoing-left ongpoing-red">Ended</span><span class="ongoing-left ">Unattended</span>';
            }
        }
        if(date_stat == 0&&attend_stat == 1){
            if(__user_id == 0){
                return_html = '<span class="ongoing-left ongpoing-red">Ended</span><span class="ongoing-left hidden">Attended</span>';
            }else{
                return_html = '<span class="ongoing-left ongpoing-red">Ended</span><span class="ongoing-left ">Attended</span>';
            }
        }
        if(date_stat == 1&&attend_stat == 0){
            return_html = '<span class="ongoing-left ongpoing-green">Ongoing</span><span class="ongoing-left hidden">Ongoing</span>';
        }
        if(date_stat == 1&&attend_stat == 1){
            return_html = '<span class="ongoing-left ongpoing-green">Ongoing</span><span class="ongoing-left">Attended</span>';
        }

        return return_html;
    }
    
    function generate_date_area(date_stat,date,time){
        var return_html = '';
        if(date_stat == 0){
            return_html = '<span class="ends-on">Ended on:</span><span class="ends-on-date">'+date+'<span class="ends-time">'+time+'</span></span>'
        }
        if(date_stat == 1){
            return_html = '<span class="ends-on">Ends on:</span><span class="ends-on-date">'+date+'<span class="ends-time">'+time+'</span></span>'
        }
        return return_html;
    }

    
    function generate_footer(date_stat,attend_stat,challenge_id){
        var return_html = '';
        if(date_stat == 0&&attend_stat == 0){
            if(__user_id == 0){
                return_html = '<a href="'+__site_url+'report/challenge_zone/'+challenge_id+'" class="challenge-link"><span class="attend-now-footer attend-orange">View Report</span> <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g><polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg></a>';
            }else{
                return_html = '<a href="'+__site_url+'challenge_zone/questions/'+challenge_id+'" class="challenge-link"><span class="attend-now-footer attend-blue">View Questions</span> <svg version="1.1" class="blue-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g><polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg></a>';
            }
        }
        if(date_stat == 0&&attend_stat == 1){
            return_html = '<a href="'+__site_url+'report/challenge_zone/'+challenge_id+'" class="challenge-link"><span class="attend-now-footer attend-orange">View Report</span> <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g><polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg></a>';
        }
        if(date_stat == 1&&attend_stat == 0){
            return_html = '<a href="'+__site_url+'material/challenge/'+challenge_id+'" class="challenge-link"><span class="attend-now-footer">Attend Now</span><svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g><polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg></a>'+generate_popup(date_stat,challenge_id);
        }
        if(date_stat == 1&&attend_stat == 1){
            return_html = '<a href="'+__site_url+'report/challenge_zone/'+challenge_id+'" class="challenge-link"><span class="attend-now-footer attend-orange">View Report</span> <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g><polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg></a>'+generate_popup(date_stat,challenge_id);
        }
        
        return return_html;
    }

    function generate_popup(date_stat,challenge_id){
        return_html = '';

        if(date_stat != 0){
            return_html += '<span onclick="invite_to_challenge('+challenge_id+')" class="mail-wrap-inline" data-toggle="tooltip" title="Invite to challenge">';
            return_html += '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 16" style="enable-background:new 0 0 20 16;" xml:space="preserve">';
            return_html += '<style type="text/css">.st0 {fill: none;}.st1 {fill: #B3B3B3;}</style>';
            return_html += '<path class="st0" d="M-2-4h24v24H-2V-4z"/>';
            return_html += '<path class="st1" d="M18,0H2C0.9,0,0,0.9,0,2l0,12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V2C20,0.9,19.1,0,18,0z M18,14H2V4l8,5l8-5V14 z M10,7L2,2h16L10,7z"/>';
            return_html += '</svg></span>';
        }

        return return_html;
    }