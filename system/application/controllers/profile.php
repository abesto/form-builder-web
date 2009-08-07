<?php
/**
 * @file   profile.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Thu Jul 30 09:47:02 2009
 *
 * @brief  Felhasználó beállítasai
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
