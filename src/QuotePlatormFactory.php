<?php
/**
 * Created by PhpStorm.
 * User: wanda
 * Date: 2018/6/14
 * Time: 下午12:16
 */
namespace Quote;

class QuotePlatormFactory
{
    public $error = '';
    public $errorNo = '';
    public $platormClientArray=[];
    public $platormClient;
    public function __construct()
    {

    }
    public function login($account)
    {

        if(isset($this->platormClientArray[$account->id]))
        {
            $this->platormClient=$this->platormClientArray[$account->id];
        }
        else{
            $platormStr          = '\Quote\Platform\\' . $account->platform;
            $this->platormClient = new $platormStr();
            $result = $this->platormClient->login($account);
            if ($result)
            {
                $this->platormClientArray[$account->id]=$this->platormClient;
                return $result;
            } else {
                $this->error   = $this->platormClient->error;
                $this->errorNo = $this->platormClient->errorNo;
                return false;
            }
        }

    }


    private function bulidData($data)
    {
        $result = $this->platormClient->bulidData($data);
        if ($result) {
            return $result;
        } else {
            $this->error   = $this->platormClient->error;
            $this->errorNo = $this->platormClient->errorNo;
            return false;
        }
    }



    public function sendData($data)
    {
        $result=$this->bulidData($data);
        if(!$result)
        {
            return $result;
        }
        $result = $this->platormClient->send();
        if ($result) {
            return $result;
        } else {
            $this->error   = $this->platormClient->error;
            $this->errorNo = $this->platormClient->errorNo;
            return false;
        }
    }



    public function quoteResult($qouteInfo)
    {





    }


}