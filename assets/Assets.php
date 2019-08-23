<?php
namespace x51\yii2\modules\recaptchav3\assets;

class Assets extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__;
    /*public $css = [
        
    ];*/
    public $js = [
        'js/script.js',
		/*
'js/editor.header.js',
		'js/editor.list.js',
		'js/editor.paragraph.js',
		'js/editor.warning.js',
        'js/editor.code.js',
        'js/editor.simpleimage.js',
        'js/editor.image.js',
        'js/editor.delimiter.js',
        'js/editor.table.js',
		'js/editor.inline.js',
		'js/editor.quote.js',
		'js/editor.raw.js',
		'js/editor.embed.js',
        'js/editor.markertool.js',
        'js/editor.link.js',
        'js/init.js',*/
    ];
    public $depends = [
        //'yii\web\YiiAsset',
    ];
    public function init() {
        
		parent::init();
    }

} // end class
