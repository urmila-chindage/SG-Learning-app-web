var __button_drag = false;
$(document).ready(function()
{
    $(".header-sortables").sortable(
    {
        connectWith: ".header-sortables", 
        placeholder: "section-highlight",
        handle: ".drager",
        scroll: true,
        stop: function()
        {
            parant_sort();
        },
        update: function(event, ui) { 
            var serielezing_class = "header-sortables";
            if( __button_drag == false )
            {
               updatePageSectionPositon(ui.item.index(), ui.item[0]['id'], serielezing_class);               
           }
           else
           {
               __button_drag = false;
               addSectionOnDrag(ui.item.index());               
           }
        }
    });
    $(".footer-sortable").sortable(
        {
            connectWith: ".footer-sortable", 
            placeholder: "section-highlight",
            handle: ".drager",
            scroll: true,
            stop: function()
            {
                parant_sort();
            },
            update: function(event, ui) { 
                var serielezing_class = "footer-sortable";
                if( __button_drag == false )
                {
                    console.log(ui);
                   updatePageSectionPositon(ui.item.index(), ui.item[0]['id'], serielezing_class);               
               }
               else
               {
                   __button_drag = false;
                   addSectionOnDrag(ui.item.index());               
               }
            }
        });

    
    parant_sort();
    $(".header-sortables, .section").disableSelection();
    
  
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
                updatePageLecturePosition($(this).attr('id'), itemList);
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