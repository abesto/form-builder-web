<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu">
  <head>
    <title><?php echo $title; ?> - FormBuilder</title>
    <meta name="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/jquery-1.3.2.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/jquery-ui-1.7.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/jquery.livequery.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/utils.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/props.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/actions.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/translation.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/htmlize.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/builder.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/scripts/builder/remote.js"></script>

    <script type="text/javascript">
      var default_lang = '<?php echo $lang; ?>';
      var form_id = <?php echo $id; ?>;
      var base_url = '<?php echo base_url(); ?>';
    </script>

    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/builder.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/smoothness/jquery-ui-1.7.custom.css" type="text/css" media="screen" />
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
      <form action="javascript:void(0)">
        <fieldset>
          <legend>TRANSLATEME Menu</legend>
          <input type="button" value="HTML" onclick="make_html()" />
          <input type="button" value="Mentés" onclick="save()" />
          <div id="lang" style="display: inline; float: right"></div>
          <div id="status"></div>
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
  </body>
</html>
