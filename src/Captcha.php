<?php

namespace chernyshev\Captcha;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class Captcha extends InputWidget
{
    const JS_API_URL = 'https://authedmine.com/lib/captcha.min.js';

    /**
     * @var string Your public Site-Key.
     */
    public $key;

    /**
     * @var boolean Optional. Whether to hide the Coinhive logo and the What is this link.
     */
    public $whitelabel;

    /**
     * @var string Optional. The name of a global JavaScript function that should be called when the goal is reached.
     */
    public $callback;

    /**
     * @var int The number of hashes that have to be accepted by the mining pool.
     */
    public $hashes = 1024;

    /**
     * @var string Optional. A CSS selector for elements that should be disabled until the goal is reached.
     * Usually this will be your form submit button.
     */
    public $disableElements;

    /** @var string  */
    private $divClass = 'coinhive-captcha';

    /**
     * @var string
     */
    public $divContent = '<em>Loading Captcha...<br>If it doesn\'t load, please disable Adblock!</em>';


    /**
     * Initializes the object.
     * This method is called at the end of the constructor.
     * The default implementation will trigger an [[EVENT_INIT]] event.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (empty($this->key)) {
                throw new InvalidConfigException('Required `key` param isn\'t set.');
        }

        $view = $this->view;
        $view->registerJsFile(self::JS_API_URL, ['position' => $view::POS_END, 'async' => true, 'defer' => true]
        );

        echo Html::tag('div', $this->divContent, $this->buildDivOptions());
    }

    protected function buildDivOptions()
    {
        $divOptions = [
            'class' => $this->divClass,
            'data-key' => $this->key
        ];

        if (!empty($this->whitelabel)) {
            $divOptions['data-whitelabel'] = $this->whitelabel;
        }

        if (!empty($this->callback)) {
            $divOptions['data-callback'] = $this->callback;
        }

        if (!empty($this->hashes)) {
            $divOptions['data-hashes'] = $this->hashes;
        }

        if (!empty($this->disableElements)) {
            $divOptions['data-disable-elements'] = $this->disableElements;
        }

        return $divOptions;
    }

}
