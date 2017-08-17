<?php
namespace Callback\Controller;

/**
 * 支付回调控制器
 * @author 小纯洁 
 */
class NotifyController extends BaseController {
    /**
    *通知方法
    */
    public function notify()
    {
        
        $apitype = I('get.apitype');#获取支付api类型
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            $notify = file_get_contents("php://input");
            if(empty($notify)){
                $this->record_logs("Access Denied");
                exit('Access Denied');
            }
        }

        $pay_way = $apitype;
        if($apitype == "swiftpass"){$apitype = "weixin";}
        //$this->wite_text(json_encode($notify),dirname(__FILE__).'/pc.txt');
        $pay = new \Think\Pay($pay_way, C($apitype));
        if ($pay->verifyNotify($notify)) {
            //获取回调订单信息
            $order_info = $pay->getInfo();
            if ($order_info['status']) {
                $pay_where = substr($order_info['out_trade_no'],0,2);
                $result = false;

                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($order_info);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($order_info);
                        break;
                    case 'AG':
                        $result = $this->set_agent($order_info); 
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if (I('get.method') == "return") {
                    redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php');
                } else {
                    $pay->notifySuccess();
                }
            }else{
                $this->record_logs("支付失败！");
            }
        }else{
            $this->record_logs("支付验证失败");
            redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php',3,'支付验证失败');
        }
    }


    /**
    *微信回调
    */
    public function wxpay_callback(){
        $values = array();
        Vendor("WxPayPubHelper.WxPayPubHelper");
        $weixin = A("WeiXin","Event");
        $request = file_get_contents("php://input");
        $reqdata = $weixin->xmlstr_to_array($request);
        if($reqdata['return_code'] != 'SUCCESS'){
             $this->record_logs("return_code返回数据错误");exit();
        }else{  
            if($_REQUEST['method']=="notify2"){//sdk
            $Common_util_pub = new \Common_util_pub(C('wei_xin_app.email'),C('wei_xin_app.partner'),C('wei_xin_app.key'));
            }elseif($_REQUEST['method']=="notify3"){ //app
            $Common_util_pub = new \Common_util_pub(C('wei_xin_apps.email'),C('wei_xin_apps.partner'),C('wei_xin_apps.key'));                    
            }elseif($_REQUEST['method']=="notify"){//扫码
            $Common_util_pub = new \Common_util_pub(C('wei_xin.email'),C('wei_xin.partner'),C('wei_xin.key'));                
            }
            if($Common_util_pub->getSign($reqdata) == $reqdata['sign']){
                $pay_where = substr($reqdata['out_trade_no'],0,2);
                $data['trade_no']     = $reqdata['transaction_id'];
                $data['out_trade_no'] = $reqdata['out_trade_no'];
                switch ($pay_where) {
                    case 'SP'://充值游戏
                        if($this->recharge_is_exist($reqdata['out_trade_no'])){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                            exit();
                        }
                        $result = $this->set_spend($data);
                        if($result){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }else{
                            echo " <xml> <return_code><![CDATA[FAILURE]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }
                        break;
                    case 'PF'://充值平台币
                        if($this->deposit_is_exist($reqdata["out_trade_no"])){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                            exit();
                        }
                        $result = $this->set_deposit($data);
                        if($result){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }else{
                            echo " <xml> <return_code><![CDATA[FAILURE]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }
                        break;
                    case 'AG'://代充
                         if($this->agent_is_exist($reqdata["out_trade_no"])){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                            exit();
                        }
                        $result = $this->set_agent($data); 
                        if($result){
                            echo " <xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }else{
                            echo " <xml> <return_code><![CDATA[FAILURE]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>";
                        }
                        break;

                    default:
                        $this->record_logs("订单号错误！！");
                    break;
                }
            }else{
                $this->record_logs("支付验证失败");
                redirect('http://'.$_SERVER['HTTP_HOST'].'/front.php/Recharge/index.html',3,'支付验证失败');
            }
        }
        // $this->wite_text(json_encode($reqdata),dirname(__FILE__)."/notify.txt");
    }

    public function heepay_callback(){
        $result = $_GET['result'];
        $pay_message = $_GET['pay_message'];
        $agent_id = $_GET['agent_id'];
        $jnet_bill_no = $_GET['jnet_bill_no'];
        $agent_bill_id = $_GET['agent_bill_id'];
        $pay_type = $_GET['pay_type'];
        $pay_amt = $_GET['pay_amt'];
        $remark = $_GET['remark'];
        $return_sign=$_GET['sign'];

        $remark = iconv("GB2312","UTF-8//IGNORE",urldecode($remark));//签名验证中的中文采用UTF-8编码;

        $signStr='';
        $signStr  = $signStr . 'result=' . $result;
        $signStr  = $signStr . '&agent_id=' . $agent_id;
        $signStr  = $signStr . '&jnet_bill_no=' . $jnet_bill_no;
        $signStr  = $signStr . '&agent_bill_id=' . $agent_bill_id;
        $signStr  = $signStr . '&pay_type=' . $pay_type;
        
        $signStr  = $signStr . '&pay_amt=' . $pay_amt;
        $signStr  = $signStr .  '&remark=' . $remark;
        
        $signStr = $signStr . '&key=' . SIGN_KEY; //商户签名密钥
        
        $sign='';
        $sign=strtolower(md5($signStr));
        if($sign==$return_sign){   //比较签名密钥结果是否一致，一致则保证了数据的一致性
            echo 'ok';
            //商户自行处理自己的业务逻辑
            $pay_where = substr($agent_bill_id,0,2);
            $data['trade_no']     = $reqdata['jnet_bill_no'];
            $data['out_trade_no'] = $reqdata['agent_bill_id'];
            switch ($pay_where) {
                case 'SP':
                    $result = $this->set_spend($data);
                    break;
                case 'PF':
                    $result = $this->set_deposit($data);
                    break;
                case 'AG':
                    $result = $this->set_agent($data); 
                    break;
                default:
                    exit('accident order data');
                    break;
            }
        }
        else{
            echo 'error';
            //商户自行处理，可通过查询接口更新订单状态，也可以通过商户后台自行补发通知，或者反馈运营人工补发
        }
        

    }
  

    /**
    *判断平台币充值是否存在
    */
    protected function deposit_is_exist($out_trade_no){
        $deposit = M('deposit','tab_');
        $map['pay_status'] = 1;
        $map['pay_order_number'] = $out_trade_no;
        $res = $deposit->where($map)->find();
        if(empty($res)){
            return  false;
        }
        else{
            return true;
        }
    }

    //判断充值是否存在
    public function recharge_is_exist($out_trade_no){
        $recharge = M('spend','tab_');
        $map['pay_status'] = 1;
        $map['pay_order_number'] = $out_trade_no;
        $res = $recharge->where($map)->find();
        if(empty($res)){
            return  false;
        }
        else{
            return true;
        }
    }


    //判断代充是否存在
    public function agent_is_exist($out_trade_no){
        $recharge = M('agent','tab_');
        $map['pay_status'] = 1;
        $map['pay_order_number'] = $out_trade_no;
        $res = $recharge->where($map)->find();
        if(empty($res)){
            return  false;
        }
        else{
            return true;
        }
    }



    function wite_text($txt,$name){
        $myfile = fopen($name, "w") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}