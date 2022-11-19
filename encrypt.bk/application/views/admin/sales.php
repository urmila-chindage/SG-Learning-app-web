<?php include_once 'header.php'; ?>
<style>
.slimScrollDiv{top:110px;height:calc(100% - 120px) !important;}
</style>
<ol class="breadcrumb">
    <li class=""><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Sales / Reports</li>
</ol>
<section class="courses-tab base-cont-top">
    <ol class="nav nav-tabs offa-tab">
        <!-- active tab start -->
        <li class="active">
            <a href="<?php echo base_url(); ?>admin/sales/">Monthly Earnings</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>
        </li>
    	<!-- active tab end -->
        <li>
            <a href="<?php echo base_url(); ?>admin/sales/teachers">Teacher Percentage</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>
        </li>
    </ol>
</section>
<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <div class="container-fluid nav-content width-100p nav-js-height">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell">
							January 2015
                        </div>                    
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" placeholder="Search Group" id="group_keyword" type="text">
                                <a class="input-group-addon" id="search_group">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- Group content section  -->
        <!-- ====================== -->
<div class="container-fluid nav-content sales-nav">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow">

                                                       

                            <div class="rTableCell dropdown sales-archive">

                                <a href="#" class="dropdown-toggle sales-filter-date" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> March 2017 <span class="caret"></span></a>
                                    <ul class="dropdown-menu white sales-drop">
                                        <li><a href="javascript:void(0)">February 2017 </a></li>
                                        <li><a href="javascript:void(0)">January 2017 </a></li>
                                        <li><a href="javascript:void(0)">December 2016</a></li>
                                        <li><a href="javascript:void(0)">November 2016</a></li>
                                        <li><a href="javascript:void(0)">October 2016</a></li>
                                        <li><a href="javascript:void(0)">September 2016</a></li>

                                    </ul>

                            </div>

                            <div class="rTableCell sales-search">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" placeholder="Search by Name">
                                    <a class="input-group-addon">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div> 
                            </div>
                            <div class="rTableCell sales-export">
								<a class="btn btn-green selected mt8"><i class="icon icon-upload"></i> EXPORT <ripples></ripples></a>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
            
        <div class="col-sm-12 group-content course-cont-wrap group-top sale-top">


            
                     
            <div class="table course-cont rTable" style="">
            	<div class="rTableRow"> 
                	<div class="rTableCell">         
                    	<span class="icon-wrap-round green sales-icons"><i class="icon icon-graduation-cap"></i></span>
                        <span class="earning-course-content">
                        	<a href="javascript:void(0)" class="normal-base-color grp-click-fn"><span class="earning-course-name">Mathematical Calculation and understanding weird concepts of basic mathematics and its fundamentals</span></a>        
                        </span>
                         <span class="label-active group-total sales-total rel3">300 Sales</span> 
                         <span class="group-total sales-total pull-right">4003.33 $</span>    
                     </div>    
                     <div class="rTableCell pos-rel active-user-custom active-table">        
                     	<span class="active-arrow sales-arrow" style="background: rgb(255, 255, 255);"></span>    
                     </div>
                </div>
                
            	<div class="rTableRow">    
                	<div class="rTableCell">         
                    	<span class="icon-wrap-round green sales-icons"><i class="icon icon-cart1"></i></span>
                        <span class="earning-course-content">
                        	<a href="javascript:void(0)" class="normal-base-color grp-click-fn"><span class="earning-course-name">Mathematical Calculation and understanding weird concepts of basic mathematics and its fundamentals</span></a>        
                        </span>
                         <span class="label-active group-total sales-total rel3">300 Sales</span> 
                         <span class="group-total sales-total pull-right">4003.33 $</span>    
                     </div>    
                     <div class="rTableCell pos-rel active-user-custom">        
                     	<span class="active-arrow sales-arrow" style="background: rgb(255, 255, 255);"></span>    
                     </div>
                </div>
                
            	<div class="rTableRow">    
                	<div class="rTableCell">         
                    	<span class="icon-wrap-round green sales-icons"><i class="icon icon-graduation-cap"></i></span>
                        <span class="earning-course-content">
                        	<a href="javascript:void(0)" class="normal-base-color grp-click-fn"><span class="earning-course-name">Mathematical Calculation and understanding weird concepts of basic mathematics and its fundamentals</span></a>        
                        </span>
                         <span class="label-active group-total sales-total rel3">300 Sales</span> 
                         <span class="group-total sales-total pull-right">4003.33 $</span>    
                     </div>    
                     <div class="rTableCell pos-rel active-user-custom">        
                     	<span class="active-arrow sales-arrow" style="background: rgb(255, 255, 255);"></span>    
                     </div>
                </div>                
                
               <div class="center-block text-center"><a class="btn btn-green selected"> LOAD MORE <ripples></ripples></a></div>
                
             </div>
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>

    <div class="col-sm-6 pad0 right-content">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content no-nav-style pos-abslt nav-js-height width-100p" id="preview_wrapper" style="display: block;">
            <div class="col-sm-12">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell txt-left">
                            <a href="javascript:void(0)" class="select-all-style"><label> <input class="user-checkbox-parent" type="checkbox">  Select All</label><span id="selected_users_count"></span></a>
                        </div>
                        <div class="rTableCell">

                        </div>
                    </div>
                </div>
            </div>
                </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        	<div class="container-fluid right-box">
                <div class="row">
                    <div class="col-sm-12 course-cont-wrap">
                    	<div class="invoice-scroll"> 
                        <div class="table course-cont rTable innerscroll">
                       
                        	<div class="rTableRow sales-invoice-modal" data-toggle="modal" data-target="#monthly-invoice"> 
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>
                             </div>
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div>  
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div>  
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div>                                                         
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div>  
                         	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                          	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                           	<div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div> 
                             <div class="rTableRow sales-invoice-modal">     
                            	<div class="rTableCell sales-sl">1</div>
                                <div class="rTableCell sales-date">Jan 14</div> 
                                <div class="rTableCell sales-candidate">John Doe</div>
                                <div class="rTableCell sales-amt">300.44 $</div>     
                             </div>                                                                                                                                                                
                          </div>
                          </div>
                	  </div>
                 </div>
        	</div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

    
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
$(document).ready(function(e) {
	$(function(){
		$('.right-box').slimScroll({
			height: '100%',
			width: '100%',
			wheelStep : 3,
			distance : '10px'
		});
	});
});
</script>
<?php include_once 'footer.php'; ?>
    <!-- ####### MODAL BOX STARTS ######### -->
    
    
    <!-- Modal pop up contents :: Invite Users -->
    <div class="modal fade in" data-backdrop="static" data-keyboard="false" id="monthly-invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                        <h4 class="modal-title" id="myModalLabel">MONTHLY EARNINGS INVOICE</h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-sm-12">
                                <div class="row pb10">
                                    <div class="col-sm-4">Invoice Date :</div>
                                    <div class="col-sm-8">25-02-2017</div>
                                </div>
                                <div class="row pb10">
                                    <div class="col-sm-4">Purchased By :</div>
                                    <div class="col-sm-8">John Doe</div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                 <div class="modal-footer">
                    <a href="#!." type="button" class="btn btn-green">EXPORT</a>
                    <a type="button" class="btn btn-red" data-dismiss="modal">CLOSE</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users --> 
