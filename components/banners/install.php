<?php

/*
 *                        CMS RuDi v1.10.9
 *                      https://cmsrudi.ru/
 *
 *              Based on InstantCMS v1.10.7 by InstantSoft
 *          (Original code © InstantCMS Team, 2007-2016)
 *          (Modifications © CMS RuDi Team / DagIT, 2026)
 *
 *                      LICENSED BY GNU/GPL v2
 */

if (!defined('VALID_CMS') && !defined('VALID_CMS_ADMIN')) {
    die('ACCESS DENIED');
}

/**
 * Информация о компоненте для установщика CMS RuDi
 */
function info_component_banners() {
    return array(
        'title' => 'Баннеры',
        'description' => 'Управление баннерами, позициями и статистикой показов/кликов. Для вставки баннера в материал или модуль используется выражение вида {БАННЕР=banner1}.',
        'link' => 'banners',
        'version' => '1.0.0',
        'author' => 'CMS RuDi',
        'author_url' => 'https://cmsrudi.ru',
        'config' => array(),
        'plugins' => array(
            'p_filters' => 'Фильтры (p_filters) — для работы выражения {БАННЕР=имя_позиции}'
        )
    );
}

/**
 * Создаёт таблицы баннеров и статистики, добавляет демонстрационный баннер.
 *
 * @return bool|string true при успехе, текст ошибки при неудаче
 */
function install_component_banners() {
    $inDB = cmsDatabase::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS {#}banners (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `position` varchar(100) NOT NULL DEFAULT 'banner1',
        `typeimg` varchar(10) NOT NULL DEFAULT 'image',
        `fileurl` varchar(250) DEFAULT NULL,
        `hits` int(11) NOT NULL DEFAULT 0,
        `clicks` int(11) NOT NULL DEFAULT 0,
        `maxhits` int(11) NOT NULL DEFAULT 0,
        `maxuser` int(11) NOT NULL DEFAULT 0,
        `user_id` int(11) NOT NULL DEFAULT 1,
        `pubdate` datetime DEFAULT NULL,
        `title` varchar(250) DEFAULT NULL,
        `link` varchar(250) DEFAULT NULL,
        `published` tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        KEY `position` (`position`),
        KEY `published` (`published`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8";

    if (!$inDB->query($sql)) {
        return 'Не удалось создать таблицу баннеров.';
    }

    $sql = "CREATE TABLE IF NOT EXISTS {#}banner_hits (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `banner_id` int(11) NOT NULL DEFAULT 0,
        `ip` varchar(45) NOT NULL DEFAULT '',
        `pubdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `ip` (`ip`, `banner_id`),
        KEY `banner_id` (`banner_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8";

    if (!$inDB->query($sql)) {
        return 'Не удалось создать таблицу статистики баннеров.';
    }

    // Каталог для загружаемых баннеров
    $image_dir = PATH . '/images/banners';

    if (!is_dir($image_dir)) {
        @mkdir($image_dir, 0755, true);
    }

    // Демонстрационный баннер: берём картинку из комплекта компонента
    $image_name = 'cmsrudi-468x60.png';
    $image_path = $image_dir . '/' . $image_name;
    $source = PATH . '/components/banners/images/' . $image_name;

    if (!file_exists($image_path) && file_exists($source)) {
        @copy($source, $image_path);
    }

    // Не перезаписываем данные, если баннеры уже есть
    $banner_id = $inDB->get_field('cms_banners', '1', 'id');

    if (!$banner_id && file_exists($image_path)) {
        $inDB->insert('cms_banners', array(
            'position' => 'banner1',
            'typeimg' => 'image',
            'fileurl' => $image_name,
            'hits' => 0,
            'clicks' => 0,
            'maxhits' => 0,
            'maxuser' => 0,
            'user_id' => 1,
            'pubdate' => date('Y-m-d H:i:s'),
            'title' => 'CMS RuDi',
            'link' => 'https://cmsrudi.ru',
            'published' => 1
        ));
    }

    return true;
}

/**
 * Обновление компонента. Создаёт недостающие таблицы и не трогает данные.
 *
 * @return bool|string
 */
function upgrade_component_banners() {
    return install_component_banners();
}

/**
 * Удаление компонента вместе с его таблицами.
 *
 * @return bool
 */
function remove_component_banners() {
    $inDB = cmsDatabase::getInstance();

    $inDB->query("DROP TABLE IF EXISTS {#}banner_hits");
    $inDB->query("DROP TABLE IF EXISTS {#}banners");

    return true;
}
