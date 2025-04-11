<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\forms\mall\bot\IndexForm;
use app\forms\mall\bot\ListForm;
use app\forms\mall\bot\PublicConfigForm;

class BotController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('index');
        }
    }
    public function actionSet()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getSet());
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->saveSet());
            }
        } else {
            return $this->render('set');
        }
    }

    public function actionUse()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->saveUse();
        }
    }
    
    /**
     * 公开智能体配置页面
     * @return string
     */
    public function actionPublicConfig()
    {
        return $this->render('public-config');
    }
    
    /**
     * 获取公开访问密码
     * @return \yii\web\Response
     */
    public function actionGetPublicPassword()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new PublicConfigForm();
            return $this->asJson($form->getPublicPassword());
        }
    }
    
    /**
     * 设置公开访问密码
     * @return \yii\web\Response
     */
    public function actionSetPublicPassword()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $form = new PublicConfigForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->setPublicPassword());
        }
    }
    
    /**
     * 获取当前选择的公开智能体
     * @return \yii\web\Response
     */
    public function actionGetPublicBot()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new PublicConfigForm();
            return $this->asJson($form->getPublicBot());
        }
    }
    
    /**
     * 设置公开智能体
     * @return \yii\web\Response
     */
    public function actionSetPublicBot()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $form = new PublicConfigForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->setPublicBot());
        }
    }
    
    /**
     * 获取所有可用的智能体列表
     * @return \yii\web\Response
     */
    public function actionGetAllBots()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new PublicConfigForm();
            return $this->asJson($form->getAllBots());
        }
    }
}
