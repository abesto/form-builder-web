/**
 * @file   props.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Wed Apr  1 16:50:19 2009
 *
 * @fileOverview Az egyes form elemekhez tartozó tulajdonságok
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
 * Minden tulajdonság-változáskor meghívjuk
 *
 * Beállítja a dirty flaget
 * Undo/redo rendszernél itt adnánk hozzá az előzményekhez
 */
function handle_prop_change()
{
    dirty = true;
}

/**
 * @class Egy tulajdonságot leíró objektum
 *
 * @param name A tulajdonság neve; fordításnál használjuk
 * @param dom A tulajdonság beállításaira szolgáló DOM
 */
function Prop(name, dom)
{
    this.name = name;
    this.dom = dom;
    /** Az utolsó megjelenített Prop, aminél .focus == true megkapja a fókuszt */
    this.focus = false;

    /** @return A megjelenítendő elem JQuery wrapperben */
    this.render = function() { return $(this.dom); };
}


/**
 * @class Prop objektumok gyűjteménye
 */
function PropsCollection()
{
    /** A tulajdonság-csoportok neveinek listája */
    this.groups = Array();
    /** A tulajdonság-csoportok [csoportnév: [{@link Prop}1, {@link Prop}2, ...], ...] asszociatív tömbje */
    this.props = new Object();

    /**
     * Új tulajdonság-csoport létrehozása
     *
     * @param name A tulajdonság-csoport neve
     */
    this.add_group = function(name) {
        this.groups.push(name);
        this.props[name] = Array();
    };

    /**
     * Tulajdonság hozzáadása egy csoporthoz
     *
     * @param group A tulajdonság-csoport, amihez hozzáadjuk a tulajdonságot
     * @param prop Prop objektum
     * @param focus Nem kötelező, ld. {@link Prop#focus}
     */
    this.add_prop = function(group, prop, focus)
    {
        if (focus) prop.focus = true;
        this.props[group].push(prop);
    };

    /** @return A tulajdonság-csoportok listája */
    this.get_groups = function() { return this.groups; };
    /**
     * @param group Egy tulajdonság-csoport neve
     * @return A csoporthoz tartozó {@link Prop} tulajdonságok listája
     */
    this.get_props = function(group) { return this.props[group]; };

    /**
     * Az adott tulajdonság-csoporthoz tartozó tualjdonságok listájának felülírása
     * @param name A tulajdonság-csoport
     * @param props Az új {@link Prop}-lista
     */
    this.set_group = function(name, props) { this.props[name] = props; };
}


/**
 * @namespace Az elemek beállítható tulajdonságaihoz tartozó input elemeket létrehozó függvények<br />
 */
const PROPS = {
    /**
     * td elem típusa
     * @return Radio inputokat tartalmazó span; lehetséges értékei: text (szöveg) és input (beviteli mező)
     */
    td_type: function()
    {
        var $span = $('<span>');
        var $selected = check_selected_type('td');
        var types = Array('text', 'input|select');
        var $child = $selected.children(':first-child');
        var child_type = '';
        if ($child.size() > 0) child_type = $child.get(0).nodeName.toLowerCase().replace('#', '');
        else child_type = 'text';
        for (var i = 0; i < types.length; i++) {
            var type = types[i];
            //var radio = $('<input type="radio" name="td_type" />').attr({'value': types[i], 'onchange': 'set_td_type(this.value)', 'id': type});
            var $radio = $('<input type="radio" name="td_type" id="td_type_'+type+'" />').value(types[i]).change(
                function()
                {
                    set_td_type($(this).value());
                    handle_prop_change();
                }
            );
            var label = trans.td_types[types[i]];
            if (types[i].match(child_type)) $radio.attr('checked', 'true');
            $span.append($radio).append(
                $('<label>').attr('for', 'td_type_'+type).append(label)
            );
        }
        return $span;
    },

    /**
     * Az input elem típusa
     *
     * @return Select elem
     */
    input_type: function()
    {
        var $select = $('<select>').attr('name', 'input_type');
        var $selected = check_selected_type('td');
        var types = Array('text', 'password', 'button', 'checkbox', 'radio', 'file', 'select');
        var type = get_input_type($selected);
        for (var i = 0; i < types.length; i++) {
            var $option = $('<option>').value(types[i])
                                       .append(trans.td_types[types[i]]);
            if (type == types[i]) $option.attr('selected', 'true');
            $select.append($option);
        }
        $select.change(function()
                      {
                          set_input_type(this.value);
                          handle_prop_change();
                      });
        return $select;
    },

    /**
     * Beviteli elem name tulajdonságához kapcsolt mező
     *
     * @return Input elem
     */
    name: function() {
        var $input = make_input('name', 'text');
        $input.change(
            function() {
                set_name($('.selected'), this.value);
                handle_prop_change();
            } );
        $input.value($('.selected').firstChild().attr('name'));
        return $input;
    },

    /**
     * Fieldset elem legend gyermekének szövegéhez kapcsolt mező
     *
     * @return Input elem
     */
    legend: function() {
        var input = make_input('legend', 'text');
        input.change(
            function() {
                $('.selected').children('legend').html(this.value);
                handle_prop_change();
            }
        );
        input.value($('.selected').children('legend').html());
        return input;
    },

    /**
     * A select elem egy option-jéhez tartozó text mező és eltávolító gomb
     *
     * @param num Az option 0-alapú sorszáma
     * @param text A kezdeti szövegtartalom
     * @return Div elem
     */
    option: function(num, text) {
        var $option = $('<div>').append(
            // Szövegmező
            make_input('option_'+num, 'text').value(text).change(
                function () {
                    with ($('.selected select')) {
                        if (children('option').size() <= num)
                            append($('<option>').attr('builder_id', num).html(this.value));
                        else
                            children('option[builder_id='+num+']').html(this.value);
                    }
                    handle_prop_change();
                    update_props('td');
                })
        );
        // Eltávolító gomb, ha ez nem az utolsó mező
        if (text != '')
            $option.append(make_input('remove_'+num, 'button').value('-').click(
                               function() {
                                   if ($('.selected option').size() == 1)
                                       $('.selected option').html('');
                                   else {
                                       $('.selected option[builder_id='+num+']').remove();
                                       // builder_id-kat frissítjük
                                       var $options = $('.selected option');
                                       for (var i = 0; i < $options.size(); i++) {
                                           $options.get(i).setAttribute('builder_id', i);
                                       }
                                   }
                                   handle_prop_change();
                                   update_props('td');
                               })
                          );

        return $option;
    }
};

/**
 * @namespace Az egyes elemekhez tartozó tulajdonságokat visszaadó függvények
 * az elem tartalmát figyelembe véve
 *
 * @return PropsCollection
 */
const props = {
    /** @return PropsCollection */
    td: function ()
    {
        var $selected = check_selected_type('td');
        var ret = new PropsCollection();
        ret.add_group('td');
        ret.add_prop('td', new Prop('td_type', PROPS.td_type()));

        var type = get_td_type($selected);
        var input_type = get_input_type($selected);
        if (type == 'input' || type == 'select') {
            ret.add_group('input');
            ret.add_prop('input', new Prop('name', PROPS.name()));
            ret.add_prop('input', new Prop('input_type', PROPS.input_type()));
        }

        var $text = make_input('td_text', 'text');
        $text.value(get_td_text($selected));
        $text.change(function () {
                         set_td_text($selected, this.value);
                         handle_prop_change();
                     });
        if (input_type == 'file')
            $text.attr('disabled', true).value(trans.no_file_label);

        if (type != 'select')
            ret.add_prop('td', new Prop('td_text', $text), true);
        else {
            ret.add_group('options');
            var $options = $selected.firstChild().children();
            $options.each(
                function(i) {
                    ret.add_prop('options', new Prop('option', PROPS.option(i, $(this).html())), true);
                }
            );
            if ($options[0].innerHTML != '')
                ret.add_prop('options', new Prop('option', PROPS.option($options.size(), '')), true);
        }

        return ret;
    },

    /**
     * Fieldsetnél be lehet állítani a legend feliratát
     * @return PropsCollection
     */
    fieldset: function()
    {
        var selected = check_selected_type('fieldset');
        var text = selected.firstChild.innerHTML;
        var ret = new PropsCollection();
        ret.add_group('fieldset');
        ret.add_prop('fieldset', new Prop('legend', PROPS.legend()), true);
        return ret;
    }
};
