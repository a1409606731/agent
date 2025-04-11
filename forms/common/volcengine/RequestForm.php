<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine;

use app\forms\common\volcengine\sdk\Basics;
use app\models\VolcengineKeys;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\BaseObject;
use yii\helpers\Json;

class RequestForm extends BaseObject
{
    public $secretId;

    public $secretKey;

    /**
     * @var Basics
     */
    public $object;

    /** @var VolcengineKeys */
    public $account;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if($this->account){
            $this->secretId = $this->account->access_id;
            $this->secretKey = $this->account->secret_key;
        }
    }

    public function request(){
        try {
            $queryParams = [];
            if($this->object->method == 'GET'){
                $httpBody = '';
                $queryParams = $this->object->getAttribute();
            }else{
                $httpBody = Json::encode($this->object->getAttribute(), JSON_UNESCAPED_UNICODE);
            }
            $queryParams['Action'] = $this->object->action;
            $queryParams['Version'] = $this->object->version;
            $query = '';
            ksort($queryParams);
            foreach ($queryParams as $k => $v) {
                $query .= rawurlencode($k) . '=' . rawurlencode($v) . '&';
            }
            $query = substr($query, 0, -1);
            // 组装请求头
            $header = $this->object->getHeader($this->secretId, $this->secretKey, $httpBody, $query);
            $url = 'https://' . $this->object->host . "/" . ($query ? "?{$query}" : '');
            $res = $this->getClient ($header)
                ->post ($url, ['body' => $httpBody]);
            $body = $res->getBody()->getContents();
        }catch (\Exception $e){
            if ($e instanceof RequestException && $e->hasResponse()) {
                $body = $e->getResponse()->getBody()->getContents();
            }else{
                \Yii::error($e);
                throw $e;
            }
        }
        return $this->response(Json::decode($body));
    }

    public function response($response){
        if(!empty($response["ResponseMetadata"]["Error"])){
            \Yii::error("对接火山引擎sdk接口异常结果：");
            $msg = "温馨提示：".$this->errorMessage($response["ResponseMetadata"]["Error"]);
            \Yii::error($response);
            throw new \Exception($msg);
        }else{
            return $response["Result"];
        }
    }

    public function errorMessage($error){
        $msg = $error['Message'] ?? '';
        $code = $error['Code'] ?? '';
        return $msg;
    }

    private function getClient($header = []): Client
    {
        return (new Client(['verify' => \Yii::$app->request->isSecureConnection, 'headers' => $header]));
    }
}
