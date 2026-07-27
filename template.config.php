<?php
/**
 * @package   Catalyst
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// Генерируем CSS для базовой раскладки (ширина страницы)
$this['config']->set('template_width', '980');
$css[] = sprintf('body { min-width: %dpx; }', $this['config']->get('template_width'));
$css[] = sprintf('.wrapper { width: %dpx; }', $this['config']->get('template_width'));

// Генерируем CSS для трёхколоночной раскладки (контент + два сайдбара)
$sidebar_a       = '';
$sidebar_b       = '';
$maininner_width = intval($this['config']->get('template_width'));
$sidebar_a_width = intval($this['config']->get('sidebar-a_width'));
$sidebar_b_width = intval($this['config']->get('sidebar-b_width'));
$rtl             = $this['config']->get('direction') == 'rtl';

// Считаем ширины колонок в зависимости от того, какие сайдбары включены
if ($this['modules']->count('sidebar-a')) {
	$sidebar_a = $this['config']->get('sidebar-a');
	$maininner_width -= $sidebar_a_width;
	$css[] = sprintf('#sidebar-a { width: %dpx; }', $sidebar_a_width);
}

if ($this['modules']->count('sidebar-b')) {
	$sidebar_b = $this['config']->get('sidebar-b');
	$maininner_width -= $sidebar_b_width;
	$css[] = sprintf('#sidebar-b { width: %dpx; }', $sidebar_b_width);
}

$css[] = sprintf('#maininner { width: %dpx; }', $maininner_width);

// Определяем расположение сайдбаров (лево/право) и генерируем нужный CSS

// Оба сайдбара справа
if (($sidebar_a == 'right' || !$sidebar_a) && ($sidebar_b == 'right' || !$sidebar_b)) {
	$sidebar_classes = 'sidebar-a-right sidebar-b-right';

// Оба сайдбара слева
} else if (($sidebar_a == 'left' || !$sidebar_a) && ($sidebar_b == 'left' || !$sidebar_b)) {
	$sidebar_classes = 'sidebar-a-left sidebar-b-left';
	$css[] = sprintf('#maininner { float: %s; }', $rtl ? 'left' : 'right');

// sidebar-a слева, sidebar-b — нет
} else if ($sidebar_a == 'left') {
	$sidebar_classes = 'sidebar-a-left sidebar-b-right';
	$css[] = '#maininner, #sidebar-a { position: relative; }';
	$css[] = sprintf('#maininner { %s: %dpx; }', $rtl ? 'right' : 'left', $sidebar_a_width);
	$css[] = sprintf('#sidebar-a { %s: -%dpx; }', $rtl ? 'right' : 'left', $maininner_width);

// sidebar-b слева, sidebar-a — нет
} else if ($sidebar_b == 'left') {
	$sidebar_classes = 'sidebar-a-right sidebar-b-left';
	$css[] = '#maininner, #sidebar-a, #sidebar-b { position: relative; }';
	$css[] = sprintf('#maininner, #sidebar-a { %s: %dpx; }', $rtl ? 'right' : 'left', $sidebar_b_width);
	$css[] = sprintf('#sidebar-b { %s: -%dpx; }', $rtl ? 'right' : 'left', $maininner_width + $sidebar_a_width);
}

// Генерируем CSS для выпадающего меню (ширина колонок в мега-меню)
foreach (array(1 => '.dropdown', 2 => '.columns2', 3 => '.columns3', 4 => '.columns4') as $i => $class) {
	$css[] = sprintf('#menu %s { width: %dpx; }', $class, $i * intval($this['config']->get('menu_width')));
}

// Подключаем CSS-файлы темы по порядку каскада
$this['asset']->addFile('css', 'css:base.css');
$this['asset']->addFile('css', 'css:layout.css');
$this['asset']->addFile('css', 'css:menus.css');
$this['asset']->addString('css', implode("\n", $css));
$this['asset']->addFile('css', 'css:modules.css');
$this['asset']->addFile('css', 'css:tools.css');
$this['asset']->addFile('css', 'css:system.css');
$this['asset']->addFile('css', 'css:extensions.css');
$this['asset']->addFile('css', 'css:custom.css');
if ($this['config']->get('animations')) $this['asset']->addFile('css', 'css:animations.css');
if (($background = $this['config']->get('background')) && $this['path']->path("css:/background/$background.css")) { $this['asset']->addFile('css', "css:/background/$background.css"); }
if (($font = $this['config']->get('font1')) && $this['path']->path("css:/font1/$font.css")) { $this['asset']->addFile('css', "css:/font1/$font.css"); }
if (($font = $this['config']->get('font2')) && $this['path']->path("css:/font2/$font.css")) { $this['asset']->addFile('css', "css:/font2/$font.css"); }
if (($font = $this['config']->get('font3')) && $this['path']->path("css:/font3/$font.css")) { $this['asset']->addFile('css', "css:/font3/$font.css"); }
$this['asset']->addFile('css', 'css:style.css');
if (($header = $this['config']->get('header')) && $this['path']->path("css:/header/$header.css")) { $this['asset']->addFile('css', "css:/header/$header.css"); }
if ($this['config']->get('direction') == 'rtl') { $this['asset']->addFile('css', 'css:rtl.css'); }
$this['asset']->addFile('css', 'css:print.css');

// Подключаем веб-шрифты (локальные файлы темы + Google Fonts для Yanone Kaffeesatz)
$http  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$fonts = array(
	'orbitron'          => 'template:fonts/orbitron.css',
	'mavenpro'          => 'template:fonts/mavenpro.css',
	'ubuntu'            => 'template:fonts/ubuntu.css',
	'metrophobic'       => 'template:fonts/metrophobic.css',
	'bebas'             => 'template:fonts/bebas.css',
	'droidsans'         => 'template:fonts/droidsans.css',
	'yanonekaffeesatz'  => $http.'://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:regular,light',
);

foreach (array_unique(array($this['config']->get('font1'), $this['config']->get('font2'), $this['config']->get('font3'))) as $font) {
	if (isset($fonts[$font])) {
		$this['asset']->addFile('css', $fonts[$font]);
	}
}

// Формируем CSS-классы для <body> (расположение сайдбаров, блог/не блог, фон, класс страницы)
$body_classes  = $sidebar_classes.' ';
$body_classes .= $this['system']->isBlog() ? 'isblog ' : 'noblog ';
$body_classes .= 'content-'.$this['config']->get('content_bg').' ';
$body_classes .= $this['config']->get('page_class');

$this['config']->set('body_classes', $body_classes);

// Настройки кнопок соцсетей — передаются в data-атрибут body для JS
$body_config['twitter']  = (int) $this['config']->get('twitter', 0);
$body_config['plusone']  = (int) $this['config']->get('plusone', 0);
$body_config['facebook'] = (int) $this['config']->get('facebook', 0);

$this['config']->set('body_config', json_encode($body_config));

// Подключаем JS-файлы темы
$this['asset']->addFile('js', 'js:warp.js');
$this['asset']->addFile('js', 'js:accordionmenu.js');
$this['asset']->addFile('js', 'js:dropdownmenu.js');
$this['asset']->addFile('js', 'js:template.js');
