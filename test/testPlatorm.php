<?php
/**
 * Created by PhpStorm.
 * User: wanda
 * Date: 2018/6/14
 * Time: 下午1:37
 */
require_once __DIR__ . '/../vendor/autoload.php';
use Quote\QuotePlatormFactory;
class TestCase
{
    public function testLogin()
    {
        $quotePlatorm=new QuotePlatormFactory();
        $account=new Account();
        $quotePlatorm->login($account);
        //$quotePlatorm->bulidData();
    }
}

$testCase=new TestCase();
$testCase->testLogin();

class Account
{
    public $account;
    public $password;
    public $platform;

    public function __construct()
    {
        $this->account  = '18500390791';
        $this->password = 'pan881229';
        $this->platform = 'luxchange';
    }


}
/*namespace Quote\Tests;

class TestCase
{

    public function testLogin()
    {
        $quotePlatorm=new \Quote\QuotePlatormFactory();
        $account=new Account();
        $quotePlatorm->login($account);
    }
}

$testCase=new TestCase();

$testCase->testLogin();


*/
