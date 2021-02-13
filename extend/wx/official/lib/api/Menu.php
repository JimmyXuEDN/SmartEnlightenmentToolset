<?php
/**
 * Created by PhpStorm.
 * User: Geekgit
 * Date: 2018/1/31
 * Time: 20:09
 */

namespace wx\official\lib\api;
use wx\official\lib\Util;


class Menu extends Base
{
  public function createMenu($button){
      $url = "menu/create?access_token=".Util::getAccessToken();
      $this->values = $button;
      return $this->post($url);
  }

    public function selectMenu()
    {
        $url = "menu/get?access_token=".Util::getAccessToken();
        return $this->get($url);
    }

    public function deleteMenu()
    {
        $url = "menu/delete?access_token=".Util::getAccessToken();
        return $this->get($url);
    }

}
