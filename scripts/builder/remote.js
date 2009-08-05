/**
 * @file   remote.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Sun Aug  2 14:50:02 2009
 *
 * @fileOverview Kommunikáció a szerverrel
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

var dirty = false;

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
function save()
{
    // id: views/builder.php
    var data = {'id':    form_id,
                'write': true
               };

    $.post(base_url+'my_forms/remote_check_rights',
           data,
           function(resp) {
               if (resp == 'NOT_LOGGED_IN') {
                   $('#login_dialog').dialog('open');
               } else if ((resp == 'FORM_NOT_FOUND')  // Létre fog jönni új azonosítóval
                 || (resp == 'OK')) {
                     var html = $('#form').html();
                     $.post(base_url+'my_forms/save',
                            {
                              'id'   : form_id,
                              'name' : get_title(),
                              'html' : html
                            },
                            function (resp) {
                                form_id = resp;
                                status.set('saved');
                                window.opener.cache.update(form_id, get_title(), '<form>'+html+'</form>');
                            },
                            'text'
                           );
                     dirty = false;
               } else
                   throw('Unknown response from server');
           },
           'text'
    );
    return false;
}

/**
 * Ha szerkesztés közben megszűnik a bejelentkezés, a bejelentkező
 * párbeszédablak ezzel a függvénnyel küldi a bejelentkezést
 */
function login()
{
    var form = $('#login_form')[0];
    var data = {
        user: form.user.value,
        pass: form.pass.value,
        lang: trans.code
    };

    $.post(base_url+'login/ajaj_do_login',
           data,
           function(resp)
           {
               if (resp == true) {
                   $('#login_dialog').dialog('close');
                   save();
               } else {
                   $('#login_error').html(resp);
               }
           },
           'json'
          );
    return false;
}

window.onbeforeunload = save_check;
function save_check()
{
    if (dirty == true)
        return trans.leave_confirm;
    return null;
}
