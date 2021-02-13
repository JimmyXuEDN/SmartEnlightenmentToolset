<?php
namespace pay;

use pay\data\TransferBank;
use pay\data\TransferPurse;
use pay\exception\PayException;
use pay\data\Order;


class Pay{
	
	protected static $config = [];// 配置参数
	
	protected static $driver; //支付驱动
	
	public static function  init($config=[]){
		$type         = isset($config['type']) ? $config['type'] : 'Wx';
		$class        = false !== strpos($type, '\\') ? $type : '\\pay\\driver\\' . ucwords($type);
		self::$config = $config;

		unset($config['type']);
		if (class_exists($class)) {
			self::$driver = new $class($config);
		} else {
			throw new PayException('class not exists:' . $class);
		}
	}

	public static function getDriver()
    {
        if(is_null(self::$driver)){
            throw new PayException('Pay must init');
        }

        return self::$driver;
    }
	
	
	public static function unifiedOrder(Order $order){
		if(is_null(self::$driver)){
			throw new PayException('Pay must init');
		}
		
		return self::$driver->unifiedOrder($order);
	}

	public static function transferToBank(TransferBank $transferBank){
        if(is_null(self::$driver)){
            throw new PayException('Pay must init');
        }

        return self::$driver->transferToBank($transferBank);
    }

    public static function transferToPurse(TransferPurse $transferPurse){
        if(is_null(self::$driver)){
            throw new PayException('Pay must init');
        }

        return self::$driver->transferToPurse($transferPurse);
    }
	
	
	/**
	 * 支付回调
	 * @param unknown $input
	 * @throws PayException
	 */
	public static function payHook($input){


		Util::log($input);
		if(is_null(self::$driver)){
			throw new PayException('Pay must init');
		}
		return self::$driver->payHook($input);
	}

    /**
     * @param $input
     * @return mixed
     * @throws PayException
     */
    public static function payWxOfficialHook($input){
        if(is_null(self::$driver)){
            throw new PayException('Pay must init');
        }
        return self::$driver->payWxOfficialHook($input);
    }

    /**
     * @param $input
     * @return mixed
     * @throws PayException
     */
	public static function payMpHook($input){
        if(is_null(self::$driver)){
            throw new PayException('Pay must init');
        }
        return self::$driver->payMpHook($input);
    }
	
	public static function payHookReply($is_success=true){
		if(is_null(self::$driver)){
			throw new PayException('Pay must init');
		}
		
		return self::$driver->payHookReply($is_success);
	}
}