<?php
/**
 * @file   builder.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sun Aug  2 12:45:35 2009
 *
 * @brief  A felhasználó jogosultságának ellenőrzése és a szerkesztő megjelenítése
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