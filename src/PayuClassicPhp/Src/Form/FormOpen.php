<?php

namespace PayuClassicPhp\Src\Form;


use PayuClassicPhp\Src\PayuConst;

class FormOpen implements FormOpenInterface
{
	/**
	 * @var int
	 */
	private $test;
	/**
	 * @var array
	 */
	private $params;

	/**
	 * FormOpen constructor.
	 * @param $params
	 * @param int $test
	 */
	public function __construct($params, $test = 0)
	{
		$this->params = $params;
		$this->test = $test;
	}

	/**
	 * @return string
	 */
	public function render()
	{
		$params = array_merge([
			'method'         => '"post"',
			'action'         => '"' . PayuConst::getUrl($this->test) . '/NewPayment"',
			'accept-charset' => '"utf-8"'
		], $this->params);

		return '<form ' .
		urldecode(http_build_query($params, '', ' ')) . ' >';
	}

}
