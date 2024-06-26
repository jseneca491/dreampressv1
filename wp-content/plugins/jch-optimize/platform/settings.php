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

use JchOptimize\Interfaces\SettingsInterface;

defined('_WP_EXEC') or die('Restricted access');

class Settings implements SettingsInterface
{
	private $params;

	/**
	 *
	 * @param   array  $params
	 *
	 * @return Settings
	 */
	public static function getInstance($params)
	{
		return new Settings($params);
	}

	/**
	 *
	 * @param   string  $param
	 * @param   mixed   $default
	 *
	 * @return mixed
	 */
	public function get($param, $default = null)
	{
		if (!isset($this->params[$param]))
		{
			return $default;
		}

		return $this->params[$param];
	}

	/**
	 *
	 * @param   array  $params
	 */
	private function __construct($params)
	{
		$this->params = $params;

		if (!defined('JCH_DEBUG'))
		{
			define('JCH_DEBUG', ($this->get('debug', 0)));
		}
	}

	/**
	 *
	 * @param   string  $param
	 * @param   mixed   $value
	 */
	public function set($param, $value)
	{
		$this->params[$param] = $value;
	}

	/**
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->params;
	}

	/**
	 * Delete a value from the settings array
	 *
	 * @param   string  $param  The parameter value to be deleted
	 *
	 * @return   null
	 */
	public function remove($param)
	{
		unset($this->params[$param]);

		return;
	}
}
