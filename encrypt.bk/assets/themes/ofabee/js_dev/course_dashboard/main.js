let __course_id = window.location.pathname.split('/');
__course_id = __course_id[__course_id.length - 1];
let __curriculum = [];
let __rating_selected = 0;

$(document).ready(function () {
    __loaded['curriculum']  = false;
    $('.tab-pane').hide();
    $('#overview').show();
    __reviews = $.parseJSON(__reviews);
    renderReviews(__reviews.reviews);
    __my_rating = +__my_rating;
    $('#my_rating').barrating({
        theme: 'fontawesome-stars',
        readonly: (__my_rating != 0),
        onSelect: function (value, text) {
            __my_rating = value;
            if (__start == true) {
                rate_course(__my_rating);
            }
        }
    });
});

$(function () {
    $('#example_course_dashboard').barrating({
        theme: 'fontawesome-stars',
        readonly: __my_rating,
        onSelect: function (value, text) {
            __rating_selected = value;
            if (__start == true) {
                rate_course(__rating_selected);
            }
        }
    });

});

function rate_course(ratingSelected) {
    __rating_selected = ratingSelected;
    __start = false;
    $('#example2').barrating({
        theme: 'fontawesome-stars',
        readonly: false,
        onSelect: function (value, text) {
            __rating_selected = value;
            $('#example_course_dashboard').barrating('set', __rating_selected);
            console.log('Bla : ' + __rating_selected);
        }
    });
    $('#example2').barrating('set', __rating_selected);
    $('#rate_course').modal('show');
}


$(document).on('hidden.bs.modal', '#rate_course', function (e) {
    __start = true;
    $('#example_course_dashboard').barrating('clear');
    $('#review_course').val('');
});
$(document).on('hidden.bs.modal', '#rate_course_preview', function (e) {
    __start = false;
    $('#rate_course_label').html('Your rating');
    $('#example_course_dashboard').barrating('set', __rating_selected);
    $('#example_course_dashboard').barrating('readonly', true);
});



$(document).on('click', '#submit_rating_course', function () {
    console.log(__rating_selected);
    var __review = $('#review_course').val();
    $.ajax({
        url: __site_url + 'material/save_rating_review',
        type: "POST",
        async: false,
        data: { "is_ajax": true, 'course_id': __course_id, 'rating': __rating_selected, 'review': __review },
        success: function (response) {
            var data = $.parseJSON(response);
            $('#rate_course').modal('hide');
            $('#rate_course').on('hidden.bs.modal', function (e) {
                $('#example4').barrating({
                    theme: 'fontawesome-stars',
                    readonly: true
                });
                $('#example4').barrating('set', __rating_selected);
                $('#my_rating').barrating('set', __rating_selected);
                $('#my_rating').barrating('readonly', true);
                $('#preview_review_course').text(__review);
                $("#rate_course_preview").modal('show');
            });

        }
    });
});


$(document).on("click", "#show_more_description", function () {
    $(".show-more-data-wrap").removeClass("show-more-collapse");
    $(".show-more-data-wrap").css({ 'max-height': 'none' });
    $(".Showmore-btm").remove();
});

function renderReviews(reviews) {
    $('#review_list').html(
        `${reviews.map(rv => {
            return `<li class="profilelist-childs">
                            <div class="profile-list-photo">
                                <img src="${rv.cc_user_image === 'default.jpg' ? __user_path.default : __user_path.native + rv.cc_user_image}" class="olp-img-rounded img-responsive svg-common profile-pic">
                                <span class="profile-name-text">${rv.cc_user_name}</span>
                                <div class="star-ratings-sprite star-rating-vertical-top-super starr-vertical-top">
                                    <span style="width:${rv.cc_rating * 20}%" class="star-ratings-sprite-rating"></span>
                                </div>
                                <span class="sub-profile-text">${relative_time_ax(rv.created_date).day}</span>
                            </div><!--profile-list-photo-->
                            <p class="profil-des">
                                ${rv.cc_reviews}
                            </p>
                        </li>`;
        })
        + (+__reviews.count <= __reviews.reviews.length ? '' : `<a href="javascript:void(0)" onclick="getReviews(${Math.ceil(__reviews.reviews.length / __reviews.limit) + 1})" id="show_more_reviews">Show more reviews</a>`)}`
    );
}

function getReviews(offset) {
    $.ajax({
        url: __site_url + 'course/load_reviews/' + __course_id + '/' + offset,
        type: "GET",
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['success'] === true) {
                __reviews.reviews = __reviews.reviews.concat(data['reviews']);
                renderReviews(__reviews.reviews);
            }
        }
    });
}
let __sechtml = '';
function loadCurriculum(e) {
    $('.tab-pane').hide();
    if(__loaded['curriculum']){
        $('#curriculum').fadeIn('slow');
        $('.nav-tabs li a').removeClass('active-bread-parent');
        $(e).addClass('active-bread-parent');
        return;
    }
    $.ajax({
        url: __site_url + 'course/get_full_curriculum/' + __course_id,
        type: "GET",
        success: function (response) {
            __loaded['curriculum'] = true;
            var data = $.parseJSON(response);
            if (data['success'] === true) {
                $('#overview').hide();
                $('.nav-tabs li a').removeClass('active-bread-parent');
                $(e).addClass('active-bread-parent');
                let cIcon = {
                    1: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>',
                    11: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>',
                    12: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>',
                    4: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>',
                    6: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8 V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>',
                    5: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8 V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>',
                    2: '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8 V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>',
                    7: '<svg version="1.1" class="svg-common" .333="" x="0px" y="0px" width="19px" height="21px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M20.5,4.5h-7.6l3.3-3.3l-0.7-0.7l-4,4l-4-4L6.8,1.2l3.3,3.3H2.5c-1.1,0-2,0.9-2,2v12c0,1.1,0.9,2,2,2h18 c1.1,0,2-0.9,2-2v-12C22.5,5.4,21.6,4.5,20.5,4.5z M20.5,18.5h-18v-12h18V18.5z M8.5,8.5v8l7-4L8.5,8.5z"></path></g></svg>',
                    3: '<svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0z M8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>',
                    8: '<svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0z M8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>'
                }
                __curriculum = data['sections'].map(sec => {
                    return { id: sec.id, name: sec.s_name, lectures: [] };
                });
                __curriculum = __curriculum.map(cur => {
                    data['lectures'].map(lec => {
                        if (cur.id == lec.cl_section_id) {
                            cur.lectures.push({ id: lec.id, name: lec.cl_lecture_name, unique: lec.unique, cl_total_page: lec.cl_total_page, type: lec.cl_lecture_type });
                        }
                    });
                    return cur;
                });
                __sechtml = __curriculum.map((sec, sIndex) => {
                    return `<ul class="solution-list solution-list-for-curriculam">
                        <li class="solution-child-head"><p class="solution-para"><span class="solution-section">Section ${sIndex + 1}:</span><span class="solution-intro">${sec.name}</span></p></li>
                        ${
                        sec.lectures.map((lec, lIndex) => {
                            let calc = '';
                            switch (+lec.type) {
                                case 1: calc = lec.unique; break;
                                case 12: calc = lec.unique; break;
                                case 2: calc = lec.cl_total_page > 1 ? lec.cl_total_page + ' Pages' : lec.cl_total_page + ' Page'; break;
                                case 7: calc = lec.unique; break;
                                case 3: calc = lec.unique > 1 ? lec.unique + ' Questions' : lec.unique + ' Question'; break;
                                case 8: calc = lec.unique > 1 ? lec.unique + ' Pages' : lec.unique + ' Page'; break;
                                default: calc = ''; break;
                            }
                            //console.log(calc+lec.unique);
                            return `<li onclick="location.href='${__site_url + 'materials/course/' + __course_id + '#' + lec.id}'" style="cursor:pointer;" class="soulution-childs ${lIndex === sec.lectures.length - 1 ? 'no-bottom-border' : ''}">
                                            <span class="solution-child-l-r-margin solution-child-table-cell">${cIcon[+lec.type]}</span>
                                            <span class="solution-child-l-r-margin solution-child-table-cell min-width-list">Lecture ${lIndex + 1}</span>
                                            <span class="solution-child-l-r-margin solution-child-table-cell lecture-des">${lec.name}</span>
                                            <span class="solution-child-l-r-margin pull-right solution-time-align time-hide ">
                                            ${calc}
                                            </span></li>`
                        }).join("")
                        }</ul>`
                }).join("");

                $('#curriculum_div').html('<h3 class="formpage-heading">Curriculum</h3>' + __sechtml);
                $('#curriculum').fadeIn('slow');
            }
        }
    });
}

function loadAnouncements(e) {
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $(e).addClass('active-bread-parent');
    $('#anouncements').addClass('active').fadeIn('slow');
}

function loadReports(e) {
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $(e).addClass('active-bread-parent');
    $('#reports').addClass('active').fadeIn('slow');
}

function loadQa(e) {
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $(e).addClass('active-bread-parent');
    $('#discussions').addClass('active').fadeIn('slow');
}

function loadOverView(e) {
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $(e).addClass('active-bread-parent');
    $('#overview').addClass('active').fadeIn('slow');
}

function plot_rank_chart() {

    var ranking = [];
    var rank_sub = [];
    var max_rank = [];
    var i = 0;
    var rank_flag = false;
    //console.log(__rank_object);
    __rank_object = $.parseJSON(__rank_object);
    if (Object.size(__rank_object) == 0) {
        __main_flag++;
        $('#db_rank').hide();
    }
    $.each(__rank_object, function (rankkey, rank) {
        if (rank['my_rank'] > 0) {
            rank_flag = true;
            ranking[i] = rank['my_rank'];
            rank_sub[i] = rank['lecture']['cl_lecture_name'];
            max_rank[i] = rank['attempts'];
            i++;
        }
    });

    Highcharts.chart('chartgreen', {
        chart: {
            type: 'line'
        },
        credits: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: rank_sub
        },
        yAxis: {
            allowDecimals: false,
            min: 1,
            max: Math.max.apply(Math, max_rank),
            reversed: true,
            title: {
                text: ''
            },
            labels: {
                formatter: function () {
                    return this.value + '';
                }
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true,
            useHTML: true
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            line: {
                marker: {
                    radius: 5,
                    lineColor: '#00c853',
                    fillColor: '#ffffff',
                    lineWidth: 2,
                    states: {
                        hover: {
                            fillColor: '#ffffff',
                            lineColor: '#00c853',
                            lineWidth: 2
                        }
                    }
                }
            },
            series: {
                states: {
                    hover: {
                        enabled: true,
                        halo: {
                            size: 20
                        }
                    }
                }
            }
        },
        series: [{
            name: ' ',
            lineColor: '#00c853',
            marker: {
                symbol: 'circle'
            }, data: ranking
        }]
    });
    if (Object.size(__rank_object) != 0 && rank_flag == false) {
        __main_flag++;
        $('#db_rank').hide();
    }
}

function plot_category_p_chart() {
    __topic_wise = $.parseJSON(__topic_wise);
    var chart_div = '';
    var classes = ['violet', 'skyblue', 'maroon'];
    var colors = [];
    colors[0] = { 'lineColor': '#7753e5', 'fillColor': '#ffffff' };
    colors[1] = { 'lineColor': '#00a1e5', 'fillColor': '#ffffff' };
    colors[2] = { 'lineColor': '#cc53e5', 'fillColor': '#ffffff' };
    var i = 0, j = 0;
    var xaxis = [];
    var yaxis = [];
    var percentage = 0;
    var bar_chart = '';
    var average_percentage = 0;
    var flag = true;
    $.each(__topic_wise, function (topickey, topic) {
        //console.log(topic);
        var attended_flag = false;
        chart_div = render_chart_place(classes[i], topic['id'], topic['qc_category_name']);
        //console.log(chart_div);
        average_percentage = 0;
        $.each(topic['assessment'], function (askey, assessment) {
            flag = false;
            if (assessment['attended'] == 1) {
                attended_flag = true;
                __main_attended_flag
                if (assessment['total_mark'] >= 0) {
                    xaxis[j] = assessment['assessment_name'];
                    if (assessment['total_mark'] == 0) {
                        percentage = 0;
                    } else {
                        if (assessment['scored_mark'] <= 0) {
                            percentage = 0;
                        } else {
                            assessment['total_mark'] = parseFloat(assessment['total_mark']);
                            assessment['scored_mark'] = parseFloat(assessment['scored_mark']);
                            percentage = (assessment['scored_mark'] / assessment['total_mark']) * 100;
                            percentage = percentage.toFixed(2);
                            percentage = parseFloat(percentage);
                        }
                    }
                    yaxis[j] = percentage;
                    j++;
                    average_percentage += percentage;
                }
            }
        });
        average_percentage = average_percentage / j;
        j = 0;
        bar_chart += render_bar_chart(average_percentage, topic['qc_category_name']);
        average_percentage = 0;
        if (yaxis.length > 0) {
            i++;
            $('#assessments').before(chart_div);
            if (attended_flag == true) {
                plot_chart_single(xaxis, yaxis, topic['id'], colors[i - 1]);
            } else {

            }
        }
        xaxis = [];
        yaxis = [];
        if (i >= classes.length) {
            i = 0;
        }
    });
    if (__main_attended_flag == false) {
        $('#topic_wise_progress').remove();
    }
    if (bar_chart == '') {
        $('#db_bar_chart').hide();
        __main_flag++;
    } else {
        $('#topic_average').html(bar_chart);
    }
    if (flag) {
        __main_flag++;
        $('#db_topic').hide();
    }
}
function render_chart_place(color, id, name) {
    var renderHtml = '';
    renderHtml += '<div class="progress-graph bar-wrap-margin-top">';
    renderHtml += '<div class="container container-res-chnger-frorm-page">';
    renderHtml += '<div class="changed-container-for-forum">';
    renderHtml += '<div class="ling-graph-wrap">';
    renderHtml += '<div class="parent-bar-details parent-bar-violet-graph">';
    renderHtml += '<span class="bar-tunnel bar-tunnel-inside-violet bar-' + color + '"></span>';
    renderHtml += '<span class="bar-text bar-text-' + color + '">' + name + '</span>';
    renderHtml += '</div><div id="chart' + id + '" class="chart' + color + '"></div>';
    renderHtml += '</div></div></div></div>';

    return renderHtml;
}

function render_bar_chart(percentage, name) {
    //console.log(percentage);
    var renderHtml = '';
    if (percentage > 0 && percentage != '') {
        renderHtml = '<div class="bar-wrap"><div class="leftprogressTexr">' + name + '</div><div class="progressBar-wrap"><span class="prgressBarchild bar bar-' + percentage_class(Math.round(percentage)) + ' cf" data-percent="' + Math.round(percentage) + '%" style="width: ' + Math.round(percentage) + '%;"><span class="count">' + Math.round(percentage) + '%</span></span></div></div>';
    } else {
        renderHtml = '';
    }
    return renderHtml;
}
function percentage_class(percentage) {
    if (percentage > 90) {
        return 'green';
    }
    if (percentage > 80 && percentage <= 90) {
        return 'blue';
    }
    if (percentage >= 60 && percentage <= 80) {
        return 'violet';
    }
    if (percentage < 60) {
        return 'peach';
    }
}
function plot_chart_single(xaxis, yaxis, id, color) {

    Highcharts.chart('chart' + id, {
        chart: {
            type: 'line'

        },
        credits: {
            enabled: false
        },

        exporting: {
            enabled: false
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: xaxis
        },
        yAxis: {
            min: 0,
            max: 100,
            reversed: false,
            title: {
                text: ''
            },
            labels: {
                formatter: function () {
                    return this.value + ' %';
                }
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true,
            useHTML: true
        },

        legend: {
            enabled: false
        },
        plotOptions: {
            line: {
                marker: {
                    radius: 5,
                    lineColor: color['lineColor'],
                    fillColor: color['fillColor'],
                    lineWidth: 2,
                    states: {
                        hover: {
                            fillColor: color['fillColor'],
                            lineColor: color['lineColor'],
                            lineWidth: 2
                        }
                    }
                }
            },
            series: {
                states: {
                    hover: {
                        enabled: true,
                        halo: {
                            size: 20
                        }
                    }
                }
            }
        },
        series: [{
            name: ' ',
            lineColor: color['lineColor'],
            marker: {
                symbol: 'circle'
            },
            data: yaxis

        }]

    });
}

function relative_time_ax(date_str) {
    var date_time = new Object();
    var d = new Date(date_str);
    var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    var date = d.getDate() + " " + month[d.getMonth()] + " " + d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase();
    date_time.day = date;
    date_time.time = time;
    return date_time;

};

function relative_time(date_str) {
    if (!date_str) { return; }
    date_str = $.trim(date_str);
    date_str = date_str.replace(/\.\d\d\d+/, ""); // remove the milliseconds
    date_str = date_str.replace(/-/, "/").replace(/-/, "/"); //substitute - with /
    date_str = date_str.replace(/T/, " ").replace(/Z/, " UTC"); //remove T and substitute Z with UTC
    date_str = date_str.replace(/([\+\-]\d\d)\:?(\d\d)/, " $1$2"); // +08:00 -> +0800
    var parsed_date = new Date(date_str);
    var relative_to = (arguments.length > 1) ? arguments[1] : new Date(); //defines relative to what ..default is now
    var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
    delta = (delta < 2) ? 2 : delta;
    var r = '';
    if (delta < 60) {
        r = delta + 'few seconds ago';
    } else if (delta < 120) {
        r = 'one minute ago';
    } else if (delta < (45 * 60)) {
        r = (parseInt(delta / 60, 10)).toString() + ' minutes ago';
    } else if (delta < (2 * 60 * 60)) {
        r = 'an hour ago';
    } else if (delta < (24 * 60 * 60)) {
        r = '' + (parseInt(delta / 3600, 10)).toString() + ' hours ago';
    } else if (delta < (48 * 60 * 60)) {
        r = 'one day ago';
    } else {
        r = (parseInt(delta / 86400, 10)).toString() + ' days ago';
    }
    return r;
};