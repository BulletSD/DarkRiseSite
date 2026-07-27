<?php
/**
 * @package   Catalyst
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// Подключаем ядро фреймворка WARP
require_once( dirname( __FILE__ ) . '/warp/warp.php' );
$warp = Warp::getInstance();

// Регистрируем пути к ресурсам темы
$warp['path']->register( dirname( __FILE__ ) . '/warp/systems/wordpress/helpers', 'helpers' );
$warp['path']->register( dirname( __FILE__ ) . '/warp/systems/wordpress/layouts', 'layouts' );
$warp['path']->register( dirname( __FILE__ ) . '/layouts', 'layouts' );
$warp['path']->register( dirname( __FILE__ ) . '/js', 'js' );
$warp['path']->register( dirname( __FILE__ ) . '/css', 'css' );

// Инициализация системы темы
$warp['system']->init();
