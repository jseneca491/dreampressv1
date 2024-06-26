<?php

/**
 * JCH Optimize - Plugin to aggregate and minify external resources for
 * optmized downloads
 *
 * @author    Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2014 Samuel Marshall
 * @license   GNU/GPLv3, See LICENSE file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

use JchOptimize\Core\Ajax;
use JchOptimize\Core\Admin;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Logger;
use JchOptimize\Platform\Cache;
use JchOptimize\Platform\Settings;
use JchOptimize\Platform\Html;
use JchOptimize\Platform\Plugin;

add_action('admin_menu', 'add_jch_optimize_menu');

function add_jch_optimize_menu()
{
        $hook_suffix = add_options_page(__('JCH Optimize Pro Settings', 'jch-optimize'), 'JCH Optimize Pro', 'manage_options', 'jchoptimize-settings',
                'jch_options_page');

        add_action('admin_enqueue_scripts', 'jch_load_resource_files');
        add_action('admin_head-' . $hook_suffix, 'jch_load_scripts');
        add_action('load-' . $hook_suffix, 'jch_initialize_settings');
}

function jch_options_page()
{
        if (version_compare(PHP_VERSION, '5.3.0', '<'))
        {

                ?>

                <div class="notice notice-error">
                        <p> <?php _e('This plugin requires PHP 5.3.0 or greater to run. Your installed version is: ' . PHP_VERSION, 'jch-optimize') ?></p>
                </div>
                <?php

        }

        ?>
        <div>
                <h2>JCH Optimize Pro Settings</h2>
                <form action="options.php" method="post" class="jch-settings-form">
                        <div style="width: 90%;">
                                <input name="Submit" type="submit" class="button button-primary"
                                       value="<?php esc_attr_e('Save Changes', 'jch-optimize'); ?>"/>
                                <?php
                                $subscribe_url = 'https://www.jch-optimize.net/subscribe/levels.html/#wordpress';
                                /* ##<freecode>##
                                  ?>
                                  <a class="right button button-secondary" href="<?php echo $subscribe_url; ?>" target="_blank"><?php _e('Upgrade to Pro', 'jch-optimize'); ?></a>

                                  <?php
                                  ##<freecode>## */

                                ?>

                        </div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                                <li class="active">
                                        <a href="#description" data-toggle="tab">
                                                <?php _e('Description', 'jch-optimize') ?>
                                        </a>
                                </li>

                                <?php

                                if (version_compare(PHP_VERSION, '5.3.0', '>='))
                                {

                                        ?>

                                        <li>
                                                <a href="#basic" data-toggle="tab">
                                                        <?php _e('Combine CSS/JS', 'jch-optimize') ?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="#exclude" data-toggle="tab">
                                                        <?php _e('Exclude Options', 'jch-optimize') ?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="#sprite" data-toggle="tab">
                                                        <?php _e('Basic Features', 'jch-optimize') ?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="#pro" data-toggle="tab">
                                                        <?php _e('Advanced Features', 'jch-optimize') ?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="#images" data-toggle="tab">
                                                        <?php _e('Optimize Images', 'jch-optimize') ?>
                                                </a>
                                        </li>
                                        <?php

                                }

                                ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                                <div class="tab-pane active" id="description">
                                        <div id="extension-container" style="text-align:left;">
                                                <h1>JCH Optimize Pro Plugin</h1>
                                                <h3>(Version pro-2.6.2)</h3>
                                                <p><?php _e('This plugin speeds up your website by performing a number of front end optimizations to your website automatically. These optimizations reduce both your webpage size and the number of http requests required to download your webpages and results in reduced server load, lower bandwidth requirements, and faster page loading times.', 'jch-optimize') ?></p>
                                                <p><img src="<?php echo JCH_PLUGIN_URL ?>/logo.png"
                                                        style="float: none"/></p>
                                                <h2><?php _e('Major Features', 'jch-optimize') ?></h2>
                                                <ul>
                                                        <li><?php _e('Combine and gzip CSS and javascript files', 'jch-optimize') ?></li>
                                                        <li><?php _e('Minify combined files and HTML', 'jch-optimize') ?></li>
                                                        <li><?php _e('Combine select background images into a sprite', 'jch-optimize') ?></li>
                                                        <li><?php _e('Page cache', 'jch-optimize') ?></li>
                                                        <li><?php _e('CDN support', 'jch-optimize') ?></li>
                                                        <li><?php _e('Lazy-load images', 'jch-optimize') ?></li>
                                                        <li><?php _e('Optimize CSS Delivery', 'jch-optimize') ?></li>
                                                </ul>
                                                <h2><?php _e('Instructions', 'jch-optimize') ?></h2>
                                                <p><?php _e('First deactivate all page caching features and plugins, then use the \'Automatic Settings\' <span class="notranslate">(Minimum - Optimum)</span> to configure the plugin. The \'Automatic Settings\' are concerned with the combining of the CSS and javascript files, and the management of the combined files, and automatically sets the options in the \'Automatic Settings Groups\'. Use the Exclude options to exclude files or plugins that don\'t work so well with JCH Optimize. You can then try the other optimization features in turn to further configure the plugin and optimize your site. Flush all your cache before re-enabling caching plugins.', 'jch-optimize') ?></p>
                                                <h2><?php _e('Support', 'jch-optimize') ?></h2>
                                                <p><?php printf(wp_kses(__('First check out the <a href="%1$s" target="_blank">documentation</a>, particularly the <a href="%2$s" target="_blank">Getting Started</a> and <a href="%3$s" target="_blank">How to optimize your site</a> pages on the plugin\'s website to learn how to use and configure the plugin.', 'jch-optimize'), array(
                                                                'a' => array(
                                                                        'href'   => array(),
                                                                        'target' => array()
                                                                )
                                                        )), esc_url('https://www.jch-optimize.net/documentation.html'), esc_url('https://www.jch-optimize.net/documentation/getting-started.html'), esc_url('https://www.jch-optimize.net/documentation/optimizing-your-site.html')); ?></p>
                                                <p><?php printf(wp_kses(__('Read <a href="%s" target="_blank">Here</a> for some troubleshooting guides to resolve some common issues users generally encounter with using the plugin.', 'jch-optimize'), array(
                                                                'a' => array(
                                                                        'href'   => array(),
                                                                        'target' => array()
                                                                )
                                                        )), esc_url('https://www.jch-optimize.net/documentation/troubleshooting.html')); ?></p>
                                                <p><?php printf(wp_kses(__('You\'ll need a subscription to submit tickets to get premium support in configuring the plugin to resolve conflicts so <a href="%1$s" target="_blank">subscribe</a> to <em>JCH Optimize Pro for WordPress</em> and access your account to submit a ticket. Otherwise you can use the <a href="%2$s" target="_blank" >WordPress support system</a> to submit support requests.', 'jch-optimize'), array(
                                                                'a'  => array(
                                                                        'href'   => array(),
                                                                        'target' => array()
                                                                ),
                                                                'em' => array()
                                                        )), esc_url($subscribe_url), esc_url('https://wordpress.org/support/plugin/jch-optimize/')); ?></p>
                                                <p class="notice notice-info"
                                                   style="margin: 1em 0; padding: 10px 12px"><?php printf(wp_kses(__('If you use this plugin please consider posting a review on the plugin\'s <a href="%s" target="_blank" >WordPress page</a>. If you\'re having problems, please submit for support and give us a chance to resolve your issues before reviewing. Thanks.', 'jch-optimize'), array(
                                                                'a' => array(
                                                                        'href'   => array(),
                                                                        'target' => array()
                                                                )
                                                        )), esc_url('https://wordpress.org/support/plugin/jch-optimize/reviews/')); ?></p>

                                        </div>
                                </div>
                                <?php do_settings_sections('jch-sections'); ?>
                        </div>

                        <?php settings_fields('jch_options'); ?>
                        <?php

                        $options = get_option('jch_options');

                        ?>
                        <input type="hidden" id="jch_options_hidden_containsgf" name="jch_options[hidden_containsgf]"
                               value="<?php echo !empty($options['hidden_containsgf']) ? $options['hidden_containsgf'] : ''; ?>">
                        <input type="hidden" id="jch_options_hidden_api_secret" name="jch_options[hidden_api_secret]"
                               value="11e603aa">
                        <input name="Submit" class="button button-primary" type="submit"
                               value="<?php esc_attr_e('Save Changes', 'jch-optimize'); ?>"/>
                </form>
        </div>
        <?php

}

add_action('admin_init', 'jch_register_options');

function jch_register_options()
{
        register_setting('jch_options', 'jch_options', 'jch_options_validate');
}

function jch_initialize_settings()
{
        wp_register_style('jch-bootstrap-css', JCH_PLUGIN_URL . 'media/css/bootstrap/bootstrap.css', array(), JCH_VERSION);
        wp_register_style('jch-admin-css', JCH_PLUGIN_URL . 'media/css/admin.css', array(), JCH_VERSION);
        wp_register_style('jch-fonts-css', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css');
        wp_register_style('jch-chosen-css', JCH_PLUGIN_URL . 'media/css/chosen/jquery.chosen.min.css', array(), JCH_VERSION);
        wp_register_style('jch-wordpress-css', JCH_PLUGIN_URL . 'media/css/wordpress.css', array(), JCH_VERSION);

        wp_register_script('jch-bootstrap-js', JCH_PLUGIN_URL . 'media/js/bootstrap/bootstrap.min.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-wordpress-js', JCH_PLUGIN_URL . 'media/js/wordpress.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-tabsstate-js', JCH_PLUGIN_URL . 'media/js/bootstrap/tabs-state.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-adminutility-js', JCH_PLUGIN_URL . 'media/js/admin-utility.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-chosen-js', JCH_PLUGIN_URL . 'media/js/chosen/jquery.chosen.min.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-collapsible-js', JCH_PLUGIN_URL . 'media/js/jquery.collapsible.js', array('jquery'), JCH_VERSION, true);

        ##<procode>##
        wp_register_style('jch-progressbar-css', JCH_PLUGIN_URL . 'media/css/pro-jquery-ui-progressbar.css', array(), JCH_VERSION);
        wp_register_style('jch-filetree-css', JCH_PLUGIN_URL . 'media/css/pro-jquery.filetree.css', array(), JCH_VERSION);

        wp_register_script('jch-filetree-js', JCH_PLUGIN_URL . 'media/js/pro-jquery.filetree.js', array('jquery'), JCH_VERSION, true);
        wp_register_script('jch-proadminutility-js', JCH_PLUGIN_URL . 'media/js/pro-admin-utility.js', array('jquery'), JCH_VERSION, true);
        ##</procode>##

        if (version_compare(PHP_VERSION, '5.3.0', '<'))
        {
                return;
        }

        global $jch_redirect;
        $jch_redirect = false;

        check_jch_tasks();
        jch_get_cache_info();
        jch_redirect();
        jch_get_admin_object();

        if (get_transient('jch_notices'))
        {
                add_action('admin_notices', 'jch_send_notices');
        }

        //Combine CSS/Js
        add_settings_section('jch_basic_pre', '', 'jch_basic_pre_section_text', 'jch-sections');
        add_settings_field('jch_options_combine_files_enable', __('Enable', 'jch-optimize'), 'jch_options_combine_files_enable_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_auto_settings', __('Automatic Settings', 'jch-optimize'), 'jch_options_auto_settings_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_cache_lifetime', __('Cache lifetime', 'jch-optimize'), 'jch_options_cache_lifetime_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_html_minify_level', __('HTML Minification Level', 'jch-optimize'), 'jch_options_html_minify_level_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_htaccess', __('Combine files delivery', 'jch-optimize'), 'jch_options_htaccess_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_try_catch', __('Use <span class="notranslate">try-catch</span>', 'jch-optimize'), 'jch_options_try_catch_string', 'jch-sections', 'jch_basic_pre');
        //Miscellaneous settings
        add_settings_section('jch_basic_misc', '', 'jch_basic_misc_section_text', 'jch-sections');
        add_settings_field('jch_options_utility_settings', __('Utility Settings', 'jch-optimize'), 'jch_options_utility_settings_string', 'jch-sections', 'jch_basic_misc');
        add_settings_field('jch_options_order_plugin', __('Order plugin', 'jch-optimize'), 'jch_options_order_plugin_string', 'jch-sections', 'jch_basic_misc');
        add_settings_field('jch_options_debug', __('Debug plugin', 'jch-optimize'), 'jch_options_debug_string', 'jch-sections', 'jch_basic_misc');
        add_settings_field('jch_options_disable_logged_in_users', __('Disable logged in Users', 'jch-optimize'), 'jch_options_disable_logged_in_users_string', 'jch-sections', 'jch_basic_misc');

        add_settings_section('jch_basic_auto', '', 'jch_basic_auto_section_text', 'jch-sections');
        //Automatic basic settings
        add_settings_field('jch_options_auto_basic', '<strong><i>' . __('Automatic Basic Settings', 'jch-optimize') . '</i></strong><hr>', 'jch_options_spacer_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_css', __('Combine CSS Files', 'jch-optimize'), 'jch_options_css_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_javascript', __('Combine Javascript Files', 'jch-optimize'), 'jch_options_javascript_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_gzip', __('Gzip Combined Files', 'jch-optimize'), 'jch_options_gzip_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_css_minify', __('Minify Combined CSS File', 'jch-optimize'), 'jch_options_css_minify_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_js_minify', __('Minify Combined Javascript File', 'jch-optimize'), 'jch_options_js_minify_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_html_minify', __('Minify HTML', 'jch-optimize'), 'jch_options_html_minify_string', 'jch-sections', 'jch_basic_auto');
        //Automatic exclude settings
        add_settings_field('jch_options_auto_exclude', '<strong><i>' . __('Automatic Exclude Settings', 'jch-optimize') . '</i></strong><hr>', 'jch_options_auto_exclude_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_includeAllExtensions', __('Include files from all plugins', 'jch-optimize'), 'jch_options_includeAllExtensions_string', 'jch-sections', 'jch_basic_auto');
        //Automatic advanced settings
        add_settings_field('jch_options_auto_advanced', '<strong><i>' . __('Automatic Advanced Settings', 'jch-optimize') . '</i></strong><hr>', 'jch_options_auto_advanced_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_replaceImports', __('Replace <span class="notranslate">@imports</span> in CSS', 'jch-optimize'), 'jch_options_replaceImports_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_phpAndExternal', __('Include PHP files and files from external domains', 'jch-optimize'), 'jch_options_phpAndExternal_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_inlineStyle', __('Include inline CSS styles', 'jch-optimize'), 'jch_options_inlineStyle_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_inlineScripts', __('Include inline scripts', 'jch-optimize'), 'jch_options_inlineScripts_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_bottom_js', __('Position javascript file at bottom', 'jch-optimize'), 'jch_options_bottom_js_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_loadAsynchronous', __('Load combined javascript asynchronously', 'jch-optimize'), 'jch_options_loadAsynchronous_string', 'jch-sections', 'jch_basic_auto');

        //Exclude Options
        add_settings_section('jch_url_exclude', '', 'jch_url_exclude_section_text', 'jch-sections');
        add_settings_field('jch_options_url_exclude', __('Exclude these urls', 'jch-optimize'), 'jch_options_url_exclude_string', 'jch-sections', 'jch_url_exclude');

        add_settings_section('jch_exclude_peo', '', 'jch_exclude_peo_section_text', 'jch-sections');
        add_settings_field('jch_options_exclude_css_spacer', '<strong><i>' . __('Exclude CSS files and Styles', 'jch-optimize') . '</i></strong><hr>', 'jch_options_spacer_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeCss', __('Exclude these CSS files', 'jch-optimize'), 'jch_options_excludeCss_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeCssComponents', __('Exclude CSS files from these plugins', 'jch-optimize'), 'jch_options_excludeCssComponents_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeStyles', __('Exclude individual internal STYLE declarations', 'jch-optimize'), 'jch_options_excludeStyles_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_exclude_js_spacer', '<strong><i>' . __('Exclude javascript files and scripts', 'jch-optimize') . '</i></strong><hr>', 'jch_options_spacer_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeJs_peo', __('Exclude these javascript files', 'jch-optimize'), 'jch_options_excludeJs_peo_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeJsComponents_peo', __('Exclude javascript files from these plugins', 'jch-optimize'), 'jch_options_excludeJsComponents_peo_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeScripts_peo', __('Exclude individual internal SCRIPT declarations', 'jch-optimize'), 'jch_options_excludeScripts_peo_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_exclude_all_scripts_spacer', '<strong><i>' . __('Exclude all Scripts and Styles', 'jch-optimize') . '</i></strong><hr>', 'jch_options_spacer_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeAllStyles', __('Exclude all STYLE declarations', 'jch-optimize'), 'jch_options_excludeAllStyles_string', 'jch-sections', 'jch_exclude_peo');
        add_settings_field('jch_options_excludeAllScripts', __('Exclude all SCRIPT declarations', 'jch-optimize'), 'jch_options_excludeAllScripts_string', 'jch-sections', 'jch_exclude_peo');

        add_settings_section('jch_exclude_ieo', '', 'jch_exclude_ieo_section_text', 'jch-sections');
        add_settings_field('jch_options_exclude__ieo_js_spacer', '<strong><i>' . __('Exclude javascript files and scripts', 'jch-optimize') . '</i></strong><hr>', 'jch_options_spacer_string', 'jch-sections', 'jch_exclude_ieo');
        add_settings_field('jch_options_excludeJs', __('Exclude these javascript files', 'jch-optimize'), 'jch_options_excludeJs_string', 'jch-sections', 'jch_exclude_ieo');
        add_settings_field('jch_options_excludeJsComponents', __('Exclude javascript files from these plugins', 'jch-optimize'), 'jch_options_excludeJsComponents_string', 'jch-sections', 'jch_exclude_ieo');
        add_settings_field('jch_options_excludeScripts', __('Exclude individual internal SCRIPT declarations', 'jch-optimize'), 'jch_options_excludeScripts_string', 'jch-sections', 'jch_exclude_ieo');

        add_settings_section('jch_dontmove', '', 'jch_dontmove_section_text', 'jch-sections');
        add_settings_field('jch_options_dontmoveJs', __('Javascript files'), 'jch_options_dontmoveJs_string', 'jch-sections', 'jch_dontmove');
        add_settings_field('jch_options_dontmoveScripts', 'Inline scripts', 'jch_options_dontmoveScripts_string', 'jch-sections', 'jch_dontmove');

        //Basic Features
        add_settings_section('jch_page_cache', '', 'jch_page_cache_section_text', 'jch-sections');
        add_settings_field('jch_options_cache_enable', __('Enable', 'jch-optimize'), 'jch_options_cache_enable_string', 'jch-sections', 'jch_page_cache');
        add_settings_field('jch_options_page_cache_lifetime', __('Cache lifetime', 'jch-optimize'), 'jch_options_page_cache_lifetime_string', 'jch-sections', 'jch_page_cache');
        add_settings_field('jch_options_cache_exclude', __('Exclude urls', 'jch-optimize'), 'jch_options_cache_exclude_string', 'jch-sections', 'jch_page_cache');

        add_settings_section('jch_sprite_manual', '', 'jch_sprite_manual_section_text', 'jch-sections');
        add_settings_field('jch_options_csg_enable', __('Enable', 'jch-optimize'), 'jch_options_csg_enable_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_direction', __('Sprite Build Direction', 'jch-optimize'), 'jch_options_csg_direction_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_wrap_images', __('Wrap Images', 'jch-optimize'), 'jch_options_csg_wrap_images_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_exclude_images', __('Exclude these images from the sprite', 'jch-optimize'), 'jch_options_csg_exclude_images_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_include_images', __('Include these images in the sprite', 'jch-optimize'), 'jch_options_csg_include_images_string', 'jch-sections', 'jch_sprite_manual');

        add_settings_section('jch_img_attributes', '', 'jch_img_attributes_section_text', 'jch-sections');
        add_settings_field('jch_img_attributes_enable', __('Enable', 'jch-optimize'), 'jch_options_img_attributes_enable_string', 'jch-sections', 'jch_img_attributes');

        //Advanced Features
        add_settings_section('jch_pro_group', '', 'jch_pro_group_section_text', 'jch-sections');
        ##<procode>##
        add_settings_field('jch_options_pro_downloadid', __('Download ID', 'jch-optimize'), 'jch_options_pro_downloadid_string', 'jch-sections', 'jch_pro_group');
        ##</procode>##
        add_settings_section('jch_pro_http2_push', '', 'jch_pro_http2_push_section_text', 'jch-sections');
        add_settings_field('jch_options_pro_http2_push_enable', __('Enable', 'jch-optimize'), 'jch_options_pro_http2_push_enable_string', 'jch-sections', 'jch_pro_http2_push');
        add_settings_field('jch_options_pro_http2_exclude_deferred', __('Exclude deferred files', 'jch-optimize'), 'jch_options_pro_http2_exclude_deferred_string', 'jch-sections', 'jch_pro_http2_push');
        add_settings_field('jch_options_pro_http2_file_types', __('File types', 'jch-optimize'), 'jch_options_pro_http2_file_types_string', 'jch-sections', 'jch_pro_http2_push');

        add_settings_section('jch_pro_ocd', '', 'jch_pro_ocd_section_text', 'jch-sections');
        add_settings_field('jch_options_optimizeCssDelivery_enable', __('Enable', 'jch-optimize'), 'jch_options_optimizeCssDelivery_enable_string', 'jch-sections', 'jch_pro_ocd');
        add_settings_field('jch_options_optimizeCssDelivery', __('Number of Elements', 'jch-optimize'), 'jch_options_optimizeCssDelivery_string', 'jch-sections', 'jch_pro_ocd');
        add_settings_field('jch_options_pro_remove_unused_css', __('Remove unused CSS', 'jch-optimize'), 'jch_options_pro_remove_unused_css_string', 'jch-sections', 'jch_pro_ocd');
        add_settings_field('jch_options_pro_dynamic_selectors', __('Dynamic Selectors', 'jch-optimize'), 'jch_options_pro_dynamic_selectors_string', 'jch-sections', 'jch_pro_ocd');

        add_settings_section('jch_pro_lazyload', '', 'jch_pro_lazyload_section_text', 'jch-sections');
        add_settings_field('jch_options_lazyload_enable', __('Enable', 'jch-optimize'), 'jch_options_lazyload_enable_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_pro_lazyload_iframe', __('Lazy load iframes', 'jch-optimize'), 'jch_options_pro_lazyload_iframe_string', 'jch-sections', 'jch_pro_lazyload');
        //add_settings_field('jch_options_pro_lazyload_video', __('Lazy load audio/video', 'jch-optimize'), 'jch_options_pro_lazyload_video_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_excludeLazyLoad', __('Exclude these images', 'jch-optimize'), 'jch_options_excludeLazyLoad_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_pro_excludeLazyLoadFolder', __('Exclude these folders', 'jch-optimize'), 'jch_options_pro_excludeLazyLoadFolder_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_pro_excludeLazyLoadClass', __('Exclude these classes', 'jch-optimize'), 'jch_options_pro_excludeLazyLoadClass_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_pro_lazyload_effects', __('Enable effects', 'jch-optimize'), 'jch_options_pro_lazyload_effects_string', 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_lazyload_autosize', __('Autosize images', 'jch-optimize'), 'jch_options_lazyload_autosize_string', 'jch-sections', 'jch_pro_lazyload');

        add_settings_section('jch_pro_cookielessdomain', '', 'jch_pro_cookielessdomain_section_text', 'jch-sections');
        add_settings_field('jch_options_cookielessdomain_enable', __('Enable', 'jch-optimize'), 'jch_options_cookielessdomain_enable_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_cdn_scheme', __('CDN scheme', 'jch-optimize'), 'jch_options_cdn_scheme_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_cookielessdomain', __('Domain 1', 'jch-optimize'), 'jch_options_cookielessdomain_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_staticfiles', __('Static Files 1', 'jch-optimize'), 'jch_options_staticfiles_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_customcdnextensions', __('Custom Extensions', 'jch-optimize'), 'jch_options_pro_customcdnextensions_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_cookielessdomain_2', __('Domain 2', 'jch-optimize'), 'jch_options_pro_cookielessdomain_2_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_staticfiles_2', __('Static Files 2', 'jch-optimize'), 'jch_options_pro_staticfiles_2_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_cookielessdomain_3', __('Domain 3', 'jch-optimize'), 'jch_options_pro_cookielessdomain_3_string', 'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_staticfiles_3', __('Static Files 3', 'jch-optimize'), 'jch_options_pro_staticfiles_3_string', 'jch-sections', 'jch_pro_cookielessdomain');

        add_settings_section('jch_images', '', 'jch_images_section_text', 'jch-sections');
//        add_settings_field('jch_options_kraken_optimization_level', __('Lossy Optimization', 'jch-optimize'),
//                                                                       'jch_options_kraken_optimization_level_string', 'jch-sections', 'jch_images');
        add_settings_field('jch_options_ignore_optimized', __('Ignore optimized images', 'jch-optimize'), 'jch_options_ignore_optimized_string', 'jch-sections', 'jch_images');

        add_settings_section('jch_images_foldertree', '', 'jch_images_foldertree_section_text', 'jch-sections');
        add_settings_field('jch_options_optimizeimages', __('Optimize Images', 'jch-optimize'), 'jch_options_optimize_images_string', 'jch-sections', 'jch_images_foldertree');

        add_settings_section('jch_section_end', '', 'jch_section_end_text', 'jch-sections');
}

function check_jch_tasks()
{
        if (isset($_GET['jch-task']))
        {
                switch ($_GET['jch-task'])
                {
                        case 'cleancache':
                                delete_jch_cache();
                                break;

                        case 'browsercaching':
                                jch_leverage_browser_cache();
                                break;

                        case 'filepermissions':
                                jch_fix_file_permissions();
                                break;

                        case 'postresults':
                                jch_process_optimize_images_results();
                                break;

                        default:
                                break;
                }
        }
}

function jch_load_resource_files($hook)
{
        if ('settings_page_jchoptimize-settings' != $hook)
        {
                return;
        }

        wp_enqueue_style('jch-bootstrap-css');
        wp_enqueue_style('jch-admin-css');
        wp_enqueue_style('jch-fonts-css');
        wp_enqueue_style('jch-chosen-css');
        wp_enqueue_style('jch-wordpress-css');

        wp_enqueue_script('jch-wordpress-js');
        wp_enqueue_script('jch-bootstrap-js');
        wp_enqueue_script('jch-tabsstate-js');
        wp_enqueue_script('jch-adminutility-js');
        wp_enqueue_script('jch-chosen-js');
        wp_enqueue_script('jch-collapsible-js');

        ##<procode>##
        wp_enqueue_style('jch-progressbar-css');
        wp_enqueue_style('jch-filetree-css');

        wp_enqueue_script('jch-filetree-js');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jch-proadminutility-js');
        ##</procode>##
}

function jch_load_scripts()
{

        ?>
        <style type="text/css">
                .chosen-container-multi .chosen-choices li.search-field input[type=text] {
                        height: 25px;
                }

                .chosen-container {
                        margin-right: 4px;
                }

        </style>
        <script type="text/javascript">
            function submitJchSettings() {
                jQuery("form.jch-settings-form").submit();
            }

            jQuery(document).ready(function () {
                jQuery(".chzn-custom-value").chosen({width: "240px"});

                jQuery('.collapsible').collapsible();
            });

            <?php ##<procode>##                                    ?>
            var jch_ajax_optimizeimages = ajaxurl + '?action=optimizeimages';
            var jch_message = "<?php _e('Please open a directory to optimize images', 'jch-optimize') ?>";
            var jch_noproid = "<?php _e('Please enter your Download ID on the Pro Options tab.') ?>";

            jQuery(document).ready(function () {
                jQuery("#file-tree-container").fileTree(
                    {
                        root: "",
                        script: ajaxurl + '?action=filetree',
                        expandSpeed: 1000,
                        collapseSpeed: 1000,
                        multiFolder: false
                    }, function (file) {
                    });
            });



            <?php ##</procode>##                                 ?>

        </script>
        <?php

}

function delete_jch_cache()
{
        global $jch_redirect;

        Helper::clearHiddenValues(Plugin::getPluginParams());

        try
        {
                $result = Cache::deleteCache();
        }
        catch (\JchOptimize\Core\Exception $e)
        {
        }

        if ($result !== false)
        {
                jch_add_notices('success', __('The plugin\'s cache files were deleted successfully!', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('error', __('An error occurred while trying to delete the plugin\'s cache files!', 'jch-optimize'));
        }

        $jch_redirect = true;
}

function jch_leverage_browser_cache()
{
        global $jch_redirect;

        $expires = Admin::leverageBrowserCaching();

        if ($expires === false)
        {
                jch_add_notices('error', __('The plugin failed to add the \'leverage browser cache\' codes to the .htaccess file.', 'jch-optimize'));
        }
        elseif ($expires == 'FILEDOESNTEXIST')
        {
                jch_add_notices('warning', __('An .htaccess file could not be found in the root folder of the site.', 'jch-optimize'));
        }
        elseif ($expires == 'CODEALREADYINFILE')
        {
                jch_add_notices('notice', __('Codes for \'leverage browser caching\' already exists in the .htaccess file.', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('success', __('Codes for \'leverage browser caching\' were added to the .htaccess file successfully.', 'jch-optimize'));
        }

        $jch_redirect = true;
}

function jch_fix_file_permissions()
{
        global $jch_redirect;

        try
        {
                $wp_filesystem = Cache::getWpFileSystem();
        }
        catch (\JchOptimize\Core\Exception $e)
        {
                $wp_filesystem = false;
        }

        if ($wp_filesystem === false)
        {
                $result = false;
        }
        else
        {
                $result = true;

                try
                {
                        jch_chmod(JCH_PLUGIN_DIR, $wp_filesystem);
                }
                catch (Exception $ex)
                {
                        $result = false;
                }
        }

        if ($result)
        {
                jch_add_notices('success', __('The permissions of all the files and folders in the plugin were successfully updated.', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('error', __('The plugin failed to update the permissions of the files and folders in the plugin.', 'jch-optimize'));
        }

        $jch_redirect = true;
}

function jch_chmod($file, $wp_fs)
{

        /** @var \WP_Filesystem_Base $wp_fs */
        if ($wp_fs->is_file($file))
        {
                $mode = FS_CHMOD_FILE;
        }
        elseif ($wp_fs->is_dir($file))
        {
                $mode = FS_CHMOD_DIR;
        }
        else
        {
                throw new Exception;
        }

        if (!(@chmod($file, $mode)))
        {
                throw new Exception;
        }

        if ($wp_fs->is_dir($file))
        {
                $file     = trailingslashit($file);
                $filelist = $wp_fs->dirlist($file);

                foreach ((array) $filelist as $filename => $filemeta)
                {
                        jch_chmod($file . $filename, $wp_fs);
                }
        }
}

function jch_redirect()
{
        global $jch_redirect;

        if ($jch_redirect)
        {
                $url = admin_url('options-general.php?page=jchoptimize-settings');

                wp_redirect($url);
                exit;
        }
}

function jch_process_optimize_images_results()
{
        global $jch_redirect;

        if (file_exists(JCH_PLUGIN_DIR . 'status.json'))
        {
                unlink(JCH_PLUGIN_DIR . 'status.json');
        }

        $cnt    = filter_input(INPUT_GET, 'cnt', FILTER_SANITIZE_NUMBER_INT);
        $dir    = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
        $msg    = filter_input(INPUT_GET, 'msg', FILTER_DEFAULT);

        //$dir = Utility::decrypt($dir);

        if ($cnt !== false && !is_null($cnt))
        {
                jch_add_notices('success', sprintf(__('<span class="notranslate">%1$d</span> images were optimized in <span class="notranslate">%2$s</span>', 'jch-optimize'), $cnt, $dir));
        }
        elseif ($status !== false && !is_null($status))
        {
                jch_add_notices('error', sprintf(__('Failed to optimize image: <span class="notranslate">%1$s</span>', 'jch-optimize'), $msg));
        }

        $jch_redirect = true;
}

function jch_add_notices($type, $text)
{
        $jch_notices = array();

        if ($notices = get_transient('jch_notices'))
        {
                $jch_notices = $notices;
        }

        $jch_notices[$type][] = $text;

        set_transient('jch_notices', $jch_notices, 60 * 5);
}

function jch_send_notices()
{
        $jch_notices = get_transient('jch_notices');

        foreach ($jch_notices as $type => $notices)
        {
                $notices = array_unique($notices);
                ?>
                <div class="notice notice-<?php echo $type ?>">
                        <?php

                        foreach ($notices as $notice)
                        {

                                ?>
                                <p> <?php echo $notice ?></p>
                                <?php

                        }

                        ?>
                </div>
                <?php

        }

        delete_transient('jch_notices');
}

function jch_options_validate($input)
{
        return $input;
}

function jch_group_start($header = '', $description = '', $class = '')
{
        echo '<fieldset class="jch-group">'
                . ($header != '' ? '             <legend>' . $header . '</legend>' : '')
                . '        <div ' . $class . '> <p><em>' . $description . '</em></p></div>'
                . '<div>';
}

function jch_group_end()
{
        echo '</div></fieldset>';
}

function jch_basic_pre_section_text()
{
        echo '<div class="tab-pane" id="basic">';

        $header      = __('Combine CSS and javascript files', 'jch-optimize');
        $description = __('These settings are concerned with combining CSS and javascript files into one respectively, and the minification of the combined files and the HTML, and also determines where in the HTML the combined files are placed. Refer to the documentation for more information..',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_combine_files_enable_string()
{
        $description = '';

        echo jch_gen_radio_field('combine_files_enable', '1', $description);
}

function jch_options_auto_settings_string()
{
        $description = __('The six icons that are above represent six preconfigured settings of the options in the \'Automatic Settings Group\'. The level of optimization increases as you go to the right but the risks of conflicts will also increase, so try each in turn and use the highest setting that work for your site. The first, which is the safest, is the default and should work on most websites. These settings do not affect the files/extensions/images etc. that you have excluded.',
                'jch-optimize');

        $aButton = jch_get_auto_settings_buttons();

        echo '<div style="display: inline-block;">';
        echo jch_gen_button_icons($aButton, $description, '</div>');
}

function jch_options_html_minify_level_string()
{
        $description = __('If \'Minify HTML\' is enabled, this will determine the level of minification. The incremental changes per level are as follows: Basic - Adjoining whitespaces outside of elements are reduced to one whitespace; Advanced - Remove HTML comments, whitespace around block elements and undisplayed elements, Remove unnecessary whitespaces inside of elements and around their attributes; Ultra - Remove redundant attributes, for example, <span class="notranslate">\'text/javascript\'</span>, and remove quotes from around selected attributes (HTML5)',
                'jch-optimize');

        $values = array(
                '0' => __('Basic', 'jch-optimize'),
                '1' => __('Advanced', 'jch-optimize'),
                '2' => __('Ultra', 'jch-optimize')
        );

        echo jch_gen_select_field('html_minify_level', '0', $values, $description);
}


function jch_options_htaccess_string()
{
        $description = __('By default the combined files will be loaded as static css and javascript files. You would need to include directives in your .htaccess file to gzip these files. You can use PHP files instead that will be gzipped if that option is set. PHP files can be loaded with a query attached with the information to find the combined files, or you can use url rewrite if it\'s available on the server so the files can be masked as static files. If your server prohibits the use of the Options +FollowSymLinks directive in .htaccess files use the respective option.',
                'jch-optimize');

        $values = array(
                '0' => __('PHP file with query', 'jch-optimize'),
                '1' => __('PHP using url re-write', 'jch-optimize'),
                '3' => __('PHP using url re-write (Without Options +FollowSymLinks)', 'jch-optimize'),
                '2' => __('Static css and js files', 'jch-optimize')
        );

        echo jch_gen_select_field('htaccess', '2', $values, $description, '');
}

function jch_options_try_catch_string()
{
        $description = __('If you\'re seeing javascript errors in the console, you can try enabling this option to wrap each javascript file in a <span class="notranslate">\'try-catch\'</span> block to prevent the errors from one file affecting the combined file.',
                'jch-optimize');

        echo jch_gen_radio_field('try_catch', '1', $description);
}

function jch_basic_misc_section_text()
{
        jch_group_end();

        $header = __('Miscellaneous Settings', 'jch-optimize');

        jch_group_start($header);
}

function jch_options_utility_settings_string()
{
        $attribute = jch_get_cache_info();

        $description = '';

        $aButtons = Admin::getUtilityIcons();

        echo '<div style="display: -webkit-flex; display: -ms-flex; display: -moz-flex; display: flex;">';
        echo jch_gen_button_icons($aButtons, $description, $attribute);
}

function jch_options_order_plugin_string()
{
        $description = __('The plugin will automatically set the execution order of plugins so to ensure compatibility with other plugins.', 'jch-optimize');

        echo jch_gen_radio_field('order_plugin', '1', $description);
}

function jch_options_debug_string()
{
        $description = __('This option will add the \'commented out\' url of the individual files inside the combined file above the contents that came from that file. This is useful when configuring the plugin and trying to resolve conflicts. This will also add a <span class="notranslate">Profiler</span> menu to the <span class="notranslate">AdminBar</span> so you can review the times that the plugin methods take to run.',
                'jch-optimize');
        echo jch_gen_radio_field('debug', '0', $description);
}

function jch_options_disable_logged_in_users_string()
{
        $description = __('When enabled the plugin will be disabled for all users that are logged in', 'jch-optimize');
        echo jch_gen_radio_field('disable_logged_in_users', '0', $description);
}


function jch_group_start_auto()
{
        jch_group_end();

        $header      = __('Automatic Settings Group', 'jch-optimize');
        $description = __('The fields in this group are automatically configured with the Automatic Settings - <span class="notranslate">(Minimum - Optimum)</span>. This is highly recommended to avoid conflicts. It is usually not necessary to set these fields manually unless you are troubleshooting a problem, so do not change these settings yourself unless you know what you are doing .',
                'jch-optimize');
        $class       = 'class="collapsible" ';

        jch_group_start($header, $description, $class);
}

function jch_basic_auto_section_text()
{
        jch_group_start_auto();
}

function jch_options_cache_lifetime_string()
{
        $description = __('The amount of time that the cache will remain valid before the plugin generates a new one. All expired cache will be expunged at this time. Selecting higher values can cause excess cache build-up.');

        $values = array(
                '1800'  => __('30 min', 'jch-optimize'),
                '3600'  => __('1 hour', 'jch-optimize'),
                '10800' => __('3 hours', 'jch-optimize'),
                '21600' => __('6 hours', 'jch-optimize'),
                '43200' => __('12 hours', 'jch-optimize'),
                '86400' => __('1 day', 'jch-optimize')
        );

        echo jch_gen_select_field('cache_lifetime', '900', $values, $description, '');
}

function jch_options_spacer_string()
{
        echo '&nbsp;';
}

function jch_options_css_string()
{
        $description = __('This will combine all CSS files into one file and remove all the links to the individual files from the page, replacing it with a link generated by the plugin to the combined file.',
                'jch-optimize');

        echo jch_gen_radio_field('css', '1', $description, 's1-on s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_javascript_string()
{
        $description = __('This will combine all javascript files into one file and remove all the links to the individual files from the page, replacing it with a link generated by the plugin to the combined file.',
                'jch-optimize');

        echo jch_gen_radio_field('javascript', '1', $description, 's1-on s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_gzip_string()
{
        $description = __('This setting compresses the generated javascript and CSS combined files with gzip, decreasing file size dramatically. This can decrease file size dramatically.',
                'jch-optimize');

        echo jch_gen_radio_field('gzip', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_css_minify_string()
{
        $description = __('If yes, the plugin will remove all unnecessary whitespaces and comments from the combined CSS file to reduce the total file size.',
                'jch-optimize');

        echo jch_gen_radio_field('css_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_js_minify_string()
{
        $description = __('If yes, the plugin will remove all unnecessary whitespaces and comments from the combined javascript file to reduce the total file size.',
                'jch-optimize');

        echo jch_gen_radio_field('js_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_html_minify_string()
{
        $description = __('If yes, the plugin will remove all unneccessary whitespaces and comments from HTML to reduce the total size of the web page.',
                'jch-optimize');

        echo jch_gen_radio_field('html_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_defer_js_string()
{
        $description = __('This option will add a <span class="notranslate">\'defer\'</span> attribute to the link of the combined javascript file. This will defer the loading of the javascript until after the page is loaded to reduce \'render-blocking\'.  Do not configure this setting manually to avoid breaking your page.',
                'jch-optimize');

        echo jch_gen_radio_field('defer_js', '0', $description, 's1-off s2-off s3-off s4-off s5-off s6-on');
}


function jch_options_auto_exclude_string()
{
        echo '&nbsp;';
}

function jch_options_includeAllExtensions_string()
{
        $description = __('By default, all files from third party plugins and external domains are excluded. If this setting is enabled, they will be included.', 'jch-optimize');
        echo jch_gen_radio_field('includeAllExtensions', '0', $description, 's1-off s2-off s3-on s4-on s5-on s6-on');
}

function jch_options_auto_advanced_string()
{
        echo '&nbsp;';
}

function jch_options_replaceImports_string()
{
        $description = __('The plugin will replace <span class="notranslate">@import</span> at-rules with the contents of the files they are importing. This will be done recursively.',
                'jch-optimize');

        echo jch_gen_radio_field('replaceImports', '0', $description, 's1-off s2-off s3-off s4-on s5-on s6-on');
}

function jch_options_phpAndExternal_string()
{
        $description = __('Javascript and css files with <span class="notranslate">\'.php\'</span> file extensions, and files from external domains will be included in the combined file. This option requires that <span class="notranslate">cURL</span> is installed on your server.', 'jch-optimize');

        echo jch_gen_radio_field('phpAndExternal', '0', $description, 's1-off s2-off s3-off s4-on s5-on s6-on');
}

function jch_options_inlineStyle_string()
{
        $description = __('In-page CSS inside <span class="notranslate">&lt;style&gt;</span> tags will be included in the aggregated file in the order they appear on the page.', 'jch-optimize');

        echo jch_gen_radio_field('inlineStyle', '0', $description, 's1-off s2-off s3-off s4-on s5-on s6-on');
}

function jch_options_inlineScripts_string()
{
        $description = __('In-page javascript inside <span class="notranslate">&lt;script&gt;</span> tags will be included in the combined file in the order they appear on the page.',
                'jch-optimize');

        echo jch_gen_radio_field('inlineScripts', '0', $description, 's1-off s2-off s3-off s4-on s5-on s6-on');
}

function jch_options_bottom_js_string()
{
        $description = __('Place combined javascript file at bottom of the page just before the ending BODY tag. If some javascript files are excluded while preserving execution order so that the combined javascript file is split around the excluded files, only the last combined javascript file will be placed at the bottom of the page. By default the plugin only combines files found in the HEAD section of the page. This option extends the search to the BODY section.',
                'jch-optimize');

        echo jch_gen_radio_field('bottom_js', '0', $description, 's1-off s2-off s3-off s4-off s5-on s6-on');
}

function jch_options_loadAsynchronous_string()
{
        $description = __('The \'asnyc\' attribute is added to the combined javascript file so it will be loaded asynchronously to avoid render blocking and speed up download of the web page. If other files/scripts are excluded while preserving execution order so that the combined file is split around the excluded files, the \'defer\' attribute is instead added to the last combined file following an excluded file/script. This option only works when the combined javascript file is placed at the bottom of the page.',
                'jch-optimize');

        echo jch_gen_radio_field('loadAsynchronous', '0', $description, 's1-off s2-off s3-off s4-off s5-off s6-on');
}

function jch_get_cache_info()
{
        static $attribute = false;

        if ($attribute === false)
        {
                try
                {
                        $wp_filesystem = Cache::getWpFileSystem();
                }
                catch (\JchOptimize\Core\Exception $e)
                {
                        $wp_filesystem = false;
                }

                if ($wp_filesystem !== false && $wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        try
                        {
                                Cache::initializecache();
                        }
                        catch (\JchOptimize\Core\Exception $e)
                        {
                                return;
                        }


                        $size    = 0;
                        $dirlist = $wp_filesystem->dirlist(JCH_CACHE_DIR);

                        foreach ($dirlist as $file)
                        {
                                if ($file['name'] == 'index.html')
                                {
                                        continue;
                                }

                                $size += $file['size'];
                        }

                        $decimals = 2;
                        $sz       = 'BKMGTP';
                        $factor   = (int) floor((strlen($size) - 1) / 3);
                        $size     = sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . $sz[$factor];

                        $no_files = number_format(count($dirlist) - 1);
                }
                else
                {
                        $size     = '0';
                        $no_files = '0';
                }

                $attribute = '<div><br><div><em>' . sprintf(__('Number of files: <span class="notranslate">%s</span>'), $no_files) . '</em></div>'
                        . '<div><em>' . sprintf(__('Size: <span class="notranslate">%s</span>'), $size) . '</em></div></div>'
                        . '</div>';
        }

        return $attribute;
}

function jch_url_exclude_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="exclude">';

        $header      = __('Exclude urls from the plugin', 'jch-optimize');
        $description = __('Enter any part of a url to exclude that page from optimization. You will need to add these urls to the list manually by typing the url in the textbox and click the \'Add item\' button.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_url_exclude_string()
{
        $description = __('Enter urls to exclude', 'jch-optimize');
        $option      = 'url_exclude';

        $values = jch_get_field_value('url', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_exclude_peo_section_text()
{
        jch_group_end();

        $header      = __('Exclude files while preserving the original execution order of codes on the page', 'jch-optimize');
        $description = __('These settings are used to exclude individual files, or files from select plugins, while maintaining the original execution order of codes on the page to ensure the page doesn\'t break. The combined file will split itself around the excluded files to preserve the order and ensure that no dependencies on any other combined files/scripts are broken. If you\'re not seeing the files or extensions you want to exclude in the drop-down list, manually add the files or extensions to the list. To add a file to the list manually, type the url in the textbox and click the \'Add item\' button.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_excludeCss_string()
{
        $description = __('Select the CSS files you want to exclude.', 'jch-optimize');
        $option      = 'excludeCss';

        $values = jch_get_field_value('css', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeJs_peo_string()
{
        $description = __('Select the javascript files you want to exclude.', 'jch-optimize');
        $option      = 'excludeJs_peo';

        $values = jch_get_field_value('js', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeCssComponents_string()
{
        $description = __('Select the plugins that you want to exclude CSS files from.', 'jch-optimize');
        $option      = 'excludeCssComponents';

        $values = jch_get_field_value('css', $option, 'extension');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeJsComponents_peo_string()
{
        $description = __('Select the plugins that you want to exclude javascript files from.',
                'jch-optimize');
        $option      = 'excludeJsComponents_peo';

        $values = jch_get_field_value('js', $option, 'extension');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeStyles_string()
{
        $description = __('Select the \'in-page\' <span class="notranslate">&lt;style&gt;</span> you want to exclude.',
                'jch-optimize');

        $option = 'excludeStyles';

        $values = jch_get_field_value('css', $option, 'style');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeScripts_peo_string()
{
        $description = __('Select the \'in-page\' <span class="notranslate">&lt;script&gt;</span> you want to exclude.',
                'jch-optimize');

        $option = 'excludeScripts_peo';

        $values = jch_get_field_value('js', $option, 'script');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeAllStyles_string()
{
        $description = __('This is useful if you are generating an excess amount of cache files due to the file name of the combined CSS file keeps changing and you can\'t identify which STYLE declaration is responsible',
                'jch-optimize');

        echo jch_gen_radio_field('excludeAllStyles', '0', $description);
}

function jch_options_excludeAllScripts_string()
{
        $description = __('This is useful if you are generating an excess amount of cache files due to the file name of the combined javascript file keeps changing and you can\'t identify which SCRIPT declaration is responsible',
                'jch-optimize');

        echo jch_gen_radio_field('excludeAllScripts', '0', $description);
}

function jch_exclude_ieo_section_text()
{
        jch_group_end();

        $header      = __('Exclude files without maintaining the original execution order of files on the page', 'jch-optimize');
        $description = __('Only use these settings if you\'re sure that the files/scripts you are excluding does not have any dependencies on any other files/scripts that are combined. If you are not sure then use the above section to exclude your files to avoid breaking your page.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_excludeJs_string()
{
        $description = __('Select the javascript files you want to exclude.', 'jch-optimize');
        $option      = 'excludeJs';

        $values = jch_get_field_value('js', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeJsComponents_string()
{
        $description = __('Select the plugins that you want to exclude javascript files from.',
                'jch-optimize');
        $option      = 'excludeJsComponents';

        $values = jch_get_field_value('js', $option, 'extension');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeScripts_string()
{
        $description = __('Select the \'in-page\' <span class="notranslate">&lt;script&gt;</span> you want to exclude.',
                'jch-optimize');

        $option = 'excludeScripts';

        $values = jch_get_field_value('js', $option, 'script');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_dontmove_section_text()
{
        jch_group_end();

        $header      = __('Don\'t move these files to the bottom of the page');
        $description = __('The plugin will move all excluded and combined javascript files to the bottom of the page when using the Premium or Optimum setting. If there\'s a javascript file or script that is excluded that you DON\'T want moved to the bottom of the page, enter them here in these settings. These files/scripts must be excluded above for these settings to take effect.');

        jch_group_start($header, $description);
}

function jch_options_dontmoveJs_string()
{
        $description = __('Don\'t move these javascript files that were excluded above to the bottom of the page. These files will be left at their original position on the page.');
        $option = 'dontmoveJs';

        $values = jch_get_field_value('js', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_dontmoveScripts_string()
{
       $description = __('Enter any substring of an excluded script here to prevent this script being moved to the bottom. Inline scripts in the BODY of the document containing the \'document.write\' method will NOT be moved by default.');
       $option = 'dontmoveScripts';

       $values = jch_get_field_value('js', $option, 'script');

       echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_gen_button_icons(array $aButton, $description = '', $attribute = '')
{
        $sField = Admin::generateIcons($aButton);
        $sField .= $attribute;
        if ($description != '')
        {
                $sField .= '<div class="description" style="margin-top:-40px"><div>' . $description . '</div></div>';
        }

        return $sField;
}

function jch_page_cache_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="sprite">';

        $header      = __('Page Cache', 'jch-optimize');
        $description = __('The HTML source of the page will be cached to significantly speed up page loads. Deactivate caching while confguring the plugin and be sure to flush cache after making changes to the site.');

        jch_group_start($header, $description);
}

function jch_options_cache_enable_string()
{
        $description = 'Enable page caching';

        echo jch_gen_radio_field('cache_enable', '0', $description);
}

function jch_options_page_cache_lifetime_string()
{
        $description = __('The period of time for which the page cache will be valid. Be sure to set this lower that the cache lifetime of combined files at all times.');

        $values = array(
                '900'   => __('15 min', 'jch-optimize'),
                '1800'  => __('30 min', 'jch-optimize'),
                '3600'  => __('1 hour', 'jch-optimize'),
                '10800' => __('3 hours', 'jch-optimize'),
                '21600' => __('6 hours', 'jch-optimize'),
                '43200' => __('12 hours', 'jch-optimize'),
                '86400' => __('1 day', 'jch-optimize')
        );

        echo jch_gen_select_field('page_cache_lifetime', '900', $values, $description, '');
}

function jch_options_cache_exclude_string()
{

        $description = __('Enter any part of a url to exclude that page from caching.', 'jch-optimize');

        $option = 'cache_exclude';

        $values = jch_get_field_value('url', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_sprite_manual_section_text()
{
        jch_group_end();


        $header      = __('Sprite Generator', 'jch-optimize');
        $description = __('If yes will combine selected background images in one image called a sprite to reduce http requests.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_csg_enable_string()
{
        $description = '';

        echo jch_gen_radio_field('csg_enable', '0', $description);
}

function jch_options_csg_direction_string()
{
        $description = __('Determine in which direction the images must be placed in the sprite.', 'jch-optimize');

        $values = array(
                'vertical'   => __('vertical', 'jch-optimize'),
                'horizontal' => __('horizontal', 'jch-optimize')
        );

        echo jch_gen_select_field('csg_direction', 'vertical', $values, $description);
}

function jch_options_csg_wrap_images_string()
{
        $description = __('This setting will wrap images in sprite into another row or column if the length of the sprite becomes longer than 2000px.',
                'jch-optimize');

        echo jch_gen_radio_field('csg_wrap_images', '0', $description);
}

function jch_options_csg_exclude_images_string()
{
        $description = __('You can exclude one or more of the images if they are displayed incorrectly.',
                'jch-optimize');

        $option = 'csg_exclude_images';

        $values = jch_get_field_value('images', $option);

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_csg_include_images_string()
{
        $description = __('You can include additional images in the sprite to the ones that were selected by default. Exercise care with this option as these files are likely to not display correctly.',
                'jch-optimize');

        $option = 'csg_include_images';

        $values = jch_get_field_value('images', $option);

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_img_attributes_section_text()
{
        jch_group_end();

        $header      = __('Add Image Attributes', 'jch-optimize');
        $description = __('When enabled, the plugin will add missing width and height attributes to <span class="notranslate">&lt;img/&gt;</span>  elements',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_img_attributes_enable_string()
{
        echo jch_gen_radio_field('img_attributes_enable', '0', '');
}

function jch_pro_group_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="pro">';

        ##<procode>##
        jch_group_start();
        ##</procode>##
}

function jch_options_pro_downloadid_string()
{
        $description = __('Enter your download ID to enable automatic updates of the pro version. Log into your account on the jch-optimize.net website and access the download id from the \'My Account -> My Download ID\' menu item',
                'jch-optimize');

        echo jch_gen_text_field('pro_downloadid', '', $description, '', '40');
}

function jch_pro_http2_push_section_text()
{
        ##<procode>##
        jch_group_end();
        ##</procode>##

        $header      = __('Http/2 Push', 'jch-optimize');
        $description = __('Plugin will send appropriate preload headers to your server to push resource files before the browser requests them and so speed up the loading of the page. Please note this only works if http/2 is enabled on the server', 'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_pro_http2_push_enable_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field();
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_radio_field('pro_http2_push_enable', '0', '');
        ##</procode>##
}

function jch_options_pro_http2_exclude_deferred_string()
{
        $description = __('Will exclude javascript files that are deferred or loaded asynchronously, deferred CSS file in Optimize CSS Delivery feature, and images that are lazy-loaded. This can help reduce bandwidth and speed up first paint rendering.', 'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field();
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_radio_field('pro_http2_exclude_deferred', '1', $description);
        ##</procode>##
}

function jch_options_pro_http2_file_types_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        ###</procode>##
        ##<procode>##
        $values = array(
                'style'  => 'style',
                'script' => 'script',
                'font'   => 'font',
                'image'  => 'image'
        );

        echo jch_gen_checkboxes_field('pro_http2_file_types', $values, 'checkboxes');
        ##</procode>##
}

function jch_pro_ocd_section_text()
{
        jch_group_end();

        $header      = __('Optimize CSS Delivery', 'jch-optimize');
        $description = __('The plugin will attempt to extract the critical CSS that is required to format the page above the fold and put this in a <span class="notranslate">&lt;style&gt;</span> element inside the <span class="notranslate">&lt;head&gt;</span> section of the HTML to prevent \'render-blocking\'. The combined CSS will then be loaded asynchronously via javascript. Select the number of HTML elements from the top of the page that you want the plugin to find the critical CSS for. The smaller the number, the faster your site but you might see some jumping of the page if the number is too small.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_optimizeCssDelivery_enable_string()
{
        echo jch_gen_radio_field('optimizeCssDelivery_enable', '0', '');
}

function jch_options_optimizeCssDelivery_string()
{
        $values = array('200' => '200', '400' => '400', '600' => '600', '800' => '800');

        echo jch_gen_select_field('optimizeCssDelivery', '200', $values, '');
}

function jch_options_pro_remove_unused_css_string()
{
        $description = __('Will attempt to remove any CSS from the combined file that is not being used on the page. NOTE: This setting will remove the CSS for dynamic content. Manually place CSS selectors that targets the dynamic elements you want rendered below.');

        /* ##<freecode>##
         echo jch_gen_proonly_field($description);
         ##</freecode>## */

        ##<procode>##
        echo jch_gen_radio_field('pro_remove_unused_css', '0', $description);
        ##</procode>##
}

function jch_options_pro_dynamic_selectors_string()
{
        $description = __('Add CSS selectors here that targets dynamic elements to ensure CSS rule-sets containing these selectors gets added to the combined CSS files when \'Remove unused CSS\' is enabled. These are normally classes that are added to elements dynamically.');

        /* ##<freecode>##
         echo jch_gen_proonly_field($description);
         ##</freecode>## */

        ##<procode>##
        $option = 'pro_dynamic_selectors';
        $values = jch_get_field_value('dynamicselectors', $option, 'style');

        echo jch_gen_multiselect_field($option, $values, $description);
        ##</procode>##
}

function jch_options_pro_optimizeCssDelivery_loadFile_string()
{
        $description = __('Select when the combined CSS file should be loaded', 'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        $values = array(
                '0' => __('After page load', 'jch-optimize'),
                '1' => __('After initial paint', 'jch-optimize')
        );

        echo jch_gen_select_field('pro_optimizeCssDelivery_loadFile', '1', $values, $description);
        ##</procode>##
}

function jch_pro_cookielessdomain_section_text()
{
        jch_group_end();

        $header      = __('CDN/Cookieless Domain', 'jch-optimize');
        $description = __('Enter your CDN or cookieless domain here. The plugin will load all static files including background images, combined javascript and css files, and generated sprite from this domain. This requires that this domain is already set up and points to your site root. You can also use multiple domains and the plugin will alternate the domains among the static files. You can also select the file types that you want to be loaded over these domains.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_cookielessdomain_enable_string()
{
        echo jch_gen_radio_field('cookielessdomain_enable', '0', '');
}

function jch_options_cdn_scheme_string()
{
        $description = __('Select the scheme that you want prepended to the CDN/Cookieless domain', 'jch-optimize');
        $values      = array(
                '0' => __('scheme relative', 'jch-optimize'),
                '1' => __('http', 'jch-optimize'),
                '2' => __('https', 'jch-optimize')
        );

        echo jch_gen_select_field('cdn_scheme', '0', $values, $description, $class = '');
}

function jch_options_cookielessdomain_string()
{
        echo jch_gen_text_field('cookielessdomain', '', '', '', '30');
}

function jch_get_static_files_options()
{
        return array(
                'css'   => 'css',
                'png'   => 'png',
                'gif'   => 'gif',
                'ico'   => 'ico',
                'pdf'   => 'pdf',
                'js'    => 'js',
                'jpe?g' => 'jp(e)g',
                'bmp'   => 'bmp',
                'webp'  => 'webp',
                'svg'   => 'svg'
        );
}

function jch_options_staticfiles_string()
{
        $values = jch_get_static_files_options();

        echo jch_gen_checkboxes_field('staticfiles', $values, 'checkboxes');
}

function jch_options_pro_customcdnextensions_string()
{
        $description = __('To add custom extensions of file types to be loaded over CDN on Domain 1, type the extension in the textbox and press the \'Add item\' button');
        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        $option = 'pro_customcdnextensions';
        $values = jch_get_field_value('customextension', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
        ##</procode>##
}

function jch_options_pro_cookielessdomain_2_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field();
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_text_field('pro_cookielessdomain_2', '', '', '', '30');
        ##</procode>##
}

function jch_options_pro_staticfiles_2_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##

        $values = jch_get_static_files_options();

        echo jch_gen_checkboxes_field('pro_staticfiles_2', $values, 'checkboxes');
        ##</procode>##
}

function jch_options_pro_cookielessdomain_3_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field();
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_text_field('pro_cookielessdomain_3', '', '', '', '30');
        ##</procode>##
}

function jch_options_pro_staticfiles_3_string()
{
        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##

        $values = jch_get_static_files_options();

        echo jch_gen_checkboxes_field('pro_staticfiles_3', $values, 'checkboxes');
        ##</procode>##
}

function jch_pro_lazyload_section_text()
{
        jch_group_end();

        $header      = __('Lazy Load Images', 'jch-optimize');
        $description = __('Enable to delay the loading of iframes, images and responsive images until they are scrolled into view. This further speeds up the loading of the page and reduces http requests.',
                'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_lazyload_enable_string()
{
        $description = __('Enable to delay the loading of images until after the page loads and they are scrolled into view. This further reduces http requests and speeds up the loading of the page.',
                'jch-optimize');

        echo jch_gen_radio_field('lazyload_enable', '0', $description);
}

function jch_options_pro_lazyload_iframe_string()
{
        $description = __('If enabled will also lazy load IFRAME elements.',
                'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_radio_field('pro_lazyload_iframe', '0', $description);
        ##</procode>##
}

function jch_options_excludeLazyLoad_string()
{
        $description = __('Select or manually add the urls of the images you want to exclude from lazy load.', 'jch-optimize');

        $option = 'excludeLazyLoad';

        $values = jch_get_field_value('lazyload', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_pro_excludeLazyLoadFolder_string()
{
        $description = __('Exclude all the images in the selected folders.', 'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        $option = 'pro_excludeLazyLoadFolder';

        $values = jch_get_field_value('lazyload', $option, 'folder');

        echo jch_gen_multiselect_field($option, $values, $description);
        ##</procode>##
}

function jch_options_pro_excludeLazyLoadClass_string()
{
        $description = __('Exclude all images that have these classes declared on the <span class="notranslate">&lt;img&gt;</span> element', 'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        $option = 'pro_excludeLazyLoadClass';

        $values = jch_get_field_value('lazyload', $option, 'class');

        echo jch_gen_multiselect_field($option, $values, $description);
        ##</procode>##
}

function jch_options_pro_lazyload_effects_string()
{
        $description = __('Enable to use fade-in effects when images are scrolled into view', 'jch-optimize');

        /* ##<freecode>##
          echo jch_gen_proonly_field($description);
          ##</freecode>## */

        ##<procode>##
        echo jch_gen_radio_field('pro_lazyload_effects', '0', $description);
        ##</procode>##
}

function jch_options_lazyload_autosize_string()
{
        $description = __('If the size of the images seem incorrect or if you see empty spaces under the images after enabling Lazy-load, try enabling this setting to correct that', 'jch-optimize');

        echo jch_gen_radio_field('lazyload_autosize', '0', $description);
}

function jch_images_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="images">';

        $header      = __('Optimize Images', 'jch-optimize');
        $description = __('Use our API to optimize the images on your server. Be sure to save your \'Download ID\' in the plugin before trying to optimize images as that will authenticate you to access the API. Use the file tree to select the subfolders and files you want to optimize. Files will be optimized in subfolders recursively. If you want to rescale your images while optimizing, enter the new width and height. Original images will be saved in the /wp-content/jch_optimize_backup_images/ directory.',
                'jch-optimize');

        jch_group_start($header, $description);
}

//function jch_options_kraken_optimization_level_string()
//{
//        $description = __('You can sacrifice a small amount of image quality for up to 90% of the original file weight by choosing lossy optimization versus Non-lossy. (Recommended!)',
//                          'jch-optimize');
//
//        $values = array('0' => __('Non-Lossy', 'jch-optimize'), '1' => __('Lossy', 'jch-optimize'));
//
//        echo jch_gen_select_field('kraken_optimization_level', '0', $values, $description);
//        ;
//}

function jch_options_ignore_optimized_string()
{
        $description = __('Will not attempt to optimize any images in subfolders that have already been marked as optimized.',
                'jch-optimize');

        echo jch_gen_radio_field('ignore_optimized', '1', $description);
}

function jch_images_foldertree_section_text()
{
        jch_group_end();
}

function jch_options_optimize_images_string()
{
        ##<procode>##
        if (!function_exists('curl_init') || !function_exists('curl_exec'))
        {

                ?>
                <div class="error">
                        <p> <?php _e('cURL is required for this feature but it\'s not enabled on this server.', 'jch-optimize'); ?></p>
                </div>
                <?php

        }
        else
        {
                echo '<div id="optimize-images-container">';
                echo '<div id="file-tree-container"></div>';
                echo '<div id="files-container"></div>';

                $aButton = jch_get_optimize_images_buttons();

                echo jch_gen_button_icons($aButton);

                echo '</div>';
        }
        ##</procode>##

        /* ##<freecode>##
          echo jch_gen_proonly_field();
          ##</freecode>## */
}

function jch_section_end_text()
{
        echo '</div>';
}

function jch_gen_radio_field($option, $default, $description, $class = '', $auto_option = false)
{
        $options = get_option('jch_options');

        $checked = 'checked="checked"';
        $no      = '';
        $yes     = '';
        $auto    = '';
        $symlink = '';

        if (!isset($options[$option]))
        {
                $options[$option] = $default;
        }

        if ($options[$option] == '1')
        {
                $yes = $checked;
        }
        elseif ($options[$option] == '2')
        {
                $auto = $checked;
        }
        elseif ($options[$option] == '3')
        {
                $symlink = $checked;
        }
        else
        {
                $no = $checked;
        }

        $radio = '<fieldset id="jch_options_' . $option . '" class="radio btn-group ' . $class . '">' .
                '        <input type="radio" id="jch_options_' . $option . '0" name="jch_options[' . $option . ']" value="0" ' . $no . ' >' .
                '        <label for="jch_options_' . $option . '0" class="btn">' . __('No', 'jch-optimize') . '</label>' .
                '        <input type="radio" id="jch_options_' . $option . '1" name="jch_options[' . $option . ']" value="1" ' . $yes . ' >' .
                '        <label for="jch_options_' . $option . '1" class="btn">' . __('Yes', 'jch-optimize') . '</label>';
        $radio .= '</fieldset>';

        if ($description)
        {
                $radio .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $radio;
}

function jch_gen_checkboxes_field($option, $values, $class)
{
        $options = get_option('jch_options');

        if (!empty($options[$option]))
        {
                $checked_static_files = $options[$option];
        }
        else
        {
                $checked_static_files = array_keys($values);
        }

        $input = '<fieldset id="jch_options_' . $option . '" class="' . $class . '">' .
                '<ul>';

        $i = 0;
        foreach ($values as $key => $value)
        {
                $checked = '';

                if (in_array($key, $checked_static_files))
                {
                        $checked = 'checked';
                }

                $input .= '<li>'
                        . '<input type="checkbox" id="jch_options_' . $option . $i++ . '" name="jch_options[' . $option . '][]" value="' . $key . '" ' . $checked . '>'
                        . '<label for="jform_params_pro_staticfiles0">' . $value . '</label>'
                        . '</li>';
        }

        $input .= '</li>'
                . '</ul>'
                . '</fieldset>';

        return $input;
}

function jch_gen_text_field($option, $default, $description, $class = '', $size = '6')
{
        $options = get_option('jch_options');

        if (!isset($options[$option]))
        {
                $value = $default;
        }
        else
        {
                $value = $options[$option];
        }

        $input = '<input type="text" name="jch_options[' . $option . ']" id="jch_options_' . $option . '" value="' . $value . '" size="' . $size . '" class="' . $class . '">';

        if ($description)
        {
                $input .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $input;
}

function jch_gen_select_field($option, $default, $values, $description, $class = '')
{
        $options = get_option('jch_options');

        if (!isset($options[$option]))
        {
                $selected_value = $default;
        }
        else
        {
                $selected_value = $options[$option];
        }

        $select = '<select id="jch_options_' . $option . '" name="jch_options[' . $option . ']" class="' . $class . '" >';

        foreach ($values as $key => $value)
        {
                $selected = $selected_value == $key ? 'selected="selected"' : '';
                $select   .= '          <option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $select .= '</select>';

        if ($description)
        {
                $select .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $select;
}

function jch_gen_multiselect_field($option, $values, $description, $class = '')
{
        $options = get_option('jch_options');

        if (isset($options[$option]))
        {
                $selected_values = Helper::getArray($options[$option]);
        }
        else
        {
                $selected_values = array();
        }

        $select = '<select id="jch_options_' . $option . '" name="jch_options[' . $option . '][]" class="inputbox chzn-custom-value input-xlarge ' . $class . '" multiple="multiple" size="5" data-custom_group_text="Custom Position" data-no_results_text="Add custom item">';

        foreach ($values as $key => $value)
        {
                $selected = in_array($key, $selected_values) ? 'selected="selected"' : '';
                $select   .= '          <option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $select .= '</select>';
        $select .= '<button class="btn" type="button" onclick="addJchOption(\'jch_options_' . $option . '\')">' . __('Add item', 'jch-optimize') . '</button>';

        if ($description)
        {
                $select .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $select;
}

function jch_get_auto_settings_buttons()
{
        return Admin::getSettingsIcons();
}

function jch_get_admin_object()
{
        static $oJchAdmin = null;

        if (is_null($oJchAdmin))
        {
                global $jch_redirect;

                $params    = Settings::getInstance(get_option('jch_options'));
                $oJchAdmin = new Admin($params, true);

                if (get_transient('jch_optimize_ao_exception'))
                {
                        delete_transient('jch_optimize_ao_exception');
                }
                else
                {
                        try
                        {
                                $oHtml = new Html($params);
                                $sHtml = $oHtml->getOriginalHtml();
                                $oJchAdmin->getAdminLinks($sHtml, '');
                        }
                        catch (RunTimeException $ex)
                        {
                                jch_add_notices('info', $ex->getMessage());
                                set_transient('jch_optimize_ao_exception', 1, 1);

                                $jch_redirect = true;
                        }
                        catch (Exception $ex)
                        {
                                Logger::log($ex->getMessage(), $params);

                                jch_add_notices('error', $ex->getMessage());
                                set_transient('jch_optimize_ao_exception', 1, 1);

                                $jch_redirect = true;
                        }
                }
        }

        return $oJchAdmin;
}

function jch_get_field_value($sType, $sExcludeParams, $sGroup = '')
{
        $oJchAdmin = jch_get_admin_object();

        return $oJchAdmin->prepareFieldOptions($sType, $sExcludeParams, $sGroup);
}

/* ##<freecode>##

  function jch_gen_proonly_field($description = '')
  {
  $field = '<div><em style="padding: 5px; background-color: white; border: 1px #ccc;">' . __('Only available in Pro Version!', 'jch-optimize') . '</em></div>';

  if ($description != '')
  {
  $field .= '<div class="description"><div>' . $description . '</div></div>';
  }

  return $field;
  }

  ##</freecode>## */


##<procode>##

function jch_get_optimize_images_buttons()
{
        $page    = add_query_arg(array('jch-task' => 'postresults'), admin_url('options-general.php?page=jchoptimize-settings'));
        $aButton = array();

        $aButton[0]['link']   = '';
        $aButton[0]['icon']   = 'fa-compress';
        $aButton[0]['color']  = '#278EB1';
        $aButton[0]['text']   = 'Optimize Images';
        $aButton[0]['script'] = 'onclick="jchOptimizeImages(\'' . $page . '\'); return false;"';
        $aButton[0]['class']  = 'enabled';

        return $aButton;
}

add_action('wp_ajax_filetree', 'jch_ajax_file_tree');

function jch_ajax_file_tree()
{
        echo Ajax::fileTree();

        die();
}

add_action('wp_ajax_optimizeimages', 'jch_ajax_optimize_images');

function jch_ajax_optimize_images()
{
        echo Ajax::optimizeImages();

        die();
}

##</procode>##
