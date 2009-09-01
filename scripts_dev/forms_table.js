/**
 * @file   my_forms.js
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 15:45:29 2009
 *
 * @fileOverview Az felhasználó saját űrlapjainak kezelése
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

var $selected   = null;
var can_preview = false;
var editor      = null;

/**
 * Az előnézetek letöltésére és gyorsítótárazása
 */
var cache = {
    forms: Array(),

    /**
     * Űrlap előnézetének megjelenítése
     * Ha még nem szerepel a gyorsítótárban az űrlap, akkor letölti a szerverről
     *
     * @param id Az űrlap azonosítója
     */
    preview: function(id, name)
    {
        if (id == 'add_command') return;

        var $pre = $('#preview-form');
        if (this.forms[id] != undefined) {
            $pre.html(this.forms[id].html);
            $('#preview-html-inner').html(make_html(this.forms[id].html));
            $pre.fadeIn('slow');
        } else {
            check_rights('', false, id);
            if (can_preview === false) return;
            $.blockUI({message: downloading+'...',
                       css: {'z-index': 2000}});
            $pre.load(forms_url+'load',
                      {'id': id},
                      function (response) {
                          $.unblockUI({
                              onUnblock: function()
                                          {
                                              cache.forms[id] = {html: response,
                                                                 name: name};
                                              $('#preview-html-inner').html(
                                                  make_html(cache.forms[id].html)
                                              );
                                          }
                          });
                      },
                      'text'
                     );
        }
    },

    clear_preview: function() {
        $('#preview-form, #preview-html-inner').html('');
    },

    /**
     * Frissíti a gyorsítótár tartalmát
     *
     * A szerkesztő-alkalmazás hívja mentéskor
     */
    update: function(id, name, html)
    {
        if (this.forms[id] === undefined)
            this.forms[id] = {name: '',
                              html: '<form></form>'};

        this.forms[id].name = name;
        if (html !== null)
            this.forms[id].html = html;

        set_name(id, name);
        if (($selected != null) && (get_id($selected) == id))
            this.preview(id);
    }
};


/**
 * Ha a szerver szerint nincs bejelentkezve a felhasználó,
 * akkor átküldi a bejelentkező oldalra.
 *
 * Ha nincs joga a kért művelethez, akkor erről értesíti a felhasználót.
 *
 * Egyébként futtatja a callback-ben stringként kapott programkódot
 *
 * @param callback A futtatandó programkód
 * @param write bool; true, írás jellegű művelethez ellenőrizzük a jogokat
 * @param id Az ellenőrzendő űrlap azonosítója; ha false, nem ellenőrizzük a létezését
 */
function check_rights(callback, write, id)
{
    var data = {'write': write};
    if (id !== false)
        data['id'] = id;

    $.ajaxSetup({async: false});
    $.post(forms_url+'remote_check_rights',
           data,
           function(resp) {
               $.ajaxSetup({async: true});
               if (resp == 'NOT_LOGGED_IN') {
                   window.location = base_url+'login';
                   throw 'User is not logged in - redirecting to login page';
               } else if (resp == 'FORM_NOT_FOUND') {
                   $.blockUI({message: not_found,
                              css: {'z-index': 2000}});
                   remove_row(id);
                   setTimeout($.unblockUI, 2000);
                   can_preview = false;
               } else if (resp == 'OK') {
                   eval(callback);
                   can_preview = true;
               } else
                   throw('Unknown response from server');
           },
           'text'
    );
}

/////////////////////////////////////////////////
/// Segédfüggvények a DOM-mal történő munkára ///
/////////////////////////////////////////////////

function get_name(id)       { return $('#'+id+' td:first-child').html();     }
function set_name(id, name) { return $('#'+id+' td:first-child').html(name); }
function get_id($row)
{
    if ($row == null)
        return null;
    return $row.attr('id');
}

/**
 * @param id Az űrlap azonosítója
 * @param form_is_public Az űrlap jelenleg nyilvános?
 *
 * @return Az űrlap nyilvánosságát ki/bekapcsoló gomb
 */
function toggle_public_icon(id, form_is_public)
{
    var public_img = 'famfamfam_silk/';
    var label = '';

    if (form_is_public) {
        public_img += 'lock_open_go';
        label = public_label;
    } else {
        public_img += 'lock_delete';
        label = private_label;
    }

    return action_icon(public_img,
                       label,
                       'set_public({id}, ' + !form_is_public + ')',
                       id, true
                      );
}

/**
 * Létrehoz egy művelet-gombot (szerkesztés, átnevezés, stb)
 *
 * @param name  A megjelenítendő kép neve
 * @param label A kép alt tulajdonsága. Ez jelenik meg hovernél
 * @param fun   Az ellenőrzés után végrehajtandó programkód stringként.
 *              Az {id} helyére az id paraméter értéke kerül
 * @param id    Az űrlap azonosítója, amin a műveleteket végezzük
 * @param write bool; true, írás jellegű művelethez ellenőrizzük a jogokat
 */
function action_icon(name, label, fun, id, write)
{
    fun = fun.replace('{id}', id);
    return $('<img>').attr({'src': base_url+'img/'+name+'.png',
                            'alt': label
                           })
                     .click(function(event) { check_rights(fun, write, id); });
}

/**
 * Hozzáad egy sort az űrlapok táblázatához
 *
 * @param id     Az űrlap azonosítója
 * @param name   Az űrlap neve
 * @param form_is_public Az űrlap publikus?
 */
function add_row(id, name, is_form_public)
{
    var $row = $('<tr>').attr('id', id)
                        .append($('<td>').append(name))
                        .append($('<td>').addClass('actions')
                                 .append(action_icon('tango/document-properties',
                                                     edit,
                                                     'open_editor({id})',
                                                     id, false
                                                     ))
                                 .append(action_icon('tango/accessories-text-editor',
                                                      rename,
                                                      'rename_dialog({id})',
                                                      id, true
                                                     ))
                                 .append(toggle_public_icon(id, is_form_public))
                                 .append(action_icon('tango/emblem-unreadable',
                                                     remove,
                                                     'remove_dialog({id})',
                                                     id, true
                                                     ))
                                 .append($('<div>').append('&nbsp;'))
                               );

    var rows = $('#forms tr');
    $row.find('td').hide();
    $(rows[rows.length-1]).before($row);
    $row.find('td').fadeIn('slow');
}

/**
 * Hozzáad egy nyilvános űrlapot leíró sort az űrlapok táblázatához
 *
 * @param id        Az űrlap azonosítója
 * @param form_name Az űrlap neve
 * @param user_name Az űrlap tulajdonosának neve
 * @param owner     A bejelentkezett felhasználóhoz tartozik az űrlap?
 * @param logged_in Van bejelentkezett felhasználó?
 */
function add_row_public(id, form_name, user_name, owner, logged_in)
{
    var $row = $('<tr>').attr('id', id)
                        .append($('<td>').append(form_name))
                        .append($('<td>').append(user_name))
                        .append($('<td>').append('&nbsp;').addClass('actions')
                                         .append($('<div>').append('&nbsp;')));

    with ($row.find('td.actions div')) {
        if (logged_in) {
            before(action_icon('tango/document-properties',
                               edit,
                               'open_editor({id})',
                               id, false));
            if (owner) {
               before(action_icon('tango/accessories-text-editor',
                                  rename,
                                  'rename_dialog({id})',
                                  id, true
                                 ));
               before(action_icon('tango/emblem-unreadable',
                                   this.remove,
                                   'remove_dialog({id})',
                                   id, true
                                 ));
            }
        }
    }
    var rows = $('#forms tr');
    $row.find('td').hide();
    $(rows[rows.length-1]).before($row);
    $row.find('td').fadeIn('slow');
}


/**
 * Törli a táblázatból az adott azonosítójú sort
 */
function remove_row(id)
{
    if (id == get_id($selected))
        cache.clear_preview();
    $('#'+id).fadeOut('slow', function() { $(this).remove(); });
}


///////////////////////
/// Űrlap-műveletek ///
///////////////////////

/**
 * Megnyitja a szerkesztőben az adott azonosítójú űrlapot
 */
function open_editor(id)
{
    window.open(base_url+"builder/"+id, "builder_app");
}

/**
 * Megnyitja az adott azonosítójú űrlapra az átnevezés párbeszédablakot
 */
function rename_dialog(id)
{
    $('#rename_form')[0].id.value = id;
    $('#rename_form #old_name').html( get_name(id) );
    $('#rename_dialog').dialog('open').find('input[type=text]').focus();
}

/**
 * Átnevez egy űrlapot a párbeszédablakból kapott adatok alapján
 */
function rename_form()
{
    var form = $('#rename_form')[0];
    var id   = form.id.value;
    var name = form.new_name.value;

    $.post(base_url+'my_forms/rename',
           {'id'  : id,
            'name': name});

    $('#rename_dialog').dialog('close').find('input[name=new_name]').val('');
    set_name(id, name);
}

/**
 * Megnyitja az új űrlap párbeszédablakot
 */
function new_dialog(id)
{
    $('#new_form')[0].id.value = id;
    $('#new_dialog').dialog('open').find('input[type=text]').focus();
}

/**
 * Létrehoz egy új űrlapot a párbeszédablakból kapott adatok alapján
 */
function new_form()
{
    var form = $('#new_form')[0];
    var name = form.name.value;

    $.post(forms_url+'create',
           {'name': name},
           function(resp) {
               cache.update(resp, name, '<form></form>');
               add_row(resp, name);
               select_id(resp);
               $('#new_dialog').dialog('close').find('input[name=name]').val('');
               open_editor(resp);
           }
          );
}

/**
 * Megnyitja a törlés párbeszédablakot
 */
function remove_dialog(id)
{
    $('#remove_form')[0].id.value = id;
    $('#remove_dialog').dialog('open').find('input[type=text]').focus();
}

/**
 * Törli az űrlapot
 *
 * @param id A törlendő űrlap azonosítója
 */
function remove_form()
{
    var form = $('#remove_form')[0];
    var id = form.id.value;

    $.post(base_url+'my_forms/remove',
           {'id': id},
           function(resp) {
               remove_row(id);
               $('#remove_dialog').dialog('close');
           }
          );
}

function set_public(id, to)
{
    $.post(base_url+'my_forms/set_public',
           {
               id: id,
               to: to
           },
           function()
           {
               with ($('#'+id+' td.actions')) {
                   var $img = toggle_public_icon(id, to);
                   find('img:nth-child(3)')
                       .after($img)
                       .remove();
                   find('div').html( $img.attr('alt') );
               }
           }
          );
}

/**
 * @param text formázandó HTML
 * @return Szépen formázott HTML kód
 */
function make_html(text)
{
    text = htmlize($(text)[0], 0);
    // tbody nem kell
    text = text.replace(/ *<tbody>.*\n/g, '');
    text = text.replace(/ *<\/tbody>.*\n/g, '');
    // Hogy meg tudjuk mutatni
    text = text.replace(/</g, '&lt;');
    text = text.replace(/>/g, '&gt;');

    return text;
}

/**
 * Kijelöli az adott sort és megmutatja a hozzá tartozó űrlap előnézetét
 */
function select($row)
{
    if (get_id($row) === 'add_command') return;

    if ($selected != null)
        $selected.removeClass('selected');

    $selected = $row;
    $selected.addClass('selected');
    cache.preview( get_id($row) );
}

/// Kijelöli az adott azonosítójú sort
function select_id(id) { select($('#'+id)); }

// Inicializálás
$(document).ready( function()
{
    $.ajaxSetup({cache: false});

    // Táblázat-sorok kiemelése hovernél és kijelölése kattintásra
    $('#forms tr:gt(0)').livequery(
        function()
        {
            $(this).hover(
                function() { $(this).addClass('hovered');    },
                function() { $(this).removeClass('hovered'); }
            ).click(
                function(event) {
                    // Ha a művelet-ikonokra kattintottak, nem jelöljük ki a sort
                    if (event.target.parentNode == this)
                        select($(this));
                }
            );
        }
    );

    // Ez ideális esetben tisztán CSS lenne
    $('#forms tr:gt(0):odd'). livequery(
        function() {
            $(this).removeClass('even').addClass('odd');
        });
    $('#forms tr:gt(0):even').livequery(
        function() {
            $(this).removeClass('odd').addClass('even');
        });

    // Művelet-ikonok alá hovernél kiírjuk a művelet nevét
    $('#forms td.actions img').livequery(
        function()
        {
            $(this).hover(
                function()
                {
                    $(this).parent().find('div')
                        .html($(this).attr('alt'))
                        .css('color', 'inherit');
                },
                function()
                {
                    $(this).parent().find('div')
                        .css('color', 'transparent');
                }
            );
        }
    );

    // Form-lista letöltése a szerverről
    $.post(forms_url+'list_forms',
           Array(),
           function (forms)
           {
               if (is_public)
                   for (var i in forms['forms']) {
                       var form = forms['forms'][i];
                       add_row_public(form['id'],
                                      form['name'],
                                      form['user_name'],
                                      form['owner'],
                                      forms['logged_in']);
                   }
               else
                   for (var i in forms)
                       add_row(forms[i]['id'],
                               forms[i]['name'],
                               forms[i]['public']=='1');

           },
           'json'
          );

    // Új űrlap párbeszédablak inicializálása
    var new_buttons = {};
    new_buttons[cancel] = function() { $(this).dialog('close'); };
    new_buttons[create] = function() { $(this).find('form').submit(); };
    $('#new_dialog').dialog({autoOpen: false,
                             modal   : true,
                             width   : 'auto',
                             buttons : new_buttons
                            });

    $('#new_form').submit(
        function()
        { check_rights('new_form()', true, false); return false; });

    // Átnevezés párbeszédablak inicializálása
    var rename_buttons = {};
    rename_buttons[cancel] = function() { $(this).dialog('close'); };
    rename_buttons[rename] = function() { $(this).find('form').submit(); };
    $('#rename_dialog').dialog({autoOpen: false,
                                modal   : true,
                                width   : 'auto',
                                buttons : rename_buttons
                               });
    $('#rename_form').submit(
        function()
        {
            check_rights('rename_form()', true, this.id.value);
            return false;
        }
    );

    // Törlés párbeszédablak inicializálása
    var remove_buttons = {};
    remove_buttons[cancel] = function() { $(this).dialog('close'); };
    remove_buttons[remove] = function() { $(this).find('form').submit(); };
    $('#remove_dialog').dialog({autoOpen: false,
                                modal   : true,
                                buttons: remove_buttons
                               });
    $('#remove_form').submit(
        function()
        {
            check_rights('remove_form()', true, this.id.value);
            return false;
        }
    );

    // Az előnézet-fülek inicializálása
    $('#preview').tabs();
    $('#preview').tabs('select', '#preview-form');

    // Értesítések stílusa
    $.blockUI.defaults.css['padding']               = '15px';
    $.blockUI.defaults.css['border']                = 'none';
    $.blockUI.defaults.css['backgroundColor']       = '#888';
    $.blockUI.defaults.css['-webkit-border-radius'] = '10px';
    $.blockUI.defaults.css['-moz-border-radius']    = '10px';
    $.blockUI.defaults.css['color']                 = '#fff';
    $.blockUI.defaults.css['font-family']           = 'sans';
});
