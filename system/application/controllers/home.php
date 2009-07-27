<?php
/**
 * @file   home.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Jul 11 15:06:57 2009
 *
 * @brief  Az alapértelmezett controller; a projekt bemutatása
 */

include('BaseController.php');
class Home extends BaseController {
	function __construct()
	{
		parent::__construct();
	}

	function index($lang=null)
	{
        $this->load_lang('guide', $lang);
        $this->slots['content'] = $this->lang->line('guide')->render($this->lang->line('toc'));
        $this->render();
	}
}
