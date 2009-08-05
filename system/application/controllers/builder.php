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

        $form = $this->forms->get_form($id);

        $data = array('title' => $form->name,
                      'form'  => $form->html,
                      'lang'  => $_SESSION['lang'],
                      'id'    => $id,
                      'login' => $this->lang->line('login')
                      );

        $this->load->view('builder', $data);
    }
}