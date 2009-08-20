<?php
/**
 * @file   home.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Jul 11 15:06:57 2009
 *
 * @brief  Az alapértelmezett controller; a projekt bemutatása
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
