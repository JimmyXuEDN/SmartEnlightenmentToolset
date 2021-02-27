<?php
/**
 * 验证基类
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2021/2/18
 * Time: 17:59
 * Class BaseValidate
 * @package
 */

namespace app\base\validate;

use app\base\exception\SaasException;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 检查数据是否经过正确的RSA加密
     * @param $data
     * @return bool
     * @throws SaasException
     */
    public function isRSAEncodeData($data)
    {
        if (is_null(saas_rsa_decode($data))) {
            return false;
        }
        return true;
    }
    /**
     * @param $data
     * @return bool
     */
    public function checkJsonFormat($data)
    {
        return is_null(json_decode($data));
    }

    /**
     * 验证字符串是否是邮箱格式
     * @param $str
     * @return bool
     */
    public static function isEmail($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#[a-z0-9&\-_.]+@[\w\-_]+([\w\-.]+)?\.[\w\-]+#is', $str) ? true : false;
    }

    /**
     * 验证字符串是否是网址
     * @param $str
     * @return bool
     */
    public static function isUrl($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#(http|https|ftp|ftps)://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?#i', $str) ? true : false;
    }

    /**
     * 验证字符串是否含有中文
     * @param $string
     * @return bool
     */
    public static function isChineseCharacter($string) {

        if (!$string) {
            return false;
        }

        return preg_match('~[\x{4e00}-\x{9fa5}]+~u', $string) ? true : false;
    }

    /**
     * 验证字符串是否含有非法字符
     * @param $string
     * @return bool
     */
    public static function isInvalidStr($string) {

        if (!$string) {
            return false;
        }

        return preg_match('#[!#$%^&*(){}~`"\';:?+=<>/\[\]]+#', $string) ? true : false;
    }

    /**
     * 验证邮政编码
     * @param $num
     * @return bool
     */
    public static function isPostNum($num) {

        if (!$num) {
            return false;
        }

        return preg_match('#^[1-9][0-9]{5}$#', $num) ? true : false;
    }

    /**
     * 验证身份证号码
     * @param $num
     * @return bool
     */
    public static function isPersonalCard($num) {
        $model = new IdCard();
        return $model->isChinaIDCard($num);
    }

    /**
     * 验证IP地址
     * @param $str
     * @return bool
     */
    public static function isIp($str) {

        if (!$str) {
            return false;
        }

        if (!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $str)) {
            return false;
        }

        $ipArray = explode('.', $str);

        //真实的ip地址每个数字不能大于255（0-255）
        return ($ipArray[0]<=255 && $ipArray[1]<=255 && $ipArray[2]<=255 && $ipArray[3]<=255) ? true : false;
    }

    /**
     * 验证手机号码
     * 增加号码段
     * @param $num
     * @return bool
     */
    public static function isMobile($num) {

        if (!$num) {
            return false;
        }

        return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^16[0-9]\d{8}$|^17[0-9]\d{8}$|^18[0-9]\d{8}$|^19[0-9]\d{8}$#', $num) ? true : false;
    }

    /**
     * 验证中文姓名
     * @param $name
     * @return bool
     */
    function isChineseName($name)
    {
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            return true;
        } else {
            return false;
        }
    }
}