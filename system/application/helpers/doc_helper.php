<?php
/**
 * @file   doc_helper.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Tue Jul 07 19:24:58 2009
 *
 * @brief  Szöveges tartalom formázott megjelenítését könnyítő osztályok
 *
 * A felépítés követi a TeX rendszereket. Jelen verzió Chapter (fejezet) és
 * Section részeket különböztet meg, de szükség esetén könnyen bővíthető.
 */


/**
 * Egy fejezet. Ezen belül lehetnek {@link Section} objektumok.
 * Írhat tartalomjegyzéket.
 */
class Chapter
{
    /**
     * @param title A fejezet címe
     */
    function __construct($title)
    {
        $this->title = $title;
        $this->sections = array();
    }

    /**
     * Létrehoz egy úgy {@link Section}t a fejezeten belül szöveggel együtt.
     * A kötelező paraméterek után tetszőleges számú paraméter adható: ezek mindegyike
     * egy-egy új bekezdés szövegét fogja képezni.
     *
     * @param title A {@link Section} címe
     * @param label A címke, amivel a fejezetben tároljuk a Sectiont
     */
    function new_section($title, $label)
    {
        $this->sections[$label] = new Section($title, $label);
        for ($i = 2; $i < func_num_args(); $i++)
            $this->sections[$label]->add_paragraph(func_get_arg($i));
    }

    /**
     * HTML kódot hoz létre a fejezet tartalmából
     *
     * @param toc Ha nem false, akkor $toc címmel generál egy tartalomjegyzéket
     *
     * @return HTML
     */
    function render($toc=false)
    {
        $ret = '<h1>' . $this->title . '</h1>' . "\n";

        if ($toc == true)
            $ret .= $this->render_toc($toc);

        foreach ($this->sections as $s)
            $ret .= $s->render();
        return $ret."\n";
    }

    /**
     * Tartalomjegyzéket generál
     *
     * @param title A tartalomjegyzék címe
     *
     * @return HTML
     */
    private function render_toc($title)
    {
        $ret = '<h2>' . $title . '</h2>'. "\n";
        $ret .= "<ol id=\"toc\">\n";

        foreach ($this->sections as $s)
            $ret .= "  <li><a href=\"#{$s->label}\">{$s->title}</a></li>\n";
        return $ret."</ol>\n";
    }
}

/**
 * Egy cím, egy címke az anchorhoz* és tetszőleges számú bekezdés
 *
 * * <a id="címke"></a>
 */

class Section
{
    /**
     * @param title Cím
     * @param label Belső tárolásra címke
     */
    function __construct($title, $label)
    {
        $this->title = $title;
        $this->label = $label;
        $this->paragraphs = array();
    }

    /**
     * Új bekezdést ad hozzá a Sectionhöz
     *
     * @param p A bekezdés szövege
     */
    function add_paragraph($p)
    {
        $this->paragraphs[] = $p;
    }

    /**
     * @return HTML
     */
    function render()
    {
        $ret = '<a id="' . $this->label . '"></a>' . "\n";
        $ret .= '<h2>' . $this->title . '</h2>' . "\n";

        foreach ($this->paragraphs as $p)
            $ret .= "<p>\n$p\n</p>";
        return $ret."\n";
    }
}