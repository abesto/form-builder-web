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
<script type="text/javascript" src="<?php echo base_url(); ?>/scripts/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/scripts/jquery.livequery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/scripts/htmlize.min.js"></script>
<script type="text/javascript">
<?php
foreach ($js as $label => $value)
    echo "  var $label = '$value';\n";
echo "  var base_url = '$base_url';\n";
echo '  var is_public   = '.(int)$public.";\n";
echo "  var forms_url = '$base_url";
if ($public)
    echo 'public_forms/';
else
    echo 'my_forms/';
echo "';\n";

?>
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/smoothness/jquery-ui-1.7.custom.css"
      type="text/css" media="screen" />
<br />
<h1><?php echo $public ? $php['public_title'] : $php['private_title']; ?></h1>

<table id="forms">
  <col></col>
<?php if ($public): ?>
  <col></col>
<?php endif; ?>
  <col style="width: 30%;"></col>
  <tr>
    <th style="text-align: left"><?php echo $php['form_name']; ?></th>
<?php if ($public): ?>
    <th style="text-align: left"><?php echo $php['user_name']; ?></th>
<?php endif; ?>
    <th style="text-align: center"><?php echo $php['actions']; ?></th>
  </tr>
  <tr id="add_command">
<?php if (!$public): ?>
    <td colspan="2">
      <span onclick="new_dialog()" style="cursor: pointer">
        <img src="<?php echo base_url(); ?>/img/tango/list-add.png"
             alt="<?php echo $php['new']; ?>"
             style="vertical-align: middle" />
        <?php echo $php['new']; ?>
      </span>
    </td>
<?php endif; ?>
  </tr>
</table>

<!-- Előnézet -->
<div id="preview">
	<ul>
		<li><a href="#preview-form"><?php echo $php['preview']; ?></a></li>
		<li><a href="#preview-html">HTML</a></li>
	</ul>
	<div id="preview-form">
	</div>
	<div id="preview-html">
      <pre id="preview-html-inner"></pre>
	</div>
</div>

<!-- Új űrlap párbeszédablak -->
<div title="<?php echo $php['new']; ?>" class="dialog" id="new_dialog">
  <form id="new_form">
    <strong><?php echo $php['create_name']; ?>:</strong>
    <input type="text" name="name" />
  </form>
</div>

<!-- Átnevezés párbeszédablak -->
<div title="<?php echo $js['rename']; ?>" class="dialog" id="rename_dialog">
  <form id="rename_form">
    <input type="hidden" name="id" value="" />
    <table style="margin: auto">
      <tr>
        <th><?php echo $php['old_name']; ?>:</th>
        <td id="old_name" style="text-align: left"></td>
      </tr>
      <tr>
        <th><?php echo $php['new_name']; ?>:</th>
        <td><input type="text" name="new_name" /></td>
      </tr>
    </table>
  </form>
</div>

<!-- Törlés párbeszédablak -->
<div title="<?php echo $js['remove']; ?>" class="dialog" id="remove_dialog">
  <form id="remove_form">
    <input type="hidden" name="id" value="" />
    <strong><?php echo $php['remove_confirm']; ?></strong>
  </form>
</div>
