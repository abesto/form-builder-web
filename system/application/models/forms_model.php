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
                      'name'    => mb_substr($name, 0, 100),
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

        return $this->db->select($select)->from('forms')->where($where)->order_by('id')->get()->result();
    }


    /**
     * Adott azonosítójú űrlap lekérdezése
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     * @param throw ld. {@link User_model::get_user($throw=true)}
     *
     * @return Ha nincs ilyen űrlap, akkor false; egyébként az űrlapot
     *         leíró objektum
     */
    function get_form($id, $throw=true)
    {
        $user = $this->user->get_user($throw);
        if ($user === false) return false;
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

        $update = array('name'    => mb_substr($name, 0, 100));
        $where  = array('id'      => $id,
                        'user_id' => $user->id);

        $this->db->where($where)->update('forms', $update);
        return ($this->db->affected_rows() == 1);
    }

    /**
     * Adott azonosítójú űrlap nyilvános flagjének beállítása
     * Megkötés: a bejelentkezett felhasználóhoz tartozzon
     *
     * @param id Az űrlap azonosítója
     * @param public Nyilvános?
     *
     * @return Sikeres volt módosítás?
     */
    function set_public($id, $to)
    {
        $user = $this->user->get_user();

        $update = array('public'    => $to);
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
     * @param name Az űrlap mentendő neve
     * @param html Az új tartalom
     *
     * @return Az mentett űrlap azonosítója
     *         Nem feltétlenül azonos az id paraméterrel. Új azonosítót kap, ha:
     *          - a mentés előtt törölték az űrlapot, vagy
     *          - az űrlap más felhasználóhoz tartozik
     */
    function save_form($id, $name, $html)
    {
        $user = $this->user->get_user();

        $where = array('id'      => $id,
                       'user_id' => $user->id);
        $update = array('html' => str_replace(' class=""', '', $html),
                        'name' => $name);

        if ($this->get_form($id) === false)
            $id = $this->create_form($name, $html, false);

        $this->db->where($where)->update('forms', $update);

        return $id;
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
        $select = array('id', 'name', 'user_name');
        $result = $this->db->select($select)->from('public_forms')->get()->result();

        $user = $this->user->get_user(false);
        if ($user !== false)
            foreach ($result as $row)
                $row->owner = ($user->name == $row->user_name);

        return $result;
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
        $where = array('id' => $id);
        $result = $this->db->from('public_forms')->where($where)->order_by('id')->get();

        if ($result->num_rows() == 0)
            return false;

        $row = $result->row();

        $user = $this->user->get_user(false);
        if ($user !== false)
            $row->owner = ($user->name == $row->user_name);
        return $row;
    }
}
