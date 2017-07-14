<?php

namespace PayuClassicPhp\Src\Form;


class FormToHtml implements FormRenderInterface
{

	/**
	 * @var
	 */
	private $data;

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function render()
	{
		$formRendered = '';
		foreach ($this->data as $name => $value) {
			$formRendered .= $this->inputField($name, $value);
		}

		return $formRendered;
	}

	/**
	 * @param $name
	 * @param $value
	 * @return string
	 */
	private function inputField($name, $value)
	{
		return '<input type="hidden" ' .
		'name="' . $name . '" ' .
		'value="' . $value . '" />';
	}


}
