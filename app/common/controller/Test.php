<?php
namespace app\common\controller;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use aliyun\green\Ocr;
use aliyun\imm\ImmExtend;
use aliyun\ocr\lib;
use aliyun\oss\File;
use aliyun\sts\StsExtend;
use app\base\model\GlobalConfigType;
use app\integral\model\IntegralRecord;
use app\member\model\MemberReal;
use app\mp\model\Mp;
use app\util\controller\Qrcode;
use email\phpmailer\Mail;
use OSS\Core\OssException;
use pay\data\TransferBank;
use pay\data\TransferPurse;
use pay\exception\PayException;
use pay\Pay;
use pay\data\Order;
use pay\data\Charge;
use app\message\model\MessageTemplate;
use app\message\model\Message;
use think\exception\DbException;
use think\facade\Env;
use think\facade\Request;
use think\response\Json;
use wx\mp\lib\api\uniformMessage;
use wx\official\lib\Util;
use think\facade\Cache;

/**
 * 通用接口,一些工具类的接口都 放在这里
 * @author Zhong
 *
 */
class Test
{
    public function index()
    {
        $data = GlobalConfigType::with(['config'])->select();;
        return json(saas_make_response(SUCCESS, $data, '通配测试方法'));
    }

    /**
     * 对字符串进行RSA公钥加密
     * @param $data
     * @return Json
     */
    public function rsa_encode()
    {
        $data = Env::get('app.rsa_key') . '_' . Request::post('string');
        return json(saas_make_response(SUCCESS, ['res' => saas_rsa_encode($data)]));
    }

    public function test()
    {
        header('content-type:text/html;charset=utf-8');
        $this->test_union_scan_pay();
    }

    /**
     * 获取微信支付到银行的加密公钥.
     * 默认格式是PKCS#1
     * 需要手动运行openssl命令将它转成PKCS#8格式,然后放到extend/pay/lib/wx/cert/pay_to_bank_public_key.pem上
     * 转化命令:PKCS#1 转 PKCS#8:
     * openssl rsa -RSAPublicKey_in -in <filename> -pubout
     *
     * @throws PayException
     */
    public function get_wx_pay_to_bank_rsa1()
    {
        saas_init_weixin_pay();
        $public_key=Pay::getDriver()->getTransferBankPublicKey();
        var_dump($public_key);
    }


    /**
     * 微信支付到银行卡
     */
   private function test_wx_pay_to_bank(){
       saas_init_weixin_pay();
       $transfer_bank=new TransferBank();
       $transfer_bank->order_id="1234";//订单号
       $transfer_bank->price="100";//金额:单位分
       $transfer_bank->bank_no="6214856551317476";//银行卡号
       $transfer_bank->bank_code="1001";//银行编码:看微信说明:https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_4
       $transfer_bank->real_name="钟建全";
       $transfer_bank->remark="测试银行付款";
       $result=Pay::transferToBank($transfer_bank);//有返回结果就表示支付成功了.
       var_dump($result);
   }

    /**
     * 微信支付到零钱
     */
    public function test_wx_pay_to_purse(){
        saas_init_weixin_pay();
        $transfer_purse=new TransferPurse();
        $transfer_purse->order_id="20200724001";//订单号
        $transfer_purse->price="100";//金额:单位分
        $transfer_purse->openid="oYk_84tFwiJwVnIPfj2X9V72MTDw";
        $transfer_purse->check_name='NO_CHECK';
        $transfer_purse->real_name="徐元庆";
        $transfer_purse->remark="测试钱包付款";
        $result=Pay::transferToPurse($transfer_purse);//有返回结果就表示支付成功了.
        var_dump($result);
    }

   /**
    * 微信公众号支付示例
    */
   private function test_wx_official_pay(){

   		$notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
   		$pay_type="JSAPI";//公众号支付
   		$openid="okK4Uv0EnTtnk9XbTRW7ExI8hZf0";
        saas_init_weixin_pay(null,$notify_url, $pay_type, $openid);

   		$order=new Order();
   		$order->title="支付测试标题";
   		$order->order_id="1234";
   		$order->detail="支付测试详情";
   		$order->price="0.01";

   		$res['charge']=Pay::unifiedOrder($order);
   		//var_dump($res);
   		$this->sendResponse(SUCCESS,$res);
   }

    /**
     * 微信二维码支付示例
     */
    private function test_wx_scan_pay(){

        $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $pay_type="NATIVE";//二维码支付
        saas_init_weixin_pay(null,$notify_url, $pay_type);

        $order=new Order();
        $order->title="支付测试标题";
        $order->order_id="0012341244";
        $order->detail="支付测试详情";
        $order->price="0.01";

        $qr_code_data=Pay::unifiedOrder($order);//weixin://wxpay/bizpayurl?pr=Pzk9G3c 需要使用第三方库将这个字符串转成二维码.
        var_dump($qr_code_data);
        //$this->sendResponse(SUCCESS,$res);
    }
   
   /**
    * 支付宝手机端网页支付
    */
   public function test_ali_wap_pay(){

    $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
    $return_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
    $pay_type="wap";

    saas_init_ali_pay(null, $notify_url, $pay_type, $return_url);

   	$order=new Order();
   	$order->title="支付测试标题";
   	$order->order_id="1234";
   	$order->detail="支付测试详情";
   	$order->price="0.01";
   	$res=Pay::unifiedOrder($order);
   	exit($res);//网页支付返回的是一个自动跳转的表单
   	//var_dump($res);
   	//$this->sendResponse(SUCCESS,$res);
   }

    /**
     * 支付宝电脑端网页支付
     */
    public function test_ali_web_pay(){
        $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $return_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $pay_type="web";

        saas_init_ali_pay(null, $notify_url, $pay_type, $return_url);

        $order=new Order();
        $order->title="支付测试标题";
        $order->order_id="1234";
        $order->detail="支付测试详情";
        $order->price="0.01";
        $res=Pay::unifiedOrder($order);
        exit($res) ;//网页支付返回的是一个自动跳转的表单
    }

    /**
     * 支付宝扫码支付
     */
    public function test_ali_scan_pay(){
        $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $pay_type="scan";

        saas_init_ali_pay(null, $notify_url, $pay_type);

        $order=new Order();
        $order->title="支付测试标题";
        $order->order_id="1234";
        $order->detail="支付测试详情";
        $order->price="0.01";
        $qr_code_data=Pay::unifiedOrder($order);//https://qr.alipay.com/bavh4wjlxf12tper3a 需要使用第三方库将这个字符串转成二维码.
        var_dump($qr_code_data);
    }


    /**
     * 银联网页支付
     */
    public function test_union_wap_pay(){

        $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $return_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $pay_type="wap";//与web一样

        saas_init_union_pay(null, $notify_url, $pay_type, $return_url);

        $order=new Order();
        $order->title="支付测试标题";
        $order->order_id="1234";
        $order->detail="支付测试详情";
        $order->price="0.01";
        $res=Pay::unifiedOrder($order);
        exit($res);//网页支付返回的是一个自动跳转的表单
        //var_dump($res);
        //$this->sendResponse(SUCCESS,$res);
    }

    /**
     * 银联扫码支付
     */
    public function test_union_scan_pay(){

        $notify_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $return_url="http://{$_SERVER['HTTP_HOST']}/rapiv2/payhook/wx_web_hook";
        $pay_type="scan";//与web一样

        saas_init_union_pay(null, $notify_url, $pay_type, $return_url);

        $order=new Order();
        $order->title="支付测试标题";
        $order->order_id="12344";
        $order->detail="支付测试详情";
        $order->price="0.01";
        $qr_code_url=Pay::unifiedOrder($order);//'https://qr.95516.com/00010001/01131042317690085140925727118774'
        var_dump($qr_code_url);
        //$this->sendResponse(SUCCESS,$res);
    }

   /**
    * 支付回调示例
    */
   public function test_pay_hook(){
   		$config=array(
   			"type"=>"wx",
   			"app_id"=>saas_config('wx_official.app_id'),
   			"mch_id"=>saas_config('wx_official.pay_mch'),
   			"key"=>saas_config('wx_official.pay_key'),
   			"app_secret"=>saas_config('wx_official.app_secret')
   		);

   		Pay::init($config);

   		//参考/pay/data/Charge
   		$charge=Pay::payHook($this->request->getContent());

   		//业务处理
   		Pay::payHookReply(true);//结果输出
   }
   
   public function test_message(){
   		//使用模板发送
   		Message::sendMemberMessageByTemplate(1, "order_1", ['order_sn'=>"1000"]);
   		
   		//不使用模板发送
   		$message=new Message();
		$message->title="标题";
		$message->detail="内容";
		$message->jump_type=0;
		$message->jump_aim="";
   		Message::sendMemberMessage($message, 1);
   }

   public function test_qr_code(){
       $qr=new Qrcode();
       $url=$qr->create_dis_qrcode(186);
       var_dump($url);
   }

    public function test_send_mail()
    {
        $model = new Mail();
        $model->send_mail('316392750@qq.com', 'TestName', 'TestSubject', '这是一封来自游视界图库的测试通知邮件。');
    }

    public function test_mp_temp_media()
    {
//        $model = new \wx\mp\lib\api\Material(saas_config('wx_mp.app_id'), saas_config('wx_mp.app_secret'));
//        $res = $model->addMaterialTemp(ROOT_PATH  . 'public/static/images/mp-message-thumb.jpeg');
//        var_dump($res);

//        $model = new Mp();
//        $res = $model->getCachePDMedia();
//        var_dump($res);

        $model = new Mp();
        $res = $model->sendPDMessage('oYk_84tFwiJwVnIPfj2X9V72MTDw');
        var_dump($res);
    }

    public function test_mp_message()
    {
        $model = new Mp();
        $model->sendCustomMessage('oYk_84tFwiJwVnIPfj2X9V72MTDw', 'miniprogrampage', '', '', '', [
            'title' => '排队提醒',
            'pagepath' => 'pages/insurance/main'
        ]);
    }

    public function test_mp_uniform_message()
    {
        $model = new uniformMessage();
        $res = $model->sendUniformMessage('oYk_84tFwiJwVnIPfj2X9V72MTDw', [
            'appid' => 'wxb35a5181a73ac20b',
            'template_id' => '9RoygBXJ9n7JdnGMSUfk5Fa6sqbRw6exXOfJ_0lg94E',
            'miniprogram' => [
                'appid' => 'wx65cbf1f3d79c2721',
                'pagepath' => 'pages/insurance/main'
            ],
            'data' => [
                'first' => [
                    'value' => '尊敬的徐元庆车主，您的排队序号有变动',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => '2',
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => '1',
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '2020-11-24 20:39:01',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '请您耐心等候，注意排队信息。',
                    'color' => '#173177'
                ],
            ]
        ]);
        saas_send_response(0, $res);
    }

    public function test_get_oss_sts()
    {
        $model = new StsExtend();
        $res = $model->getSts();
        var_dump($res['Credentials']);
        exit;
        // saas_send_response(0, $res);
    }

    public function test_imm_convert()
    {
        $model = new ImmExtend();
        $res = $model->convertFile('png', 'oss://ppd2020/plocy/2020-09-07/208c9fb6-775e-44d9-943e-3d6ed7d8a7f9.pdf');
        var_dump($res);
        exit;
    }

    public function test_img_ocr()
    {
        $path = 'imm-format-convert-tgt/2020-09-07/208c9fb6-775e-44d9-943e-3d6ed7d8a7f9/1.png';
        $oss = new File();
//        $exist = $oss->fileExists($path);
//        var_dump($exist);
//        if ($exist === false)
//        {
//            saas_send_response(2000, [], '文件不存在');
//        }
        $url = $oss->getOriginTempUrl($path);
        $model = new lib();
        $model->img_ocr($url);
    }

    /**
     * @throws ClientException
     * @throws ServerException
     * @throws OssException
     */
    public function test_green_ocr()
    {
        $path = 'imm-format-convert-tgt/2020-09-07/208c9fb6-775e-44d9-943e-3d6ed7d8a7f9/1.png';
        $oss = new File();
//        $exist = $oss->fileExists($path);
//        if ($exist === false)
//        {
//            saas_send_response(2000, [], '文件不存在');
//        }
        $url = $oss->getOriginTempUrl($path);
        $model = new Ocr();
        $res = $model->ossImg($url, 'test_green_ocr_001');
        saas_send_response(0, $res);
    }

    public function send_template_message()
    {
        //发送模板消息
        //添加data,每个参数可以指定颜色
        $data=array();
        $data['first']['value']="尊敬的XXX车主，您的排队进度提醒";
        $data['first']['color']="#173177";
        $data['keyword1']['value']="a987nu8982";
        $data['keyword1']['color']="#173177";
        $data['keyword2']['value']="19";
        $data['keyword2']['color']="#173177";
        $data['keyword3']['value']="2020-11-21";
        $data['keyword3']['color']="#173177";
        $data['remark']['value']="请您在大厅耐心等候，注意排队信息，以免过号重排！";
        $data['remark']['color']="#173177";

        //填写跳转小程序时需要指定appid与path
        $miniprogram['appid']="wx65cbf1f3d79c2721";
        $miniprogram['pagepath']="pages/insurance/main";

        $res = Util::sendTemplateMessage("oYk_84tFwiJwVnIPfj2X9V72MTDw", "9RoygBXJ9n7JdnGMSUfk5Fa6sqbRw6exXOfJ_0lg94E", $data,null,$miniprogram);
        var_dump($res);
    }

    /**
     * @throws DbException
     */
    public function correctIntegralRecord()
    {
        $record = IntegralRecord::all();
        foreach ($record as $k => $v)
        {
            $real = MemberReal::get(['member_id' => $v->member_id]);
            if ($v->type == 16)
            {
                $v->save(['role_id' => 0]);
            }
            else if ($v->type == 15 && in_array($v->ratio, [3]))
            {
                $v->save(['role_id' => 2]);
            }
            else if ($v->type == 15 && in_array($v->ratio, [12, 17, 5]))
            {
                $v->save(['role_id' => 1]);
            }
            else if ($v->type == 15 && in_array($v->ratio, [20]))
            {
                $v->save(['role_id' => 0]);
            }
            else if (!is_null($real) && $v->role_id != $real->role_id)
            {
                echo 'id' . $v->id . '需要纠正';
                echo '<br />';
                $v->save(['role_id' => $real->role_id]);
            }
        }

    }
}
