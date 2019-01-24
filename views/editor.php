<?php
/**
 * Created by PhpStorm.
 * User: Mike Finch
 * Date: 29.03.2018
 * Time: 13:51
 *
 * @var $content array;
 * @var $input string;
 */

use yii\helpers\Html;

if (!$content) $content = [];


?>


<div class="fancy-editor-container">

    <div class="blocks">

        <? foreach ($content as $block): ?>
            <div class="editor-block" data-type="<?=$block->type?>">
                <? if ($block->type == "text"): ?>
                    <textarea placeholder="Введите текст"><?=$block->content?></textarea>
                <? endif;?>

                <? if ($block->type == "header"): ?>
                    <input placeholder="Заголовок" value="<?=$block->content?>">
                <? endif;?>

                <? if ($block->type == "image"): ?>
                    <label data-src="<?=$block->content?>">
                        <img src="<?=$block->content?>" alt="">
                    </label>
                <? endif;?>

                <? if ($block->type == "video"): ?>
                    <label data-src="<?=$block->content?>">
                        <iframe src="<?=$block->content?>" width="750" height="400" frameborder="0"></iframe>
                    </label>
                <? endif;?>

                <div class="sorter"><i class="fa fa-bars" aria-hidden="true"></i></div>
                <div class="remove"><i class="fa fa-times" aria-hidden="true"></i></div>
            </div>
        <? endforeach; ?>

    </div>

    <div class="new-block">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" data-add="header" ><i class="fa fa-header" aria-hidden="true"></i> Заголовок</button>
            <button type="button" class="btn btn-default" data-add="text"><i class="fa fa-font" aria-hidden="true"></i> Текст</button>
            <button type="button" class="btn btn-default" data-add="image"><i class="fa fa-picture-o" aria-hidden="true"></i> Фото</button>
            <button type="button" class="btn btn-default" data-add="video"><i class="fa fa-play" aria-hidden="true"></i> Видео</button>
        </div>
    </div>

    <div class="input">
        <?= $input;?>
    </div>


</div>


<script>

    $(function() {

        autosize($('textarea'));

        $(".fancy-editor-container").each(function() {
            var container = $(this);
            var blocks = container.find(".blocks");

            $( blocks).sortable({
                handle: ".sorter"
            });

            // Добавление блока
            $(this).on("click", "[data-add]", function() {
                var type = $(this).data("add");
                var block = generateNewBlock(type);

                var sorter = $("<div>").addClass("sorter").html('<i class="fa fa-bars" aria-hidden="true"></i>');
                var remove = $("<div>").addClass("remove").html('<i class="fa fa-times" aria-hidden="true"></i>');

                sorter.appendTo(block);
                remove.appendTo(block);

                block.attr("data-type", type);

                blocks.append(block);
                autosize($('textarea'));
            });

            // Удаление блока
            $(this).on("click", ".remove", function() {
                $(this).closest(".editor-block").remove();
            });

            // Загрузка изображения
            $(this).on("change", "input[type='file']", function() {

                var formData = new FormData();
                var file = $(this)[0].files[0];


                var label = $(this).parent();
                var progress = $("<div>").addClass("progress-bar");
                progress.appendTo(label);

                label.find("span").html("Загрузка изображения");

                formData.append("image", file);

                $.ajax({
                    type: "POST",
                    url: "/fancyArticleEditor/upload-image",
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', function() {
                                var percent = 0;
                                var position = event.loaded || event.position;
                                if (event.lengthComputable) {
                                    percent = Math.ceil(position / event.total * 100);
                                }
                                progress.css({width: percent + "%"});
                            }, false);
                        }
                        return myXhr;
                    },
                    async: true,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    timeout: 60000
                }).then(function(response) {

                    label.find("input").remove();

                    var img = $("<img>").attr({
                        src: response
                    })

                    label.html("").append(img);

                    label.attr({
                        "data-src": response
                    })
                })



            });

            // Парсинг видео
            $(this).on("paste input change", "[data-type='video'] input", function() {
                var field = $(this);
                var block = field.closest(".editor-block");
                block.removeClass("error");

                clearTimeout(field.data("timer"));
                field.data("timer", window.setTimeout(function() {

                    var link = field.val();
                    var parsedLink = link.match(/watch\?v=([^]+)/);

                    if (!!parsedLink) {
                        var iframe = $("<iframe>").attr({
                            height: "400",
                            frameborder: "0",
                            width: "750",
                            src: 'https://www.youtube.com/embed/' + parsedLink[1]
                        })

                        block.find("label").html("").append(iframe).attr({
                            "data-src" : 'https://www.youtube.com/embed/' + parsedLink[1]
                        })

                    } else {
                        block.addClass("error");
                    }
                }, 500))

            });

            // Собираем информацию
            $(this).closest("form").on("beforeValidate", function() {

                var data = [];

                $(this).find(".blocks .editor-block").each(function() {

                    var content = "";

                    var type = $(this).data("type");

                    switch (type) {
                        case "text": content = $(this).find("textarea").val(); break;
                        case "header":
                            content = $(this).find("input").val(); break;
                        case "video":
                        case "image": content = $(this).find("label").data("src"); break;

                    }

                    if (content.length) {
                        data.push({
                            content: content,
                            type: type,
                        })
                    }
                });

                $(this).find(".input input").val(JSON.stringify(data));
            })

        })

    });

    function generateNewBlock(type, position) {

        var newBlock = $("<div>").addClass("editor-block");

        switch (type) {
            case "text":
                $("<textarea>").attr({
                    'placeholder' : "Введите текст"
                }).appendTo(newBlock);
                break;

            case "header":
                $("<input>").attr({
                    'placeholder' : "Заголовок",
                    'class': 'header'
                }).appendTo(newBlock);
                break;

            case "video":
                var label = $("<label>")
                $("<input>").attr({
                    'placeholder' : "Ссылка на youtube.com"
                }).appendTo(label);
                label.appendTo(newBlock);
                break;

            case "image":

                var label = $("<label>").html("<span>Выбрать файл</span>")
                $("<input>").attr({
                    'placeholder': "Изображение",
                    'type': 'file'
                }).appendTo(label);

                label.appendTo(newBlock);

                break;

            default:
                newBlock.html("This type isn't implemented yet");
        }

        return newBlock;

    }

</script>