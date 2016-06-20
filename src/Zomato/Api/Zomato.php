<?php 

namespace Zomato\Api;

use Zomato\Api\Request;
use Zomato\Exceptions\ZomatoException;

class Zomato
{
	/**
	 * Zomato api key
	 * 
	 * @var string
	 */
	private $apiKey;

	/**
	 * Zomato api url
	 * 
	 * @var string
	 */
	private $apiUrl = 'https://developers.zomato.com/api/v2.1/';

	/**
	 * Constructor
	 * 
	 * @param string $apiKey
	 */
	public function __construct($apiKey) 
	{
		if ( ! $apiKey) {
			throw new ZomatoException("No API key provided.");
		}

		$this->apiKey = $apiKey;
	}

	/**
	 * Query the api
	 * 
	 * @param  string $module
	 * @param  array  $params
	 * @return mix
	 */
	public function query($module, array $params = []) 
	{
		$this->validateModule($module);

		$url = $this->getUrl($module, $params);

		return $this->request($url);
	}

	/**
	 * Check if the module exists
	 * 
	 * @param  string $module
	 * @return \Zomato\Exceptions\ZomatoException | bool
	 */
	private function validateModule($module) 
	{
		$modules = $this->getModules();

		if ( ! array_key_exists($module, $modules)) {
			throw new ZomatoException("Invalid module: {$module}");
		}

		return true;
	}

	/**
	 * Get request url
	 * 
	 * @param  string $module
	 * @param  array  $params
	 * @return string
	 */
	private function getUrl($module, $params = []) 
	{
		if ( ! $this->validateParams($module, $params)) {
			throw new ZomatoException("Please provide the required parameters for {$module}");
		}

		return $this->apiUrl . $module . $this->encodeParams($params);
	}

	/**
	 * Check for required paramaters of the module
	 * 
	 * @param  string $module
	 * @param  array  $params
	 * @return bool        
	 */
	private function validateParams($module, $params = []) 
	{
		$required = $this->getRequiredParams($module);

		foreach ($required as $param) {
			if ( ! array_key_exists($param, $params)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Getter of module's required parameters
	 * 
	 * @param  string $module
	 * @return array        
	 */
	private function getRequiredParams($module) 
	{
		$modules = $this->getModules();

		return ! empty($modules[$module]) ? $modules[$module] : [];
	}

	/**
	 * UR encode parameters
	 * 
	 * @param  array  $params
	 * @return string       
	 */
	private function encodeParams(array $params = []) 
	{
		if (empty($params)) return '';

		return '?' . http_build_query($params);
	}

	/**
	 * Perform request
	 * 
	 * @param  string $url
	 * @return json   
	 */
	private function request($url) 
	{
		$request = new Request($this->apiKey, $url);

		$request->send();

		if ($request->fails()) {
			throw new ZomatoException("cURL error: {$request->getError()}");
		}

		return $request->getResult();
	}

	/**
	 * Getter for modules
	 * 
	 * @return array
	 */
	private function getModules() 
	{
		$modules = [
			// Common
			'categories'       => [], 
			'cities'           => [], 
			'collections'      => ['city_id'], 
			'cuisines'         => [], 
			'establishments'   => ['city_id'], 
			'geocode'          => ['lat', 'lon'],
			// Location
			'location_details' => ['entity_id', 'entity_type'], 
			'locations'        => ['query'], 
			// Restaurant
			'dailymenu'        => ['res_id'], 
			'restaurant'       => ['res_id'], 
			'reviews'          => ['res_id'], 
			'search'           => [],
		];

		return $modules;
	}

	/**
	 * Handle dynamic method call
	 * 
	 * @param  string $method
	 * @param  array $args  
	 * @return mix      
	 */
	public function __call($method, $args) 
	{
		$this->validateModule($method);

		$args = ! empty($args) ? current($args) : [];

		return $this->query($method, $args);
	}
}
