<?php
if (!defined('ABSPATH')) die();


/**
 * MISC: Disable Projects Custom Post Type
 * @since 2.1.0
 */
function divi_child_unregister_projects() {
  unregister_taxonomy('project_category');
  unregister_taxonomy('project_tag');
  unregister_post_type('project');
}
if (divi_child_get_theme_option('disable_projects') === 'on') {
  add_action('init','divi_child_unregister_projects', 100);
}


/**
 * MISC: Stops core auto update email notifications
 * @since 2.0.0
 * @since WordPress 5.5
 */
function divi_child_stop_update_mails($send, $type, $core_update, $result)
{
  if (!empty($type) && $type == 'success') {
    return false;
  }
  return true;
}
if (divi_child_get_theme_option('stop_mail_updates') === 'on') {
  add_filter('auto_core_update_send_email', 'divi_child_stop_update_mails', 10, 4); // core
  add_filter('auto_plugin_update_send_email', '__return_false'); // plugins
  add_filter('auto_theme_update_send_email', '__return_false'); // themes
}


/**
 * MISC: Adds SVG & WebP support for file uploads
 */
function divi_child_supported_filetypes($filetypes)
{
  $new = array();
  if (divi_child_get_theme_option('svg_support') === 'on') {
    $new['svg'] = 'image/svg';
  }
  if (divi_child_get_theme_option('webp_support') === 'on') {
    $new['webp'] = 'image/webp';
  }
  return array_merge($filetypes, $new);
}
add_action('upload_mimes', 'divi_child_supported_filetypes');


/**
 * MISC: Add hyphenation to the whole website
 * @since 2.1.0
 */
function divi_child_hyphens() {
  ?><style id="divi-child-hyphens" type="text/css">*,html{word-break: break-word;hyphens: auto;-ms-hyphens: auto;-webkit-hyphens: auto}</style><?php
}
if (divi_child_get_theme_option('hyphens') === 'on') {
  add_action('wp_head', 'divi_child_hyphens', 10);
}