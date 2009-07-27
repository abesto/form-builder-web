<table>
  <tr>
    <td>
<form method="post" id="login" action="login">
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  <fieldset>
    <legend><?php echo $login; ?></legend>
    <p>
      <?php echo $login_desc."\n"; ?>
    </p>
    <table>
      <tr>
        <td><?php echo $user; ?>:</td>
        <td>
          <input type="text" name="user" />
        </td>
      </tr>
      <tr>
        <td><?php echo $pass; ?>:</td>
        <td>
          <input type="text" name="pass" />
        </td>
      </tr>
    </table>
    <input type="submit" value="<?php echo $login; ?>" />
  </fieldset>
</form>
</td>
<td>
<form method="post" id="register" action="/login/register" onsubmit="return is_valid();">
  <fieldset>
    <legend><?php echo $register; ?></legend>
    <p>
      <?php echo $register_desc."\n"; ?>
    </p>
    <table>
      <tr>
        <td><?php echo $user; ?>:</td>
        <td>
          <input type="text" name="user" value="<?php echo $user_val; ?>" />
        </td>
      </tr>
      <tr>
        <td><?php echo $pass; ?>:</td>
        <td>
          <input type="text" name="pass" value="<?php echo $pass_val; ?>" />
        </td>
      </tr>
      <tr>
        <td><?php echo $pass_check; ?>:</td>
        <td>
          <input type="text" name="pass_match" value="<?php echo $pass_match_val; ?>" />
        </td>
      </tr>
      <tr>
        <td><?php echo $email; ?>:</td>
        <td>
          <input type="text" name="email" value="<?php echo $email_val; ?>" />
        </td>
      </tr>
    </table>
    <input type="submit" value="<?php echo $register; ?>" />
  </fieldset>
</form>
</td>
</tr>
</table>
