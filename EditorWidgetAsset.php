<?php
/**
 * Created by PhpStorm.
 * User: Mike Finch
 * Date: 25.12.2017
 * Time: 3:43
 */


namespace mikefinch\fancyArticleEditor;

use yii\web\AssetBundle;


class EditorWidgetAsset extends AssetBundle {

    public $sourcePath = '@app/vendor/mikefinch/yii2-fancy-article-editor/assets';
    public $css = [
        'editor.scss',

    ];
    public $js = [
        'autosize.min.js',
        'jquery-ui.min.js',
        //'editor.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}