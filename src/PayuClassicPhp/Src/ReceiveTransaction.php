<?php

namespace PayuClassicPhp\Src;


use PayuClassicPhp\Src\Exception\InvalidRequestDataException;
use Prophecy\Exception\Exception;

class ReceiveTransaction
{
	private $posId;
	private $key1;
	private $key2;
	private $test;

	public function __construct($posId, $key1, $key2, $test)
	{
		$this->posId = $posId;
		$this->key1 = $key1;
		$this->key2 = $key2;
		$this->test = $test;
	}

	/**
	 * @param $postData
	 * @return \SimpleXMLElement
	 * @throws \Exception
	 */
	public function receive($postData)
	{
		$this->validateRequest($postData);
		$response = $this->getPaymentData($postData);
		$this->validateResponse($response->trans);

		return $response;
	}

	/**
	 * @param $postData
	 * @throws InvalidRequestDataException
	 */
	private function validateRequest($postData)
	{
		if (!isset($postData['pos_id']) || !isset($postData['session_id']) || !isset($postData['ts']) || !isset($postData['sig'])) {
			throw new InvalidRequestDataException(sprintf("Missing params in post '%s'",
				print_r($postData, true)
			));
		}

		if ($postData['pos_id'] != $this->posId) {
			throw  new InvalidRequestDataException(sprintf("Invalid pos_id '%s'",
				$postData['pos_id']));
		}

		$firstSigCheck = md5($postData['pos_id'] . $postData['session_id'] . $postData['ts'] . $this->key2);
		if ($postData['sig'] != md5($postData['pos_id'] . $postData['session_id'] . $postData['ts'] . $this->key2)) {
			throw new InvalidRequestDataException(sprintf("Invalid sig '%s' != '%s'",
				$postData['sig'], $firstSigCheck));
		}
	}

	/**
	 * @param $postData
	 * @return \SimpleXMLElement
	 * @throws \Exception
	 */
	private function getPaymentData($postData)
	{
		$ts = time();
		$params = [
			'pos_id'     => $this->posId,
			'session_id' => $postData['session_id'],
			'ts'         => $ts,
			'sig'        => md5($this->posId . $postData['session_id'] . $ts . $this->key1)
		];
		try {
			return $this->decodeXmlResponse($this->makeRequest($params));
		} catch (Exception $e) {
			throw(new \Exception('Payment Request exception', 0, $e));
		}
	}

	/**
	 * @param $response
	 * @return \SimpleXMLElement
	 */
	private function decodeXmlResponse($response)
	{
		return simplexml_load_string(substr($response,
			strpos($response, '<?xml'),
			strlen($response)
		));
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	private function makeRequest($params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, PayuConst::getUrl($this->test) . '/Payment/get');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		return curl_exec($ch);
	}

	/**
	 * @param $response
	 */
	private function validateResponse($response)
	{
		if (!isset($response->pos_id) || !isset($response->session_id) || !isset($response->order_id) || !isset($response->status)
			|| !isset($response->amount) || !isset($response->desc) || !isset($response->ts)
		) {
			throw new InvalidRequestDataException(
				sprintf("Missing params in response '%s'", print_r($response, true)
			));
		}

		if ($response->pos_id != $this->posId) {
			throw  new InvalidRequestDataException(
				sprintf("Invalid pos_id in response '%s'",$response->pos_id)
			);
		}

		$sig = md5($response->pos_id . $response->session_id . $response->order_id . $response->status .
			$response->amount . $response->desc . $response->ts . $this->key2);
		if ($sig != $response->sig) {
			throw new InvalidRequestDataException(
				sprintf("Invalid sig in response '%s' != '%s'", $response->sig, $sig
			));
		}
	}


}
