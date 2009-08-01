/**
 * @file   forms_table.js
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 15:45:29 2009
 *
 * @fileOverview Az űrlapokat megjelenítő táblázathoz tartozó JS
 */

var cache = {
    forms: Array(),

    cache_form: function(id, html) { this.forms[id] = html; },

    preview: function(id)
    {
        if (this.forms[id] != undefined)
            $('#preview').html(this.forms[id]);
        else {
            $('#preview').html('Betöltés...')
                         .load('/my_forms/load',
                               {'id': id},
                               function (response) { cache.cache_form(id, response); }
                              );
        }
    }
};

function preview($row)
{
    $row.addClass('hovered');
    cache.preview($row.children(0).html());
}

function unpreview($row)
{
    $row.removeClass('hovered');
}

$(document).ready( function() {
                       $('#forms tr:gt(0)').hover(
                           function() { preview($(this)); },
                           function() { unpreview($(this)); }
                       );
                       $('#forms tr:gt(0):odd').addClass('odd');
                       $('#forms tr:gt(0):even').addClass('even');
                   });
