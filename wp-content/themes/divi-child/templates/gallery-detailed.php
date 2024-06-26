<?php 
$temp_no = get_the_ID().'_'.$args['index_number'];
$field_name = $args['slug'].'_'.$args['name'].'_'.$temp_no;
d(get_field($field_name));
if($image_ids = get_field('field_602a13e6671a6',get_the_ID())){
	
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
		<a id="content-carousel-control-prev-unq-1" data-swiper="prev">
			<i class="fa fa-angle-left"></i>
		</a>
		<a id="content-carousel-control-next-unq-1" data-swiper="next">
			<i class="fa fa-angle-right"></i>
		</a>
    </div>
    <!-- /.controls -->

	<div id="content-carousel-container-unq-1" class="swiper-container" data-swiper="container">
		<div class="swiper-wrapper content-items">
            <?php 
                foreach($image_ids as $image_id){ 
                    $img_attr = acf_get_attachment($image_id);
            ?> 
			<div class="swiper-slide item">
				<div class="slide-image" data-swiper-parallax="-23%">
                <img src="<?php echo $img_attr['sizes']['medium_large']; ?>" alt="<?php echo $img_attr['title']; ?>" />
				</div><!-- /.slide-image -->

				<div class="content">
					<h2 class="title" data-swiper-parallax="-300"><a href="#">Holisticl coordinat cross media best practices.</a></h2>

					<div class="meta" data-swiper-parallax="-100">
						<span>Posted by </span>
						<strong>Lionel Rasel</strong>
						<span> - </span>
						<span>Sep 16, 2015</span>
					</div>
				</div><!-- /.content -->
            </div><!-- /.item -->
            <?php } ?>
		</div><!-- /.swiper-wrapper -->
    </div><!-- /.swiper-container -->
    
    <!--Slider Bullet Pagination-->
	<div id="content-slider-pagination-unq-1" class="slider-pagination" data-swiper="pagination"></div><!-- /.slider-pagination -->
</div><!-- /#content-carousel -->
<?php } ?>