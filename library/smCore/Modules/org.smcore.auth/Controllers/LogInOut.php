<?php

/**
 * 
 *
 * @package smCore
 * @author smCore Dev Team
 * @license MPL 1.1
 * @version 1.0 Alpha
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1
 * (the "License"); you may not use this package except in compliance with the
 * License. You may obtain a copy of the License at http://www.mozilla.org/MPL/
 *
 * The Original Code is smCore.
 *
 * The Initial Developer of the Original Code is the smCore project.
 *
 * Portions created by the Initial Developer are Copyright (C) 2011
 * the Initial Developer. All Rights Reserved.
 */

namespace smCore\Modules\Auth\Controllers;

use smCore\Application, smCore\Module\Controller;

class LogInOut extends Controller
{
	public function preDispatch()
	{
		$this->_getParentModule()->loadLangPackage();
	}

	public function login()
	{
		$module = $this->_getParentModule();
		$input = Application::get('input');

		// I'd actually like to use the router to route to a different method depending on whether this was a GET or a POST
		if ($input->post->keyExists('submit'))
		{
			// @todo
		}
		else
		{
			return $module->render('login');
		}
	}

	public function logout()
	{
	}
}