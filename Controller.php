<?php
/**
 * Created by PhpStorm.
 * User: Mike Finch
 * Date: 29.03.2018
 * Time: 22:08
 */

namespace mikefinch\fancyArticleEditor;

use yii\web\UploadedFile;

class Controller extends \yii\base\Controller {

    private $savePath = "@webroot";
    private $folder = "uploads";

    public function actionUploadImage() {

        $file = UploadedFile::getInstanceByName("image");

        if ($file instanceof UploadedFile) {
            $fileName = DIRECTORY_SEPARATOR.$this->folder.DIRECTORY_SEPARATOR.uniqid().".".$file->extension;
            $file->saveAs(\Yii::getAlias($this->savePath).$fileName);

            return $fileName;
        }
    }

}