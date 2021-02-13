<?php
namespace app\article\validate;
use think\Validate;
class ArticleType extends Validate
{
  protected $rule = [
    'name'  =>  'require',
  ];

  protected $message = [
    'name.require'  => '{%VALIDATE_ARTICLE_TYPE_NAME_REQUIRE}',
  ];

}
?>
