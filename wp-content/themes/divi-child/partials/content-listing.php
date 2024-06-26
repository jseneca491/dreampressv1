<div class="listing-container">
    <?php
        //checks what page is currently in
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = array(
            //'post_type' => 'post_type_name',
            'post_status' => 'publish',
            'posts_per_page' => 6, 
            'orderby' => 'date', 
            'order' => 'DESC',
            'paged' => $paged,
        );

        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post();
    ?>    
    <div class="listing-item">
        <h2><?php the_title(); ?></h2>

        <div class="content-item">
            <?php 
                //the_content(); 
                $content = get_the_content();
                echo wp_trim_words($cotent, 15, '...');
            ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn-md">Read more</a>
    </div>
    <?php 
        endwhile;
    ?>
    
    <!-- Pagination Section -->
    <div class="listing-pagination">
        <?php 
            $total_pages = $loop->max_num_pages;

            if ($total_pages > 1){

                $current_page = max(1, get_query_var('paged'));

                echo paginate_links(array(
                    'base' => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
                    'format' => '?page=%#%',
                    'type'	=> 'list',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text'    => __('«'),
                    'next_text'    => __('»'),
                ));
            }
        ?>
    </div>
    <?php wp_reset_postdata(); ?>
</div>