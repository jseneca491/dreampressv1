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

class JchPluginUpdater {

	private $downloadid = '';

	private $updateinfo = '';

	/**
	 * Class constructor
	 *
	 *
	 * $param string $downloadid Download ID
	 *
	 * @return null
	 */
	public function __construct ($downloadid)
	{
		add_filter('pre_set_site_transient_update_plugins', array($this, 'filterTransient'));

		$this->downloadid = $downloadid;
	}

	/**
	 * Update transient with information for automatic pro update
	 *
	 *
	 * @param object $transient
	 *
	 * @return object
	 */ 
	public function filterTransient($transient)
	{
		if(empty($this->downloadid) || !$this->queryUpdateSite())
		{
			return $transient;
		}

		$updateversion = (string) $this->updateinfo->version;
		$downloadurl = (string) $this->updateinfo->downloads->downloadurl;
		
		$downloadurl = add_query_arg( array( 'dlid' => $this->downloadid ), $downloadurl );

		//Check if there's a newer version to the current version installed
		$doupdate = version_compare($updateversion, str_replace('pro-', '', JCH_VERSION), '>');

		if($doupdate)
		{//Insert the transient for the new version
			$obj = new stdClass();

			$obj->slug        = 'jch-optimize';
			$obj->plugin      = 'jch-optimize/jch-optimize.php';
			$obj->new_version =  $updateversion;
			$obj->url         = (string) $this->updateinfo->infourl;
			$obj->package     = $downloadurl;

			$transient->response['jch-optimize/jch-optimize.php'] = $obj;

			unset($transient->no_update['jch-optimize/jch-optimize.php']);
		}

		return $transient;
	}

	/**
	 * Get update information from our update site
	 *
	 *
	 * @return null
	 */
	private function queryUpdateSite()
	{
		$return = false;
		//update site
		$url = 'https://www.jch-optimize.net/index.php?option=com_ars&view=update&task=stream&format=xml&id=3';

		$response = wp_remote_get($url);

		if (!is_wp_error($response) && 200 == (int) wp_remote_retrieve_response_code($response))
		{
			//Should return an xml document containing the update information
			$oXml = simplexml_load_string(wp_remote_retrieve_body($response));

			if($oXml instanceof SimpleXMLElement)
			{
				//Get the most recent update site in the document
				$this->updateinfo = $oXml->update;
				$return = true;
			}
		}

		return $return;
	}
}
