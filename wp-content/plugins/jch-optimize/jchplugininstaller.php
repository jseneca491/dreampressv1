<?php

/**
 * JCH Optimize - Plugin to aggregate and minify external resources for
 * optmized downloads
 *
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2014 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
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

use JchOptimize\Platform\Cache;
use JchOptimize\Core\Admin;

class JchPluginInstaller {


	public function __construct ()
	{
	}

	/**
	 * Fires when plugin is activated and create a dir.php file in plugin root containing 
	 * absolute path of plugin install
	 *
	 *
	 * @return null
	 */
	public function activate()
	{
		//$this->initializeFileSystem();

		//global $wp_filesystem;

		try
		{
			$wp_filesystem = Cache::getWpFileSystem();
		}
		catch(Exception $e)
		{
			return false;
		}

		if ($wp_filesystem === false)
		{
			return false;
		}

		$file    = JCH_PLUGIN_DIR . 'dir.php';
		$abspath = ABSPATH;
		$code    = <<<PHPCODE
<?php
           
\$DIR = '$abspath';
           
PHPCODE;

		$wp_filesystem->put_contents($file, $code, FS_CHMOD_FILE);
		Admin::leverageBrowserCaching();
	}

	/**
	 * Initialize the WordPress FileSystem API, 
	 *
	 *
	 * @return null
	 */
	protected function initializeFileSystem()
	{

		$access_type = get_filesystem_method();
		if($access_type === 'direct')
		{
			/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
			$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

			/* initialize the API */
			if ( ! \WP_Filesystem($creds) ) {
				/* any problems and we exit */
				return false;
			}	

		}	
		else
		{
			/* don't have direct write access. Prompt user with our notice */
			//add_action('admin_notices', 'you_admin_notice_function'); 	
		}
	}	
}
