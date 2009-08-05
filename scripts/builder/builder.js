/**
 * @file   builder.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Wed Apr  1 16:50:19 2009
 *
 * @fileOverview Inicializáció és a felhasználó parancsainak kezelése
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

/** Az aktuális nyelv */
var trans;
var $hovered = $('#main form');

var main_start = 0;

/**
 * A műveleteket tartalmazó form frissítése
 */
function update_actions(type)
{
    // Kitöröljük a régi tartalmat
    var bar = $('#actions_fs');
    $('#actions_fs input, #actions_fs > span, #actions_fs br').remove();
    if (type == 'none') return;

    var current = actions[type];
    if (!current) {
        current = function() { return Array(); };
    }
    current = current();
    // Ezeket mindenhol mutatjuk
    if (current.length > 0)
        current.push('br');
    current.push('add_fieldset()');

    // Létrehozzuk a műveletekhez tartozó gombokat
    for (var i = 0; i < current.length; i++) {
        var name = current[i];
        if (name == 'br') {
            bar.append($('<br />'));
            continue;
        }
        bar.append(
            $('<input>').attr({'type'    : 'button',
                               'name'    : name,
                               'value'   : trans.actions[name],
                               'onclick' : 'call_action("'+name+'")'})
        );
    }
}

/**
 * A tulajdonságokat tartalmazó form frissítése
 */
function update_props(type)
{
    // Kitöröljük a régi tartalmat
    $('#props_form fieldset').remove();
    $('#props_form > span').remove();
    var form = $('#props_form');
    if (type == 'none') return;

    var current = props[type];
    if (!current) {
        // Ha nincs beállítható tulajdonság, ezt tudatjuk a felhasználóval, és végeztünk
        $('#props_form').append(node_with_text('span', trans.no_props('<strong>'+type+'</strong>')));
        return;
    }
    current = current();

    // Létrehozzuk a tulajdonságokhoz tartozó beviteli mezőket
    var groups = current.get_groups();
    for (var groupi = 0; groupi < groups.length; groupi++) {
        var group = groups[groupi];
        var fs = $('<fieldset>').attr('id', group).append(
            $('<legend>').append(trans.prop_groups[group]));
        form.append(fs);

        var table = $('<table>');
        fs.append(table);

        var _props = current.get_props(group);
        for (var propsi = 0; propsi < _props.length; propsi++) {
            var prop = _props[propsi];
            var text = trans.prop_names[prop.name];
            if (text == undefined) text = prop.name;

            var $elem = prop.render();
            table.append(
                $('<tr>').append(
                    $('<td>').append(text)
                ).append(
                    $('<td>').append($elem)
                ));
            if (prop.focus)
                if ($elem[0].nodeName != "INPUT")
                    $elem.children('input[type=text]').focus();
            else
                $elem.focus();
        }
    }
}

/**
 * Az eseményt indító elemet jelöli meg hoveredként
 */
function hover()
{
    $hovered.unbind('click');
    $hovered.removeClass('hovered');
    $(this).addClass('hovered').click(select_hovered);
    $hovered = $(this);
}

/**
 * Az eseményt indító elem hovered-ségét megszűnteti
 * Ha van kijelölhető szülője az elemnek, akkor azt jelöli meg hoveredként
 */
function unhover()
{
    $hovered.unbind('click');
    $hovered.removeClass('hovered');
    var name = $hovered[0].parentNode.nodeName;
    if ((name != 'INPUT') && (name != 'TD') && (name != 'FORM') && (name != 'TABLE'))
    {
        $hovered = $($hovered[0].parentNode);
        while ((name == 'TBODY') || (name == 'TR')) {
            $hovered = $($hovered[0].parentNode);
            name = $hovered[0].nodeName;
        }
        $hovered.addClass('hovered').click(select_hovered);
    }
}

/**
 * A hover és unhover eseménykezelők csak az éppen aktuálisan hovered
 * elem click eseménykezelőjeként állítják be ezt, tehát 'this' a kattintást kapott elem
 *
 * Az ezzel megoldott probléma: ha table-nek és td-nek is van regisztrált click eseménykezelője,
 * akkor table kezelője indul el, nem td-é.
 */
function select_hovered()
{
    $(this).select();
}

/**
 * Kijelöli a kapott elemet
 */
$.fn.select = function ()
{
    $('.selected').removeClass('selected');

    var $elem = $(this);
    $elem.addClass('selected');
    var type = $elem[0].nodeName.toLowerCase();
    update_actions(type);
    update_props(type);
};

/**
 * Az UI nyelvének megváltoztatása
 * @param id A nyelv indexe a TRANS tömbben
 */
function set_lang(lang)
{
    trans = eval('TRANS.'+lang);
    $('#actions_fs legend').html(trans.actions_label);

    var prop_groups = $('#props_form fieldset');
    for (var i = 0; i < prop_groups.length; i++)
        prop_groups[i].firstChild.innerHTML = trans.prop_groups[prop_groups[i].getAttribute('id')];

    var selected = $('.selected')[0];
    if (selected) {
        var name = selected.nodeName.toLowerCase();
        update_actions(name);
        update_props(name);
    }

    // Bejelentkező párbeszédablak
    $('#user_label').html(trans.user+':');
    $('#pass_label').html(trans.pass+':');

    var login_buttons = {};
    login_buttons[trans.cancel] = function() { $(this).dialog('close'); };
    login_buttons[trans.login]  = function() { $(this).find('form').submit(); };
    $('#login_dialog').dialog('option', {title: trans.login,
                                         buttons: login_buttons
                                        });

}

function make_html()
{
    var text = htmlize($('#main form')[0], 0);
    // tbody nem kell
    text = text.replace(/ *<tbody>.*\n/g, '');
    text = text.replace(/ *<\/tbody>.*\n/g, '');
    // amit muszáj
    text = text.replace(/</g, '&lt;');
    text = text.replace(/>/g, '&gt;');
    // megmutatjuk
    var $html = $('#html');
    $html.html('<pre>' + text + '</pre>').dialog('option', 'title', $('#title').html()+' HTML');
    $html.dialog('open');;
    if ($html.width() < 300) $html.css({'width': '300px'});
}

$(document).ready(function (){
    // #main alap magassága
    $('#main').height($(window).height() - $('#actions').height());

    // Átméretezhető tulajdonságok div
    $('#props').resizable({handles: 'w',  ghost: true,
                           stop: function(event, ui) {
                               $('#main').width($('body').width() - ui.size.width);
                               $('#actions').width($('body').width() - ui.size.width);
                           }});

    // A kijelölhető elemek eseménykezelése
    $('#main td, #main table, #main fieldset').livequery(function() {$(this).hover(hover, unhover);})
                                              .livequery(function() {if ($(this).hasClass('hovered')) $(this).select(); });

    // A felesleges szóközöket mindig levágjuk
    $('body input').livequery('change', function() { this.value = trim(this.value); });

    // checked tulajdonság
    $('#main input[type=checkbox], #main input[type=radio]').change(
        function()
        {
            if (this.checked)
                this.setAttribute('checked', 'checked');
        }
    );

    var lang = $('#lang');
    for (var i = 0; i < TRANS.list.length; i++) {
        var id = TRANS.list[i];
        var name = eval('TRANS.'+id).name;
        var span = node_with_text('span', name).attr('id', id).click(
            function() {set_lang($(this).attr('id'));}
        );
        lang.append(span);
    }

    // Bejelentkező párbeszédablak inicializálása
    $('#login_dialog').dialog({
                        autoOpen : false,
                        width    : 'auto',
                        modal    : true
                       });
    $('#login_form').submit(function() { return login(); });

    set_lang(default_lang);
    $('#props_form fieldset legend').html(trans.props);

    // HTML párbeszédablak inicializálása
    $('#html').dialog({autoOpen: false,
                       width: 'auto',
                       modal: true,
                       // Tulajdonság-mező fókuszának visszaállítására
                       close: function() { update_props($('.selected')[0].nodeName.toLowerCase()); }});

    $('#main form').select();
});
