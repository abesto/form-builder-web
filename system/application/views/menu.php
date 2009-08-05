<?php
echo "<ul>\n";
foreach ($items as $label => $text) {
    echo '<li><a href="'.base_url().$label.'">'.$text.'</a></li>';
}
echo "</ul>\n";