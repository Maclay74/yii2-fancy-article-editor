<?php
/**
 * Created by PhpStorm.
 * User: Mike Finch
 * Date: 29.03.2018
 * Time: 13:36
 *
 */

namespace mikefinch\fancyArticleEditor;

use yii\base\Widget;
use yii\helpers\Html;

class RenderWidget extends Widget {

    public $content;
    public $options = ['class' => 'render-widget'];
    public $textOptions = [];
    public $textTag = "p";
    public $headerOptions = [];
    public $headerTag = "h2";
    public $imageOptions = ['style' => 'max-width: 100%;'];
    public $videoOptions = ['height' => 400, 'width' => 750, 'frameborder' => 0];

    public $videoRenderClosure = null;

    public function run() {
        parent::run();

        return $this->render("render", [
            'model' => $this
        ]);
    }

}