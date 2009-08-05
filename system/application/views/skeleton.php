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
    echo '<script type="text/javascript" src="'.base_url().'scripts/jquery-1.3.2.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url().'scripts/jquery.blockUI.js"></script>'."\n";
    if (isset($js)) echo '<script type="text/javascript" src="'.base_url().$js.'"></script>'."\n";
?>

  </head>
  <body>
    <div id="header">
      <div id="langs">
        <ul>
<?php
$page_url = current_url();
// Levesszük az esetleg már ottlevő nyelvet az url-ből
foreach ($langs as $lang => $name)
    if (substr($page_url, strlen($page_url) - strlen($lang)) == $lang) {
        $page_url = str_replace($lang, '', $page_url);
        break;
    }

// Kell egy perjel is..
if (substr($page_url, strlen($page_url) -1) != '/')
    $page_url .= '/';

// És most a linkek:
foreach($langs as $lang => $name): ?>
          <li><a href="<?php echo $page_url.$lang; ?>">
                <img src="<?php echo base_url(); ?>img/famfamfam/png/<?php echo $lang; ?>.png"
                     alt="<?php echo $name; ?>" />
                <?php echo $name; ?></a></li>
<?php endforeach; ?>
        </ul>
      </div>
      <img src="<?php echo base_url(); ?>img/header.jpg" alt="FormBuilder" />
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
