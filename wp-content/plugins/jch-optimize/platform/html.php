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

namespace JchOptimize\Platform;

defined('_WP_EXEC') or die('Restricted access');

use JchOptimize\Core\FileRetriever;
use JchOptimize\Core\Logger;
use JchOptimize\Core\Exception;
use JchOptimize\Interfaces\HtmlInterface;

class Html implements HtmlInterface
{
        protected $params;
        
        public function __construct($params)
        {
                $this->params = $params;
        }
        
        public function getOriginalHtml()
        {
                JCH_DEBUG ? Profiler::mark('beforeGetHtml') : null;
                
                $url = home_url() . '/?jchbackend=1';
                
                try
                {
                        $oFileRetriever = FileRetriever::getInstance();

                        $response = $oFileRetriever->getFileContents($url);

                        if ($oFileRetriever->response_code != 200)
                        {
                                throw new Exception(
                                Utility::translate('Failed fetching front end HTML with response code ' . $oFileRetriever->response_code)
                                );
                        }

                        JCH_DEBUG ? Profiler::mark('afterGetHtml') : null;

                        return $response;
                }
                catch (Exception $e)
                {
                        Logger::log($url . ': ' . $e->getMessage(), $this->params);

                        JCH_DEBUG ? Profiler::mark('afterGetHtml)') : null;

                        throw new \RunTimeException(_('Load or refresh the front-end site first then refresh this page '
                                . 'to populate the multi select exclude lists.'));
                }
        }
}

