<?php
/**
 * @file   BaseController.php
 * @author Zoltán Nagy <abesto0@gmail.com>
 * @date   Sun Jul  5 08:08:38 2009
 *
 * @brief  Minden controllerben közös funkcionalitás
 */


/**
 * Ennek az alosztályai lesznek azok a controllerek, amiknek meg kell
 * jeleníteniük a fő template-t.
 *
 * @example ../../../doc/php/BaseController_example.php
 * Hello world! kiiratása egy alosztályból
 */
class BaseController extends Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('html');
    $this->load->helper('url');
    $this->slots = array();

    // A menü elemei
    $this->slots['menu'] = $this->load->view('menu',
                                             array('items' => array('Home', 'About', 'Manual', 'FAQ', 'FuQ', 'Whatever')),
                                             true);
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
    return $this->load->view('skeleton', $this->slots, $return);
  }


  /**
   * Betölti a megfelelő nyelvből a kért fájlt.
   *
   * A megfelelő nyelv meghatározása:
   * Ha kaptunk értéket az URLből, akkor mindenképpen azt használjuk és mentjük session cookie-ba
   * Egyébként a session cookie-ban tárolt értéket használjuk
   * Ha ilyen nincs, akkor a config.php-ben beállított nyelvet használjuk
   *
   * @param file A betöltendő nyelvi fájl neve
   * @param in Az URLből kapott nyelv
   */
  protected function load_lang($file, $in=null)
  {
      $this->session =& load_class('Session');
      if ($in !== null) {
          $this->session->set_userdata('lang', $in);
          $this->lang->load($file, $in);
      } else if ($this->session->userdata('lang') === false)
          $this->lang->load($file);
      else
          $this->lang->load($file, $this->session->userdata('lang'));
  }
}
?>
