<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: Trae AI
 */

namespace app\controllers;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\api\Chat;
use app\forms\common\coze\ApiForm;
use app\models\BotConf;
use app\models\CozeAccount;

/**
 * 公开访问的智能体控制器
 * 不需要登录即可访问
 */
class BotPublicController extends Controller
{
    /**
     * 智能体公开访问页面
     * @return string
     */
    public function actionIndex()
    {
        // 检查是否已经通过密码验证
        $session = \Yii::$app->session;
        $authenticated = $session->get('bot_public_authenticated', false);
        
        if (!$authenticated) {
            return $this->render('password');
        }
        
        return $this->render('index');
    }
    
    /**
     * 验证访问密码
     * @return \yii\web\Response
     */
    public function actionVerifyPassword()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $password = \Yii::$app->request->post('password');
            $correctPassword = $this->getAccessPassword();
            
            if ($password === $correctPassword) {
                // 设置session标记已通过验证
                $session = \Yii::$app->session;
                $session->set('bot_public_authenticated', true);
                
                return $this->asJson([
                    'code' => ApiCode::CODE_SUCCESS,
                    'msg' => '密码验证成功',
                ]);
            } else {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '密码错误',
                ]);
            }
        }
    }
    
    /**
     * 获取智能体配置信息
     * @return \yii\web\Response
     */
    public function actionGetBotConfig()
    {
        if (\Yii::$app->request->isAjax) {
            $setting = $this->getBotSetting();
            
            if (empty($setting['bot_id'])) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '未配置智能体',
                ]);
            }
            
            $botConf = BotConf::findOne(['bot_id' => $setting['bot_id'], 'is_delete' => 0]);
            if (!$botConf) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '智能体配置不存在',
                ]);
            }
            
            $width = $botConf->is_width == 2 ? $botConf->width : 460;
            
            return $this->asJson([
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'bot_id' => $setting['bot_id'],
                    'title' => $botConf->title,
                    'icon' => $botConf->icon,
                    'lang' => $botConf->lang,
                    'layout' => $botConf->layout,
                    'width' => $width,
                    'version' => $botConf->version ?: '0.1.0-beta.6',
                ]
            ]);
        }
    }
    
    /**
     * 与智能体聊天接口
     * @return \yii\web\Response
     */
    public function actionChat()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $session = \Yii::$app->session;
            $authenticated = $session->get('bot_public_authenticated', false);
            
            if (!$authenticated) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '请先验证访问密码',
                ]);
            }
            
            $message = \Yii::$app->request->post('message');
            $setting = $this->getBotSetting();
            
            if (empty($setting['bot_id']) || empty($setting['account_id'])) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '智能体配置不完整',
                ]);
            }
            
            try {
                $model = CozeAccount::findOne(['id' => $setting['account_id'], 'is_delete' => 0]);
                if (!$model) {
                    return $this->asJson([
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '账号不存在',
                    ]);
                }
                
                $chat = new Chat([
                    'bot_id' => $setting['bot_id'],
                    'user_id' => 'public_user_' . md5(\Yii::$app->request->userIP),
                    'query' => $message,
                ]);
                
                $response = ApiForm::common(['object' => $chat, 'account' => $model])->request();
                
                return $this->asJson([
                    'code' => ApiCode::CODE_SUCCESS,
                    'data' => $response,
                ]);
            } catch (\Exception $e) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $e->getMessage(),
                ]);
            }
        }
    }
    
    /**
     * 获取智能体设置
     * @return array
     */
    private function getBotSetting()
    {
        $setting = CommonOption::get(CommonOption::NAME_COZE_WEB_SDK);
        return $setting ? (array)$setting : [];
    }
    
    /**
     * 获取访问密码
     * @return string
     */
    private function getAccessPassword()
    {
        // 从配置中获取密码，如果没有设置则使用默认密码
        $option = CommonOption::get('bot_public_password');
        return $option ?: 'cozex123';
    }
    
    /**
     * 设置访问密码
     * @param string $password
     * @return bool
     */
    public function actionSetPassword()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $password = \Yii::$app->request->post('password');
            if (empty($password)) {
                return $this->asJson([
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '密码不能为空',
                ]);
            }
            
            $result = CommonOption::set('bot_public_password', $password);
            
            return $this->asJson([
                'code' => $result ? ApiCode::CODE_SUCCESS : ApiCode::CODE_ERROR,
                'msg' => $result ? '密码设置成功' : '密码设置失败',
            ]);
        }
    }
}