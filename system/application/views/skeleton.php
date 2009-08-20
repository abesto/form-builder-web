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
    <title>Form builder, he</title>
    <meta name="Content-Type" content="text/html; charset=UTF-8" />
    <!--meta name="description" content="" /-->

<?php
    echo link_tag("css/style.css")."\n";
    if (isset($css)) echo link_tag($css)."\n";
    echo '<script type="text/javascript" src="'.base_url().'scripts/jquery-1.3.2.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url().'scripts/jquery.blockUI.min.js"></script>'."\n";
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
