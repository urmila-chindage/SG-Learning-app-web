var __button_drag = false;
$(document).ready(function()
{
    $("#sortable").sortable(
    {
        connectWith: "#sortable", 
        placeholder: "section-highlight",
        handle: ".drager",
        scroll: true,
        stop: function()
        {
            parant_sort();
        },
        update: function(event, ui) { 
           if( __button_drag == false )
           {
               updateSectionPositon(ui.item.index(), ui.item[0]['id']);               
           }
           else
           {
               __button_drag = false;
               addSectionOnDrag(ui.item.index());               
           }
        }
    });

    
    parant_sort();
    $("#sortable, .section").disableSelection();
    $('.btn-add-section').draggable(
    {
        helper: "clone", 
        revert: "invalid",
        connectToSortable: "#sortable",
        drag: function( event, ui ) {
            __button_drag = true;
        }
    });
    $('#sortable').droppable(
    {
        drop: function(event, ui)
        {
            var droped_element = $(this).find('.btn-add-section');
            droped_element.html($('<div class="section-title-holder"><div class="section-counter"></div><div class="drager ui-sortable-handle"><img src="'+assets_url+'images/drager.png"></div><span class="section-name" id="section_name_0"> Section Name </span><div class="btn-group section-control"><span class="dropdown-tigger" data-toggle="dropdown"><span class="label-text"><i class="icon icon-down-arrow"></i></span><span class="tilder"></span></span><ul class="dropdown-menu pull-right" id="button_group_0" role="menu"><li><a href="#">Edit</a></li><li><a href="#">Delete</a></li></ul></div></div><ul class="lecture-wrapper ui-sortable"></ul>'));
            droped_element.removeClass();
            droped_element.addClass("section");
            droped_element.attr("id", "section_wrapper_0");
            droped_element.removeAttr('style');
            return;
        }
    });
});
function parant_sort()
    {
        $(".lecture-wrapper").sortable(
        {
            connectWith: ".lecture-wrapper",
            placeholder: "lecture-highlight",
            handle: ".drager",
            scroll: false,
            update: function(){
                var itemList = $(this).sortable(
                    "serialize", {
                    attribute: "id",
                    key: 'lecture_id[]'
                });
                updateLecturePosition($(this).attr('id'), itemList);
            }
        });
    }
$(document).ready(function() {
    function e() {
        var e = $(window).width(),
            t = $(".right_block");
        e >= 992 ? t.css("right", "0px") : t.css("right", "-350px")
    }
    $("#nav-icon1").on("click", function(e) {
        e.preventDefault();
        var t = $(this),
            a = $(".right_block");
        t.hasClass("open") ? (t.removeClass("open"), a.css("right", "-350px")) : (t.addClass("open"), a.css("right", "0px"))
    }), $(window).resize(function() {
        e()
    });
    var t = document.getElementById("drop-to-pop");
    document.getElementById("status");
    t.ondragover = function() {
        return this.className = "hover drop-area-section border-bottom-white ui-droppable", !1
    }, t.ondragend = function() {
        return this.className = "", !1
    }, t.ondrop = function(e) {
        this.className = "drop-area-section border-bottom-white ui-droppable", e.preventDefault();
        var t = e.dataTransfer.files[0],
            a = new FileReader;
        return a.onload = function(e) { /*$("#upload-lecture").modal()*/ }, a.readAsDataURL(t), !1
    }
});