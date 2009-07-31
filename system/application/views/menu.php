<?php
echo "<ul>\n";
foreach ($items as $label => $text) {
    echo '<li><a href="/'.$label.'">'.$text.'</a></li>';
}
echo "</ul>\n";