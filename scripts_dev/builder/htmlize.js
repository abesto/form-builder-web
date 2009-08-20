/**
 * @file   htmlize.js
 * @author Nagy Zoltán <abesto0@gmail.com>
 * @date   Fri Apr  1 16:23:52 2009
 *
 * @fileOverview Szép HTML létrehozása DOM objektumokból.
 *
 * Egy parser általában vagy nagyon bonyolult, vagy közepesen bonyolult és nagyon csúnya.
 * Ez a második.
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

/** A parser */
function htmlize(el, level)
{
  var name = el.nodeName.toLowerCase();
  // Már nem kapja meg a h1-t
  //if (name == 'h1') return '';
  var ret = '';

  if (name == 'tbody')
    level -= 1;

  if (name == "#text")
    ret += el.textContent;
  else {
    for (var i = 0; i < level; i++)
      ret += '  ';
    ret += "<" + name;
    var $el = $(el);
    var attrs = {'input': Array('type', 'name', 'id', 'checked'),  // Amiket át kell másolni, ha van
                 'td': Array('colspan'),
                 'label': Array('for')};
    if (attrs[name] != undefined)
      for (i = 0; i < attrs[name].length; i++) {
        var attr = attrs[name][i];
        if (($el.attr(attr) != undefined) && ($el.attr(attr) != ''))
          ret += ' ' + attr  + '="' + $el.attr(attr) + '"';
      }
    if ((name == 'input') && ($el.attr('type') != 'radio') && ($el.attr('type') != 'checkbox') && ($el.attr('value') != ""))
      ret += ' value="' + $el.attr('value') + '"';
    if (name == "input") {
      return ret + " />\n";
    }
    ret += ">";
  }

  if ((el.childNodes != undefined) && (el.childNodes.length > 0) && (el.childNodes[0].nodeName != '#text'))
    ret += "\n";

  for (var i = 0; i < el.childNodes.length; i++)
    ret += htmlize(el.childNodes[i], level+1);

  if (name != "#text") {
    if ((el.childNodes != undefined) && (el.childNodes.length > 0) && (el.childNodes[0].nodeName != '#text'))
      for (i = 0; i < level; i++)
        ret += '  ';
    ret += "</" + name + ">\n";
  }
  return ret;
}
