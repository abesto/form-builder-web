<?php
include('BaseController.php');
class HelloWorld extends BaseController {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
        $this->slots['content'] = 'Hello world!';
        $this->render();
	}
}
