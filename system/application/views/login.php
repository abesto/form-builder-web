<script type="text/javascript">
<?php if ($reg_failed_val == true): ?>
     var errors = true;
<?php else: ?>
    var errors = false;
<?php endif; ?>
</script>
<table>
  <tr>
    <td>
      <form method="post" id="login" action="<?php echo base_url(); ?>/login/do_login">
        <input type="hidden" name="redirect" value="<?php echo base_url().$redirect; ?>" />
        <fieldset>
          <legend><?php echo $login; ?></legend>
          <p>
            <?php echo $login_desc."\n"; ?>
          </p>
<?php if ($login_failed_val == true): ?>
          <div id="login_failed">
            <?php echo $login_failed; ?>
          </div>
<?php endif; ?>
          <table>
            <tr>
              <td><?php echo $user; ?>:</td>
              <td>
                <input type="text" name="user" value="<?php echo $login_user_val; ?>"/>
              </td>
            </tr>
            <tr>
              <td><?php echo $pass; ?>:</td>
              <td>
                <input type="password" name="pass" />
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center !important">
                <input type="submit" value="<?php echo $login; ?>" />
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
    <td>
      <form method="post" id="register" action="<?php echo base_url(); ?>/login/register">
        <input type="hidden" name="redirect" value="<?php echo base_url().$redirect; ?>" />
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
                <input type="password" name="pass" value="<?php echo $pass_val; ?>" />
              </td>
            </tr>
            <tr>
              <td><?php echo $pass_check; ?>:</td>
              <td>
                <input type="password" name="pass_match" value="<?php echo $pass_match_val; ?>" />
              </td>
            </tr>
            <tr>
              <td><?php echo $email; ?>:</td>
              <td>
                <input type="text" name="email" value="<?php echo $email_val; ?>" />
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center !important">
                <input type="submit" value="<?php echo $register; ?>" />
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
  </tr>
</table>
