<?php

namespace PayuClassicPhp\Src;


final class PayuConst
{
	const REQUEST_FORM_URL = 'https://secure.payu.com/paygw/UTF';
	const REQUEST_FORM_URL_SANDBOX = 'https://secure.snd.payu.com/paygw/UTF';

	/**
	 * @param int $test
	 * @return string
	 */
	public static function getUrl($test = 0) {
		if ($test == 0) {
			return self::REQUEST_FORM_URL;
		}

		return self::REQUEST_FORM_URL_SANDBOX;
	}
}
