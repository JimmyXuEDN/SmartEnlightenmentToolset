<?php
namespace app\article\model;

use app\base\model\BaseModel;

class ArticleType extends BaseModel
{

  protected $pk = "type_id";

  // 前端获取文章列表
  public static function getArticleTypeList()
  {
    return self::getList();
  }


  // 前端获取文章详情
  public static function getDetail($id)
  {
    $res['detail'] = self::find($id);
    return $res;
  }



}
 ?>
