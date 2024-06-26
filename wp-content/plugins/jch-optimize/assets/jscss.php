<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for 
 *   optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall. All rights reserved.
 * @license GNU/GPLv3, See LICENSE file 
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
 * 
 * This plugin includes other copyrighted works. See individual 
 * files for details.
 */
include dirname(dirname(__FILE__)) . '/dir.php';

define('SHORTINIT', true);

if (!isset($wp_did_header))
{
        $wp_did_header = true;

        require_once( $DIR . 'wp-load.php' );
}

require_once( ABSPATH . WPINC . '/formatting.php' );
require_once( ABSPATH . WPINC . '/link-template.php' );
require_once( ABSPATH . WPINC . '/l10n.php');

wp_plugin_directory_constants();

$GLOBALS['wp_plugin_paths'] = array();

$plugin = WP_PLUGIN_DIR . '/jch-optimize/jch-optimize.php';

if(!file_exists($plugin))
{
        exit('Plugin not found');
}

wp_register_plugin_realpath($plugin);

require_once($plugin);


JchOptimize\Core\Output::getCombinedFile();

