<?php
namespace app\advertisement\model;

use app\base\model\BaseModel;

class Advertisement extends BaseModel
{
  //修改器图片集操作
  public function setDatasAttr($value){
    $photos = json_encode($value);
    return $photos;
  }

  // 前端获取文章列表
  public static function getAdminList()
  {
    $map['status'] = 1;
    return self::getList(array(),array(),$map);
  }

  //处理图片
  public function getDatasAttr($value)
  {
    $photos = json_decode($value,true);
    return $photos;
  }


  //查看文章详情
  public static function getDetail($id){
    $res['ad'] = self::find($id);
    return $res;
  }

  //后台文章列表
  public static function getAdminArticleList(){
    return self::getList();
  }


}
 ?>
