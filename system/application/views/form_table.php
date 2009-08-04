<script type="text/javascript" src="/scripts/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="/scripts/jquery.livequery.js"></script>
<script type="text/javascript" src="/scripts/builder/htmlize.js"></script>
<script type="text/javascript">
<?php
  foreach ($labels as $label => $value)
  echo "  var ${label}_label = '$value';\n";
?>
</script>
<link rel="stylesheet" href="/css/smoothness/jquery-ui-1.7.custom.css" type="text/css" media="screen" />

<p>
  Itt tudod b*szogatni őket.
</p>
<table id="forms">
  <col style="width: 70%;"></col>
  <col style="width: 30%;"></col>
  <tr>
    <th style="text-align: left">Név</th>
<?php if ($owner === true): ?>
    <th style="text-align: center">Műveletek</th>
<?php endif; ?>
  </tr>
  <tr id="add_command">
    <td colspan="2">
      <span onclick="new_dialog()" style="cursor: pointer">
        <img src="/img/tango/list-add.png"
             alt="TRANSLATE ME TOO"
             style="vertical-align: middle" />
        <?php echo $labels['new']; ?>
      </span>
    </td>
  </tr>
</table>

<!-- Előnézet -->
<div id="preview">
	<ul>
		<li><a href="#preview-form">Form</a></li>
		<li><a href="#preview-html">HTML</a></li>
	</ul>
	<div id="preview-form">
	</div>
	<div id="preview-html">
      <pre id="preview-html-inner"></pre>
	</div>
</div>

<!-- Átnevezés párbeszédablak -->
<div title="<?php echo $labels['rename']; ?>" class="dialog" id="rename_dialog">
  <form action="" id="rename_form" onsubmit="return check_rights('rename()', true, get_id($selected));">
    <input type="hidden" name="id" value="" />
    <table style="margin: auto">
      <tr>
        <th>Eredeti név:</th>
        <td id="old_name" style="text-align: left"></td>
      </tr>
      <tr>
        <th>Új név:</th>
        <td><input type="text" name="new_name" /></td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="submit" value="Átnevezés" />
          <input type="button" value="Mégsem" onclick="$('#rename_dialog').dialog('close');" />
        </td>
      </tr>
    </table>
  </form>
</div>

<!-- Új űrlap párbeszédablak -->
<div title="<?php echo $labels['new']; ?>" class="dialog" id="new_dialog">
  <form action="" id="new_form" onsubmit="return check_rights('new_form()', true, false);">
    <strong>Az új űrlap neve:</strong>
    <input type="text" name="name" />
    <div style="margin-top: 15px;">
      <input type="submit" value="Létrehozás" />
      <input type="button" value="Mégsem" onclick="$('#new_dialog').dialog('close');" />
    </div>
  </form>
</div>

<!-- Törlés párbeszédablak -->
<div title="<?php echo $labels['remove']; ?>" class="dialog" id="remove_dialog">
  <form action="" id="remove_form" onsubmit="return check_rights('remove()', true, get_id($selected));">
    <input type="hidden" name="id" value="" />
    <strong>Az űrlap törlésével végleg elveszik. Biztos?</strong>
    <div style="margin-top: 15px;">
      <input type="submit" value="Törlés" />
      <input type="button" value="Mégsem" onclick="$('#remove_dialog').dialog('close');" />
    </div>
  </form>
</div>
