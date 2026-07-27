<?php
/**
 * @package   Catalyst
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// Инициализация базовых переменных модуля
$id             = $module->id;
$position       = $module->position;
$title          = $module->title;
$showtitle      = $module->showtitle;
$content        = $module->content;
$split_color    = '';
$subtitle       = '';
$title_template = '';

// Инициализация параметров модуля.
// $$var — динамическая переменная: создаёт $suffix, $style, $color, $badge, $icon, $dropdownwidth
// из массива $params, если они там заданы (иначе null)
foreach (array('suffix', 'style', 'color', 'badge', 'icon', 'dropdownwidth') as $var) {
	$$var = isset($params[$var]) ? $params[$var] : null;
}

// Стиль по умолчанию для позиций типа "блок" (box), если стиль явно не задан
if ($style == '') {
	$box_positions = array('top-a', 'top-b', 'bottom-a', 'bottom-b', 'innertop', 'innerbottom', 'sidebar-a', 'sidebar-b');
	if (in_array($module->position, $box_positions)) {
		$style = 'box';
	}
}

// Принудительный стиль для определённых позиций — их вид жёстко задан темой
if (in_array($module->position, array('absolute', 'breadcrumbs', 'logo', 'banner', 'search', 'debug'))) {
	$style = 'raw';
	$showtitle = 0;
}
if (in_array($module->position, array('headerbar', 'toolbar-r', 'toolbar-l', 'footer'))) {
	$style = '';
	$showtitle = 0;
}
if ($module->position == 'menu') {
	$style = $module->menu ? 'raw' : 'dropdown';
}

// Определяем шаблон рендеринга модуля по стилю
switch ($style) {
	case 'box':
	case 'color':
	case 'line':
		$template       = 'default-1';
		$style          = 'mod-'.$style;
		$split_color    = 1;
		$subtitle       = 1;
		$title_template = '<h3 class="module-title">%s</h3>';
		break;

	case 'metal':
		$template       = 'default-2';
		$style          = 'mod-'.$style;
		$split_color    = 1;
		$subtitle       = 1;
		$title_template = '<h3 class="module-title">%s</h3>';
		break;

	case 'dropdown':
		$template  = 'dropdown';
		$subtitle  = 1;
		break;

	case 'raw':
		$template = 'raw';
		break;

	default:
		$template       = 'default-1';
		$style          = $suffix;
		$title_template = '<h3 class="module-title">%s</h3>';
}

$style .= ' '.$suffix;

// Значок (badge), если задан
if ($badge) {
	$badge = '<div class="badge badge-'.$badge.'"></div>';
}

// Разбиваем заголовок на два цвета по первому пробелу
if ($split_color) {
	$pos = mb_strpos($title, ' ');
	if ($pos !== false) {
		$title = '<span class="color">'.mb_substr($title, 0, $pos).'</span>'.mb_substr($title, $pos);
	}
}

// Формируем подзаголовок, если в заголовке есть разделитель '||'
if ($subtitle) {
	$pos = mb_strpos($title, '||');
	if ($pos !== false) {
		$title = '<span class="title">'.mb_substr($title, 0, $pos).'</span><span class="subtitle">'.mb_substr($title, $pos + 2).'</span>';
	}
}

// Добавляем иконку к заголовку, если задана
if ($icon) {
	$title = '<span class="icon icon-'.$icon.'"></span>'.$title;
}

// Оборачиваем заголовок в HTML-шаблон (например, <h3>)
if ($title_template) {
	$title = sprintf($title_template, $title);
}

// Ширина выпадающего меню, если задана
if ($dropdownwidth) {
	$dropdownwidth = 'style="width: '.$dropdownwidth.'px;"';
}

// Рендерим меню, если модуль является меню
if ($module->menu) {

	// Определяем рендерер меню в зависимости от позиции модуля
	if (isset($params['menu'])) {
		$renderer = $params['menu'];
	} else if (in_array($module->position, array('menu'))) {
		$renderer = 'dropdown';
	} else if (in_array($module->position, array('toolbar-l', 'toolbar-r', 'footer'))) {
		$renderer = 'default';
	} else {
		$renderer = 'accordion';
	}

	// Задаём CSS-класс стиля меню в зависимости от рендерера
	if ($renderer == 'dropdown') {
		$module->menu_style = 'menu-dropdown';
	} else if ($renderer == 'accordion') {
		$module->menu_style = 'menu-sidebar';
	} else if ($renderer == 'default') {
		$module->menu_style = 'menu-line';
	} else {
		$module->menu_style = null;
	}

	$content = $this['menu']->process($module, array_unique(array('pre', 'default', $renderer, 'post')));
}

// Рендерим итоговый шаблон модуля
echo $this->render("modules/templates/{$template}", compact('style', 'badge', 'showtitle', 'title', 'content', 'dropdownwidth'));