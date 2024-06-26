<?php
add_action('admin_menu','dreampress_action_options');
add_action( 'admin_init', 'dreampress_register_settings' );
add_action( 'template_redirect', 'dreampress_maintenance_condition', 9);
function dreampress_maintenance_condition(){
    global $post, $wp;
    $val_option = get_option('dreampress_page_setting_field');
    $page_id = isset( $val_option ) ? esc_attr( $val_option ) : '';
    if ( $page_id != '' && $page_id != NULL ) {
					
        $path_redirect_to = get_permalink( $page_id );
    
        // Check if user is logged in
        if ( is_user_logged_in() ) {
            return false;
        }
        
        
        // Check for custom login page
        $admin_url = get_admin_url( null, '/' );
        $site_url  = site_url();
        $admin_url = str_replace( $site_url, '', $admin_url );
        $admin_url = str_replace( '/', '', $admin_url );
        
        if ( preg_match("/login|admin|$admin_url/i", $_SERVER['REQUEST_URI'] ) > 0 ) {
            
            return false;
        }
        
        
        // Sets the headers to prevent caching for the different browsers and other popular plugins
        nocache_headers();
        
        if ( !defined('DONOTCACHEPAGE') ) {
            define( 'DONOTCACHEPAGE', true );
        }
        
        if ( !defined( 'DONOTCDN' ) ) {
            define( 'DONOTCDN', true );
        }
        
        if ( !defined( 'DONOTCACHEOBJECT' ) ) {
            define( 'DONOTCACHEOBJECT', true );
        }
        
        if ( !defined( 'DONOTCACHEDB' ) ) {
            define( 'DONOTCACHEDB', true );
        }
        
        
        // Check current url to prevent redirect loop
        $current_url = trailingslashit( home_url( $wp->request ) );
        
        if ( $current_url != $path_redirect_to ) {
        
            wp_redirect( $path_redirect_to );
            exit;
        }
        else {
            
            return false;
        }
    }
}


function dreampress_action_options(){

    add_menu_page(
          'Dreampress Options',
          'Dreampress Options',
          'manage_options',
          'dreampress-maintenance-mode',
          'dreampress_maintenance_mode_callback',
          'dashicons-superhero',
           80
    );
    
    add_submenu_page(
          'dreampress-maintenance-mode',
          'Maintenance Mode',
          'Maintenance Mode',
          'manage_options',
          'dreampress-maintenance-mode',
          'dreampress_maintenance_mode_callback'
    );

    add_submenu_page(
        'dreampress-maintenance-mode',
        'Age Verification',
        'Age Verification',
        'manage_options',
        'dreampress-age-verification',
        'dreampress_age_verification_callback'
    );

    add_submenu_page(
        'dreampress-maintenance-mode',
        'Page Load Settings',
        'Page Load Settings',
        'manage_options',
        'dreampress-page-loader',
        'dreampress_page_load_callback'
    );
}



function dreampress_register_settings(){
    register_setting( 
        'dreampressmaintenance_settings', 
        'dreampress_page_setting_field', 
    );
    
    add_settings_section(
        'dreampressmaintenance_description',
        '',
        'dreampressmaintenance_description',
        'dreampress-maintenance-settings'
    );

    function dreampressmaintenance_description() {
        echo '';
                
    }

    $get_options = array();
    $pages = get_pages();
    $field_name = "dreampress_page_setting_field";

    foreach($pages as $page) {

        $get_options[] = array(
            'title' => $page->post_title,
            'value' => $page->ID
        );
    }

    $options = array( 
        'type' => 'select',
        'name' => $field_name,
        'placeholder' => 'Choose a page...',
        'options' => $get_options
    );

    add_settings_field(
        'dreampress_page_setting_field', 
        'Choose "Coming Soon" page', 
        'dreampress_page_select_callback', 
        'dreampress-maintenance-settings', 
        'dreampressmaintenance_description',
        $options
    );

    // Register settings for the age Verification page
    register_setting( 
        'dreampress_age_verification_settings', 
        'dreampress_age_verification_field', 
    );
    
    add_settings_section(
        'dreampress_age_verification_description',
        '',
        'dreampress_age_verification_description',
        'dreampress-age-verification-settings'
    );

    function dreampress_age_verification_description() {
        echo '';
    }

    $options = array( 
        'type' => 'select',
        'name' => 'dreampress_age_verification_field',
        'placeholder' => 'Select Age Verification',
        'options' => array(
            array(
                'title' => 'Disabled',
                'value' => ''
            ),
            array(
                'title' => '18+',
                'value' => '18'
            ),
            array(
                'title' => '21+',
                'value' => '21'
            ),
            array(
                'title' => '25+',
                'value' => '25'
            )
        )
    );

    add_settings_field(
        'dreampress_age_verification_field', 
        'Select Age Verification', 
        'dreampress_age_verification_field_callback', 
        'dreampress-age-verification-settings', 
        'dreampress_age_verification_description',
        $options
    );
}


function dreampress_page_select_callback($options){
    $option_val = get_option('dreampress_page_setting_field');
    $selected = isset( $option_val ) ? esc_attr( $option_val ) : '';

    echo "<select name='dreampress_page_setting_field' id='dreampress_page_setting_field'>";
    ?>
        <option value=""><?php  if ( $selected != '' && $selected != NULL ) { echo "Disable"; }else{  echo "Disabled"; } ?></option>
    <?php
        foreach($options['options'] as $option){
            $value = $option['value'];
            $label = $option['title'];
    ?>
        <option <?php selected( $selected, $option['value'] ); ?> value="<?php print $value; ?>"><?php print $label; ?></option>
    <?php
        }
    echo'</select>';
}

function dreampress_maintenance_mode_callback(){
    ?>
        <div class="wrap">
            <h1>Maintenance Mode</h1>
            <form id="dreampressmaintenance_settings" method="post" action="options.php">
            <?php
                settings_fields( 'dreampressmaintenance_settings' );
                do_settings_sections( 'dreampress-maintenance-settings' );
                submit_button();
            ?>
            </form>
        </div>
    <?php
}

function dreampress_age_verification_callback() {
    ?>
    <div class="wrap">
        <h1>Age Verification Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('dreampress_age_verification_settings'); ?>
            <?php
            add_settings_section(
                'dreampress_age_verification_section',
                '',
                'dreampress_age_verification_section_callback',
                'dreampress-age-verification-settings'
            );
            ?>
            <?php do_settings_sections('dreampress-age-verification-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function dreampress_age_verification_section_callback() {
    echo '';
}

function dreampress_age_verification_field_callback($options){
    $option_val = get_option('dreampress_age_verification_field');
    $selected = isset( $option_val ) ? esc_attr( $option_val ) : '';

    echo "<select name='dreampress_age_verification_field' id='dreampress_age_verification_field'>";
    ?>
        <option value=""><?php  if ( $selected != '' && $selected != NULL ) { echo "Disable"; }else{  echo "Disabled"; } ?></option>
        <option <?php selected( $selected, '18' ); ?> value="18">18+</option>
        <option <?php selected( $selected, '21' ); ?> value="21">21+</option>
        <option <?php selected( $selected, '25' ); ?> value="25">25+</option>
    <?php
    echo'</select>';
}

function load_age_verification_script(){
    $dpag_age = get_option('dreampress_age_verification_field');
    if($dpag_age != NULL){
    ?>
    <style>
        .body{
            position: relative;
        }
        .dpag-popup{
            position: fixed;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            top: 0;
            left: 0;
            z-index: 99998;
        }

        .dpag-container {
            background: #fdfdfd;
            padding: 15px 30px;
        }

        .dpag-logo {
            text-align: center;
        }

        .dpag-logo img{
            width: 100px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .dpag-content {
            text-align: center;
        }

        .dpag-content p{
            margin-bottom: 30px;
        }

        .dpag-content button{
            margin-bottom: 30px;
        }
    </style>
    <div class="dpag-popup">
        <div class="dpag-container">
            <?php 
                if(!empty(et_get_option( 'divi_logo' ))){
            ?>
            <div class="dpag-logo">
                <img src="<?php echo et_get_option( 'divi_logo' ); ?>" alt="<?php echo bloginfo('name'); ?>">
            </div>
            <?php } ?>
            <div class="dpag-content">
                <h2>Are you <?php echo $dpag_age ?> or older?</h2>
                <p>This website requires you to be <?php echo $dpag_age ?> years of age or older.<br/> Please Verify your age to view the content, or click 'EXIT' to leave.</p>
                <button id="dpag_yes" name="dpag_yes">Im over <?php echo $dpag_age ?></button> <button id="dpag_no" name="dpag_no">EXIT</button> 
            </div>
        </div>
    </div>
    <script>
        $(function() {
            //Check it the user has been accepted the agreement
            if (document.cookie != "accepted") {
                $(".dpag-popup").show();
            }else{
                $(".dpag-popup").hide();
            }

            $('#dpag_yes').on('click', function(e) {
                var targeted_popup_class = jQuery(this).attr('data-popup-close');
                $(".dpag-popup").fadeOut(350);
                //Set a cookie to remember the state
                document.cookie = "accepted";
                e.preventDefault();
            });

            // Popup Age Verification
            //Check it the user has been accpeted the agreement
            if (!localStorage.getItem('accepted')) {
                $(".dpag-popup").show();
            }

            $('#dpag_yes').on('click', function(e) {
                var targeted_popup_class = jQuery(this).attr('data-popup-close');
                $(".dpag-popup").fadeOut(350);

                //Set a cookie to remember the state
                localStorage.setItem('accepted', true);
                e.preventDefault();
            });

            $('#dpag_no').on('click', function(e) {
                //Set a cookie to remember the state
                $('.dpag-content h2').html('Sorry!');
                $('.dpag-content p').html('You are not allowed to visit this wesbite.');
                $('.dpag-content button').remove();
                e.preventDefault();
            });
        });
    </script>
    <?php
        }
    }
add_action( 'wp_footer', 'load_age_verification_script');

function dreampress_page_load_callback(){
    ?>
        <div class="wrap">
            <h1>Feature coming soon...</h1>
        </div>
    <?php
}


