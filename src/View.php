<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers;

use fafcms\fafcms\assets\LoadCSSRelPreloadAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yiiui\yii2materialize\Html;

class View extends \yii\web\View
{
    private $pushAssetsCache = [];

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_END_PAGE, function () {
            $pushAssets = $this->pushAssetsCache;
            $pushAssetsKeys = array_keys($pushAssets);
            $pushedAssets = Yii::$app->getRequest()->getCookies()->getValue('pushedAssets', []);
            $newAssets = array_diff($pushAssetsKeys, $pushedAssets);

            if (count($newAssets) > 0) {
                Yii::$app->getResponse()->getHeaders()->add('Link', $this->buildPushString($pushAssets));
                Yii::$app->getResponse()->getCookies()->add(new Cookie([
                    'name' => 'pushedAssets',
                    'value' => $pushAssetsKeys,
                ]));
            }
        });
    }

    public function pushAsset($url, $type)
    {
        $time = @filemtime(Yii::getAlias('@webroot/' . ltrim($url, '/'), false));
        $key = md5($url.$time.$type);

        $this->pushAssetsCache[$key] = [
            'url' => $url,
            'time' => $time,
            'type' => $type
        ];
    }

    private function buildPushString($assets)
    {
        $pushString = '';
        $i = 0;

        foreach($assets as $asset) {
            $i++;
            $pushString .= '<' . $asset['url'] . '>; rel=preload; as=' . $asset['type'];

            if ($i < count($assets)) {
                $pushString .= ',';
            }
        }

        return $pushString;
    }

    public function registerCssFile($url, $options = [], $key = null)
    {
        if (isset($options['fafcmsPush'])) {
            unset($options['fafcmsPush']);
            $this->pushAsset($url, 'stylesheet');
        }

        if (isset($options['fafcmsAsync']) && $options['fafcmsAsync']) {
            unset($options['fafcmsAsync']);
            $optionsAsync = $options;
            $optionsAsync['rel'] = 'preload';
            $optionsAsync['as'] = 'style';
            $optionsAsync['onload'] = 'this.onload=null;this.rel=\'stylesheet\'';

            $options['noscript'] = true;

            $cssrelpreload = $this->getAssetManager()->publish(     '@npm/fg-loadcss/dist/cssrelpreload.min.js');
            $this->registerJsFile($cssrelpreload[1], ['fafcmsPush' => true]);

            $noscriptUrl = Yii::getAlias($url);
            $noscriptKey = $key ?: $noscriptUrl;

            parent::registerCssFile($noscriptUrl, $optionsAsync, $noscriptKey.'noscript');
        }

        parent::registerCssFile($url, $options, $key);
    }

    public function registerJsFile($url, $options = [], $key = null)
    {
        if (isset($options['fafcmsPush'])) {
            unset($options['fafcmsPush']);
            $this->pushAsset($url, 'script');
        }

        parent::registerJsFile($url, $options, $key);
    }

    /**
     * @var array
     */
    public $jsWithOptions = [];

    public function registerJs($js, $position = \yii\web\View::POS_READY, $key = null, $scriptOptions = null)
    {
        if ($scriptOptions === null) {
            parent::registerJs($js, $position, $key);
        } else {
            $this->jsWithOptions[$position][] = [
                'js' => $js,
                'options' => $scriptOptions
            ];
        }
    }

    public function clear()
    {
        $this->jsWithOptions = [];
        parent::clear();
    }

    protected function renderHeadHtml()
    {
        $lines = [];

        if (!empty($this->jsWithOptions[self::POS_HEAD])) {
            $lines = $this->getScriptWithOptions($this->jsWithOptions[self::POS_HEAD]);
        }

        $default = parent::renderHeadHtml();
        return $default.(!empty($default)?"\n":'').(empty($lines) ? '' : implode("\n", $lines));
    }

    protected function renderBodyBeginHtml()
    {
        $lines = [];

        if (!empty($this->jsWithOptions[self::POS_BEGIN])) {
            $lines = $this->getScriptWithOptions($this->jsWithOptions[self::POS_BEGIN]);
        }

        $default = parent::renderBodyBeginHtml();
        return $default.(!empty($default)?"\n":'').(empty($lines) ? '' : implode("\n", $lines));
    }

    private function getScriptWithOptions($scripts, $prepend = '', $append = '')
    {
        $lines = [];

        foreach ($scripts as $jsWithOptions) {
            $lines[] = Html::script($prepend.$jsWithOptions['js'].$append, $jsWithOptions['options']);
        }

        return $lines;
    }

    protected function renderBodyEndHtml($ajaxMode)
    {
        $lines = [];

        if ($ajaxMode) {
            if (!empty($this->jsWithOptions[self::POS_END])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_END]));
            }
            if (!empty($this->jsWithOptions[self::POS_READY])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_READY]));
            }
            if (!empty($this->jsWithOptions[self::POS_LOAD])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_LOAD]));
            }
        } else {
            if (!empty($this->jsWithOptions[self::POS_END])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_END]));
            }
            if (!empty($this->jsWithOptions[self::POS_READY])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_READY], 'jQuery(function ($) {'."\n", "\n".'});'));
            }
            if (!empty($this->jsWithOptions[self::POS_LOAD])) {
                $lines = array_merge($lines, $this->getScriptWithOptions($this->jsWithOptions[self::POS_LOAD], 'jQuery(window).on(\'load\', function () {'."\n", "\n".'});'));
            }
        }

        $default = parent::renderBodyEndHtml($ajaxMode);
        return $default.(!empty($default)?"\n":'').(empty($lines) ? '' : implode("\n", $lines));
    }
}
