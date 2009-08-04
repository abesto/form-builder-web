<?php
/**
 * @file   forms_model.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sat Aug  1 08:25:49 2009
 *
 * @brief  Űrlapok kezelése adatbázisban
 *
 * A _public postfixű függvények a publikus űrlapokon dolgoznak.
 * A többi a bejelentkezett felhasználó saját űrlapjain dolgozik. Ezeknél előfeltétel, hogy legyen bejelentkezett felhasználó.
 */


class Forms_model extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model', 'user');
    }

    ///////////////////////////////////////////////////////////
    /// A bejelentkezett felhasználóra vonatkozó függvények ///
    ///////////////////////////////////////////////////////////

    /**
     * Létrehoz egy új űrlapot a bejelentkezett felhasználóhoz kapcsolva
     *
     * @param name Az űrlap neve
     * @param html Az űrlap tartalma
     * @param public Publikus?
     *
     * @return A létrehozott űrlap azonosítója
     */
    public function create_form($name, $html, $public)
    {
        $user = $this->user->get_user();

        $form = array('user_id' => $user->id,
                      'name'    => $name,
                      'html'    => $html,
                      'public'  => $public
                      );

        $this->db->insert('forms', $form);
        return $this->db->insert_id();
    }


    /**
     * @return A bejelentkezett felhasználó űrlapjait leíró objektumok tömbje
     */
    function get_form_list()
    {
        $user = $this->user->get_user();
        $where = array('user_id' => $user->id);
        $select = array('id', 'name', 'public');

        return $this->db->select($select)->from('forms')->where($where)->get()->result();
    }


    /**
     * Adott azonosítójú űrlap lekérdezése
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     *
     * @return Ha nincs ilyen űrlap, akkor false; egyébként az űrlapot
     *         leíró objektum
     */
    function get_form($id)
    {
        $user = $this->user->get_user();
        $where = array('user_id' => $user->id,
                       'id'      => $id
                       );

        $result = $this->db->from('forms')->where($where)->get();
        if ($result->num_rows() == 0)
            return false;
        return $result->row();
    }


    /**
     * Adott azonosítójú űrlap átnevezése
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     * @param name Az úrlap új neve
     *
     * @return Sikeres volt az átnevezés? (true vagy false)
     */
    function rename_form($id, $name)
    {
        $user = $this->user->get_user();

        $update = array('name' => $name);
        $where  = array('id'      => $id,
                        'user_id' => $user->id);

        $this->db->where($where)->update('forms', $update);
        return ($this->db->affected_rows() == 1);
    }

    /**
     * Az adott azonosítójő űrlap törlése
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     *
     * @return Sikeres volt a törlés? (true vagy false)
     */
    function remove_form($id)
    {
        $user = $this->user->get_user();
        $where = array('id'      => $id,
                       'user_id' => $user->id);

        $this->db->where($where)->delete('forms');

        return ($this->db->affected_rows() == 1);
    }


    /**
     * Az adott azonosítójú űrlap tartalmának felülírása
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     * @param html Az új tartalom
     *
     * @return True ha sikeres volt a mentés; különben false
     */
    function save_form($id, $html)
    {
        $user = $this->user->get_user();

        $update = array('html' => $html);
        $where = array('id'      => $id,
                       'user_id' => $user->id);

        $this->db->where($where)->update('forms', $update);

        return ($this->db->affected_rows() == 1);
    }


    /**
     * Másolat készítése az űrlapról a bejelentkezett felhasználó sajátjaként
     *
     * @param id Az űrlap, amiről másolatot készítünk
     * @param name Az új űrlap neve
     *
     * @return Ha a kért űrlap nem létezik, vagy a felhasználónak nincs olvasási joga, akkor false
     *         Egyébként az új űrlap azonosítója
     */
    function make_copy($id, $name)
    {
        $user = $this->user->get_user();
        $where = "`id` = '$id' AND (`public` = TRUE OR `user_id` = '{$user->id}'";

        $result = $this->db->from('forms')->where($where)->get();

        if ($result->num_rows() == 0)
            return false;

        return $this->create_form($name, $form->html, $form->public);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////
    /// A publikus űrlapokra, bejelentkezett felhasználótól függetlenül vonatkozó függvények ///
    ////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return A publikus űrlapokat leíró objektumok tömbje
     */
    function get_form_list_public()
    {
        $where  = array('public' => true);
        $select = array('id', 'name', 'public');

        return $this->db->select($select)->from('forms')->where($where)->get()->result;
    }


    /**
     * Adott azonosítójú űrlap lekérdezése
     * Megkötés: az űrlap legyen publikus
     *
     * @param id Az űrlap azonosítója
     *
     * @return Az űrlapot leíró objektum
     */
    function get_form_public($id)
    {
        $user = $this->user->get_user();
        $where = array('id'     => $id,
                       'public' => true
                       );
        return $this->db->from('forms')->where($where)->get()->row();
    }
}
