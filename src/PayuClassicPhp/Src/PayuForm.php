<?php
namespace PayuClassicPhp\Src;

class PayuForm
{
    /**
     * @var string
     */
    private $session_id;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $desc;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $post_code;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $client_ip;

	/**
	 * @var int
	 */
	private $order_id;
    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->session_id = $sessionId;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    }

	/**
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * @param string $postCode
	 */
	public function setPostCode($postCode)
	{
		$this->post_code = $postCode;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @param int $orderId
	 */
	public function setOrderId($orderId)
	{
		$this->order_id = $orderId;
	}

	/**
	 * @param mixed $clientIp
	 */
	public function setClientIp($clientIp)
	{
		$this->client_ip = $clientIp;
	}


	/**
	 * @param $posId
	 * @param $posAuthKey
	 * @param $key2
	 * @return array
	 */
	public function getDataReady($posId, $posAuthKey, $key2)
	{
		$formData = [];

		foreach( get_class_vars(get_class($this)) as $key => $value ) {
            if( $this->{$key} != '') {
                $formData[$key] = $this->{$key};
            }
		}
		$formData['pos_id'] = $posId;
		$formData['pos_auth_key'] = $posAuthKey;
		$formData['ts'] = time();
		ksort($formData);
		$formData['sig'] = $this->buildSig($formData, $key2);

		return $formData;
	}

	/**
	 * @param $formData
	 * @param $key2
	 * @return string
	 */
	private function buildSig($formData, $key2)
	{
		$content = '';
		foreach ($formData as $key => $value) {
			$content .= $key . '=' . urlencode($value) . '&';
		}
		$content .= $key2;

		return hash('sha256', $content);
	}


}
