<?php
/**
 * @package   Catalyst
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// Получаем инстанс WARP
$warp = Warp::getInstance();

// Забираем контент страницы из буфера (открытого в header.php)
// и передаём его в слот 'content' для рендерера шаблона
$warp['template']->set( 'content', ob_get_clean() );

// Рендерим главный layout-файл темы (/layouts/template.php),
// который собирает итоговую разметку страницы: шапку, меню, сайдбары, подвал
echo $warp['template']->render( 'template' );
