<?php
namespace x51\yii2\modules\recaptchav3;

use \Yii;
use \yii\helpers\Url;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $publicKey;
    public $privateKey;
    public $validUserScore = 0.6; // if score >= then user
    public $defaultUserScore = 0.1;
    public $disableForRegisterUser = true; // register user always good
    protected $_errors;
    public $useOnRoute = [];

    public function bootstrap($app)
    {
        $app->on($app::EVENT_BEFORE_REQUEST, [$this, 'runCheckRecaptcha']);        
    }

    public function verifyToken($token, $action = 'user_check')
    {
        $this->_errors = false;
        $recaptcha = new \ReCaptcha\ReCaptcha($this->privateKey);
        $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
            ->setExpectedAction($action)
/*->setScoreThreshold($this->module->validUserScore)*/;
        $response = $recaptcha->verify($token, $_SERVER['REMOTE_ADDR']);

        if ($response->isSuccess()) {
            return $response->toArray();
        } else {
            $this->_errors = $response->getErrorCodes();
            return false;
        }
    } // end verifyToken

    public function getErrors()
    {
        return $this->_errors;
    }

    public function validUser()
    {
        if (defined('DISABLE_RECAPTCHA')) {
            return true;
        }
        if ($this->disableForRegisterUser && !Yii::$app->user->isGuest) {
            return true;
        }

        $session = Yii::$app->session;
        if ($session->has('RECAPTCHAV3_VALID')) {
            return $session['RECAPTCHAV3_VALID'];
        } else {
            return floatval($this->defaultUserScore) >= floatval($this->validUserScore);
        }
    }

    public function runCheckRecaptcha() {
        if ($this->checkStart()) {
            $view = Yii::$app->view;
            $view->registerJs('RECAPTCHA_PUBLIC_KEY = "' . $this->publicKey . '"; RECAPTCHA_VERIFY = "' . Url::to(['/' . $this->id . '/verify/default']) . '";', $view::POS_HEAD);
            $view->registerJsFile('https://www.google.com/recaptcha/api.js?render=' . $this->publicKey/*, ['position' => $view::POS_END]*/);
            Yii::$app->view->registerAssetBundle('\x51\yii2\modules\recaptchav3\assets\Assets'/*, ['position' => $view::POS_END]*/);
        }
    }

    protected function checkStart()
    {
        if (!$this->publicKey || !$this->privateKey || defined('DISABLE_RECAPTCHA') || Yii::$app->session->has('RECAPTCHAV3_VALID')) {
            return false;
        }
        if ($this->disableForRegisterUser && !Yii::$app->user->isGuest) {
            return false;
        }
        if ($this->useOnRoute) {
            $currRoute = \Yii::$app->controller->route;
            $match = false;
            foreach ($this->useOnRoute as $path) {
                $match = fnmatch($path, $currRoute);
                if ($match) {
                    break;
                }
            }
            if (!$match) {
                return false;
            }
        }
        return true;
    }



} // end class
