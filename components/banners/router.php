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

function routes_banners() {
    return array(
        array(
            '_uri' => '/^banners\/([0-9]+)$/i',
            'do' => 'view',
            1 => 'id'
        )
    );
}
