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

function save()
{
    var url_parts = location.href.split('/');
    var id = url_parts[url_parts.length - 1];

    var html = $('#form').html();
    $.post('/my_forms/save',
           {
               'id'   : id,
               'html' : html
           },
           function (resp) {
               $('#status').html('TRANSLATEME! vege');
           }
          );

    window.opener.cache.update(id, '<form>'+html+'</form>');
    dirty = false;
}

window.onbeforeunload = save_check;
function save_check()
{
    if (dirty == true) return 'die, biaaaatch';
    return null;
}
