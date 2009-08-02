<h1>A te űrlapjaid, he.</h1>
<p>
  Itt tudod b*szogatni őket.
</p>
<table id="forms">
  <col style="width: 100%;"></col>
  <col style="width: 1px;"></col>
  <tr>
    <th>Név</th>
    <th>Publikus?</th>
<?php if ($owner === true): ?>
    <th>Szerkesztés</th>
<?php endif; ?>
  </tr>
<?php foreach ($forms as $form): ?>
  <tr id="<?php echo $form->id; ?>">
    <td><?php echo $form->name; ?></td>
    <td><?php echo $form->public ? 'igen' : 'nem'; ?></td>
<?php if ($owner === true): ?>
    <td onclick="open_editor(<?php echo $form->id; ?>)">Na.</td>
<?php endif; ?>
  </tr>
<?php endforeach; ?>
</table>
<form id="preview">
    <h3>Nézzél már elő valamit na...</h3>
</form>
