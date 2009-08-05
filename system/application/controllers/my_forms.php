<?php
/**
 * @file   my_forms.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 08:24:18 2009
 *
 * @brief  A felhasználó saját űrlapjaival kapcsolatos műveletek
 */

include('BaseController.php');
class My_forms extends BaseController {
	function __construct()
	{
		parent::__construct();
        $this->slots['js'] = 'scripts/forms_table.js';
	}

    /**
     * Az űrlapok listáját megjelenítő lap
     * A listát AJAJ-al mutatjuk meg, ezért itt nem adjuk át
     *
     * @param lang Nyelv
     */
	function index($lang=null)
	{
        $this->check_login();
        $this->load_lang('forms', $lang);

        $js_labels = $this->lang->line('js');
        foreach ($js_labels as $key => $text)
            $js_labels[$key] = str_replace(' ', '&nbsp;', $text);

        $data = array('owner'    => true,
                      'js'       => $js_labels,
                      'php'      => $this->lang->line('php'),
                      'base_url' => base_url()
                      );

        $this->slots['content'] = $this->load->view('form_table', $data, true);
        $this->render();
	}

    /**
     * Megnyitja az űrlapot szerkesztésre
     *
     * @param id Az űrlap azonosítója
     */
    function edit($id)
    {
        $this->check_login();
        $form = $this->forms->get_form($id);
        $this->load->view('builder', $form);
    }


    /////////
    /// AJAJ kérések miatt hívott függvények
    /// Paramétereket POST kérésből kapnak, visszatérési értéküket kiírják
    ///
    /// Nem használhatjuk a $this->check_login függvényt, mivel az átirányít
    /// a bejelentkező oldalra. Ezzel adatot veszíthetünk. A felhasználó ellenőrzését
    /// és esetleges újra-beléptetését AJAJ végzi
    /////////

    /**
     * Új űrlapot hoz létre
     *
     * @param name Az űrlap neve
     *
     * @return Az új űrlap azonosítója
     */
    function create()
    {
        $this->check_login(false);

        $name = $_POST['name'];

        $id = $this->forms->create_form($name, '<form></form>', false);
        echo $id;
    }


    /**
     * @return A felhasználó űrlapjainak listája JSON-ban
     */
    function list_forms()
    {
        $this->check_login(false);

        echo json_encode($this->forms->get_form_list());
    }

    /**
     * @return Egy űrlap tartalma
     */
    function load()
    {
        $this->check_login(false);

        $id = $_POST['id'];
        $form = $this->forms->get_form($id);
        echo $form->html;
    }

    /**
     * Menti az űrlap tartalmát
     *
     * @param id A mentendő űrlap azonosítója
     * @param name A mentendő űrlap neve
     * @param html A mentendő űrlap tartalma
     *
     * @return 'false' ha hiba történt, egyébként a mentett űrlap azonosítója
     *         Nem feltétlenül azonos az id paraméterrel (ha a mentés előtt törölték az űrlapot, új azonosítót kap)
     */
    function save()
    {
        $this->check_login(false);

        $id   = $_POST['id'];
        $name = $_POST['name'];
        $html = $_POST['html'];

        $res = $this->forms->save_form($id, $name, '<form>'.$html.'</form>');
        if ($res == false)
            echo 'false';
        else
            echo $res;
    }

    /**
     * Átnevezi az űrlapot
     *
     * @param id A mentendő űrlap azonosítója
     * @param name A mentendő űrlap neve
     *
     * @return 'true' ha sikeres volt a mentés, különben 'false'
     */
    function rename()
    {
        $this->check_login(false);

        $id   = $_POST['id'];
        $name = $_POST['name'];

        echo $this->forms->rename_form($id, $name) ?
            'true' : 'false';
    }

    /**
     * Törli az űrlapot
     * Itt a törlés megerősítése már megtörtént
     *
     * @param id A törlendő űrlap
     *
     * @return 'true' ha sikeres volt a mentés, különben 'false'
     */
    function remove()
    {
        $this->check_login(false);

        $id = $_POST['id'];
        echo $this->forms->remove_form($id) ?
            'true' : 'false';
    }
}
