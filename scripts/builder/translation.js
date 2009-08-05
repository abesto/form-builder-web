/**
 * @file   translation.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Wed Apr  1 16:50:19 2009
 *
 * @fileOverview UI lefordított sztringjei
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

/**
 * I18n sztringeket tároló objektum
 */
const TRANS = {
    list: Array('en', 'hu'), // Ismert nyelvek listája

    /* Angol */
    en: {
        name: 'English',
        code: 'en',
        wrong_node: function(name) {return 'Selected node is not a(n) ' + name;},
        no_actions: function(name) {return 'No actions for node ' + name;},
        no_props:   function(name) {return 'No properties for node ' + name;},
        actions_label : "Actions",
        props         : "Properties",
        no_file_label : "No label",
        user          : 'Username',
        pass          : 'Password',
        login         : 'Login',
        cancel        : 'Cancel',
        prop_groups: {
            input    : "Input field",
            td       : "Table cell",
            fieldset : "Fieldset",
            form     : "Form",
            options  : "Values"
        },
        prop_names: {
            td_type   : "Content type:",
            input_type: "Input field type:",
            td_text   : "Text:",
            label     : "Label:",
            name      : "Name:",
            legend    : "Legend:",
            form_name : "Form name:",
            option    : ""
        },
        td_types: {
            text     : "Text",
            'input|select' : "Input",
            button   : "Button",
            checkbox : "Checkbox",
            radio    : "Radio button",
            file     : "File",
            select   : "Drop-down menu"
        },
        actions: {
            'create_cell(LEFT)' : "New cell (left)",
            'create_cell(RIGHT)': "New cell (right)",
            'create_row(UP)'    : "New row (above)",
            'create_row(DOWN)'  : "New row (below)",
            'remove_cell()'     : "Remove cell",
            'merge_cells(LEFT)' : "Merge cells (left)",
            'merge_cells(RIGHT)': "Merge cells (right)",
            'split_cell(LEFT)'  : "Split cell (from the left)",
            'split_cell(RIGHT)' : "Split cell (from the right)",
            'add_table()'       : "Add table",
            'add_fieldset()'    : "Add fieldset",
            'remove_table()'    : "Remove table",
            'remove_fieldset()' : "Remove fieldset"
        }
    },

    /* Magyar */
   hu: {
        name: 'Magyar',
        code: 'hu',
        wrong_node: function(name) {return 'A kiválasztott elem nem ' + name;},
        no_actions: function(name) {return 'Nincs művelet a(z) ' + name + ' elemhez';},
        no_props:   function(name) {return 'A(z) ' + name + ' elemnek nincsenek beállítható tulajdonságai';},
        actions_label : "Műveletek",
        props         : "Tulajdonságok",
        no_file_label : "Nincs felirat",
        user          : 'Felhasználónév',
        pass          : 'Jelszó',
        login         : 'Bejelentkezés',
        cancel        : 'Mégsem',
        prop_groups: {
            td       : "Cella",
            input    : "Beviteli mező",
            fieldset : "Fieldset",
            form     : "Űrlap",
            options  : "Értékek"
        },
        prop_names: {
            td_type    : "Cella típusa:",
            input_type : "Beviteli mező típusa:",
            td_text    : "Szöveg:",
            label      : "Felirat:",
            name       : "Mezőnév:",
            legend     : "Felirat:",
            form_name  : "A form neve:",
            option     : ""
        },
        td_types: {
            'input|select' : "Beviteli mező",
            text     : "Szöveg",
            button   : "Gomb",
            checkbox : "Checkbox",
            radio    : "Radio",
            file     : "Fájl",
            select   : "Lenyíló menü"
        },
        actions: {
            'create_cell(LEFT)' : "Új cella (balra)",
            'create_cell(RIGHT)': "Új cella (jobbra)",
            'create_row(UP)'    : "Új sor (fel)",
            'create_row(DOWN)'  : "Új sor (le)",
            'remove_cell()'     : "Cella törlése",
            'merge_cells(LEFT)' : "Cellák egyesítése (balra)",
            'merge_cells(RIGHT)': "Cellák egyesítése (jobbra)",
            'split_cell(LEFT)'  : "Cella felosztása (balról)",
            'split_cell(RIGHT)' : "Cella felosztása (jobbról)",
            'add_table()'       : "Új táblázat",
            'add_fieldset()'    : "Új fieldset",
            'remove_table()'    : "Táblázat eltávolítása",
            'remove_fieldset()' : "Fieldset eltávolítása"
        }
    }
};
