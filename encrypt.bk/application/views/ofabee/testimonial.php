<?php include_once 'header.php'; ?>
<section>
    <div class="fundamentals fundamentals-size-reduce">
        <div class="container">
            <div class="container-reduce-width">
                <h2 class="funda-head">Testimonials</h2>
            </div><!--container-reduce-width-->
        </div><!--container-->
    </div><!--fundamentals-->
</section>

<section class="testimonial-section">
    <div class="container">
        <div class="container-reduce-width padding-top-container">
            <div class="testimonial-wrapper">
                
                <?php if(!empty($testimonials)): ?>
                    <?php foreach($testimonials as $testimonial): ?>
                        <!-- =========== -->
                        <div class="col-md-4 col-sm-12 col-xs-12 testimonial-card">
                            <div class="testimonial-author-info">
                                <div class="avatar">
                                <img src="<?php echo testimonial_path() . $testimonial['t_image'] ?>" alt="">
                                </div>
                                <div class="author-details">
                                <h4 class="name"><?php echo $testimonial['t_name']; ?></h4>
                                <h5 class="designation" title="<?php echo $testimonial['t_other_detail']?>"><?php echo $testimonial['t_other_detail']?></h5>
                                </div>
                            </div>
                            <div class="testimonial-writeup">
                                <p><?php echo $testimonial['t_text']; ?></p>
                            </div>
                            <!-- <div class="quote-icon">
                                <svg version="1.1" x="0px" y="0px" width="475.082px" height="475.081px" viewBox="0 0 475.082 475.081" style="enable-background:new 0 0 475.082 475.081;fill: #f5f5f5;width: 75px;height: 75px;" xml:space="preserve"><g><g>
                                    <path d="M164.454,36.547H54.818c-15.229,0-28.171,5.33-38.832,15.987C5.33,63.193,0,76.135,0,91.365v109.632    c0,15.229,5.327,28.169,15.986,38.826c10.66,10.656,23.606,15.988,38.832,15.988h63.953c7.611,0,14.084,2.666,19.414,7.994    c5.33,5.325,7.994,11.8,7.994,19.417v9.131c0,20.177-7.139,37.397-21.413,51.675c-14.275,14.271-31.499,21.409-51.678,21.409    H54.818c-4.952,0-9.233,1.813-12.851,5.427c-3.615,3.614-5.424,7.898-5.424,12.847v36.549c0,4.941,1.809,9.233,5.424,12.848    c3.621,3.613,7.898,5.427,12.851,5.427h18.271c19.797,0,38.688-3.86,56.676-11.566c17.987-7.707,33.546-18.131,46.68-31.265    c13.131-13.135,23.553-28.691,31.261-46.679c7.707-17.987,11.562-36.877,11.562-56.671V91.361c0-15.23-5.33-28.171-15.987-38.828    S179.679,36.547,164.454,36.547z"/>
                                    <path d="M459.089,52.534c-10.656-10.657-23.599-15.987-38.828-15.987H310.629c-15.229,0-28.171,5.33-38.828,15.987    c-10.656,10.66-15.984,23.601-15.984,38.831v109.632c0,15.229,5.328,28.169,15.984,38.826    c10.657,10.656,23.6,15.988,38.828,15.988h63.953c7.611,0,14.089,2.666,19.418,7.994c5.324,5.328,7.994,11.8,7.994,19.417v9.131    c0,20.177-7.139,37.397-21.416,51.675c-14.274,14.271-31.494,21.409-51.675,21.409h-18.274c-4.948,0-9.233,1.813-12.847,5.427    c-3.617,3.614-5.428,7.898-5.428,12.847v36.549c0,4.941,1.811,9.233,5.428,12.848c3.613,3.613,7.898,5.427,12.847,5.427h18.274    c19.794,0,38.684-3.86,56.674-11.566c17.984-7.707,33.541-18.131,46.676-31.265c13.134-13.135,23.562-28.695,31.265-46.679    c7.706-17.983,11.563-36.877,11.563-56.671V91.361C475.078,76.131,469.753,63.19,459.089,52.534z"/>
                                </g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                <g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                </svg>
                            </div> -->
                        </div>
                        <!-- =================/ -->
                    <?php endforeach; ?>
                <?php endif; ?>
                  
            </div><!-- box-wrap -->
        </div>
    </div>
</section>

<?php include_once 'footer.php'; ?> 