<?php
    //GLOBAL FUNCTIONS
	function get_admin_post() {
		$post_id = absint( isset($_GET['post']) ? $_GET['post'] : ( isset($_POST['post_ID']) ? $_POST['post_ID'] : 0 ) );
		$post = $post_id != 0 ? get_post( $post_id ) : false; // Post Object, like in the Theme loop
		return $post;
    }

    //Theme Settings
    if( function_exists('acf_add_options_page') ) {
	
        acf_add_options_page(array(
            'page_title' 	=> 'Theme Settings',
            'menu_title'	=> 'Theme Settings',
            'menu_slug' 	=> 'cd-theme-options',
            'capability'	=> 'edit_posts',
            'redirect'		=> false
        ));
    }

    function list_all_post_types(){
        $choices_arr = array();
        $args = array(
            'public'   => true,
        );
     
        $output = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
     
        $post_types_obj = get_post_types( $args, $output, $operator );

        foreach($post_types_obj as $p_type){
            $choices_arr["Post Type - ".$p_type->name] = "Post Type - ".$p_type->label;
        }

        $taxonomies_obj = get_terms();
        foreach($taxonomies_obj as $tax){
            $choices_arr["Taxonomy - ".$tax->taxonomy] = "Taxonomy - ".$tax->taxonomy;
        }

        acf_add_local_field_group(array(
            'key' => 'group_60261886c82d4',
            'title' => 'Theme Template Locations',
            'fields' => array(
                array(
                    'key' => 'field_602619e1a1f8b',
                    'label' => 'Template',
                    'name' => 'cd_template_options',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $choices_arr,
                    'default_value' => array(
                        0 => 'Post Type - post',
                        1 => 'Post Type - page',
                    ),
                    'allow_null' => 0,
                    'multiple' => 1,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'cd-theme-options',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
        
    }
    add_action('init', 'list_all_post_types');
    
	//Usage [ds_theme_template slug="gallery" name="navigation" number="1"]
	function get_ds_theme_shortcode($attr) {
        if(!empty($attr['slug'])){
            if(!empty($attr['name'])){
                $slug = 'templates/'.$attr['slug'];
                $name = $attr['name'];
                $number = $attr['number'];
                ob_start();
                $args = array('index_number' => $number, 'slug' => $attr['slug'], 'name' => $attr['name']);
                get_template_part("{$slug}","{$name}", $args);
                $local_template = ob_get_clean();
            }else{
                $slug = 'templates/'.$attr['slug'];
                ob_start();
                get_template_part("{$slug}");
                $local_template = ob_get_clean();
            }
        }else{
            $local_template = 'Error on using the shortcode. Slug should not be empty!';
        }
        return $local_template;
    }
    add_shortcode('ds_theme_template', 'get_ds_theme_shortcode');
    
    //Admin Notice
    function my_theme_activation_notice(){
        $path = "advanced-custom-fields-pro/acf.php";
        $link = wp_nonce_url(admin_url('plugins.php?action=activate&plugin='.$path), 'activate-plugin_'.$path);
        ?>
        <div class="notice notice-error is-dismissible">
            <h3>This theme requires <strong style="color: red;">Advance Custom Fields - PRO</strong>.</h3>
            <p><a href="<?php echo $link; ?>" class="button button-primary">Active ACF - PRO</a></p>
        </div>
        <?php
    }

    function require_acf_pro(){
        if(is_plugin_active("advanced-custom-fields-pro/acf.php") != true){
            add_action( 'admin_notices', 'my_theme_activation_notice' );
        }
    }
    //add_action("admin_init", "require_acf_pro");

    //GENERATE LOCATION ARRAY
    function cd_location_array(){
        $loc_array = array();
        if($locations = get_field('cd_template_options', 'option')){
            foreach($locations as $loc){
                if(strpos($loc, 'Post Type') !== false){
                    $loc_val = explode(" - ", $loc);
                    $loc_array[] = array(
                                array(
                                    'param' => 'post_type',
                                    'operator' => '==',
                                    'value' =>  $loc_val[1],
                                ),
                            );
                }
    
                if(strpos($loc, 'Taxonomy') !== false){
                    $loc_val = explode(" - ", $loc);
                    $loc_array[] = array(
                                array(
                                    'param' => 'taxonomy',
                                    'operator' => '==',
                                    'value' =>  $loc_val[1],
                                ),
                            );
                }
            }
        }else{
            $loc_array = array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
            );
        }
        return $loc_array;
    }

    //Populate Theme Options
    if( function_exists('acf_add_local_field_group') ){
        acf_add_local_field_group(array(
            'key' => 'group_60135ed77c3b6',
            'title' => 'Theme Templates',
            'fields' => array(
                array(
                    'key' => 'field_60135ee7e2cc7',
                    'label' => 'Add Template',
                    'name' => 'cd_add_template',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => '',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_60135eefe2cc8',
                            'label' => 'Select Template',
                            'name' => 'cd_select_template',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'None' => 'None',
                                'Gallery - Simple' => 'Gallery - Simple',
                                'Gallery - Detailed' => 'Gallery - Detailed',
                                'Testimonials' => 'Testimonials',
                            ),
                            'default_value' => false,
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'return_format' => 'value',
                            'ajax' => 0,
                            'placeholder' => '',
                        ),
                        array(
                            'key' => 'field_60135f31e2cc9',
                            'label' => 'Shortcode',
                            'name' => 'cd_shortcode',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => 'divisass-shortcode',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_60135f41e2cca',
                            'label' => 'Custom Field Key',
                            'name' => 'custom_field_key',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => 'divisass-fieldkey',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => '',
                        ),
                    ),
                ),
            ),
            'location' => cd_location_array(),
            'menu_order' => 1,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }

    //Template Functions
    function save_custom_field_key(){
        if($post = get_admin_post()){
            $post_id = $post->ID;
            $themeplates = get_field('cd_add_template', $post_id);
            $str_keys = get_field('stored_field_keys', $post_id);
            if($themeplates != false){
                foreach(@$themeplates as $key_index => $template){
                    $save_index = $key_index + 1;
                    $custom_field_key = $themeplates[$key_index]['custom_field_key'];
                    $cd_shortcode_key = $themeplates[$key_index]['cd_shortcode'];
                    
                    if($template['cd_select_template'] == "Gallery - Simple"){
                        $shortcode_str = '[ds_theme_template slug="gallery" name="simple" number="'.$save_index.'"]';
                    }

                    if($template['cd_select_template'] == "Gallery - Detailed"){
                        $shortcode_str = '[ds_theme_template slug="gallery" name="detailed" number="'.$save_index.'"]';
                    }

                    if($template['cd_select_template'] == "Testimonials"){
                        $shortcode_str = '[ds_theme_template slug="testimonials" number="'.$save_index.'"]';
                    }
                    
                    if(empty($custom_field_key)){
                        update_row('cd_add_template', $save_index, array('custom_field_key' => uniqid('field_')), $post_id);
                    }
                    if($shortcode_str != $cd_shortcode_key){
                        update_row('cd_add_template', $save_index, array('cd_shortcode' => $shortcode_str), $post_id);
                    }
                }
            }
            if($themeplates == false && count($str_keys) <= 1){
                delete_row('stored_field_keys', 1, $post_id);
            }
        }
	}
    add_filter("save_post", "save_custom_field_key");

    function populate_gallery_nav_fields($field_arr = false){
        if($field_arr != false){
            foreach($field_arr as $field_data){
                if($field_data['template'] == 'Gallery - Simple'){
                    if( function_exists('acf_add_local_field_group') ){
                        acf_add_local_field_group(array(
                            'key' => 'group_'.$field_data['field key'].'_'.$field_data['post ID'].'_'.$field_data['index'],
                            'title' => $field_data['index'].'.) Gallery - Simple',
                            'fields' => array(
                                array(
                                    'key' => 'field_'.$field_data['field key'],
                                    'label' => 'Gallery',
                                    'name' => 'gallery_simple_'.$field_data['post ID'].'_'.$field_data['index'],
                                    'type' => 'gallery',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'return_format' => 'array',
                                    'preview_size' => 'medium',
                                    'insert' => 'append',
                                    'library' => 'all',
                                    'min' => '',
                                    'max' => '',
                                    'min_width' => '',
                                    'min_height' => '',
                                    'min_size' => '',
                                    'max_width' => '',
                                    'max_height' => '',
                                    'max_size' => '',
                                    'mime_types' => '',
                                ),
                            ),
                            'location' => cd_location_array(),
                            'menu_order' => 2,
                            'position' => 'normal',
                            'style' => 'default',
                            'label_placement' => 'top',
                            'instruction_placement' => 'label',
                            'hide_on_screen' => '',
                            'active' => true,
                            'description' => '',
                        ));
                    }
                }

                if($field_data['template'] == 'Gallery - Detailed'){
                    if( function_exists('acf_add_local_field_group') ){
                        acf_add_local_field_group(array(
                            'key' => 'group_'.$field_data['field key'].'_'.$field_data['post ID'].'_'.$field_data['index'],
                            'title' => $field_data['index'].'.) Gallery - Detailed',
                            'fields' => array(
                                array(
                                    'key' => 'field_'.$field_data['field key'],
                                    'label' => 'Gallery List',
                                    'name' => 'gallery_detailed_'.$field_data['post ID'].'_'.$field_data['index'],
                                    'type' => 'repeater',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'collapsed' => '',
                                    'min' => 0,
                                    'max' => 0,
                                    'layout' => 'row',
                                    'button_label' => 'Add Image',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_image_'.$field_data['field key'],
                                            'label' => 'Image',
                                            'name' => 'image',
                                            'type' => 'image',
                                            'instructions' => '',
                                            'required' => 1,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'return_format' => 'array',
                                            'preview_size' => 'medium',
                                            'library' => 'all',
                                            'min_width' => '',
                                            'min_height' => '',
                                            'min_size' => '',
                                            'max_width' => '',
                                            'max_height' => '',
                                            'max_size' => '',
                                            'mime_types' => '',
                                        ),
                                        array(
                                            'key' => 'field_image_link_'.$field_data['field key'],
                                            'label' => 'Link',
                                            'name' => 'image_link',
                                            'type' => 'url',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_image_title_'.$field_data['field key'],
                                            'label' => 'Title',
                                            'name' => 'image_title',
                                            'type' => 'text',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_description_'.$field_data['field key'],
                                            'label' => 'Description',
                                            'name' => 'image_description',
                                            'type' => 'textarea',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'maxlength' => '',
                                            'rows' => '',
                                            'new_lines' => '',
                                        ),
                                    ),
                                ),
                            ),
                            'location' => cd_location_array(),
                            'menu_order' => 2,
                            'position' => 'normal',
                            'style' => 'default',
                            'label_placement' => 'top',
                            'instruction_placement' => 'label',
                            'hide_on_screen' => '',
                            'active' => true,
                            'description' => '',
                        ));
                    }
                }

                if($field_data['template'] == 'Testimonials'){
                    if( function_exists('acf_add_local_field_group') ){
                        acf_add_local_field_group(array(
                            'key' => 'group_'.$field_data['field key'].'_'.$field_data['post ID'].'_'.$field_data['index'],
                            'title' => $field_data['index'].'.) Testimonials',
                            'fields' => array(
                                array(
                                    'key' => 'field_'.$field_data['field key'],
                                    'label' => 'Testimonial List',
                                    'name' => 'testimonial_list_'.$field_data['post ID'].'_'.$field_data['index'],
                                    'type' => 'repeater',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'collapsed' => '',
                                    'min' => 0,
                                    'max' => 0,
                                    'layout' => 'row',
                                    'button_label' => 'Add Testimonial',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_image_'.$field_data['field key'],
                                            'label' => 'Image',
                                            'name' => 'image',
                                            'type' => 'image',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'return_format' => 'array',
                                            'preview_size' => 'medium',
                                            'library' => 'all',
                                            'min_width' => '',
                                            'min_height' => '',
                                            'min_size' => '',
                                            'max_width' => '',
                                            'max_height' => '',
                                            'max_size' => '',
                                            'mime_types' => '',
                                        ),
                                        array(
                                            'key' => 'field_first_text_'.$field_data['field key'],
                                            'label' => 'First Text Title',
                                            'name' => 'first_text_title',
                                            'type' => 'text',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_second_text_'.$field_data['field key'],
                                            'label' => 'Second Text Title',
                                            'name' => 'second_text_title',
                                            'type' => 'text',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_description_'.$field_data['field key'],
                                            'label' => 'Description',
                                            'name' => 'description',
                                            'type' => 'textarea',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'maxlength' => '',
                                            'rows' => '',
                                            'new_lines' => '',
                                        ),
                                    ),
                                ),
                            ),
                            'location' => cd_location_array(),
                            'menu_order' => 2,
                            'position' => 'normal',
                            'style' => 'default',
                            'label_placement' => 'top',
                            'instruction_placement' => 'label',
                            'hide_on_screen' => '',
                            'active' => true,
                            'description' => '',
                        ));
                    }
                }
            }
        }
    }
    
    function show_template_fields(){
        if($post = get_admin_post()){
            $post_id = $post->ID;
            $themeplates = get_field('cd_add_template', $post_id);
            $field_arr = array();
            if(is_array($themeplates)){
                foreach(@$themeplates as $temp_index => $template){
                    $num_index = $temp_index + 1;
                    $template_name = $themeplates[$temp_index]['cd_select_template'];

                    if(empty($themeplates[$temp_index]['custom_field_key'])){
                        $unique_key = uniqid();
                    }else{
                        $unique_key = $themeplates[$temp_index]['custom_field_key'];
                    }

                    $field_arr[] = array(
                        'template' => $template_name,
                        'post ID' => $post_id,
                        'field key' => $unique_key,
                        'index' => $num_index
                    );
                }
                populate_gallery_nav_fields($field_arr);
            }
        }
    }
    add_action("init", "show_template_fields");
?>