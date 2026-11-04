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

function f_banners(&$text) {
    $phrases = array('БАННЕР', 'BANNER');
    $has_banner = false;

    foreach ($phrases as $phrase) {
        if (mb_strpos($text, $phrase) !== false) {
            $has_banner = true;
            break;
        }
    }

    if (!$has_banner) {
        return true;
    }

    if (!cmsCore::getInstance()->isComponentEnable('banners')) {
        return true;
    }

    $regex = '/{((?:БАННЕР|BANNER)=)\s*(.*?)}/iu';
    $matches = array();

    preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

    if (!$matches) {
        return true;
    }

    foreach ($matches as $elm) {
        $raw = trim($elm[0], '{}');
        $args = array();

        mb_parse_str($raw, $args);

        $position = reset($args);

        if ($position) {
            $output = cms_model_banners::getBannerHTML($position);
        } else {
            $output = '';
        }

        $text = str_replace($elm[0], $output, $text);
    }

    return true;
}
