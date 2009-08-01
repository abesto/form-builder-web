<h1>A te űrlapjaid, he.</h1>
<p>
  Itt tudod b*szogatni őket.
</p>
<table id="forms">
  <tr>
    <th>ID</th>
    <th>Név</th>
    <th>Publikus?</th>
  </tr>
<?php foreach ($forms as $form): ?>
  <tr>
    <td><?php echo $form->id; ?></td>
    <td><?php echo $form->name; ?></td>
    <td><?php echo $form->public ? 'igen' : 'nem'; ?></td>
  </tr>
<?php endforeach; ?>
</table>
<form id="preview">
    <h3>Nézzél már elő valamit na...</h3>
</form>
