/**
 * @file   utils.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Wed Apr  1 16:50:19 2009
 *
 * @fileOverview Segédfüggvények
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

/// Megspórolunk egy kis gépelést
$.fn.firstChild = function() {
    return $(this).children(':first-child');
};

/// A szöveg elejéről és végéről levágjuk a szóközöket
function trim(text) { return text.replace(/^\s+|\s+$/g, ''); }

/// $().value(text) <==> $().value(trim(text))
$.fn.value = function(text) {
    if (text == undefined)
        return $(this).val();
    else
        return $(this).val(trim(text));
};

/// A gyári jQuery html függvény, egy trim hívással kiegészítve
$.fn.html = function( value ) {
		return value === undefined ?
			(this[0] ?
				this[0].innerHTML.replace(/ jQuery\d+="(?:\d+|null)"/g, "") :
				null) :
			this.empty().append( trim(value) );
};


/**
 * Ellenőrzi a kiválasztott elem típusát
 * @param type A kívánt típus nodename-ja (pl. TD), vagy namejei Array-ban
 * @exception Ha az elem nem type típusú
 * @return A kiválasztott elem
 */
function check_selected_type(type)
{
    var selected = $('.selected')[0];

    if (typeof(type) == "string")
        type = Array(type);

    for (var i = 0; i < type.length; i++)
        if ($.nodeName(selected, type[i]))
            return $(selected);

    throw new Error(trans.wrong_node(type));
    return $(selected);
}

/**
 * Létrehoz egy szöveget tartalmazó elemet
 * @param name Az elem neve
 * @param text Az elem szövege
 * @example node_with_text('td', 'szöveg')    // &lt;td&gt;szöveg&lt;/td&gt;
 * @return HTML
 */
function node_with_text(name, text) { return $('<' + name + '>').append(text); }


/**
 * Létrehoz egy input elemet
 * @param name Az elem name tulajdonsága
 * @param type Az input típusa
 * @example make_input('teszt', 'checkbox')    // &lt;input type="checkbox" name="teszt" /&gt;
 * @return Az elem
 */
function make_input(name, type) { return $('<input />').attr({'type': type, 'name': name}); }


/**
 * @param td A td elem
 * @return A td elem tartalmának típusa. 'text', 'input' vagy 'select'
 */
function get_td_type($td) {
    if ($td.firstChild().size() == 0) return 'text';
    if ($td.firstChild()[0].nodeName == '#text') return 'text';
    if ($td.firstChild()[0].nodeName == 'INPUT') return 'input';
    if ($td.firstChild()[0].nodeName == 'SELECT') return 'select';
    return false;
}

/**
 * Beállítja a kapott cellában levő input mező name és id tulajdonságát.
 * Az id tulajdonság végére egy szám kerül, hogy az azonosító egyedi legyen.
 * A szám 1-től indul és a törlés miatt keletkező "lyukakat" feltölti
 *
 * @param td A td elem
 * @param name A name tulajdonság értéke
 */
function set_name($td, name) {
    var $input = $td.firstChild();

    // Ha üres a kapott name, töröljük a tulajdonságokat
    if (name == '') {
        $input.removeAttr('name').removeAttr('id');
        return;
    }

    // Megkeressük és beállítjuk az első szabad azonosítót
    var num = 1;
    $('#main input[id^=' + name + ']').each(
        function() {
            if ($(this).attr('id') == name + '_' + num)
                num += 1;
        });
    var id = name + '_' + num;
    $input.attr({'name': name, 'id': id});
    $input.next().attr('for', id); // Radio és checkbox elemek feliratához
}

/**
 * @param td Egy td elem
 * @return A cella beviteli mezőjének típusa
 */
function get_input_type($td) {
    var $input = $td.firstChild();
    var td_type = get_td_type($td);
    if (td_type == 'text') return 'none';
    else if (td_type == 'select') return 'select';
    else return $input.attr('type');
}

/**
 * Megadja egy elem adott tulajdonságát int típusú változóként
 * @param el Egy elem
 * @param attrib A tulajdonság neve
 * @return Ha a tulajdonság értéke null akkor 1, különben az érték intként
 */
function get_int_attrib($el, attrib)
{
    var val = $el.attr(attrib);
    if (!val) return 1;
    return parseInt(val);
}

/**
 * @return A mező szöveges tartalma
 */
function get_td_text($td)
{
    var text = "";
    var $selected = check_selected_type('td');
    var type = get_td_type($td);
    // Szöveges tartalom
    if (type == 'text') {
        text = $selected.html();
        // Input mező
    } else if (type == 'input') {
        var intype = $td.firstChild().attr('type');
        if ((intype == 'text') || (intype == 'button') || (intype == 'password')) text = $td.firstChild().value();
        else if ((intype == 'radio') || (intype == 'checkbox')) text = $td.text();
        // Select mező
    } else if (type == 'select') {
        if ($td.find('option').size() > 0)
            text = $td.find('option').get(0).innerHTML;
        else
            return '';
    }
    return text;
}

/**
 * Az adott cella szöveges tartalmát állítja ba
 * @param td A cella
 * @param text A szöveg
 */
function set_td_text($td, _text)
{
    var type = get_td_type($td);
    _text = _text.replace(/^\s+|\s+$/g, '');
    if (type == 'text') {
        $td.html(_text);
    } else if (type == 'input') {
        type = $td.firstChild().attr('type');
        if ($td.children().size() == 2) $td.children(':last-child').remove();
        if      ((type == 'text') || (type == 'button') || (type == 'password')) $td.firstChild().value(_text);
        else if ((type == 'radio') || (type == 'checkbox')) {
            var label = node_with_text('label', _text);
            label.attr('for', $td.firstChild().attr('id'));
            $td.append(label);
        }
    } else if (type == 'select') {
        with ($td.children('select')) {
            children().remove();
            append('<option builder_id="0">'+_text+'</option>');
        }
    }
}

/**
 * A kiválasztott cella input elemének típusát állítja be
 * @param type Az elem type tulajsonsága
 */
function set_input_type(type)
{
    var $td = check_selected_type('td');
    var text = get_td_text($td);
    var old_type = get_input_type($td);

    var $input = null;

    if ((old_type == 'select') && (type != 'select'))
        $input = $('<input type="'+type+'">');
    else if ((old_type != 'select') && (type == 'select'))
    $input = $('<select>');

    if ((old_type != 'select') && (type != 'select')) {
        $input = $td.firstChild();
        $input.get(0).setAttribute('type', type);  // JQuery nem szereti átállítani a type tulajdonságot
    } else {
        var $old_input = $td.firstChild();
        $input.attr('name', $old_input.attr('name')).attr('id', $old_input.attr('id'));  // id és name tulajdonságokat átvesszük
        $td.children().remove();
        $td.append($input);
    }

    if (type == 'text') $input.change( function() { update_props('td'); } );
    else $input.unbind('change');

    set_td_text($td, text);
    // Fájl input esetén nincs felirat, ezért frissíteni kell a tulajdonságokat
    // Selectnél pedig borul a szokásos felépítés
    if ((type == 'file') || (old_type == 'file') || (old_type == 'select') || (type == 'select'))
        update_props('td');
}

/**
 * A kiválasztott td elem tartalmának típusát állítja be
 * @param type Az elem tartalmának típusa
 */
function set_td_type(in_type)
{
    var $selected = check_selected_type('td');
    var text = get_td_text($selected);
    if      (in_type == 'text') $selected.html('');
    else if (in_type == 'input|select') {
        var $input = make_input('', 'text');
        $input.change( function() { update_props('input'); } );
        $selected.html('').append($input);
    }
    set_td_text($selected, text);
    update_props('td');
}

/**
 * @return Az éppen szerkesztett űrlap neve
 */
function get_title() { return $('#title').html(); }

/**
 * @param title Az űrlap új neve
 */
function set_title(title)
{
    $('#title').html(title);
    $('title').html(title + ' - FormBuilder');
    handle_prop_change();
}

// Az állapotsor kezelése
var status = {
    date: new Date(),
    status: null,

    // Az adott értékkel frissíti az állapotsor szövegét
    set: function(val)
    {
        this.status = val;
        this.date = new Date();
        $('#status').html(this.date.toLocaleString() + ': '+trans.status[val]);
    },

    // Újratölti a tartalmat az aktuális nyelvvel
    update_lang: function()
    {
        $('#status').html(this.date.toLocaleString() + ': '+trans.status[this.status]);
    }
};
