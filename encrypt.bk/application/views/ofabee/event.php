<?php include('header.php'); ?>
<section>
    <div class="dynamic-page events-container">
        <div class="container">
            <div class="container-reduce-width">
                
            <div class="event-title-row align-items-center">
                <h3 class="dynamic-heading"><?php echo $data['events']['ev_name']; ?></h3>
                <?php if(isset($data['events']['ev_date'])): ?>
                    <h3  class="dynamic-heading"><?php echo date('d M, Y',strtotime($data['events']['ev_date'])); ?></h3>
                    <h3 ><?php echo date('g:i A',strtotime($data['events']['ev_time'])); ?></h3>
                
                <?php endif; ?>
            </div>

            <div class="dynamic-content">
                <?php echo isset($data['events']['ev_description'])?$data['events']['ev_description']:''; ?>
            </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

