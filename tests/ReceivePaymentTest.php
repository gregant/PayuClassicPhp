<?php

use PayuClassicPhp\PayuClassicPhp;
use PayuClassicPhp\Src\Exception\InvalidPosidException;
use PayuClassicPhp\Src\Exception\InvalidRequestDataException;
use PayuClassicPhp\Src\Exception\InvalidSigException;
use PayuClassicPhp\Src\Exception\PayuResponseIsEmpty;

class ReceivePaymentTest extends PHPUnit_Framework_TestCase
{
    public function testReceiveTransactionNoParamsException()
    {
        $payu = $this->setPayuObject();
        $this->setExpectedException(InvalidRequestDataException::class);
        $postData = [];
        $payu->receiveTransaction($postData, 0);
    }

    private function setPayuObject()
    {
        return new PayuClassicPhp(
            111111, 'aaaaaabbbbbcccccdddd', 'ddddccccbbbbaaaaaa', '22222'
        );
    }

    public function testReceiveTransactionWrongPosIdException()
    {
        $payu = $this->setPayuObject();
        $this->setExpectedException(InvalidPosidException::class);
        $postData = [
            'pos_id'     => '123456',
            'session_id' => '1111222333344445555',
            'ts'         => time(),
            'sig'        => 'aaaabbbbccccddddeeeeffffgggghhhhiiiijjjjkkkk'
        ];
        $payu->receiveTransaction($postData, 0);
    }

    public function testReceiveTransactionWrongSigException()
    {
        $payu = $this->setPayuObject();
        $this->setExpectedException(InvalidSigException::class);
        $postData = [
            'pos_id'     => '111111',
            'session_id' => '1111222333344445555',
            'ts'         => 1500030855,
            'sig'        => '2c443df851c457c1d95bd4e77042f9f9-1'
        ];
        $payu->receiveTransaction($postData, 0);
    }

    public function testReceiveTransactionValidSig()
    {
        $payu = $this->setPayuObject();
        $this->setExpectedException(PayuResponseIsEmpty::class);
        $postData = [
            'pos_id'     => '111111',
            'session_id' => '1111222333344445555',
            'ts'         => 1500030855,
            'sig'        => '2c443df851c457c1d95bd4e77042f9f9'
        ];
        $payu->receiveTransaction($postData, 1);
    }
}