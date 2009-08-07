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
 * Ha nincs bejelentkezett felhasználó, megnyitja a bejelentkezés párbeszédablakot
 * és beállítja a bejelentkezés után hívandó függvényt a callback paraméterre.
 *
 * Ha létezik az adatbázisban űrlap a szerkesztett űrlap azonosítójával
 * és van rá írásjoga a bejelentkezett felhasználónak, futtatja a callback-et.
 *
 * Ha nem létezik az űrlap, vagy nincs rá a bejelnetkezett felhasználónak írásjoga,
 * egy új azonosítót kap a szervertől. Átirányítja a böngészőt az új űrlap szerkesztő-oldalára.
 */
function update_or_create(callback)
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
                   $.data($('#login_form').get(0), 'callback', callback);

               } else if ((resp == 'FORM_NOT_FOUND') || (resp == 'OK')) {

                   callback.call();

               } else
                   throw('Unknown response from server');
           },
           'text'
    );

}

/**
 * Elmenti a szerkesztett űrlapot a szerverre
 *
 * Ha az adott azonosítóra a felhasználónak nincs írásjoga,
 * egy új azonosítót kap, és átküldi oda a böngészőt.
 */
function save()
{
    update_or_create(function()
        {
            var html = $('#form').html();
            $.post(
                base_url+'my_forms/save',
                {
                    'id'   : form_id,
                    'name' : get_title(),
                    'html' : html
                },
                function (resp)
                {
                    window.opener.cache.update(resp, get_title(), '<form>'+html+'</form>');
                    if (form_id != resp) {
                        if (is_public)
                            window.opener.add_row_public(resp, get_title(), user, true, true);
                        else
                            window.opener.add_row(resp, get_title(), false);
                        window.opener.select_id(resp);
                        window.location = base_url + 'builder/' + resp;
                    }
                    status.set('saved');
                },
                'text'
            );
            dirty = false;
        });
}


/**
 * Létrehoz egy új űrlapot a szerveren az aktuális tartalommal
 * és átirányítja oda a böngészőt
 *
 * Előfeltétel: legyen a felhasználó bejelentkezve
 */
function save_as(form)
{
    var name = form.new_name.value;
    update_or_create(function()
    {
        var html = $('#form').html();
        $.post(
            base_url+'my_forms/create',
            {
                'name' : name,
                'html' : html
            },
            function (resp)
            {
                window.opener.add_row(resp, name);
                window.opener.cache.update(resp, name, '<form>'+html+'</form>');
                window.opener.select_id(resp);
                window.location = base_url + 'builder/' + resp;
            },
            'text'
        );
        dirty = false;
    });
}


/**
 * Ha szerkesztés közben megszűnik a bejelentkezés, a bejelentkező
 * párbeszédablak ezzel a függvénnyel küldi a bejelentkezést
 *
 * Ha a bejelentkezés sikeres, a {@link update_or_create} függvény
 * által beállított, annak a callback paraméterben átadott függvény
 * lefut.
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

                   $.removeData(form);
               } else {
                   $('#login_error').html(resp);
               }
           },
           'json'
          );
}

window.onbeforeunload = save_check;
function save_check()
{
    if (dirty == true)
        return trans.leave_confirm;
    return null;
}
