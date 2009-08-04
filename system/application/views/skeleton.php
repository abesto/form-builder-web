<?php echo doctype('xhtml11'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu">
  <head>
    <title>Form builder, he</title>
    <meta name="Content-Type" content="text/html; charset=UTF-8" />
    <!--meta name="description" content="" /-->

<?php
    echo link_tag("css/style.css")."\n";
    if (isset($css)) echo link_tag($css)."\n";
    echo '<script type="text/javascript" src="/scripts/jquery-1.3.2.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="/scripts/jquery.blockUI.js"></script>'."\n";
    if (isset($js)) echo '<script type="text/javascript" src="/'.$js.'"></script>'."\n";
?>

  </head>
  <body>
    <div id="header">
      <img src="/img/header.jpg" alt="FormBuilder" />
      <div id="menu">

<?php echo $menu; ?>

      </div>
    </div>
    <div id="content">

<?php echo $content; ?>

    </div>
    <div>
      Page generated in {elapsed_time} seconds
    </div>
  </body>
</html>
