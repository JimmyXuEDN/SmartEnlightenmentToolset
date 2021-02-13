<?php
namespace app\article\model;

use app\base\model\BaseModel;

class Article extends BaseModel
{


  // 前端获取文章列表
  public static function getApiArticleList()
  {
    $map['status'] = 1;
    $with = ['type'];
    return self::getList($with,array(),$map);
  }


  //查看文章详情
  public static function getArticleDetail($id){
    $with = ['type'];
    $res['article'] = self::with($with)->find($id);
    return $res;
  }

  //后台文章列表
  public function getAdminArticleList(){
    $with = ['type'];
    $list = self::get_list($with);
    return $list;
  }

  //分类
  public function type(){
    $bind_array=array(
        "type_name"=>'name',
    );//一对一关联时,绑定属性相当于join查询,需要返回列表时必须绑定.

    return $this->belongsTo('app\article\model\ArticleType','type_id')->bind($bind_array);
  }

}
 ?>
