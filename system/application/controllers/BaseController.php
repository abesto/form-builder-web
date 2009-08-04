<?php
/**
 * @file   BaseController.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sun Jul  5 08:08:38 2009
 *
 * @brief  Minden controllerben közös funkcionalitás
 *
 * Ha CI libraryként töltjük be, a CI_Loader megpróbál okos lenni.
 * Ennek az eredménye, hogy a model betöltése sikertelen - ezért includeoljuk.
 */


/**
 * Ennek az alosztályai lesznek azok a controllerek, amiknek meg kell
 * jeleníteniük a fő template-t.
 *
 * Hello world! kiiratása egy alosztályból:
 * @example ../../../doc/php/BaseController_example.php
 */
class BaseController extends Controller
{
    public function __construct()
    {
        session_start();

        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->slots = array();

        $this->load->model('User_model', 'user');
        $this->load->model('Forms_model', 'forms');

        $this->def_lang = 'hu';
    }


    /**
     * Átadja a $this->slots tömb adatait a skeleton view-nak és
     * megjeleníti vagy visszaadja a nézet eredményét.
     *
     * @param return Az eredményt visszaadja ha true, egyébként megjeleníti.
     *
     * @return A view eredménye vagy semmi
     */
    protected function render($return=false)
    {
        // A menü
        $menu = array('items' => $this->build_menu());
        $this->slots['menu'] = $this->load->view('menu', $menu, true);

        // Ha van az aktuális controllernek saját css fájlja, akkor azt átadjuk a skeletonnak
        $file = 'css/' . strtolower(get_class($this)) . '.css';
        if (file_exists($file))
            $this->slots['css'] = $file;

        // Ha van az aktuális controllernek saját js fájlja, akkor azt átadjuk a skeletonnak
        $file = 'scripts/' . strtolower(get_class($this)) . '.js';
        if (file_exists($file))
            $this->slots['js'] = $file;

        return $this->load->view('skeleton', $this->slots, $return);
    }


    /**
     * Ha nincs bejelentkezve a felhasználó, átküldi a login oldalra
     *
     * @param redirect A sikeres bejelentkezés után erre a lapra küldjük a felhasználót
     */
    protected function check_login($redirect='/my_forms')
    {
        $user = $this->user->get_user(false);

        if ($user === false) {
            $_SESSION['set']['redirect'] = $redirect;
            redirect('/login');
        }
    }


    /**
     * AJAJ függvény
     *
     * @return 'OK'                : a felhasználó be van jelentkezve, és az űrlap az övé
     *         'FORM_NOT_FOUND'    : a felhasználó be van jelentkezve, de az űrlap nem található, vagy nem módosíthatja
     *         'NOT_LOGGED_IN'     : a felhasználó nincs bejelentkezve
     */
    public function remote_check_rights()
    {
        $write = $_POST['write'];
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if (($write == true) && ($this->user->get_user(false) === false)) {
            echo 'NOT_LOGGED_IN';
            return;
        } else {
            if ($id !== false) {
                // Szerkesztésre nyitáskor ha nem a felhasználó űrlapja;
                // Olvasásra nyitáskor ha nem a felhasználóé és nem is publikus
                if ((($write == true) && ($this->forms->get_form($id) === false))
                ||  (($write == false) && (($this->forms->get_form($id) === false) || ($this->forms->get_form_public($id) === false)))) {
                    echo 'FORM_NOT_FOUND';
                    return;
                }
            }
        }
        echo 'OK';
    }


    /**
     * Összeállítja a menü elemeit annak megfelelően, hogy a felhasználó be van-e jelentkezve
     *
     * @return A megjelenítendő menü-elemek [link => felirat] tömbje
     *         Ha a felhasználó be van jelentkezve, akkor ['login'] == false
     *           Ilyenkor ['welcome'] a megjelenítendő szöveg, %s-el a név helyén
     *           ['user'] a felhasználó neve
     *         Egyébként ['login'] a belépő/regisztráló oldalra mutató linkhez megjelenítendő szöveg
     */
    protected function build_menu()
    {
        $this->load_lang('menu');
        $trans = $this->lang->line('menu');
        $items = array('home'   => $trans['home'],
                       'manual' => $trans['manual']);

        $user = $this->user->get_user(false);
        if ($user == false)
            $items['login'] = $trans['login'];
        else {
            $items['my_forms'] = $trans['my_forms'];
            $items['logout']  = $trans['logout'] . ' (' . $user->name . ')';
        }
        return $items;
    }

    /**
     * Betölti a megfelelő nyelvből a kért fájlt.
     *
     * A megfelelő nyelv meghatározása:
     * Ha kaptunk értéket az URLből, akkor mindenképpen azt használjuk és mentjük session cookie-ba
     * Egyébként a session cookie-ban tárolt értéket használjuk
     * Ha ilyen nincs, akkor a konstruktorban beállított nyelvet használjuk
     *
     * @param file A betöltendő nyelvi fájl neve
     * @param in Az URLből kapott nyelv
     */
    protected function load_lang($file, $in=null)
    {
        if ($in !== null)
            $_SESSION['lang'] = $in;
        else if (!isset($_SESSION['lang']))
            $_SESSION['lang'] = $this->def_lang;

        $this->lang->load($file, $_SESSION['lang']);
    }
}
?>
