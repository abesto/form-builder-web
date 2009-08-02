/**
 * @file   forms_table.js
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 15:45:29 2009
 *
 * @fileOverview Az űrlapokat megjelenítő táblázathoz tartozó JS
 */

var $selected = null;

/**
 * Az előnézetek letöltésére és gyorsítótárazása
 */
var cache = {
    forms: Array(),

    cache_form: function(id, html) { this.forms[id] = html; },

    /**
     * Űrlap előnézetének megjelenítése
     *
     * @param id Az űrlap azonosítója
     */
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
    },

    update: function(id, html)
    {
        this.forms[id] = html;
        if (($selected != null) && ($selected.attr('id') == id))
            this.preview(id);
    }
};

function open_editor(id)
{
    var win = window.open ("/builder/"+id,"builder_app","status=0,toolbar=0,location=0,menubar=0,directories=0,scrollbars=0");
}

function preview($row)
{
    $row.addClass('hovered');
    if (($selected == null) || ($selected == $row))
        cache.preview($row.attr('id'));
}

function unpreview($row)
{
    $row.removeClass('hovered');
}

function select($row)
{
    if ($selected != null)
        $selected.removeClass('selected');

    $selected = $row;
    $selected.addClass('selected');
    preview($selected);
}

$(document).ready( function() {
                       $('#forms tr:gt(0)').hover(
                           function() { preview($(this));   },
                           function() { unpreview($(this)); }
                       ).click(
                           function() { select($(this));    }
                       );
                       $('#forms tr:gt(0):odd').addClass('odd');
                       $('#forms tr:gt(0):even').addClass('even');
                   });
