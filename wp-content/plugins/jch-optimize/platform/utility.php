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

use JchOptimize\Interfaces\UtilityInterface;

defined('_WP_EXEC') or die('Restricted access');

class Utility implements UtilityInterface
{

	/**
	 *
	 * @param   string  $text
	 *
	 * @return string
	 */
	public static function translate($text)
	{
		return __($text, 'jch-optimize');
	}

	/**
	 *
	 * @return integer
	 */
	public static function unixCurrentDate()
	{
		return current_time('timestamp', true);
	}

	/*
	 *
	 */

	public static function getEditorName()
	{
		return '';
	}

	/**
	 *
	 * @param   string  $message
	 * @param   string  $priority
	 * @param   string  $filename
	 */
	public static function log($message, $priority, $filename)
	{
		$file = Utility::getLogsPath() . '/jch-optimize.log';

		error_log($message . "\n", 3, $file);
	}

	/**
	 *
	 * @return string
	 */
	public static function lnEnd()
	{
		return "\n";
	}

	/**
	 *
	 * @return string
	 */
	public static function tab()
	{
		return "\t";
	}

	/**
	 *
	 * @param   string  $path
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public static function createFolder($path)
	{
		//Create all necessary parent folders
		if (!is_dir(dirname($path)))
		{
			if (!self::createFolder(dirname($path)))
			{
				return false;
			}
		}

                $wp_filesystem = Cache::getWpFileSystem();

		return $wp_filesystem->mkdir($path);
	}

	/**
	 *
	 * @param   string  $file
	 * @param   string  $contents
	 *
	 * @return bool
	 * @throws \JchOptimize\Core\Exception
	 * @throws \Exception
	 */
	public static function write($file, $contents)
	{
		//Make sure parent folder exists
		if (!file_exists(dirname($file)))
		{
			if (!self::createFolder(dirname($file)))
			{
				return false;
			}
		}

		$wp_filesystem = Cache::getWpFileSystem();

		return $wp_filesystem->put_contents($file, $contents);
	}

	/**
	 *
	 * @param   string  $value
	 *
	 * @return string
	 */
	public static function decrypt($value)
	{
		return self::encrypt_decrypt($value, 'decrypt');
	}

	/**
	 *
	 * @param   string  $value
	 *
	 * @return string
	 */
	public static function encrypt($value)
	{
		return self::encrypt_decrypt($value, 'encrypt');
	}

	/**
	 *
	 * @param   string  $value
	 * @param   string  $action
	 *
	 * @return string
	 */
	private static function encrypt_decrypt($value, $action)
	{

		$output = false;

		$encrypt_method = "AES-256-CBC";
		$secret_key     = AUTH_KEY;
		$secret_iv      = AUTH_SALT;

		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		if ($action == 'encrypt')
		{
			if (version_compare(PHP_VERSION, '5.3.3', '<'))
			{
				$output = @openssl_encrypt($value, $encrypt_method, $key, 0);
			}
			else
			{
				$output = openssl_encrypt($value, $encrypt_method, $key, 0, $iv);
			}
			$output = base64_encode($output);
		}
		else if ($action == 'decrypt')
		{
			if (version_compare(PHP_VERSION, '5.3.3', '<'))
			{
				$output = @openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0);
			}
			else
			{
				$output = openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0, $iv);
			}
		}

		return $output;
	}

	/**
	 *
	 *
	 * @param   string  $value
	 * @param   string  $default
	 * @param   string  $filter
	 * @param   string  $method
	 *
	 * @return mixed
	 */
	public static function get($value, $default = '', $filter = 'cmd', $method = 'request')
	{
		$request = '_' . strtoupper($method);

		if (!isset($GLOBALS[$request][$value]))
		{
			$GLOBALS[$request][$value] = $default;
		}

		switch ($filter)
		{
			case 'int':
				$filter = FILTER_SANITIZE_NUMBER_INT;

				break;

			case 'array':
			case 'json':
				return (array) $GLOBALS[$request][$value];
			case 'string':
			case 'cmd':
			default :
				$filter = FILTER_SANITIZE_STRING;

				break;
		}

		switch ($method)
		{
			case 'get':
				$type = INPUT_GET;

				break;

			case 'post':
				$type = INPUT_POST;

				break;

			default:

				return filter_var($_REQUEST[$value], $filter);
		}


		$input = filter_input($type, $value, $filter);

		return is_null($input) ? $default : $input;
	}

	/**
	 *
	 */
	public static function getLogsPath()
	{
		return JCH_PLUGIN_DIR . 'logs';
	}

	/**
	 *
	 * @param   string  $url
	 */
	public static function loadAsync($url)
	{

	}

	/**
	 *
	 */
	public static function menuId()
	{

	}

	/**
	 *
	 * @param   string  $path
	 * @param   string  $filter
	 * @param   bool    $recurse
	 * @param   array   $exclude
	 *
	 * @return array
	 * @throws \JchOptimize\Core\Exception
	 */
	public static function lsFiles($path, $filter = '.', $recurse = false, $exclude = array())
	{
		$wp_filesystem = Cache::getWpFileSystem();

		$items = $wp_filesystem->dirlist($path, false, $recurse);

		$files = array();

		if (!empty($items))
		{
			self::filterItems($path, $filter, $items, $files);
		}

		return $files;
	}

	/**
	 *
	 * @param   string  $path
	 * @param   string  $filter
	 * @param   array   $items
	 * @param   array   $files
	 */
	protected static function filterItems($path, $filter, $items, &$files)
	{
		foreach ($items as $item)
		{
			if ($item['name'] == 'jch_optimize_backup_images')
			{
				continue;
			}

			if ($item['type'] == 'f' && preg_match('#' . $filter . '#', $item['name']))
			{
				$files[] = $path . '/' . $item['name'];
			}

			if ($item['type'] == 'd' && !empty($item['files']))
			{
				self::filterItems($path . '/' . $item['name'], $filter, $item['files'], $files);
			}
		}
	}

	/**
	 * Checks if user is not logged in
	 *
	 */
	public static function isGuest()
	{
		return !is_user_logged_in();
	}


	public static function sendHeaders($headers)
	{
		if (!empty($headers))
		{
			foreach ($headers as $header => $value)
			{
				header($header . ': ' . $value);
			}
		}

	}
}
