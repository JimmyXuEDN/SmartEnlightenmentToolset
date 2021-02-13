<?php
namespace app\article\validate;
use think\Validate;
class Article extends Validate
{
  protected $rule = [
    'title'  =>  'require',
    // 'content' => 'require',
    'type_id'=>'require',
  ];

  protected $message = [
    'title.require'  => '{%VALIDATE_ARTICLE_TITLE_REQUIRE}',
    'type_id.require'  => '{%VALIDATE_ARTICLE_TYPE_ID_REQUIRE}',
    'content.require'  => '{%VALIDATE_ARTICLE_CONTENT_REQUIRE}',
  ];

}
?>
