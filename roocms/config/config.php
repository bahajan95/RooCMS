<?php
/**
* @package		RooCMS
* @subpackage	Configuration
* @subpackage	Config RooCMS
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.1.1
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//#########################################################
//	Настройки подключения к Базе Данных MySQL
//---------------------------------------------------------
$db_info = array();
//---------------------------------------------------------
$db_info['host'] = "localhost";					#	Хост Базы Данных
$db_info['user'] = "";							#	Имя пользователя Базы Данных
$db_info['pass'] = "";							#	Пароль пользователя Базы Данных
$db_info['base'] = "";							#	Название Базы с данными
$db_info['prefix'] = "";						#	Префикс таблиц в Базе Данных
//#########################################################


//#########################################################
//	Логин и пароль Администратора
//---------------------------------------------------------
$adm = array();
//---------------------------------------------------------
$adm['login'] = "";								#	Логин Администратора
$adm['passw'] = "";								#	Пароль Администратора
//#########################################################


//#########################################################
//	Различные параметры
//---------------------------------------------------------
$site = array();
//---------------------------------------------------------
$site['title'] = "RooCMS";						#	Заголовок сайта (используется в случае сбоя БД)
$site['domain'] = "";							#	Является значением по умолчаню и используется в случае сбоя БД
$site['sysemail'] = "";							#	Системный почтовый адрес, для уведомления о сбоях в БД
$site['skin'] = "default";						#	Шаблоны дизайна по умолчанию
//#########################################################

?>