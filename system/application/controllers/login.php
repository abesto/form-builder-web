<?php
/**
 * @file   login.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Mon Jul 20 10:07:13 2009
 *
 * @brief  Regisztráció, bejelentkezés és a formok hibakezelése
 *
 * A hibakezelés kliensoldalon AJAX kérésekkel történik, ezért az ellenőrzéseket
 * nem bízhatjuk a CodeIgniter beépített form_validation paramétereire.
 */

include('BaseController.php');
class Login extends BaseController {

	function __construct()
	{
		parent::__construct();
        $this->slots['js'] = 'scripts/login.js';
        $this->fields = array('user', 'pass', 'pass_match', 'email');
	}

    /**
     * Megjeleníti a bejelentkező és a regisztráló űrlapot
     *
     * @param lang Nyelv
     */
	function index($lang=null)
	{
        if (!isset($_SESSION['set']))
            $_SESSION['set'] = array();
        foreach ($this->fields as $field)
            if (!isset($_SESSION['set'][$field]))
                $_SESSION['set'][$field] = '';
        if (!isset($_SESSION['set']['login_failed']))
            $_SESSION['set']['login_failed'] = false;
        if (!isset($_SESSION['set']['reg_failed']))
            $_SESSION['set']['reg_failed'] = false;

        $this->load_lang('login', $lang);
        $slots = $this->lang->line('login');
        $slots['redirect'] = '/profile';

        foreach ($_SESSION['set'] as $key => $val)
            $slots[$key.'_val'] = $val;

        $this->slots['content'] = $this->load->view('login', $slots, true);
        $this->render();
	}


    /**
     * Elvégzi a bejelentkezési adatok ellenőrzését
     * Ha helyesek, a redirect hidden input által megadott oldlra küldi a felhasználót
     * Egyébként vissza a bejelentkezéshez
     */
    public function do_login($lang=null)
    {
        print_r($_SESSION);
        $_SESSION['set']['reg_failed'] = false;

        if (($_POST['user'] == '') && ($_POST['pass'] == '')) {
            $_SESSION['set']['login_failed'] = false;
            redirect('/login/'.$lang);
        } elseif ($this->user->login($_POST['user'], $_POST['pass']) == false) {
            $_SESSION['set']['login_failed'] = true;
            redirect('/login/'.$lang);
        } else {
            $_SESSION['set']['login_failed'] = false;
            redirect($_POST['redirect'].'/'.$lang);
        }
    }


    public function logout($lang=null)
    {
        $this->user->logout();
        redirect('/home/'.$lang);
    }


    /**
     * Ellenőrzi a regisztráláshoz megadott adatokat
     * Ha volt hiba, visszaküldi a felhasználót az űrlaphoz
     *   A kapott adatokat visszaadja az űrlapnak
     * Ha nem volt hiba, átküldi a felhasználót a profil lapra
     */
    function register($lang=null)
    {
        $_SESSION['set']['login_failed'] = false;

        foreach ($this->fields as $field) {
            $fun = 'check_'.$field;
            if (sizeof($this->$fun($_POST[$field])) > 0) {
                // Van hiba, betöltjük a session cookie-ba a kapott adatokat
                $_SESSION['set']['user']  = $_POST['user'];
                $_SESSION['set']['email'] = $_POST['email'];
                $_SESSION['set']['reg_failed']   = true;
                // És vissza a reg formra
                redirect('/login/'.$lang);
            }
        }

        // Ha idáig eljut, akkor az adatok helyesek
        unset($_SESSION['set']);

        $this->user->register($_POST['user'], $_POST['email'], $_POST['pass']);
        redirect($_POST['redirect'].'/'.$lang);
    }

    /**
     * Lefuttatja a kapott ellenőrzéseket, és visszaadja a hibaüzeneteket
     *
     * @param checks [${ellenőrző kód} => $üzenet] alakú tömb
     *               $üzenet: ld. {@link Login::parse_errors}
     *
     * @return Hibaüzenetek tömbje amit a lang fájlból kaptunk
     */
    private function check($checks)
    {
        $errors = array();

        foreach ($checks as $check => $error) {
            $fun = create_function('$controller', 'return ('.$check.');');
            if ($fun($this) === true)
                $errors[] = $error;
        }
        return $errors;
    }

    /**
     * A regisztrációs formról érkező ellenőrzés-kérés kezelése
     *
     * @return echo true ha a kapott adat jó, különben a hibaüzenet(ek <br />-el elválasztva)
     */
    function check_remote()
    {
        $this->load_lang('login');
        $error_msgs = $this->lang->line('errors');

        $type  = $_POST['type'];
        $value = $_POST['value'];

        $fun    = 'check_'.$type;
        $errors = $this->$fun($value);

        if (sizeof($errors) == 0)
            echo 'true';
        else {
            $msgs = array();
            /* Az összes hibaa kiírása
            foreach ($errors as $error) {
                $error[0] = $error_msgs[$error[0]];
                $msg = call_user_func_array('sprintf', $error);
                $msgs[] = $msg;
            }
            echo '"'.implode('<br />', $msgs).'"';
            */

            // Csak az első hibát írjuk ki
            $error = $errors[0];
            $error[0] = $error_msgs[$error[0]];
            echo '"'.call_user_func_array('sprintf', $error).'"';
        }
    }

    /**
     * Felhasználónév ellenőrzése
     *
     * 3 <= n <= 100 karakter hosszú
     * Az adatbázisban még ne legyen ilyen
     */
    function check_user($user)
    {
        $min_length = 3;
        $max_length = 100;

        $length = mb_strlen($user);
        $user = str_replace("'", "\\'", $user);

        $_SESSION['set']['user'] = $user;

        return $this->check(
                            array(
                                  "'$user' == ''" => array('required'),
                                  "mb_strpos('$user', ' ') !== false" => array('space'),
                                  "$length < $min_length" => array('short', 3),
                                  "$length > $max_length" => array('long', 100),
                                  "\$controller->user->not_available('$user')" => array('name_exists')
                                  )
                            );
    }

    /**
     * Jelszó ellenőrzése
     *
     * 5 <= n <= 100 karakter hossú
     */
    function check_pass($pass)
    {
        $min_length = 5;
        $max_length = 100;

        $length = mb_strlen($pass);

        $pass = str_replace("'", "\\'", $pass);

        return $this->check(
                            array(
                                  "'$pass' == ''" => array('required'),
                                  "mb_strpos('$pass', ' ') !== false" => array('space'),
                                  "$length < $min_length" => array('short', 5),
                                  "$length > $max_length" => array('long', 100)
                                  )
                            );
    }

    /**
     * Jelszómegerősítés ellenőrzése
     *
     * Ha pass2 nem üres, a jelszavak megegyeznek
     *
     * @param pass2 a "jelszó mégegyszer" mező; az eredeti a post tömbből jön
     */
    function check_pass_match($pass2)
    {
        $pass1 = $_POST['pass'];
        $length = mb_strlen($pass2);

        $pass1 = str_replace("'", "\\'", $pass1);
        $pass2 = str_replace("'", "\\'", $pass2);

        return $this->check(
                            array(
                                  "'$pass2' == ''" => array('required'),
                                  "'$pass1' != '$pass2'" => array('passes_dont_match'),
                                  )
                            );
    }

    /**
     * Email cím ellenőrzése
     */
    function check_email($email)
    {
        $max_length = 100;
        $length = mb_strlen($email);
        $email = str_replace("'", "\\'", $email);

        $regex = "/^a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/";
        $chars = '[a-zA-Z0-9!#$%&\'*]';
        $regex = "/^$chars+@($chars+.)+[a-zA-Z]{2,3}$/";

        $_SESSION['set']['email'] = $email;

        return $this->check(
                            array(
                                  "'$email' == ''" => array('required'),
                                  "preg_match(\"$regex\", \"$email\") == 0" => array('email'),
                                  "$length > $max_length" => array('long', 100),
                                  "\$controller->user->not_available('$email')" => array('email_exists')
                                  )
                            );
    }
}
