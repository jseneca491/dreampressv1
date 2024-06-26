<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
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

namespace JchOptimize\Platform;

defined('_WP_EXEC') or die('Restricted access');

use JchOptimize\Core\Exception;

class Cache implements \JchOptimize\Interfaces\CacheInterface
{
	protected static $wp_filesystem;

        /**
         *
         * @param   string  $id
         * @param   bool    $check_expire
         * @param   bool    $page_cache
         *
         * @return string|false
         * @throws Exception
         */
	public static function getCache($id, $check_expire = false, $page_cache = false)
	{
		$wp_filesystem = self::getWpFileSystem();

		if ($wp_filesystem === false)
		{
			return false;
		}

		$file = self::_getFileName($id, $page_cache);

		if (!$wp_filesystem->exists($file))
		{
			return false;
		}

		if ($check_expire && time() > $wp_filesystem->mtime($file) + self::getLifetime($page_cache))
		{
			return false;
		}

		return self::_getCacheFile($file, $wp_filesystem);
	}

	/**
	 *
	 * @param   string    $id
	 * @param   callable  $function
	 * @param   array     $args
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function getCallbackCache($id, $function, $args)
	{
		$wp_filesystem = self::getWpFileSystem();

		if ($wp_filesystem === false)
		{
			return false;
		}

		$file = self::_getFileName($id);

		if (!self::getCache($id, true))
		{
			$contents = call_user_func_array($function, $args);

			return self::saveCache($contents, $id);
		}

		return self::_getCacheFile($file, $wp_filesystem);
	}

	/**
	 *
	 * @param   string  $context
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function deleteCache($context = 'both')
	{
		//Purge LiteSpeed cache
		if ($context != 'plugin' && class_exists('LiteSpeed_Cache_API'))
		{
			\LiteSpeed_Cache_API::purge_all();
		}

		$wp_filesystem = self::getWpFileSystem();

		if ($wp_filesystem === false || !$wp_filesystem->exists(JCH_CACHE_DIR))
		{
			return false;
		}

		if ($context == 'plugin')
		{
		        $result = true;
			$result |= (bool) $wp_filesystem->rmdir(JCH_CACHE_DIR . 'js', true);
                        $result |= (bool) $wp_filesystem->rmdir(JCH_CACHE_DIR . 'css', true);

                        return  $result;
		}

		$cache_dir = dirname(JCH_CACHE_DIR);
		//Get list of all folders in the cache directory (.../wp-content/cache/)
		$cache_dir_list = $wp_filesystem->dirlist($cache_dir, false, false);

		foreach ($cache_dir_list as $entry)
		{
			//Skip the jch-optimize cache if we're only deleting page cache
			if ($context == 'page' && $entry['name'] == 'jch-optimize')
			{
			        $wp_filesystem->rmdir($cache_dir . '/jch-optimize/page', true);

			        continue;
			}


			if ($entry['type'] == 'd')
			{
				//Delete each cache folder, including JCH Optimize cache
				if (!$wp_filesystem->rmdir($cache_dir . DIRECTORY_SEPARATOR . $entry['name'], true))
				{
					return false;
				}
			}
		}

		return true;
	}


	/**
	 *
	 * @param   string               $file
	 * @param   \WP_Filesystem_Base  $wp_filesystem
	 *
	 * @return string
	 */
	private static function _getCacheFile($file, $wp_filesystem)
	{
		$content = $wp_filesystem->get_contents($file);

		return unserialize(base64_decode($content));
	}

	/**
	 *
	 * @throws Exception
	 */
	public static function initializeCache()
	{
		$wp_filesystem = self::getWpFileSystem();

		if ($wp_filesystem !== false )
                {
                        $index_contents = '<html lang=""><body></body></html>';

                        if (!$wp_filesystem->exists(JCH_CACHE_DIR))
                        {
                                if (!$wp_filesystem->exists($wp_filesystem->wp_content_dir() . 'cache'))
                                {
                                        $wp_filesystem->mkdir($wp_filesystem->wp_content_dir() . 'cache', FS_CHMOD_DIR);
                                }

                                $wp_filesystem->mkdir(JCH_CACHE_DIR, FS_CHMOD_DIR);
                                $wp_filesystem->put_contents(JCH_CACHE_DIR . 'index.html', $index_contents, FS_CHMOD_FILE);
                        }

                        if (!$wp_filesystem->exists(JCH_CACHE_DIR . 'page'))
                        {
                                $wp_filesystem->mkdir(JCH_CACHE_DIR . 'page', FS_CHMOD_DIR);
                                $wp_filesystem->put_contents(JCH_CACHE_DIR . 'page/index.html', $index_contents, FS_CHMOD_FILE);
                        }
                }
	}

	/**
	 *
	 * @return \WP_Filesystem_Base|false
	 * @throws Exception
	 */
	public static function getWpFileSystem()
	{
		if (!isset(self::$wp_filesystem))
		{
			/** @var \WP_Filesystem_Base $wp_filesystem */
			global $wp_filesystem;

			$wp_filesystem_cache = $wp_filesystem;

			if (!function_exists('request_filesystem_credentials'))
			{
				include_once Paths::rootPath() . '/wp-admin/includes/file.php';
			}

			add_filter('request_filesystem_credentials', array('JchOptimize\Platform\Cache', 'requestFilesystemCredentials'), 10, 7);

			if (false === ($creds = request_filesystem_credentials(admin_url('options-general.php?page=jchoptimize-settings'), '', false,
					WP_CONTENT_DIR, null, true)))
			{
				$message = 'The plugin needs to access the filesystem via FTP. Please define your FTP credentials in your wp_config.php file like this: <br /><br /><textarea disabled="disabled" rows="3" cols="40" style="background-color: ghostwhite; color: #444; resize: none;">define(\'FTP_HOST\', \'[Your FTP host]\'); &#13;&#10;define(\'FTP_USER\', \'[Your FTP Username]\'); &#13;&#10;define(\'FTP_PASS\', \'[Your FTP password]\'); </textarea>';

				self::$wp_filesystem = false;

				if (function_exists('jch_add_notices'))
				{
					jch_add_notices('error', __($message, 'jch-optimize'));

					return false;
				}
				else
				{
					throw new Exception(__($message, 'jch-optimize'));
				}
			}

			if (false === \WP_Filesystem($creds, WP_CONTENT_DIR, true))
			{
				$message = 'Could not connect to the filesystem. Please check your FTP credentials in wp_config.php file';

				if (function_exists('jch_add_notices'))
				{
					jch_add_notices('error', __($message, 'jch-optimize'));

					return false;
				}
				else
				{
					throw new Exception(__($message));
				}
			}

			self::$wp_filesystem = $wp_filesystem;

			if (!defined('JCH_CACHE_DIR'))
			{
				define('JCH_CACHE_DIR', $wp_filesystem->wp_content_dir() . 'cache/jch-optimize/');
			}

			$wp_filesystem = $wp_filesystem_cache;
		}

		return self::$wp_filesystem;
	}

	/**
	 * @param $value
	 * @param $form_post
	 * @param $type
	 * @param $error
	 * @param $context
	 * @param $extra_fields
	 * @param $allow_relaxed_file_ownership
	 *
	 * @return bool|mixed|void
	 */
	public static function requestFilesystemCredentials($value, $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership)
	{
		$method = get_filesystem_method(array(), $context, $allow_relaxed_file_ownership);

		if ($method == 'direct')
		{
			return true;
		}

		if (!function_exists('wp_generate_password'))
		{
			include_once Paths::rootPath() . '/wp-includes/pluggable.php';
		}

		$credentials = get_option('ftp_credentials', array('hostname' => '', 'username' => ''));

		$credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : $credentials['hostname'];
		$credentials['username'] = defined('FTP_USER') ? FTP_USER : $credentials['username'];
		$credentials['password'] = defined('FTP_PASS') ? FTP_PASS : '';

		if ($method == 'ssh2')
		{
			// Check to see if we are setting the public/private keys for ssh
			$credentials['public_key']  = defined('FTP_PUBKEY') ? FTP_PUBKEY : '';
			$credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : '';
		}

		if (in_array('', $credentials))
		{
			return false;
		}

		return $credentials;
	}

        /**
         *
         * @param   string  $id
         * @param   bool    $page_cache
         *
         * @return string
         */
	private static function _getFileName($id, $page_cache=false)
	{
		return JCH_CACHE_DIR . ($page_cache ? 'page/' : '') . md5(NONCE_SALT . $id);
	}

	/**
	 *
	 * @throws Exception
	 */
	public static function gc()
	{
		$wp_filesystem = self::getWpFileSystem();

		if ($wp_filesystem === false)
		{
			return false;
		}

		$result = true;

                //Delete any page cache
                $result |= self::deleteCache('page');

                $files = Utility::lsFiles(rtrim(JCH_CACHE_DIR, '/\\'), '.', true);
		$now   = time();

		foreach ($files as $file)
		{
			$time = $wp_filesystem->mtime($file);

			if (($time + self::getLifetime()) < $now || empty($time))
			{
				$result |= $wp_filesystem->delete($file);
			}
		}

		return $result;
	}

        /**
         *
         *
         * @param   string  $content
         * @param   string  $id
         * @param   bool    $page_cache
         *
         * @return string
         * @throws Exception
         */
	public static function saveCache($content, $id, $page_cache=false)
	{
		$wp_filesystem = self::getWpFileSystem();
		$file_contents  = base64_encode(serialize($content));
		$file          = self::_getFileName($id, $page_cache);

		self::initializeCache();

		if ($wp_filesystem->put_contents($file, $file_contents, FS_CHMOD_FILE))
		{
			return $content;
		}
		else
		{
			throw new Exception(__('Error writing files to cache'));
		}
	}

	protected static function getLifetime($page_cache=false)
	{
		static $lifetime, $page_cache_lifetime;

		if ($page_cache)
                {
                        if (!$page_cache_lifetime)
                        {
                                $params = Plugin::getPluginParams();
                                $page_cache_lifetime = $params->get('page_cache_lifetime', '900');
                        }

                        return (int) $page_cache_lifetime;
                }

		if (!$lifetime)
		{
			$params = Plugin::getPluginParams();

			$lifetime = $params->get('cache_lifetime', '900');
		}

		return (int) $lifetime;
	}
}
