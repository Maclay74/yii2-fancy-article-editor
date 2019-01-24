<?php
/**
 * Created by PhpStorm.
 * User: Mike Finch
 * Date: 29.03.2018
 * Time: 21:33
 *
 * @var $model \mikefinch\fancyArticleEditor\RenderWidget;
 *
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>


<?=Html::beginTag("div", $model->options)?>

    <? foreach (json_decode($model->content) as $block){

        switch ($block->type) {
            case "text":
                echo Html::tag($model->textTag, $block->content, $model->textOptions);
                break;

            case "header":
                echo Html::tag($model->headerTag, $block->content, $model->headerOptions);
                break;

            case "image":
                echo Html::img($block->content, $model->imageOptions);
                break;

            case "video":

                if (is_callable($model->videoRenderClosure)) {
                    echo call_user_func_array($model->videoRenderClosure, ['widget' => $model, 'id' => $block->content]);
                    break;
                }

                echo Html::tag("iframe","", ArrayHelper::merge($model->videoOptions, ['src' => $block->content]));
                break;
        }

    } ?>

<?=Html::endTag("div")?>


