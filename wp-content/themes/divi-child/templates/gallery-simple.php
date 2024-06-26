<?php 
    $temp_no = get_the_ID().'_'.$args['index_number'];
    $field_name = $args['slug'].'_'.$args['name'].'_'.$temp_no;
    if($image_ids = get_field($field_name)){
?>
<div id="content-carousel" class="content-carousel-widget" data-carousel="swiper" data-loop="true" data-autoplay="true" data-parallax="true">
  <!--
  You can use: data-items="5" for total items display in single page
  data-autoplay="true" or "false" for autoplay.
  data-loop="true" or "false" for looping the carousel
  data-effect="fade" or "cube" for slide changing effects
  data-direction="horizontal" or "vertical" for sliding direction
  data-initlal="3" for for first active slide
  data-center="true" or "false" for centerize slider
  -->
    <!-- /.controls-->
	<div class="controls">
		<a id="content-carousel-control-prev-unq-<?php echo $temp_no; ?>" data-swiper="prev" class="swiper-button-prev">
		</a>
		<a id="content-carousel-control-next-unq-<?php echo $temp_no; ?>" data-swiper="next" class="swiper-button-next">
		</a>
    </div>
  <!-- /.controls -->

	<div id="content-carousel-container-unq-<?php echo $temp_no; ?>" class="swiper-container" data-swiper="container">
		<div class="swiper-wrapper content-items">
            <?php 
                foreach($image_ids as $image_id){ 
                    $img_attr = acf_get_attachment($image_id);
            ?> 
			<div class="swiper-slide item">
				<div class="slide-image" data-swiper-parallax="-23%">
                <img src="<?php echo $img_attr['sizes']['medium_large']; ?>" alt="<?php echo $img_attr['title']; ?>" />
				</div><!-- /.slide-image -->
      </div><!-- /.item -->
            <?php } ?>
		</div><!-- /.swiper-wrapper -->
    </div><!-- /.swiper-container -->
    
    <!--Slider Bullet Pagination-->
	<div id="content-slider-pagination-unq-<?php echo $temp_no; ?>" class="slider-pagination" data-swiper="pagination"></div><!-- /.slider-pagination -->
</div><!-- /#content-carousel -->
<?php } ?>