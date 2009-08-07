<?php
/**
 * @file   user_model.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Mon Jul 20 10:08:49 2009
 *
 * @brief  A felhasználók kezelését végző model
 *
 */

class User_model extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ellenőrzi, hogy létezik-e már ilyen felhasználónév
     *
     * @param name
     *
     * @return true ha nincs, false ha van
     */
    function not_available($name)
    {
        $c = $this->db->from('users')->where('name', $name)->count_all_results();
        return $c > 0;
    }

    /**
     * Ellenőrzi, hogy létezik-e már ilyen e-mail cím az adatbázisban
     *
     * @param email
     *
     * @return true ha nincs, false ha van
     */
    function check_email($email)
    {
        $c = $this->db->from('users')->where('email', $email)->count_all_results();
        return $c == 0;
    }

    /**
     * A kapott paraméterekkel létrehoz egy új felhasználót
     * és bejelentkezteti
     *
     * @param name
     * @param email
     * @param pass
     *
     * @return 'name' vagy 'email' ha már foglalt, különben true
     */
    function register($name, $email, $pass)
    {
        $this->db->set('name'        , $name);
        $this->db->set('pass'        , "SHA('$pass')", false);
        $this->db->set('email'       , $email);
        $this->db->set('sid'         , 'SHA(\''.session_id().'\')', false);
        $this->db->set('last_action' , 'NOW()', false);
        $this->db->insert('users');
    }

    /**
     * Ellenőrzi a name/pass kombinációt és bejelentkezik, ha jó
     *
     * @param name
     * @param pass
     *
     * @return false ha rossz a name/pass, különben true
     */
    function login($name, $pass)
    {
        $where = "`name` = '$name' AND `pass` = SHA('$pass')";
        $res = $this->db->from('users')->where($where)->get();
        if ($res->num_rows() != 1) return false;

        $uid = $res->row()->id;
        $this->db->set('sid'         , 'SHA(\''.session_id().'\')', false);
        $this->db->set('last_action' , 'NOW()', false);
        $this->db->where('id', $uid)->update('users');
        return true;
    }

    /**
     * A munkamenetben mentett felhasználót kijelentkezteti
     */
    function logout()
    {
        $where= array('sid'         => '',
                      'last_action' => 'NOW()');
        $this->db->set('sid', '');
        $this->db->set('last_action', 'NOW()', false);
        $this->db->where('sid', "SHA('".session_id()."')", false);
        $this->db->update('users');
    }

    /**
     * Megkeresi a bejelentkezett felhasználó adatait
     * Bejelentkezés feltételei:
     *   - session_id megegyezik az adatbázisban tárolttal
     *   - kevesebb, mint egy napja volt az utolsó ellenőrzés/bejelentkezés
     *
     * @param throw True esetén kivételt dob, ha nincs bejelentkezett felhasználó
     *              False esetén false-t ad vissza, ha nincs bejelentkezett felhasználó.
     *
     * @return A felhasználó adatai egy objektumban, ha be van jelentkezve; egyébként false
     */
    function get_user($throw=true)
    {
        $where = "`sid` = SHA('".session_id()."') AND NOW() < DATE_ADD(`last_action`, INTERVAL 1 DAY)";
        $rel = $this->db->select(array('id', 'name', 'email'))->from('users')->where($where)->get();

        if ($rel->num_rows() !== 1)
            if ($throw === true)
                throw new Exception('You are not logged in. Also, you shouldn\'t get this message - doing something funny?');
            else
                 return false;

        $this->update_last_action();
        return $rel->row();
    }

    /**
     * Előfeltétel: a felhasználó be van jelentkezve
     *
     * A felhasználó utolsó akciójának időpontját frissíti
     */
    function update_last_action()
    {
        $this->db->set('last_action', 'NOW()', false);
        $this->db->where('sid = SHA(\''.session_id().'\')');
        $this->db->update('users');
    }
}
