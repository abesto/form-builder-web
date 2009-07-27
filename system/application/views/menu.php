<?php
echo "<ul>\n";
// Ha $items['login'] == false, akkor a felhasználó be van jelentkezve
// Egyéb esetekben $items[$label] a megjelenítendő link szövegét tartalmazza
foreach ($items as $label => $text) {
    if ($label == 'login')
        $logged_in = ($text === false);
    else
        echo '<li><a href="/'.$label.'">'.$text.'</a></li>';
}

if ($logged_in === true) {
    $text = str_replace('%s', '<a href="/profile">%s</a>', $items['welcome']);
    echo sprintf('<li>'.$text.'</li>', $items['user']);
} else
    echo '<li><a href="/login">'.$items['login'].'</a></li>'."\n";
echo "</ul>\n";