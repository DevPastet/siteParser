<?php
$config = array(
    'default' => array( //указываем статичные настройки для документов
        'template' => 2,
        'isfolder' => 0,
        'published' => 0,
        'parent' => 0,
    ),
    'parser' => array( // указываем настройки для парсинга
        'pagetitle' => array('tags' => '.page_title_area .apphub_HomeHeaderContent .apphub_AppName'),
        'alias' => array('tags' => '.page_title_area .apphub_HomeHeaderContent .apphub_AppName'),
        'content' => array('tags' => '#game_area_description'),
        'tv.img' => array('tags' => 'img.game_header_image_full', 'attr' => 'src'), // пример парсинга в tv поле атрибута тега
    )
);