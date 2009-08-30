<?php
/*
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
 */
?>
<?php echo '<?xml version="1.1" encoding="UTF-8"?>'; ?>
<?php echo doctype('xhtml11'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu">
  <head>
    <title><?php echo $title; ?> - FormBuilder</title>
    <meta name="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="<?php echo base_url(); ?>scripts/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>scripts/jquery-ui-1.7.2.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>scripts/jquery.livequery.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>scripts/htmlize.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>scripts/builder.min.js"></script>

    <script type="text/javascript">
      var default_lang = '<?php echo $lang; ?>';
      var form_id      = <?php echo $id; ?>;
      var base_url     = '<?php echo base_url(); ?>';
      var is_public    = <?php echo (string)$public; ?>;
      var user         = '<?php echo $user; ?>';
    </script>

    <link rel="stylesheet" href="<?php echo base_url(); ?>css/builder.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/smoothness/jquery-ui-1.7.custom.min.css" type="text/css" media="screen" />
  </head>
  <body>
    <h1 id="title"><?php echo $title; ?></h1>
    <div id="main"><form action="javascript:void(0)" id="form"><?php echo $form; ?></form></div>
    <div id="actions">
      <form action="javascript:void(0)">
        <fieldset id="actions_fs">
          <legend></legend>
        </fieldset>
      </form>
    </div>
    <div id="props">
      <form action="javascript:void(0)" id="menu" onsubmit="return false;">
        <fieldset>
          <legend id="menu_label"></legend>
          <input type="button" value="HTML" onclick="make_html()" />
          <input type="button" value="" id="save_button" onclick="save()" />
          <input type="button" value="" id="save_as_button" onclick="$('#save_as_dialog').dialog('open')" />
          <div id="lang"></div>
        </fieldset>
      </form>
      <form id="props_form" action="javascript:void(0)">
        <fieldset>
          <legend></legend>
          <table>
            <tr>
              <td></td>
            </tr>
          </table>
        </fieldset>
      </form>
    </div>
    <div id="html"></div>

    <div id="login_dialog">
      <div id="login_error" style="font-weight: bold; text-align: center"></div>
      <form id="login_form" >
        <table>
          <tr>
            <th id="user_label" style="text-align: right"></th>
            <td>
              <input type="text" name="user" value=""/>
            </td>
          </tr>
          <tr>
            <th id="pass_label" style="text-align: right"></th>
            <td>
              <input type="password" name="pass" />
            </td>
          </tr>
        </table>
      </form>
    </div>

    <div id="save_as_dialog">
      <form id="save_as_form" >
        <table>
          <tr>
            <th id="name_label" style="text-align: right"></th>
            <td>
              <input type="text" name="new_name" />
            </td>
          </tr>
        </table>
      </form>
    </div>
  </body>
  <div id="status">&nbsp;</div>
</html>
