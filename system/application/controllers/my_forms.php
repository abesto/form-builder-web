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
        $this->load->model('Forms_model', 'forms');
        $this->slots['js'] = 'scripts/forms_table.js';
	}

    /**
     * Az űrlapok listázása
     *
     * @param lang Nyelv
     */
	function index($lang=null)
	{
        $this->check_login('/my_forms/'.$lang);

        $data = array();
        $data['forms'] = $this->forms->get_form_list();
        $data['owner'] = true;

        $this->slots['content'] = $this->load->view('form_table', $data, true);
        $this->render();
	}

    /**
     * Új formot ad az adatbázishoz és megnyitja szerkesztésre
     *
     * @param name Az űrlap neve
     */
    function create($name)
    {
        $this->check_login();

        $id = $this->forms->create_form($name, '<form></form>', false);
        $this->edit($id);
    }

    /**
     * Megnyitja az űrlapot szerkesztésre
     *
     * @param id Az űrlap azonosítója
     */
    function edit($id)
    {
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

    function load()
    {
        $id = $_POST['id'];

        $form = $this->forms->get_form($id);

        echo '<h3>'.$form->name.'</h3>'.$form->html;
    }

    /**
     * Menti az űrlap tartalmát
     *
     * @param id A mentendő űrlap azonosítója
     * @param html A mentendő űrlap tartalma
     *
     * @return 'true' ha sikeres volt a mentés, különben 'false'
     */
    function save()
    {
        $id   = $_POST['id'];
        $html = $_POST['html'];

        echo $this->forms->save_form($id, $html) ?
            'true' : 'false';
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
        $id   = $_POST['id'];
        $name = $_POST['name'];

        echo $this->forms->rename($id, $name) ?
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
        $id = $_POST['id'];
        echo $this->forms->remove_form($id) ?
            'true' : 'false';
    }
}
