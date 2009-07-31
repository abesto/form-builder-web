/**
 * @file   login.js
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Mon Jul 24 16:23:16 2009
 *
 * @fileOverview A regisztrációs form hibakezelésének kliensoldali része
 */


/**
 * A kapott adatok alapján elvégezteti a szerverrel az ellenőrzést
 * és megjeleníti az eredményét, ha nem üres a mező
 *
 * @param data A küldendő adat
 * @param $input Az input mező, ami után ki kell írni az eredményt
 * @param initial Kezdeti automatikus ellenőrzés? Ha igen, akkor az üres mezők után is írunk hibát
 */
function check_ajax(data, $input, initial)
{
    $.post('/login/check_remote',
           data,
           function (response) {
               if (response == true) {
                   $input.after('<img src="/img/valid.jpg" alt="valid" />');
               } else if ((data['value'].length > 0) || (initial == true)) {
                   if ((data['value'] != '') || (errors == true))
                       $input.after('<div class="error">'+response+'</div>').
                              after('<img src="/img/error.jpg" alt="error" />');
               }
           },
          'json'
          );
}

/**
 * Leellenőrizteti a szerverrel az adott mező értékének helyességét
 *
 * @param name Az ellenőrzendő mező neve
 * @param initial ld. {@link check_ajax}
 */
function check(name, initial)
{
    var $input = $('#register').find('input[name='+name+']');
    var val = $input.val();
    var data = { type: name, value: val };

    $input.next().remove();
    $input.next().remove();

    check_ajax(data, $input, initial);
}

/**
 * A jelszavak egyezését ellenőrzi
 *
 * @param initial ld. {@link check_ajax}
 */
function check_pass_match(initial)
{
    var $input1 = $('#register').find('input[name=pass]');
    var $input2 = $('#register').find('input[name=pass_match]');

    var pass1 = $input1.val();
    var pass2 = $input2.val();

    var data = { type: 'pass_match', pass: pass1, value: pass2 };

    $input2.next().remove();
    $input2.next().remove();

    check_ajax(data, $input2, initial);
}

$(document).ready( function() {
    // onchange eventre ellenőrizzük a mezőket
    with ($('#register')) {
        find('input[name=user]').change(function() {
                                            check('user');
                                        });
        find('input[name=pass]').change(function() {
                                            check('pass');
                                            check_pass_match();
                                        });
        find('input[name=pass_match]').change(function() {
                                                check_pass_match();
                                            });
        find('input[name=email]').change(function() {
                                             check('email');
                                         });
    }

    // Ez ideális esetben CSS lenne, de a böngészők még nem támogatják a
    // :first-child selectort
    $('#login td:first-child, #register td:first-child').css({'vertical-align': 'top',
                                                              'text-align'    : 'right'});

    // Ha a szerver visszaküldött erre az oldalra, mert hibás formot küldtünk
    check('user', true);
    check('pass', true);
    check_pass_match(true);
    check('email', true);
});
