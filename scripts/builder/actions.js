/**
 * @file   actions.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Wed Apr  1 16:50:19 2009
 *
 * @fileOverview Az egyes form elemekhez tartozó műveletek
 *
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
 *
 */

// Tábla sor/cella műveletekhez irányok
const LEFT  = 100;
const RIGHT = 101;
const UP    = 102;
const DOWN  = 103;

const WrongDir = new Error('Wrong direction');

/**
 * Ezen keresztül hívjuk meg a műveleteket
 *
 * Beállítja a dirty flaget
 * Undo/redo rendszernél itt adnánk hozzá az előzményekhez
 */
function call_action(fun)
{
    dirty = true;
    eval('ACTIONS.'+fun);
}

/**
 * @namespace A végezhető műveleteket tartalmazó objektum
 *
 * A tárol-append-select trükkre azért van szükség, mert a select függvény
 * elvárja, hogy már a DOM része legyen az elem
 */
const ACTIONS = {
    /** Új fieldset táblázattal */
    add_fieldset: function()
    {
        var $selected = check_selected_type('form');
        var $fieldset = $('<fieldset>').append($('<legend>'));
        $selected.append($fieldset);
        $fieldset.select();
    },

    /** Fieldset törlése */
    remove_fieldset: function()
    {
        check_selected_type('fieldset').remove();
        update_actions('none');
        update_props('none');
    },

    /** Új tábla */
    add_table: function()
    {
        var $selected = check_selected_type(Array('form', 'fieldset'));
        var $td = $('<td>');
        $selected.append($('<table>').append($('<tr>').append($td)));
        $td.select();
    },

    /** Tábla törlése */
    remove_table: function() { check_selected_type('table').remove(); },

    /**
     * Létrehoz egy új cellát a kiválasztott cella mellett
     * @param dir Az új cella helye a kiválasztott cellához képest
     * @exception WrongDir Ha az irány nem LEFT vagy RIGHT
     */
    create_cell: function(dir)
    {
        var $selected = check_selected_type('td');
        var $tr = $selected.parent();
        var $td = $('<td>');
        if      (dir == RIGHT) $td.insertAfter($selected);
        else if (dir == LEFT)  $td.insertBefore($selected);
        else    throw WrongDir;
        $td.select();
        update_actions('td');
        update_props('td');
    },

    /**
     * Létrehoz egy új sort a táblázatban
     *
     * @param dir Az új sor helye a kiválasztott cella sorához képest
     * @exception WrongDir Ha az irány nem UP vagy DOWN
     */
    create_row: function(dir)
    {
        var $selected = check_selected_type('td');
        //var $table = selected.parentNode.parentNode;
        var $current = $selected.parent();
        var $td = $('<td>');
        var $tr = $('<tr>').append($td);
        if      (dir == DOWN) $tr.insertAfter($current);
        else if (dir == UP)   $tr.insertBefore($current);
        else    throw WrongDir;
        $td.select();
    },

    /**
     * Két vízszintesen egymás mellett elhelyezkedő, azonos magasságú cellát
     * egyesít. Ha mindkét cella tartalma szöveg, akkor a tartalmakat
     * összekapcsolja egy szóközzel. Különben a kiválasztott cella tartalma
     * felülírja a másik celláét
     *
     * @param dir A cella-egyesítés iránya
     * @exception WrongDir Ha az irány nem LEFT vagy RIGHT
     */
    merge_cells: function(dir) {
        var $selected = check_selected_type('td');
        var $tr = $selected.parent();
        var $victim;

        if (dir == LEFT) {
            var $prev = $selected.prev();
            if ((get_td_type($selected) == 'text') && (get_td_type($prev) == 'text'))
                $prev.append(' ' + $selected.html());
            else
                $prev.html($selected.html());
            $victim = $selected;
            $selected = $prev;
            $prev.addClass('selected');

        } else if (dir == RIGHT) {
            var $next = $selected.next();
            if ((get_td_type($selected) == 'text') && (get_td_type($next) == 'text'))
                $selected.append(' ' + $next.html());
            $victim = $next;
        } else throw WrongDir;
        $victim.remove();

        var size0 = get_int_attrib($selected, 'colspan');
        var size1 = get_int_attrib($victim, 'colspan');
        $selected.attr('colspan', size0 + size1);

        update_actions('td');
        update_props('td');
    },

    /**
     * Egy több oszlopos cellát oszt fel
     * A cella tartalma a kiválasztott cellában marad
     * Az új cella a megadott irányban jön létre, tartalom nélkül
     *
     * @param dir A cella-osztás iránya
     * @exception WrongDir Ha az irány nem LEFT vagy RIGHT
     */
    split_cell: function(dir)
    {
        var $td = $('<td>');
        var $selected = check_selected_type('td');
        var $tr = $selected.parent();

        $selected.attr('colspan', get_int_attrib($selected, 'colspan')-1);
        if      (dir == LEFT)  $td.insertBefore($selected);
        else if (dir == RIGHT) $td.insertAfter($selected);
        else    throw WrongDir;

        update_actions('td');
        update_props('td');
    },

    /** Cella törlése */
    remove_cell: function()
    {
        var $selected = check_selected_type('td');
        var $tr = $selected.parent();
        var $table = $tr.parent().parent();
        $selected.remove();  // <-- öngyilkosság :(
        if ($tr.children().size() == 0) $tr.remove();  // <-- családostól :'(
        if ($table.find('tr').size() == 0) $table.remove();  // <-- tiszta lemmingek :(
        update_actions('none');
        update_props('none');
    }
};

/**
 * @namespace Az egyes elemeken végezhető műveletek listáját visszaadó függvények a
 *            kiválasztott elem helyzetét és környezetét figyelembevéve
 */
const actions = {
    /**
     * td elemen végrehajtható műveletek<br />
     * Mindig: cella létrehozása (jobbra, balra), új sor létrehozása (fel, le)<br />
     * Ha van azonos magasságú szomszédos cella: cellák egyesítése (balra és/vagy jobbra)<br />
     * Ha a cella több oszlopos: cella felosztása (balra, jobbra)
     */
    td: function()
    {
        var $sel = $(check_selected_type('td'));
        var ret = Array('create_cell(LEFT)', 'create_cell(RIGHT)', 'create_row(UP)', 'create_row(DOWN)', 'br');

        if ($sel.prev().attr('rowspan') == $sel.attr('rowspan'))
            ret.push('merge_cells(LEFT)');
        if ($sel.next().attr('rowspan') == $sel.attr('rowspan'))
            ret.push('merge_cells(RIGHT)');

        if (ret[ret.length-1] != 'br')
            ret.push('br');

        if ($sel.attr('colspan') > 1) {
            ret = ret.concat(Array('split_cell(LEFT)', 'split_cell(RIGHT)'));
            ret.push('br');
        }

        ret.push('remove_cell()');
        return ret;
    },

    /** Táblázatot csak törölni lehet. Szegény :( */
    table: function()
    {
        check_selected_type('table');
        return Array('remove_table()');
    },

    /** A fieldsethez lehet hozzáadni táblázatot, vagy lehet törölni a fieldsetet */
    fieldset: function()
    {
        check_selected_type('fieldset');
        return Array('add_table()', 'remove_fieldset()');
    }
};
