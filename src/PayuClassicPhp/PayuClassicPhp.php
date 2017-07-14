<?php
namespace PayuClassicPhp;

use PayuClassicPhp\Src\Form\FormOpenInterface;
use PayuClassicPhp\Src\Form\FormRenderInterface;
use PayuClassicPhp\Src\PayuForm;
use PayuClassicPhp\Src\ReceiveTransaction;

class PayuClassicPhp
{
	/**
	 * @var Number
	 */
	protected $posId;
	/**
	 * @var String
	 */
	protected $key1;
	/**
	 * @var String
	 */
	protected $key2;
	/**
	 * @var String
	 */
	protected $posAuthKey;
	/**
	 * @var PayuForm
	 */
	private $formData;

	/**
	 * PayuClassicPhp constructor.
	 * @param Number $posId
	 * @param String $key1
	 * @param String $key2
	 * @param String $posAuthKey
	 */
	public function __construct($posId, $key1, $key2, $posAuthKey)
	{
		$this->posId = $posId;
		$this->key1 = $key1;
		$this->key2 = $key2;
		$this->posAuthKey = $posAuthKey;
	}

	public function receiveTransaction($postData, $test)
	{
		$receive = new ReceiveTransaction($this->posId, $this->key1, $this->key2, $test);
		
		return $receive->receive($postData);
	}

	/**
	 * @param PayuForm $payuForm
	 */
	public function setFormData(PayuForm $payuForm)
	{
		$this->formData = $payuForm;
	}

	/**
	 * @param FormRenderInterface $formRender
	 * @return string
	 */
	public function getFormFieldsRendered(FormRenderInterface $formRender)
	{
		$formRender->setData(
			$this->formData->getDataReady(
				$this->posId,
				$this->posAuthKey,
				$this->key2
			)
		);
		
		return $formRender->render();
	}


	/**
	 * @param FormOpenInterface $formOpen
	 * @return mixed
	 */
	public function getFormOpen( FormOpenInterface $formOpen)
	{
		return $formOpen->render();
	}

}
