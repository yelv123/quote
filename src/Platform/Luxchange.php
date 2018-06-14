<?php
/**
 * Created by PhpStorm.
 * User: wanda
 * Date: 2018/6/14
 * Time: 下午12:17
 */

namespace Quote\Platform;

use  GuzzleHttp\Client;

class Luxchange
{
    public $httpClient;
    public $error = '';
    public $errorNo = '';

    public function __construct()
    {
        $this->httpClient = new Client(['cookies' => true, 'base_uri' => 'https://luxchange.cn/']);

    }

    /**
     * @param $account 登录的账号
     * @return bool 是否登录成功
     */
    public function login($account)
    {
        try {
            $data['loginType'] = "password";
            $data['mobile']    = $account->account;
            $data['password']  = md5($account->password);
            $response          = $this->httpClient->post('/mj-app/api/login', ['json' => $data]);
            $data              = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() == 200 && $data['code'] == 0) {
                return true;
            } else {

                $this->error   = $data['message'];
                $this->errorNo = '登录失败';
                return false;
            }
        } catch (\Exception $e) {

            $this->error   = '平台登录异常';
            $this->errorNo = 'Platform login exception';
            return false;

        }
    }

    /**
     * @param $data 整理数据
     */
    public function bulidData($data)
    {

    }


    private function uploadFile($file)
    {

        $policy = $this->getPolicy();
        if (!$policy) {
            return false;
        }
        $filename = $policy['dir'] . md5($file . uniqid()) . ".jpg";
        $response = $this->httpClient->request('POST', $policy['host'], ['multipart' => [
            ['name' => 'name', 'contents' => 'image.jpg'],
            ['name' => 'key', 'contents' => $filename],
            ['name' => 'policy', 'contents' => $policy['policy']],
            ['name' => 'OSSAccessKeyId', 'contents' => $policy['accessId']],
            ['name' => 'success_action_status', 'contents' => 200],
            ['name' => 'signature', 'contents' => $policy['signature']],
            ['name' => 'expire', 'contents' => $policy['expire']],
            ['name' => 'host', 'contents' => $policy['host']],
            ['file' => 'host', 'contents' => file_get_contents($file)]
        ]
        ]);
        if ($response->getStatusCode()==200) {
            $response = $this->httpClient->request('post', ['form_params' => ['type' => 5, 'imageUrl' => $policy['host'] . "/" . $filename, 'id' => '']]);
            $data=json_decode($response->getBody()->getContents(),true);
            if($response->getStatusCode()==200)
            {
                return $data['id'];
            }
            else{

                $this->error   = $data['message'];
                $this->errorNo = 'Failed to insert picture';
                return false;
            }

        } else {
            $this->error   = '上传图片失败';
            $this->errorNo = 'Failed to upload picture';
            return false;
        }
    }

    private function getPolicy()
    {
        try{
            $url      = "/mj-app/oss/policy?filePath=c2b/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";
            $response = $this->httpClient->get($url);
            $data     = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() == 200) {
                return $data;
            } else {

                $this->error   = $data['message'];
                $this->errorNo = 'Failed to get upload credentials';
                return false;
            }
        }
        catch (\Exception $e)
        {
            $this->error   = '获取上传凭证异常';
            $this->errorNo = 'Gets an upload credential exception';
            return false;
        }

    }


    /**
     * @param $qouteInfo 获取报价
     * @return mixed
     */
    public function quoteResult($qouteInfo)
    {
        try{
            $url="/mj-c2b/eval/queryPriceAndContentByBizCode?bizCode=".$qouteInfo->bizcode;
            $response = $this->httpClient->get($url);
            $data     = json_decode($response->getBody()->getContents(), true);
            if($response->getStatusCode()==200&&$data['code']==0)
            {
                $bak['max_price']=$data['acquisition_price_top'];
                $bak['max_price']=$data['acquisition_price_lower'];
                return $bak;
            }
            else{
                $this->error   = $data['message'];
                $this->errorNo = 'Failed to get quotation';
                return false;
            }
        }
        catch (\Exception $e)
        {
            $this->error   = '获取报价异常';
            $this->errorNo = 'get quote exception';
            return false;
        }
    }


}