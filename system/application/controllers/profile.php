<?php
/**
 * @file   profile.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Thu Jul 30 09:47:02 2009
 *
 * @brief  Felhasználó beállítasai
 *
 * Copyright 2009 Nagy Zoltán
 *
 * This file is part of FormBuilder.
 *
 * FormBuilder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FormBuilder is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FormBuilder.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

include('BaseController.php');
class Profile extends BaseController {
	function __construct()
	{
		parent::__construct();
        $this->slots['js']  = 'scripts/login.js';
        $this->slots['css'] = 'css/login.css';
        $this->fields = array('pass', 'pass_match', 'email', 'result');
	}

    public function index($lang=null)
    {
        if (!isset($_SESSION['set']))
            $_SESSION['set'] = array();
        foreach ($this->fields as $field)
            if (!isset($_SESSION['set'][$field]))
                $_SESSION['set'][$field] = '';

        $this->load_lang('login', $lang);
        $slots = $this->lang->line('login');
        $slots['redirect'] = '/profile';

        foreach ($this->fields as $field)
            $slots[$field.'_val'] = $_SESSION['set'][$field];

        $this->slots['content'] = $this->load->view('login', $slots, true);

        $this->render();
    }
}
