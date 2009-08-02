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
        $data = array();
        $form = $this->forms->get_form($id);
        $data['title'] = $form->name;
        $data['form']  = $form->html;
        $this->load->view('builder', $data);
    }
}