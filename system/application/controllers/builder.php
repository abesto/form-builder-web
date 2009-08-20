<?php
/**
 * @file   builder.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sun Aug  2 12:45:35 2009
 *
 * @brief  A felhasználó jogosultságának ellenőrzése és a szerkesztő megjelenítése
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
class Builder extends BaseController {
	function __construct()
	{
		parent::__construct();
        $this->load->model('Forms_model', 'forms');
	}

    function open($id)
    {
        $this->load_lang('login');

        if ($this->user->get_user(false) === false)
            redirect('/login');

        $public = false;
        $form = $this->forms->get_form($id);

        if ($form == false) {
            $form = $this->forms->get_form_public($id);
            $public = true;
        }

        if ($form == false)
            redirect('/my_forms');

        $data = array('title' => $form->name,
                      'form'  => $form->html,
                      'lang'  => $_SESSION['lang'],
                      'id'    => $id,
                      'login' => $this->lang->line('login'),
                      'public' => true,
                      'user'  => $this->user->get_user()->name
                      );

        $this->load->view('builder', $data);
    }
}
