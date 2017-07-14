<?php

use PayuClassicPhp\PayuClassicPhp;
use PayuClassicPhp\Src\Form\FormOpen;
use PayuClassicPhp\Src\Form\FormToHtml;
use PayuClassicPhp\Src\PayuForm;

class FormCreationTests extends PHPUnit_Framework_TestCase
{
    public function testHtmlFormFieldsCreation()
    {
        $pos_id = 111111;
        $key1 = 'aaaaaabbbbbcccccdddd';
        $key2 = 'ddddccccbbbbaaaaaa';
        $pos_auth_key = '22222';

        $payu = new PayuClassicPhp(
            $pos_id, $key1, $key2, $pos_auth_key
        );

        $fielsToCheck = [
            'pos_id' => $pos_id, 'pos_auth_key' => $pos_auth_key,
            'amount' => 12345, 'city' => 'Wroclaw', 'client_ip' => '127.0.0.1',
            'desc' => 'Shop Transaction', 'email' => 'myemail@domain.com', 'first_name' => 'John',
            'last_name' => 'Doe', 'order_id' => 123, 'post_code' => '50-073',
            'session_id' => '12345678901234567890123456789012'
        ];
        $payuForm = new PayuForm();
        $payuForm->setAmount($fielsToCheck['amount']);
        $payuForm->setCity($fielsToCheck['city']);
        $payuForm->setClientIp($fielsToCheck['client_ip']);
        $payuForm->setDesc($fielsToCheck['desc']);
        $payuForm->setEmail($fielsToCheck['email']);
        $payuForm->setFirstName($fielsToCheck['first_name']);
        $payuForm->setLastName($fielsToCheck['last_name']);
        $payuForm->setOrderId($fielsToCheck['order_id']);
        $payuForm->setPostCode($fielsToCheck['post_code']);
        $payuForm->setSessionId($fielsToCheck['session_id']);
        $payu->setFormData($payuForm);
        $formFields = $payu->getFormFieldsRendered(new FormToHtml());

        foreach( $fielsToCheck as $name => $value ) {
            $this->assertContains('<input type="hidden" name="'. $name .'" value="'. $value .'" />', $formFields,
                'Expected different '. $name .' field'
            );
        }
    }

    public function testFormOpenCreation()
    {
        $payu = new PayuClassicPhp(
            111111, 'aaaaaabbbbbcccccdddd', 'ddddccccbbbbaaaaaa', '22222'
        );

        $this->assertEquals(
            '<form method="post" action="https://secure.payu.com/paygw/UTF/NewPayment" accept-charset="utf-8" target=blank >',
            $payu->getFormOpen(
                new FormOpen(['target' => 'blank'])
            ), 'Error in production version of form open'
        );

        $this->assertEquals(
            '<form method="post" action="https://secure.snd.payu.com/paygw/UTF/NewPayment" accept-charset="utf-8" >',
            $payu->getFormOpen(
                new FormOpen([], 1)
            ), 'Error in sandbox version of form open'
        );
    }
}