<?php
echo "<ul>\n";
foreach ($items as $item)
    echo '<li><a href="/'.strtolower($item).'">'.$item.'</a></li>'."\n";
echo "</ul>\n";