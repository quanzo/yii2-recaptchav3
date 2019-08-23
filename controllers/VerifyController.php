<?php
namespace x51\yii2\modules\recaptchav3\controllers;
use \yii\web\Controller;
use \Yii;

class VerifyController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'default' => ['POST'],
                ],
            ],
        ];
    }

    public function actionDefault()
    {
        $request=Yii::$app->request;        
        $session = Yii::$app->session;
        Yii::debug(print_r($session->isActive, true), 'session');

        //if (!$session->isActive) {
            $session->open();
        //}

        if ($this->module->privateKey && $token = $request->post('token', false)) {
            $arRes = $this->module->verifyToken($token);
            Yii::debug($arRes, 'recaptcha info');
            Yii::debug($_SESSION);
            Yii::debug($_COOKIE);
            Yii::debug($session->id);


            if ($arRes) {
                $session['RECAPTCHAV3_DATA'] = $arRes;
                $session['RECAPTCHAV3_VALID'] = floatval($arRes['score']) >= floatval($this->module->validUserScore);
            } else {
                if ($arErrors = $this->module->errors) {
                    $strErrors = '';
                    foreach ($arErrors as $errCode) {
                        if ($strErrors) {
                            $strErrors .= ', ';
                        }
                        $strErrors .= strval($errCode);
                    }
                    Yii::debug($strErrors, 'recaptcha error');
                }
            }
        }
        $session->close();
    } // end func
} // end class
