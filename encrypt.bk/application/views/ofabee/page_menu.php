<?php 
$dynamic_pages = isset($header_pages)?$header_pages:menu_pages(array('type' => 'header'));
$page_id = $this->uri->segment(2);
$segment = $this->uri->segment(0);
//echo '<pre>';print_r($dynamic_pages['parent']);die;
$area_selected = 'aria-selected="true"';
?>
<!-- horizontal menu scroller starts -->
<?php if(isset($dynamic_pages['parent']) && !empty($dynamic_pages['parent'])): ?>
<style>
.page-nav-item{ padding:0px !important;}
.page-nav-item a{ padding:10px 20px; border-top:1px solid <?php echo config_item('menu_color'); ?>;border-bottom:1px solid <?php echo config_item('menu_color'); ?>;}
.page-nav-item-ul, .page-nav-wrapper-container{ background:<?php echo config_item('menu_color'); ?> !important;} 
.page-nav-item .active{ 
    background: #fff;
    color: <?php echo config_item('menu_color'); ?> !important;
    border-top: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8; 
}  

/* pages submenu */
.page-nav {overflow-x: unset;overflow-y: unset;}
.page-nav-item:hover .dropdown-menu{display:block !important;}
.page-nav-item .menu-down svg path:first-child{fill:#fff;}
.pages-submenu ul{
    border: 0;
    overflow: hidden;
    border-radius: 0px;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    margin-top: 0px;
}
.pages-submenu li{border: 0px !important;}
.pages-submenu li a{
    border: 0px !important;
    padding: 10px 15px;
}

/* pages submenu ends */

</style>
<section>
    <div class="page-nav-wrapper-container">
        <div class="page-nav-wrapper container">
            <nav id="pnProductNav" class="page-nav">
                <ul id="pnProductNavContents" class="page-nav-item-ul">
                <?php //print_r($dynamic_pages['parent']);die;?>
                    <?php foreach($dynamic_pages['parent'] as $parent): ?>  
                        <?php 
                            $parent_link= (($parent['mm_connected_as_external'] == '1' ) || ($parent['mm_item_connected_slug'] != '') ? true : false );
                            if($parent_link || isset($parent['child']) && count($parent['child']) > 0)
                            { 
                                $page_url   = site_url($parent['mm_item_connected_slug']);
                                $attributes = ((($parent['mm_new_window'])) ? 'target="_blank"':'');
                                $page_url   = (($parent['mm_connected_as_external'] == '1' ) ? $parent['mm_external_url']: $page_url);
                                $page_url   =  $page_url;
                                $active_li  = (($page_id == $parent['pageid']) && ($segment == 'page') ? 'active menu-scroll-left':'');
                        ?> 
                        <li class="page-nav-item dropdown pages-submenu">
                        
                            <a id="parenEl_<?php echo $parent['id'];?>" href="<?php echo $page_url ?>" <?php echo $attributes ?> <?php echo $area_selected ?> class="dropdown-toggle <?php echo $active_li; ?> " <?php if(!$parent_link): ?> data-toggle="dropdown" href="javascript:void(0)"<?php endif; ?> aria-expanded="true">
                                <?php echo $parent['mm_name'];?>
                                <?php if(isset($parent['child']) && count($parent['child']) > 0):?>
                                    <span class="menu-down">
                                        <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                        </g>
                                        </svg>
                                    </span>
                                <?php endif;?>
                            </a>
                            
                            <?php if(isset($parent['child']) && count($parent['child']) > 0):?>
                                <ul class="dropdown-menu" style="border: 0;">
                                    <?php  $menuActiveEl = 'childli_'.$page_id;?>
                                    <?php foreach($parent['child'] as $child): ?>
                                        <?php
                                            $page_url   = site_url($child['mm_item_connected_slug']);
                                            $showLinkC  = (($child['mm_connected_as_external'] == '1' ) ? $child['mm_external_url']: $child['mm_item_connected_slug']);
                                            $attributes = ((($child['mm_new_window'])) ? 'target="_blank"':'');
                                            $page_url   = (($child['mm_connected_as_external'] == '1' ) ? $child['mm_external_url']: $page_url);
                                            $page_url   =  $page_url;
                                            $active_li  = (($page_id == $child['pageid']) && ($segment == 'page') ? 'active menu-scroll-left':'');
                                        ?>
                                        <?php if(!empty($showLinkC)):?>
                                            <li>
                                                <a id="<?php echo $menuActiveEl;?>" class="<?php echo $active_li; ?>" href="<?php echo $page_url ?>" <?php echo $attributes ?> <?php echo $area_selected ?> parent-id="<?php echo $child['mm_parent_id']; ?>" page-id="<?php echo $child['pageid']; ?>" ><?php echo $child['mm_name'];?></a>
                                            </li>
                                        <?php endif;?>
                                    <?php endforeach; ?>
                                </ul>
                        <?php endif;?>

                        </li>
                        <?php $area_selected = ''; ?>
                    <?php } endforeach; ?>
                </ul>
            </nav>
            <button id="pnAdvancerLeft" class="item-navigator item-navigator-left" type="button">
                <svg class="item-navigator-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 551 1024"><path d="M445.44 38.183L-2.53 512l447.97 473.817 85.857-81.173-409.6-433.23v81.172l409.6-433.23L445.44 38.18z"/></svg>
            </button>
            <button id="pnAdvancerRight" class="item-navigator item-navigator-right" type="button">
                <svg class="item-navigator-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 551 1024"><path d="M105.56 985.817L553.53 512 105.56 38.183l-85.857 81.173 409.6 433.23v-81.172l-409.6 433.23 85.856 81.174z"/></svg>
            </button>
        </div>
    </div>
</section>
<script>

    var __page_id   = '<?php echo $page_id;?>';
    var page_id     = $('#childli_'+__page_id).attr('page-id');
    var parent_id   = $('#childli_'+__page_id).attr('parent-id');
    var segment     = '<?php echo $segment;?>';

    if(page_id == __page_id && segment == 'page'){
        $('#parenEl_'+parent_id).addClass('active menu-scroll-left');
    }

    var li = $(".menu-scroll-left").position();
    
    if(li){
        //console.log(li);
        $( "#pnProductNav" ).scrollLeft( li.left );
    }
    // horizontal menu scroller starts here
    var SETTINGS = {
        navBarTravelling: false,
        navBarTravelDirection: "",
        navBarTravelDistance: 150
    }
    document.documentElement.classList.remove("no-js");
    document.documentElement.classList.add("js");
    // Out advancer buttons
    var pnAdvancerLeft  = document.getElementById("pnAdvancerLeft");
    var pnAdvancerRight = document.getElementById("pnAdvancerRight");
    var pnProductNav            = document.getElementById("pnProductNav");
    var pnProductNavContents    = document.getElementById("pnProductNavContents");
        pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
    // Handle the scroll of the horizontal container
    var last_known_scroll_position = 0;
    var ticking = false;
    function launchingAnimationFrame(scroll_pos) {
        pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
    }
    pnProductNav.addEventListener("scroll", function() {
        last_known_scroll_position = window.scrollY;
        if (!ticking) {
            window.requestAnimationFrame(function() {
                launchingAnimationFrame(last_known_scroll_position);
                ticking = false;
            });
        }
        ticking = true;
    });
    pnAdvancerLeft.addEventListener("click", function() {
        // If in the middle of a move return
        if (SETTINGS.navBarTravelling === true) {
            return;
        }
        // If we have content overflowing both sides or on the left
        if (determineOverflow(pnProductNavContents, pnProductNav) === "left" || determineOverflow(pnProductNavContents, pnProductNav) === "both") {
            // Find how far this panel has been scrolled
            var availableScrollLeft = pnProductNav.scrollLeft;
            // If the space available is less than two lots of our desired distance, just move the whole amount
            // otherwise, move by the amount in the settings
            if (availableScrollLeft < SETTINGS.navBarTravelDistance * 2) {
                pnProductNavContents.style.transform = "translateX(" + availableScrollLeft + "px)";
            } else {
                pnProductNavContents.style.transform = "translateX(" + SETTINGS.navBarTravelDistance + "px)";
            }
            // We do want a transition (this is set in CSS) when moving so remove the class that would prevent that
            pnProductNavContents.classList.remove("page-nav-item-ul-no-transition");
            // Update our settings
            SETTINGS.navBarTravelDirection = "left";
            SETTINGS.navBarTravelling = true;
        }
        // Now update the attribute in the DOM
        pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
    });
    pnAdvancerRight.addEventListener("click", function() {
        // If in the middle of a move return
        if (SETTINGS.navBarTravelling === true) {
            return;
        }
        // If we have content overflowing both sides or on the right
        if (determineOverflow(pnProductNavContents, pnProductNav) === "right" || determineOverflow(pnProductNavContents, pnProductNav) === "both") {
            // Get the right edge of the container and content
            var navBarRightEdge = pnProductNavContents.getBoundingClientRect().right;
            var navBarScrollerRightEdge = pnProductNav.getBoundingClientRect().right;
            // Now we know how much space we have available to scroll
            var availableScrollRight = Math.floor(navBarRightEdge - navBarScrollerRightEdge);
            // If the space available is less than two lots of our desired distance, just move the whole amount
            // otherwise, move by the amount in the settings
            if (availableScrollRight < SETTINGS.navBarTravelDistance * 2) {
                pnProductNavContents.style.transform = "translateX(-" + availableScrollRight + "px)";
            } else {
                pnProductNavContents.style.transform = "translateX(-" + SETTINGS.navBarTravelDistance + "px)";
            }
            // We do want a transition (this is set in CSS) when moving so remove the class that would prevent that
            pnProductNavContents.classList.remove("page-nav-item-ul-no-transition");
            // Update our settings
            SETTINGS.navBarTravelDirection = "right";
            SETTINGS.navBarTravelling = true;
        }
        // Now update the attribute in the DOM
        pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
    });
    pnProductNavContents.addEventListener(
        "transitionend",
        function() {
            // get the value of the transform, apply that to the current scroll position (so get the scroll pos first) and then remove the transform
            var styleOfTransform = window.getComputedStyle(pnProductNavContents, null);
            var tr = styleOfTransform.getPropertyValue("-webkit-transform") || styleOfTransform.getPropertyValue("transform");
            // If there is no transition we want to default to 0 and not null
            var amount = Math.abs(parseInt(tr.split(",")[4]) || 0);
            pnProductNavContents.style.transform = "none";
            pnProductNavContents.classList.add("page-nav-item-ul-no-transition");
            // Now lets set the scroll position
            if (SETTINGS.navBarTravelDirection === "left") {
                pnProductNav.scrollLeft = pnProductNav.scrollLeft - amount;
            } else {
                pnProductNav.scrollLeft = pnProductNav.scrollLeft + amount;
            }
            SETTINGS.navBarTravelling = false;
        },
        false
    );
    function determineOverflow(content, container) {
        var containerMetrics = container.getBoundingClientRect();
        var containerMetricsRight = Math.floor(containerMetrics.right);
        var containerMetricsLeft = Math.floor(containerMetrics.left);
        var contentMetrics = content.getBoundingClientRect();
        var contentMetricsRight = Math.floor(contentMetrics.right);
        var contentMetricsLeft = Math.floor(contentMetrics.left);
        if (containerMetricsLeft > contentMetricsLeft && containerMetricsRight < contentMetricsRight) {
            return "both";
        } else if (contentMetricsLeft < containerMetricsLeft) {
            return "left";
        } else if (contentMetricsRight > containerMetricsRight) {
            return "right";
        } else {
            return "none";
        }
    }
    (function (root, factory) {
        if (typeof define === 'function' && define.amd) {
            define(['exports'], factory);
        } else if (typeof exports !== 'undefined') {
            factory(exports);
        } else {
            factory((root.dragscroll = {}));
        }
    }(this, function (exports) {
        var _window = window;
        var _document = document;
        var mousemove = 'mousemove';
        var mouseup = 'mouseup';
        var mousedown = 'mousedown';
        var EventListener = 'EventListener';
        var addEventListener = 'add'+EventListener;
        var removeEventListener = 'remove'+EventListener;
        var newScrollX, newScrollY;
        var dragged = [];
        var reset = function(i, el) {
            for (i = 0; i < dragged.length;) {
                el = dragged[i++];
                el = el.container || el;
                el[removeEventListener](mousedown, el.md, 0);
                _window[removeEventListener](mouseup, el.mu, 0);
                _window[removeEventListener](mousemove, el.mm, 0);
            }
            // cloning into array since HTMLCollection is updated dynamically
            dragged = [].slice.call(_document.getElementsByClassName('dragscroll'));
            for (i = 0; i < dragged.length;) {
                (function(el, lastClientX, lastClientY, pushed, scroller, cont){
                    (cont = el.container || el)[addEventListener](
                        mousedown,
                        cont.md = function(e) {
                            if (!el.hasAttribute('nochilddrag') ||
                                _document.elementFromPoint(
                                    e.pageX, e.pageY
                                ) == cont
                            ) {
                                pushed = 1;
                                lastClientX = e.clientX;
                                lastClientY = e.clientY;
                                e.preventDefault();
                            }
                        }, 0
                    );
                    _window[addEventListener](
                        mouseup, cont.mu = function() {pushed = 0;}, 0
                    );
                    _window[addEventListener](
                        mousemove,
                        cont.mm = function(e) {
                            if (pushed) {
                                (scroller = el.scroller||el).scrollLeft -=
                                    newScrollX = (- lastClientX + (lastClientX=e.clientX));
                                scroller.scrollTop -=
                                    newScrollY = (- lastClientY + (lastClientY=e.clientY));
                                if (el == _document.body) {
                                    (scroller = _document.documentElement).scrollLeft -= newScrollX;
                                    scroller.scrollTop -= newScrollY;
                                }
                            }
                        }, 0
                    );
                })(dragged[i++]);
            }
        }
        
        if (_document.readyState == 'complete') {
            reset();
        } else {
            _window[addEventListener]('load', reset, 0);
        }
        exports.reset = reset;
    }));
</script>
<?php endif; ?>

<!-- horizontal menu scroller ends -->