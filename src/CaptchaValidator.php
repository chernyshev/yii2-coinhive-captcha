<?php

namespace chernyshev\Captcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\validators\Validator;

class CaptchaValidator extends Validator
{
    const URL = 'https://api.coinhive.com/token/verify';

    /**
     * @var string Your private Secret-Key
     */
    public $secret;

    /**
     * @var int The number of hashes this token must have reached in order to be valid.
     */
    public $hashes = 512;

    /**
     * @var string HttpClient transport
     */
    public $transport = 'yii\httpclient\CurlTransport';

    /** @var bool */
    public $skipOnEmpty = false;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->secret === null) {
            throw new InvalidConfigException('"secret" param is required.');
        }

        if ($this->message === null) {
            $this->message = 'The verification code is incorrect.';
        }
    }

    /**
     * @param mixed $value
     * @return array|null
     * @throws \yii\base\InvalidParamException
     */
    protected function validateValue($value)
    {
        //todo:
        $value = Yii::$app->request->post('coinhive-captcha-token');

        $client = new Client(['transport' => $this->transport]);
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl(self::URL)
            ->setData([
                'secret' => $this->secret,
                'token' => $value,
                'hashes' => $this->hashes,
            ])
            ->send();

        if ($response->isOk && $response->data['success']) {
            return null;
        }

        if (isset($response->data['error'])) {
            switch ($response->data['error']) {
                case 'invalid_token':
                    $this->message = "The token could not be verified. Either the token name was not found, or the token hasn't reached the requested number of hashes.";
                    break;
                case 'missing_input':
                    $this->message = 'No token or hashes provided as POST parameters.';
                    break;
                default:
                    $this->message = 'Validation error';
            }
        }

        return [$this->message, []];
    }
}
