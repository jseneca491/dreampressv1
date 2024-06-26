<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
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
 */

namespace JchOptimize\Platform;

defined('_WP_EXEC') or die('Restricted access');

class Plugin implements \JchOptimize\Interfaces\PluginInterface
{

        protected static $plugin = null;

        /**
         * 
         * @return void
         */
        public static function getPluginId()
        {
                return;
        }

        /**
         * 
         * @return void
         */
        public static function getPlugin()
        {
                return;
        }

        /**
         * 
         * @param Settings $params
         */
        public static function saveSettings($params)
        {
                $options = $params->getOptions();

                update_option('jch_options', $options);
        }

        /**
         * 
         * @return Settings
         */
        public static function getPluginParams()
        {
                static $params = null;

                if (is_null($params))
                {
                        $options = get_option('jch_options');
                        $params  = Settings::getInstance($options);
                }

                return $params;
        }

}
