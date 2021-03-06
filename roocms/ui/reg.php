<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage	 User Registration
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.2
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_Reg
 */
class UI_Reg {

	public function __construct() {

		global $structure, $roocms, $users, $post;

		# title
		$structure->page_title = "Регистрация";

		# breadcumb
		$structure->breadcumb[] = array('part'=>'reg', 'title'=>'Регистрация');

		# if users registred
		if($users->uid != 0) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}
		
		# action
		switch($roocms->act) {
			case 'join':
				if(isset($post->join)) {
					$this->join();
				}
				break;

			case 'expressreg':
				if(isset($post->expressreg)) {
					$this->expressreg();
				}
				break;

			case 'activation':
				$this->activation();
				break;

			case 'verification':
				$this->verification();
				break;
			
			default:
				$this->profile();
				break;
		}
	}


	/**
	 * Функция запроса анкеты будущего пользователя (формы регистрации)
	 */
	private function profile() {

		global $tpl;

		$tpl->load_template("reg_profile");
	}


	/**
	 * Функция проверки регистрационных данных (анкеты пользователя) и регистрации
	 */
	private function join() {

		global $db, $smarty, $users, $tpl, $post, $logger, $security, $site;

		# nickname
		$users->check_create_nickname();

		# login
		$users->check_create_login();

		# email
		$users->valid_user_email($post->email);

		if(!isset($_SESSION['error'])) {

			#password
			if(!isset($post->password)) {
				$post->password = $security->create_new_password();
			}

			$salt = $security->create_new_salt();
			$password = $security->hashing_password($post->password, $salt);

			# check personal data
			$users->correct_personal_data();

			# activation code
			$activation = array();
			$activation['code'] = randcode(7);


			$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, mailing, password, salt, date_create, date_update, last_visit, activation_code,
								 user_name, user_surname, user_last_name, user_birthdate, user_sex)
							 VALUES ('".$post->login."', '".$post->nickname."', '".$post->email."', '".$post->mailing."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '".$activation['code']."',
								 '".$post->user_name."', '".$post->user_surname."', '".$post->user_last_name."', '".$post->user_birthdate."', '".$post->user_sex."')");
			$uid = $db->insert_id();


			# avatar
			$users->upload_avatar($uid);


			# activation link
			$activation['link'] = $site['domain'].SCRIPT_NAME."?part=reg&act=activation&email=".$post->email."&code=".$activation['code'];


			# Уведомление пользователю на электропочту
			$smarty->assign("login", $post->login);
			$smarty->assign("nickname", $post->nickname);
			$smarty->assign("email", $post->email);
			$smarty->assign("password", $post->password);
			$smarty->assign("activation", $activation);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_new_registration", true);

			sendmail($post->email, "Вы зарегистрировались на сайте ".$site['title'], $message);

			# уведомление
			$logger->info("Поздравляем с Регистрацией. Вам осталось подтвердить адрес электронной почты для этого пройдите по ссылке отправленной Вам в письме.", false);

			# переход
			go(SCRIPT_NAME."?part=reg&act=activation&email=".$post->email);
		}
		else {
			goback();
		}
	}


	/**
	 * Функция экспресс регистрации
	 */
	private function expressreg() {

		global $post;

		$var = explode("@", $post->email);
		$post->nickname = $var[0];
		$post->mailing = 1;

		// TODO: Не уверен что тут то самое место. Надо обдумать этот момент.
		$exp = time()+(60*60*24*7);
		setcookie("mailing", true, $exp);

		$this->join();
	}


	/**
	 * Функция активации аккаунта и проверки электронной почты
	 */
	private function activation() {

		global $get, $parse, $smarty, $tpl;

		$email = (isset($get->_email) && $parse->valid_email($get->_email)) ? $get->_email : "" ;
		$code  = (isset($get->_code)) ? $get->_code : "" ;

		# tpl
		$smarty->assign("email", $email);
		$smarty->assign("code",  $code);
		$tpl->load_template("reg_activation");
	}


	/**
	 * Функция проверки подтверждения регистрации пользователя.
	 */
	private function verification() {

		global $db, $parse, $logger, $post;
		
		if(isset($post->email, $post->code) && $parse->valid_email($post->email) && $db->check_id($post->email, USERS_TABLE, "email", "activation_code='".$post->code."'")) {
			$db->query("UPDATE ".USERS_TABLE." SET status='1', activation_code='', last_visit='".time()."' WHERE email='".$post->email."'");
			$logger->info("Спасибо. Ваша учетная запись активирована. Добро пожжаловать.", false);
			go("/");
		}
		else {
			$logger->error("Активация не удалась. Мы не нашли подходящих сведений в базе данных.", false);
			goback();
		}

	}
}

/**
 * Init Class
 */
$uireg = new UI_Reg;