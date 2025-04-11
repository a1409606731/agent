<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: Trae AI
 */

namespace app\forms\mall\bot;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\BotConf;
use app\models\CozeAccount;

class PublicConfigForm extends \app\forms\common\BaseForm
{
    public $password;
    public $bot_id;
    public $space_id;
    public $account_id;
    
    /**
     * 获取公开访问密码
     * @return array
     */
    public function getPublicPassword()
    {
        $password = CommonOption::get('bot_public_password');
        
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'password' => $password ?: ''
            ]
        ];
    }
    
    /**
     * 设置公开访问密码
     * @return array
     */
    public function setPublicPassword()
    {
        if (empty($this->password) || strlen($this->password) < 6) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '密码长度至少为6个字符'
            ];
        }
        
        $result = CommonOption::set('bot_public_password', $this->password);
        
        return [
            'code' => $result ? ApiCode::CODE_SUCCESS : ApiCode::CODE_ERROR,
            'msg' => $result ? '密码设置成功' : '密码设置失败'
        ];
    }
    
    /**
     * 获取当前选择的公开智能体
     * @return array
     */
    public function getPublicBot()
    {
        $setting = CommonOption::get(CommonOption::NAME_COZE_WEB_SDK);
        
        if (!$setting || empty($setting['bot_id'])) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => null
            ];
        }
        
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $setting
        ];
    }
    
    /**
     * 设置公开智能体
     * @return array
     */
    public function setPublicBot()
    {
        if (empty($this->bot_id) || empty($this->account_id)) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '参数不完整'
            ];
        }
        
        // 检查智能体是否存在
        $botConf = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0]);
        if (!$botConf) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '智能体不存在'
            ];
        }
        
        // 检查账号是否存在
        $account = CozeAccount::findOne(['id' => $this->account_id, 'is_delete' => 0]);
        if (!$account) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '账号不存在'
            ];
        }
        
        $data = [
            'bot_id' => $this->bot_id,
            'space_id' => $this->space_id,
            'account_id' => $this->account_id
        ];
        
        $result = CommonOption::set(CommonOption::NAME_COZE_WEB_SDK, $data);
        
        return [
            'code' => $result ? ApiCode::CODE_SUCCESS : ApiCode::CODE_ERROR,
            'msg' => $result ? '设置成功' : '设置失败'
        ];
    }
    
    /**
     * 获取所有可用的智能体列表
     * @return array
     */
    public function getAllBots()
    {
        $list = [];
        
        // 获取所有智能体
        $botConfs = BotConf::find()->where(['is_delete' => 0])->all();
        foreach ($botConfs as $botConf) {
            $list[] = [
                'bot_id' => $botConf->bot_id,
                'bot_name' => $botConf->title,
                'icon_url' => $botConf->icon,
                'description' => '',
                'publish_time' => $botConf->created_at,
                'space_id' => '',
                'account_id' => 1 // 默认账号ID，实际应用中应该根据实际情况获取
            ];
        }
        
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list
            ]
        ];
    }
}