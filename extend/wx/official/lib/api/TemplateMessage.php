<?php
namespace wx\official\lib\api;

use wx\official\lib\Util;

/**
 * 微信公众号:消息模板
 * @author Zhong
 *
 */
class TemplateMessage extends Base{
	
	public function sendTemplateMessage($openid,$template_id,$data,$jump_url=null,$miniprogram=null){
		$url="message/template/send?access_token=".Util::getAccessToken();
		$this->values['touser']=$openid;
		$this->values['template_id']=$template_id;
		$this->values['data']=$data;
		if (!is_null($jump_url)){
		    $this->values['url']=$jump_url;
		}
		if (!is_null($miniprogram)){
		    $this->values['miniprogram']=$miniprogram;
		}
		
		return $this->post($url);
	}
	
	
	public function getTemplateList(){
	    $url="template/get_all_private_template?access_token=".Util::getAccessToken();
	    return $this->get($url);
	}
	
	public function deleteTemplate($template_id){
	    $url="template/del_private_template?access_token=".Util::getAccessToken();
	    $this->values['template_id']=$template_id;
	    return $this->post($url);
	}
}