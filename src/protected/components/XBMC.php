<?php

/**
 * Application component for accessing XBMC's JSON-RPC API and related functions
 *
 * @author Sam Stenvall <neggelandia@gmail.com>
 * @copyright Copyright &copy; Sam Stenvall 2013-
 * @license https://www.gnu.org/licenses/gpl.html The GNU General Public License v3.0
 */
class XBMC extends CApplicationComponent
{
	
	/**
	 * @var SimpleJsonRpcClient\Client the JSON-RPC client
	 */
	private $_client;
	
	/**
	 * @var array configuration parameters
	 */
	private $_params;
	
	/**
	 * Initializes the component
	 */
	public function init()
	{
		// Set up the JSON-RPC client
		$endpoint = 'http://'.Yii::app()->config->get('hostname').':'
				.Yii::app()->config->get('port').'/jsonrpc';

		$this->_client = new SimpleJsonRpcClient\Client($endpoint, 
				Yii::app()->config->get('username'), Yii::app()->config->get('password'));

		parent::init();
	}
	
	/**
	 * Wrapper for \SimpleJsonRpcClient\Request
	 * @param string $method
	 * @param mixed $params
	 * @param mixed $id
	 * @return \SimpleJsonRpcClient\Response
	 * @throws CHttpException if the request fails completely
	 */
	public function performRequest($method, $params = null, $id = 0)
	{
		try
		{
			$request = new SimpleJsonRpcClient\Request($method, $params, $id);
			
			return $this->_client->performRequest($request);
		}
		catch (SimpleJsonRpcClient\Exception $e)
		{
			// Rethrow as CHttpException so we get to the error page
			throw new CHttpException(500, $e->getMessage());
		}
	}

	/**
	 * Returns the absolute URL to the specified API path
	 * @param string $path a path returned from an API call
	 * @return string
	 */
	public function getAbsoluteVfsUrl($path)
	{
		// Use reverse proxy for vfs:// paths (if specified)
		$proxyLocation = Yii::app()->config->get('proxyLocation');

		if (!empty($proxyLocation) && substr($path, 0, 3) === 'vfs')
			return 'http://'.$_SERVER['HTTP_HOST'].$proxyLocation.'/'.$path;
		else
		{
			return 'http://'.Yii::app()->config->get('username').':'.Yii::app()->config->get('password').'@'
					.Yii::app()->config->get('hostname').':'.Yii::app()->config->get('port').'/'
					.$path;
		}
	}

}
