/**
 * @file   forms_table.js
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 15:45:29 2009
 *
 * @fileOverview Az űrlapokat megjelenítő táblázathoz tartozó JS
 */

var $selected = null;
var can_preview = false;

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
    preview: function(id)
    {
        if (id == 'add_command') return;

        var $pre = $('#preview-form');
        if (this.forms[id] != undefined) {
            $pre.html(this.forms[id]['html']);
            $('#preview-html-inner').html(make_html(this.forms[id]['html']));
            $pre.fadeIn('slow');
        } else {
            check_rights('', false, id);
            if (can_preview === false) return;
            $.blockUI({message: downloading+'...',
                       css: {'z-index': 2000}});
            $pre.load(base_url+'my_forms/load',
                      {'id': id},
                      function (response) {
                          $.unblockUI({
                              onUnblock: function()
                                          {
                                              cache.forms[id] = response;
                                              $('#preview-html-inner').html(make_html(cache.forms[id]));
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
        this.forms[id] = {name: name,
                          html: html};
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
    $.post(base_url+'my_forms/remote_check_rights',
           data,
           function(resp) {
               if (resp == 'NOT_LOGGED_IN') {
                   window.location = base_url+'login';
                   throw 'User is not logged in - redirecting to login page';
               } else if (resp == 'FORM_NOT_FOUND') {
                   $.blockUI({message: not_found,
                              css: {'z-index': 2000}});
                   remove_row(id);
                   setTimeout($.unblockUI, 3000);
                   can_preview = false;
               } else if (resp == 'OK') {
                   eval(callback);
                   can_preview = true;
               } else
                   throw('Unknown response from server');
           },
           'text'
    );
    $.ajaxSetup({async: true});
    return false;
}

/////////////////////////////////////////////////
/// Segédfüggvények a DOM-mal történő munkára ///
/////////////////////////////////////////////////

function get_name(id)       { return $('#'+id+' td:first-child').html();     }
function set_name(id, name) { return $('#'+id+' td:first-child').html(name); }
function get_id($row)       { return $row.attr('id');                        }

/**
 * Létrehoz egy művelet-gombot (szerkesztés, átnevezés, stb)
 *
 * @param name  A megjelenítendő kép neve
 * @param label A kép alt tulajdonsága. Ez jelenik meg hovernél
 * @param fun   Az ellenőrzés után végrehajtandó programkód stringként. Az {id} helyére az id paraméter értéke kerül
 * @param id    Az űrlap azonosítója, amin a műveleteket végezzük
 * @param write bool; true, írás jellegű művelethez ellenőrizzük a jogokat
 */
function action_icon(name, label, fun, id, write)
{
    fun = fun.replace('{id}', id);
    return $('<img>').attr({'src': base_url+'img/tango/'+name+'.png',
                            'alt': label
                           })
                     .click(function() { check_rights(fun, write, id); });
}

/**
 * Hozzáad egy sort az űrlapok táblázatához
 *
 * @param id Az űrlap azonosítója
 * @param name Az űrlap neve
 */
function add_row(id, name)
{
    var $row = $('<tr>').attr('id', id)
                        .append($('<td>').append(name))
                        .append($('<td>').addClass('actions')
                                        .append(action_icon('document-properties',
                                                            edit,
                                                            'open_editor({id})',
                                                            id, true
                                                            ))
                                         .append(action_icon('accessories-text-editor',
                                                             rename,
                                                             'rename_dialog({id})',
                                                             id, true
                                                            ))
                                        .append(action_icon('emblem-unreadable',
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
    window.open (base_url+"builder/"+id,
                 "builder_app");
}

/**
 * Megnyitja az adott azonosítójú űrlapra az átnevezés párbeszédablakot
 */
function rename_dialog(id)
{
    $('#rename_form')[0].id.value = id;
    $('#rename_form #old_name').html( get_name(id) );
    $('#rename_dialog').dialog('open');
}

/**
 * Átnevez egy űrlapot a párbeszédablakból kapott adatok alapján
 */
function rename_form()
{
    var form = $('#rename_form')[0];
    var id   = form.id.value;
    var name = form.new_name.value;

    $.post('/my_forms/rename',
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
    $('#new_dialog').dialog('open');
}

/**
 * Létrehoz egy új űrlapot a párbeszédablakból kapott adatok alapján
 */
function new_form()
{
    var form = $('#new_form')[0];
    var name = form.name.value;

    $.post(base_url+'my_forms/create',
           {'name': name},
           function(resp) {
               add_row(resp, name);
               cache.forms[resp] = '<form></form>';
               $('#new_dialog').dialog('close').find('input[name=name]').val('');
           }
          );
}

/**
 * Megnyitja a törlés párbeszédablakot
 */
function remove_dialog(id)
{
    $('#remove_form')[0].id.value = id;
    $('#remove_dialog').dialog('open');
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


// Inicializálás
$(document).ready( function() {
                       $.ajaxSetup({cache: false});

                       // Táblázat-sorok kiemelése hovernél és kijelölése kattintásra
                       $('#forms tr:gt(0)').livequery(
                           function()
                           {
                               $(this).hover(
                                   function() { $(this).addClass('hovered');    },
                                   function() { $(this).removeClass('hovered'); }
                               ).click(
                                   function() { select($(this));    }
                               );
                           }
                       );

                       // Ez ideális esetben tisztán CSS lenne
                       $('#forms tr:gt(0):odd'). livequery( function() { $(this).removeClass('even').addClass('odd'); });
                       $('#forms tr:gt(0):even').livequery( function() { $(this).removeClass('odd').addClass('even'); });

                       // Művelet-ikonok alá hovernél kiírjuk a művelet nevét
                       $('#forms td.actions img').livequery(
                           function()
                           {
                               $(this).hover(
                                   function() { $(this).parent().find('div').html($(this).attr('alt')); },
                                   function() { $(this).parent().find('div').html('&nbsp;');            }
                               );
                           }
                       );

                       // Form-lista letöltése a szerverről
                       $.post(base_url+'my_forms/list_forms',
                              Array(),
                              function (forms)
                              {
                                  for (var i in forms) {
                                      add_row(forms[i]['id'], forms[i]['name']);
                                  }
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
                       $('#new_form').submit(function() { return check_rights('new_form()', true, false); });

                       // Átnevezés párbeszédablak inicializálása
                       var rename_buttons = {};
                       rename_buttons[cancel] = function() { $(this).dialog('close'); };
                       rename_buttons[rename] = function() { $(this).find('form').submit(); };
                       $('#rename_dialog').dialog({autoOpen: false,
                                                   modal   : true,
                                                   width   : 'auto',
                                                   buttons : rename_buttons
                       });
                       $('#rename_form').submit(function() { return check_rights('rename_form()', true, get_id($selected)); });

                       // Törlés párbeszédablak inicializálása
                       var remove_buttons = {};
                       remove_buttons[cancel] = function() { $(this).dialog('close'); };
                       remove_buttons[remove] = function() { $(this).find('form').submit(); };
                       $('#remove_dialog').dialog({autoOpen: false,
                                                   modal   : true,
                                                   buttons: remove_buttons
                       });
                       $('#remove_form').submit(function() { return check_rights('remove_form()', true, get_id($selected)); });

                       // Az előnézet-fülek inicializálása
                       $('#preview').tabs();
                       $('#preview').tabs('select', '#preview-form');
                   });
