<?php
namespace app\common\controller;
use app\BaseController;
use app\base\model\GlobalExp;
use app\base\model\Kuai;

/**
 * 通用接口,物流相关接口
 * @author Zhong
 *
 */
class Logistics extends BaseController
{

	//返回物流公司列表
   public function getExpList(){
   		//返回物流列表
	   $res['list'] = GlobalExp::all();
	   $this->sendResponse(0, $res);
   }

	//根据快递单号返回物流公司列表
   public function expCompanylist(){

	   $exp_sn = $this->getParams('exp_sn');
	   $url="https://www.kuaidi100.com/autonumber/autoComNum?resultv2=1&text=".$exp_sn;
	   $result_data=json_decode($this->curl($url,'GET',null,null),true);
	   $company = array();
	   foreach($result_data['auto'] as $k=>$v){
		   $exp_map['code'] = $v['comCode'];
		   $exp = GlobalExp::where($exp_map)->find();
		   if($exp){
			   $company[] = $exp;
		   }
	   }

	   $res['list'] = $company;
	   $this->sendResponse(SUCCESS,$res);
   }


	//物流查询
   public function logisticsInfo(){
	   $exp_sn = $this->getParams('exp_sn');
	   $exp_code = $this->getParams('exp_code');

	   $kuai = new Kuai();
       $result_data = $kuai->getExpressQuery($exp_sn, $exp_code);

       $info = null;
       if(!isset($result_data['status']) || $result_data['status']!=200){
           $this->sendResponse(ERROR_PARAMS, array(), $result_data["message"]);
           trace('快递100请求信息返回错误，返回内容：' . json_encode($result_data), 'error');
       }else{
           $exp_map['code'] = $exp_code;
           $exp = db('Exp')->where($exp_map)->find();
           $info['exp_name'] = $exp['name'];
           $info['exp_sn'] = $exp_sn;
           $info['exp_code'] = $exp_code;
           $info['detail'] = $result_data['data'];
           $info['state'] = $result_data['state'];
       }

	   $res['logistics'] = $info;
	   $this->sendResponse(SUCCESS,$res);
   }
}
