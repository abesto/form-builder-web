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
        foreach ($this->fields as $field)
            if (!isset($_SESSION[$field])) $_SESSION[$field] = '';

        $this->load_lang('login', $lang);
        $slots = $this->lang->line('login');
        $slots['redirect'] = '/profile';

        foreach ($this->fields as $field)
            $slots[$field.'_val'] = $_SESSION[$field];

        $this->slots['content'] = $this->load->view('login', $slots, true);
        $this->render();
	}

    /**
     * Ellenőrzi a regisztráláshoz megadott adatokat
     * Ha volt hiba, visszaküldi a felhasználót az űrlaphoz
     *   A kapott adatokat visszaadja az űrlapnak
     * Ha nem volt hiba, átküldi a felhasználót a "Regisztráció sikeres, email ment" lapra
     */
    function register()
    {
        foreach ($this->fields as $field)
            $_SESSION[$field] = $_POST[$field];

        foreach ($this->fields as $field) {
            $fun = 'check_'.$field;
            if (sizeof($this->$fun($_POST[$field])) > 0)
                redirect('/login');
        }
        /*
        $_SESSION['set'] = array('user'    => $this->input->post('user'),
                                 'url'     => $this->input->post('url'),
                                 'message' => $this->input->post('message'),
                                 'title'   => $this->input->post('post'));


        if ($this->form_validation->run()) {
            $this->blog_model->post_comment($_SESSION['set']);
            $_SESSION['set']['message'] = '';
        }
        $_SESSION['errors'] = validation_errors();
        redirect($this->input->post('post').'#post-comment');*/
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

        return $this->check(
                            array(
                                  "'$email' == ''" => array('required'),
                                  "preg_match(\"$regex\", \"$email\") == 0" => array('email'),
                                  "$length > $max_length" => array('long', 100)
                                  )
                            );
    }
}
