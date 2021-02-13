<?php
namespace app\advertisement\validate;
use think\Validate;
class Advertisement extends Validate
{
  protected $rule = [
    'datas'  =>  'require',
  ];

  protected $message = [
    'datas.require'  => '{%VALIDATE_AD_PHOTOS_REQUIRE}',
  ];

}
?>
