<?php
$search = sanitize_text_field( $_POST[ 'live_search' ] );

$q = new WP_Query(
    array(
        's'	=> $search,
        'posts_per_page' => 10,
    )
);

if( $q->have_posts() ) {
    while( $q->have_posts() ) : $q->the_post();
?>
    <a href='<?php the_permalink(); ?>' class='livesearch-item'>    
        <div class='title'>
            <h6><?php the_title(); ?></h6>
        </div>
        <div class='author'>
            by: <span><?php the_author(); ?></span>
        </div>
        <div class='description'>
            <?php echo wp_trim_words(get_the_content(), 20, '...'); ?>
        </div>
    </a>
<?php
    endwhile;
}else{
        echo '<div class="livesearch-item nothing-item"><h4>Nothing Found.</h4></div>';
}
?>