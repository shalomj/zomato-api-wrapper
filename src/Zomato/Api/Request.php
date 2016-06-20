<?php 

namespace Zomato\Api;

use Zomato\Exceptions\ZomatoException;

class Request 
{
	/**
	 * The key used for the API
	 * 
	 * @var string
	 */
	private $apiKey;

	/**
	 * The request url for the API
	 * 
	 * @var string
	 */
	private $url;

	/**
	 * Result of the request
	 * 
	 * @var string
	 */
	private $result;

	/**
	 * Error flag
	 * 
	 * @var boolean
	 */
	private $error = false;

	/**
	 * The error message from the API
	 * 
	 * @var string
	 */
	private $errorMessage = '';

	/**
	 * Constructor
	 * 
	 * @param string $apiKey
	 * @param string $url   
	 */
	public function __construct($apiKey, $url) 
	{
		$this->apiKey = $apiKey;
		$this->url = $url;
	}

	/**
	 * Send the request
	 * 
	 * @return \Zomato\Api\Request
	 */
	public function send() 
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_USERAGENT, 'Zomato API PHP wrapper');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'user_key: ' . $this->apiKey]);
        curl_setopt($ch, CURLOPT_URL, $this->url);

        $result = curl_exec($ch);

        if ( ! $result) {
        	$this->setError(true, curl_error($ch));
        } else {
        	$this->handleResponseCode($ch);

        	$this->result = $result;
        }

        curl_close($ch);

        return $this;
	}

	/**
	 * Throw message for different response code from the API
	 * 
	 * @param  curl $ch
	 */
	private function handleResponseCode($ch) 
	{
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		switch ($http_code) {
			case 404:
					$message = 'The requested url not found';
				break;
			
			case 403:
					$message = 'Invalid API Key';
				break;

			default:
					$message = '';
				break;
		}

		if ( ! empty($message)) {
			throw new ZomatoException($message);
		}
	}

	/**
	 * Result getter
	 * 
	 * @return string
	 */
	public function getResult() 
	{
		return $this->result;
	}

	/**
	 * Determines if the request is successful
	 * 
	 * @return bool
	 */
	public function success() 
	{
		return ! $this->fails();
	}

	/**
	 * Determines if the request is failed
	 * 
	 * @return bool
	 */
	public function fails() 
	{
		return $this->error;
	}

	/**
	 * Error setter
	 * 
	 * @param boolean $status
	 * @param string  $message
	 */
	private function setError($status = true, $message = '') 
	{
		$this->error = $status;
		$this->errorMessage = $message;
	}

	/**
	 * Error message getter
	 * 
	 * @return string
	 */
	public function getError() 
	{
		return $this->errorMessage;
	}
}
