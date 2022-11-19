
        <style type="text/css" media="screen">
            .quiz-list{
                border-bottom: 1px solid #ccc;
                height: 40px;
                padding: 10px 0px;
            }
            .quiz-list-title th{
                font-size: 14px;
                font-weight: 600;
                padding: 20px 25px 5px 25px;
                border-bottom: 1px solid #ccc;
            }
            .quiz-list td{
                font-size: 14px;
                padding: 0 25px;
            }
            .quiz-list-avatar{display: inline-block;vertical-align: inherit;padding: 0 15px;}
            .invisible{visibility: hidden;}
            .bold{font-weight: 600;}
            .course-performance-wrapper{
                top: 10px;
                position: relative;
                padding:0 30px;
            }
            .export-btn{
                padding:15px;
            }
            .contact-info{padding: 5px 0;}
            .envelope-icon{
                display: inline-block;
                width: 20px;
                height: 20px;
                margin-right: 10px;
            }
            .envelope-icon svg{width: 20px;height: 20px;vertical-align: text-top;}
            .order-table-title{text-align:center; padding:15px 0;margin-top: 40px;}
            .float-div{float:right;text-align:right}
            .notify-place{position: relative;top: 35px;background: #fff;}
            .order-wrapper{font-size: 25px;}
            table *{text-align:center}
            table{
                width: 100%;
                font-size: 17px;
                margin-bottom: 40px;
            }
            table tr td {padding-top: 15px;}
            .total-strip{
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 35px 10px 35px;
                background: #eaeaea;
                font-size: 14px;
                font-weight: 600;
                background: rgb(37, 15, 46);
                color: #fff;
                margin: 0px;
            }
            .total-strip .total-amount{font-size: 17px;font-weight: 600;}
            .order-detail_table{font-size:14px;}
            .order-detail_table p{margin:0px !important;}
            #order-details-model .modal-body{
                float: right;
                width: 100%;
                background: #f3f3f3;
            }
            #order-details-model .modal-content{
                float: right;
                width: 100%;
                border-radius: 6px;
                overflow: hidden;
            }
            #order-details-model .modal-dialog{min-width:95%;}
            #order-details-model .export-btn-row{margin-top: 30px;}
            .invoice-wrapper{
                margin: 0 auto;
                width: 80%;
                background: #fff;
                padding: 40px;
            }
        </style>
  

       <!-- Manin Iner container start -->

    <div class="col-sm-12 question-block p0">
        <div class="invoice-wrapper">
            <?php
                $users                  = json_decode($orders['ph_user_details'],true);
                $transaction_details    = json_decode($orders['ph_transaction_details'],true);
                $tax                    = json_decode($orders['ph_tax_objects'],true);
                $promocode              = json_decode($orders['ph_promocode'],true);
                // echo "<pre>";print_r($tax['cgst']['percentage']);exit;
                if(!empty($orders)){
                ?> 
            <div class="row p0 notify-place" style="top:0">
                <div class="col-md-7">
                    <h3><span class="glyphicon glyphicon-search"></span><?php echo $users['name']; ?></h3>
                    <div class="contact-info">
                    <span class="envelope-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">
                            <g>
                                <title>background</title>
                                <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/>
                            </g>
                            <g>
                                <title>Layer 1</title>
                                <path id="svg_2" fill="#666666" d="m0.5,18.5l0,91l127,0l0,-91l-127,0zm104.69922,10l-41.24414,28.45654l-42.59082,-28.45654l83.83496,0zm-94.69922,71l0,-66.23242l53.54492,35.77637l53.45508,-36.88184l0,67.33789l-107,0z"/>
                            </g>
                        </svg>
                    </span>
                    <span><?php echo $users['email'] ?></span> 
                    </div>
                    <div class="contact-info">
                    <span class="envelope-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                            <title/>
                                <desc/>
                                <g>
                            <title>background</title><rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/></g>
                            <g>
                                <title>Layer 1</title>
                                <path fill="#666666" id="Shape" d="m3.6,7.8c1.4,2.8 3.8,5.1 6.6,6.6l2.2,-2.2c0.3,-0.3 0.7,-0.4 1,-0.2c1.1,0.4 2.3,0.6 3.6,0.6c0.6,0 1,0.4 1,1l0,3.4c0,0.6 -0.4,1 -1,1c-9.4,0 -17,-7.6 -17,-17c0,-0.6 0.4,-1 1,-1l3.5,0c0.6,0 1,0.4 1,1c0,1.2 0.2,2.4 0.6,3.6c0.1,0.3 0,0.7 -0.2,1l-2.3,2.2l0,0z"/>
                            </g>
                        </svg>
                    </span>
                    <span> +91 <?php echo $users['phone']; ?></span>
                    </div>
                </div>
                <div class="col-md-5">
                    <!-- Order details table -->
                    <table class="order-detail_table">
                    <tr>
                        <td>
                            <p class="text-left">Order Id</p>
                        </td>
                        <td>
                            <p>:</p>
                        </td>
                        <td>
                            <p class="text-right"><span class="order-wrapper" style="font-size: 25px;"><b> <?php echo $orders['ph_order_id'] ?></b></span></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-left">Transaction Id</p>
                        </td>
                        <td>
                            <p>:</p>
                        </td>
                        <td>
                            <p class="text-right"><b><?php echo $orders['ph_transaction_id'] ?></b></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-left">Payment Gateway</p>
                        </td>
                        <td>
                            <p>:</p>
                        </td>
                        <td>
                            <p class="text-right"><?php echo $orders['ph_payment_gateway_used'] ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-left">Payment Date</p>
                        </td>
                        <td>
                            <p>:</p>
                        </td>
                        <td>
                            <p class="text-right"><?php echo $orders['ph_payment_date'] ?></p>
                        </td>
                    </tr>
                    </table>
                    <!-- Order details table ends -->
                </div>
            </div>
            <div class="row">
                <!-- ======== -->
                <div class="col-md-12">
                    <h3 class="order-table-title m0"><b>Payment Details</b></h3>
                    <table style="width: 100%;">
                        <thead class="quiz-list-title">
                        <tr>
                            <th>#</th>
                            <th>Item name</th>
                            <th>Price</th>
                            <?php
                                if(!empty($orders['ph_item_discount_price'])){
                                    ?>
                            <th>Discount Price</th>
                            
                            <?php
                            if(count($promocode)>0){
                                ?>
                            <th>Promocode Discount Rate</th>
                            <?php
                                }
                                }
                                if($orders['ph_tax_type'] == '1'){ 
                                
                                    if(isset($tax['sgst']['percentage'])){
                                        if(($tax['sgst']['amount']) > 0){
                                        ?>
                            <th>SGST (<b><?php echo $tax['sgst']['percentage'] ?>%</b>)</th>
                            <?php
                                }
                                }
                                if(isset($tax['cgst']['percentage'])){ 
                                if(($tax['cgst']['amount']) > 0){
                                ?>
                            <th>CGST (<b><?php echo $tax['cgst']['percentage'] ?>%</b>)</th>
                            <?php
                                }
                                }
                                }
                                
                                ?>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $orders['ph_item_name'] ?></td>
                            <td>&#8377; <?php echo $orders['ph_item_base_price'] ?></td>
                            <?php
                                if(!empty($orders['ph_item_discount_price'])){
                                    ?>
                            <td>&#8377; <?php echo $orders['ph_item_discount_price'] ?></td>
                            <?php
                            if(isset($promocode['discount_rate']))
                            {
                            ?>
                            <td>&#8377; <?php echo $promocode['discount_rate'] ?></td>
                            <?php
                                }
                                }
                                if($orders['ph_tax_type'] == '1'){
                                
                                    if(($tax['sgst']['amount']) > 0){
                                    ?>
                            <td>&#8377; <?php echo $tax['sgst']['amount'] ?></td>
                            <?php
                                }
                                if(($tax['cgst']['amount']) > 0){
                                ?>
                            <td>&#8377; <?php echo $tax['cgst']['amount'] ?></td>
                            <?php
                                }
                                }
                                
                                ?>
                            <td>&#8377; <?php echo $orders['ph_item_amount_received'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="total-strip">
                        <div>Total</div>
                        <div class="total-amount">&#8377; <?php echo $orders['ph_item_amount_received'] ?></div>
                    </div>
                    <?php
                        }else{
                        ?>
                    <p>No Data to Display</p>
                    <?php
                        }
                        ?>
                </div>
                <!-- =========== -->
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="text-center export-btn-row">
        <?php if($orders['ph_status']=='1' && $orders['ph_payment_mode'] !='2' && $orders['ph_item_amount_received'] > 0): ?>
            <a onClick="$('#closePopup').click();" href="<?php echo admin_url().'orders/pdf/'.$id ?>" target="_blank" >
                <button style="margin:0px;" class="btn btn-green">Order Invoice</button>
            </a>
        <?php endif;?>
    </div>

   