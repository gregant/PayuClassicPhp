<?php

namespace PayuClassicPhp\Src;

use PayuClassicPhp\Src\Exception\InvalidPosidException;
use PayuClassicPhp\Src\Exception\InvalidRequestDataException;
use PayuClassicPhp\Src\Exception\InvalidSigException;
use PayuClassicPhp\Src\Exception\PayuResponseIsEmpty;
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
		$this->validateResponse($response);

		return $response;
	}

	/**
	 * @param $postData
	 */
	private function validateRequest($postData)
	{
		if (!isset($postData['pos_id']) || !isset($postData['session_id']) || !isset($postData['ts']) || !isset($postData['sig'])) {
			throw new InvalidRequestDataException(sprintf("Missing params in post '%s'",
				print_r($postData, true)
			));
		}

		if ($postData['pos_id'] != $this->posId) {
			throw new InvalidPosidException(sprintf("Invalid pos_id '%s'",
				$postData['pos_id']));
		}

		$firstSigCheck = md5($postData['pos_id'] . $postData['session_id'] . $postData['ts'] . $this->key2);
		if ($postData['sig'] != md5($postData['pos_id'] . $postData['session_id'] . $postData['ts'] . $this->key2)) {
			throw new InvalidSigException(sprintf("Invalid sig '%s' != '%s'",
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
	 * @throws PayuResponseIsEmpty
	 */
	private function validateResponse($response)
	{
		if( !isset($response->trans) ) {
			throw new PayuResponseIsEmpty(sprintf("Missing params in Payu response '%s'",
				print_r($response, true)
			));
		}

		$trans = $response->trans;
		if (!isset($trans->pos_id) || !isset($trans->session_id) || !isset($trans->order_id) || !isset($trans->status)
			|| !isset($trans->amount) || !isset($trans->desc) || !isset($trans->ts)
		) {
			throw new InvalidRequestDataException(
				sprintf("Missing params in response '%s'", print_r($trans, true)
			));
		}

		if ($trans->pos_id != $this->posId) {
			throw  new InvalidRequestDataException(
				sprintf("Invalid pos_id in response '%s'",$trans->pos_id)
			);
		}

		$sig = md5($trans->pos_id . $trans->session_id . $trans->order_id . $trans->status .
			$trans->amount . $trans->desc . $trans->ts . $this->key2);
		if ($sig != $trans->sig) {
			throw new InvalidRequestDataException(
				sprintf("Invalid sig in response '%s' != '%s'", $trans->sig, $sig
			));
		}
	}


}
