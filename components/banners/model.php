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


if (!defined('VALID_CMS')) {
    die('ACCESS DENIED');
}

class cms_model_banners {
    public $config = array();

    public function __construct() {
        $this->config = cmsCore::getInstance()->loadComponentConfig('banners');
    }

    public static function getBanner($id) {
        $id = (int) $id;

        $banner = cmsDatabase::getInstance()->get_fields('cms_banners', "id = '$id'", '*');

        if ($banner) {
            return \cms\events::call('banners.get_banner', $banner);
        } else {
            return false;
        }
    }

    public static function getImageBanner($banner) {
        return '<a href="/gobanner' . $banner['id'] . '" title="' . $banner['title'] . '" target="_blank"><img src="/images/banners/' . $banner['fileurl'] . '" border="0" alt="' . $banner['title'] . '"/></a>';
    }

    public static function getSwfBanner($banner) {
        return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="468" height="60">' . "\n" .
                '<param name="movie" value="/images/banners/' . $banner['fileurl'] . '?banner_id=' . $banner['id'] . '" />' . "\n" .
                '<param name="quality" value="high" />' . "\n" .
                '<param name="FlashVars" value="banner_id=' . $banner['id'] . '" />' . "\n" .
                '<embed src="/images/banners/' . $banner['fileurl'] . '?banner_id=' . $banner['id'] . '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="468" height="60">' . "\n" .
                '</embed>' . "\n" .
                '</object>';
    }

    /**
     * Возвращает код баннера по названию позиции
     * Считаются просмотры
     * @param int $id
     * @return html
     */
    public static function getBannerHTML($position) {
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        $position = $inDB->escape_string($position);

        $html = '';

        $banner = $inDB->get_fields('cms_banners', "position = '$position' AND published = 1 AND ((maxhits > hits) OR (maxhits = 0))", '*', 'RAND()');
        if (!$banner) {
            return $html;
        }

        if ($banner['typeimg'] == 'image') {
            $html = self::getImageBanner($banner);
        }

        if ($banner['typeimg'] == 'swf') {
            $html = self::getSwfBanner($banner);
        }

        if ($html) {
            // обновляем статистику просмотра баннера
            $banner_id = (int) $banner['id'];
            $ip = $inDB->escape_string($inUser->ip);

            $inDB->query("INSERT IGNORE INTO {#}banner_hits (banner_id, ip) VALUES ('$banner_id', '$ip')");

            if ($inDB->affected_rows()) {
                $inDB->query("UPDATE {#}banners SET hits = hits + 1 WHERE id = '$banner_id'");
            }
        }

        return $html;
    }

    /**
     * Возвращает код баннера по ID
     * @param int $id
     * @return html
     */
    public static function getBannerById($id) {
        $html = '';

        $banner = self::getBanner($id);
        if (!$banner) {
            return $html;
        }

        if ($banner['typeimg'] == 'image') {
            $html = self::getImageBanner($banner);
        }
        if ($banner['typeimg'] == 'swf') {
            $html = self::getSwfBanner($banner);
        }

        return $html;
    }

    /**
     * Возвращает элементы <option> для списка баннерных позиций
     * @param int $selected
     * @return html
     */
    public static function getBannersListHTML($selected = 0) {
        $html = '';

        for ($bp = 1; $bp <= 30; $bp++) {
            if (@$selected == $bp) {
                $s = 'selected';
            } else {
                $s = '';
            }
            $html .= '<option value="banner' . $bp . '" ' . $s . '>banner' . $bp . '</option>' . "\n";
        }

        return $html;
    }

    public static function clickBanner($id) {
        $id = (int) $id;

        cmsDatabase::getInstance()->query("UPDATE {#}banners SET clicks = clicks + 1 WHERE id = '$id'");

        \cms\events::call('banners.click_banner', $id);

        return true;
    }
}