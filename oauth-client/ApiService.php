<?php

namespace App\Services;

use Cache;
use URl;
use App\Models\ApiSetting;
use GuzzleHttp\Client;
class ApiService
{
	private $access_token;
	private $refresh_token;
	public function __construct(ApiSetting $apiSetting, Client $client)
	{
		$this->apiSetting = $apiSetting;
		$this->client     = $client;
		$this->apiUrl     = env('API_URL', 'http://api.abcn.com');
		$this->api_key    = config('api.api_key');
		$this->api_secret = config('api.api_secret');
		$this->getAccessToken();
		$this->checkApiCredentials();
	}

	public function getAllLocations()
	{
		$response = $this->client->request('GET', 
			$this->apiUrl.'/location?api_key='.$this->api_key.'&api_secret='.$this->api_secret.'&access_token='.$this->access_token
		);
		return json_decode($response->getBody(), true);
	}

	public function getSearch($key)
	{
		$response = $this->client->request('GET', 
			$this->apiUrl.'/location/search/'.$key.'?api_key='.$this->api_key.'&api_secret='.$this->api_secret.'&access_token='.$this->access_token
		);		
		return json_decode($response->getBody(), true);
	}

	private function checkApiCredentials()
	{				
		if(null == $this->access_token){			
			$this->getNewAccessToken();
		}
		$response = $this->client->request('POST',$this->apiUrl.'/check-access-token',[
				'query' => [
					'api_key' => $this->api_key,
					'api_secret' => $this->api_secret,
					'access_token' => $this->access_token,
				]
			]);
		if($response->getStatusCode() == 301){
			$this->refreshAccessToken();
		}
		$body = json_decode($response->getBody());
	}

	private function refreshAccessToken()
	{
		$response = $this->client->request('POST',$this->apiUrl.'/refresh-token',[
				'query' => [
					'api_key' => $this->api_key,
					'api_secret' => $this->api_secret,
					'refresh_token' => $this->refresh_token
				]
			]);
		$body = json_decode($response->getBody());
		$access_token = $body->access_token;
		$refresh_token = $body->refresh_token;
		$this->saveNewAccessToken($access_token, $refresh_token);
	}

	private function getNewAccessToken()
	{		
		$response = $this->client->request('POST',$this->apiUrl.'/authorization',[
				'query' => [
					'api_key' => $this->api_key,
					'api_secret' => $this->api_secret,
				]
			]);
		$body = json_decode($response->getBody());
		$access_token = $body->access_token;
		$refresh_token = $body->refresh_token;
		$this->saveNewAccessToken($access_token, $refresh_token);		
	}

	private function saveNewAccessToken($access_token, $refresh_token)
	{
		if(null!== $this->apiSetting->create([
			'access_token' => $access_token,
			'refresh_token' => $refresh_token
		])){
			$this->access_token = $access_token;
			$this->refresh_token = $refresh_token;
		}
	}

	private function getAccessToken()
	{
		$access_token = $this->apiSetting->first();
		if(null!== $access_token){
			$this->access_token = $access_token->access_token;
		}
	}
}