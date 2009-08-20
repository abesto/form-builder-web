<?php
/**
 * @file   public_forms.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Thu Aug  6 18:01:28 2009
 *
 * @brief  A nyilvános űrlapok csak olvasható listájának megjelenítése
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
class Public_forms extends BaseController {
	function __construct()
	{
		parent::__construct();
        $this->slots['css'] = 'css/my_forms.min.css';
        $this->slots['js'] =  'scripts/forms_table.min.js';
	}

	function index($lang=null)
    {
        $this->load_lang('forms', $lang);

        $js_labels = $this->lang->line('js');
        foreach ($js_labels as $key => $text)
            $js_labels[$key] = str_replace(' ', '&nbsp;', $text);

        $data = array('public'   => true,
                      'js'       => $js_labels,
                      'php'      => $this->lang->line('php'),
                      'base_url' => base_url()
                      );

        $this->slots['content'] = $this->load->view('form_table', $data, true);
        $this->render();
    }

    function list_forms()
    {
        $data = array('forms' => $this->forms->get_form_list_public(),
                      'logged_in' => ($this->user->get_user(false) !== false));
        echo json_encode($data);

    }

    /**
     * @return Egy űrlap tartalma
     */
    function load()
    {
        $id = $_POST['id'];
        $form = $this->forms->get_form_public($id);
        echo $form->html;
    }
}
