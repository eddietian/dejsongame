<?php

// +----------------------------------------------------------------------

// | OneThink [ WE CAN DO IT JUST THINK IT ]

// +----------------------------------------------------------------------

// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.

// +----------------------------------------------------------------------

// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>

// +----------------------------------------------------------------------

namespace Media\Controller;

use User\Api\MemberApi;

use Org\XiguSDK\Xigu;
use Org\UcenterSDK\Ucservice;

/**

 * 文档模型控制器

 * 文档模型列表和详情

 */

class MemberController extends BaseController {



    public function __construct(){

    	parent::__construct();

        $arr = array(

			"Member/index","Member/safe",

			"Member/platform","Member/profile",

			"Member/record","Member/phone",

            "Member/resetpwd"

		);

		if (in_array($_SERVER['PATH_INFO'],$arr,true)) {

			$mid = parent::is_login();

			if ($mid<1) {

				$this->redirect("Member/plogin");

			}

		}

    }



	public function index($value='')

	{

		$gift = array(

			'm_name'=>'Giftbag',

			'prefix'=>'tab_',

			'field' =>array('tab_giftbag.*,tab_game.icon,tab_game.cover'),

			'join' =>'tab_game ON tab_giftbag.game_id = tab_game.id',

			'map'=>array('game_status'=>1),

			'limit'=>5,

			'order'=>'create_time',

		);

		$game = array(

			'm_name'=>'Game',

			'prefix'=>'tab_',

			'field' =>true,

			'map'=>array('game_status'=>1),

			'limit'=>5,

			'order'=>'create_time',

		); 


		$list_gift = parent::join_data($gift);

		$list_game = parent::list_data($game);

		$this->assign('list_gift',$list_gift);

		$this->assign('list_game',$list_game);

        $this->assign('name',session('member_auth.account'));

		$this->display();

	}



	/**

	*平台币

	*/

	public function platform(){

		$user=M('User','tab_');

		$map['id']=session('member_auth.mid');

		$data=$user->where($map)->find();

		$this->assign('mid',$data);

        $this->assign('name',session('member_auth.account'));

		$this->display();

	}



	/**

	*消费记录

	*/

	public function record($p=0){

		$page1 = intval($p);

        $page1 = $page1 ? $page1 : 1; //默认显示第一页数据

		$game1  = M('spend','tab_');

		$user = session("member_auth");

		$map1['user_account']=$user['account'];//'wan001'

        $map1['pay_status']=1;

        $map1['pay_way']=0;

		$data1  = $game1->where($map1)->order('pay_time desc')->limit('0,10')->select();

		$count1 = $game1->where($map1)->count();

		//分页

        if($count1 > 10){

            $page1 = new \Think\Page($count1, 10);

            $page1->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');

            $this->assign('_page1', $page1->show());

        }

        $this->assign("count1",$cuont1);

        $this->assign('agent_data', $data1);

        $this->assign('name',$user['account']);





		$model = array(

			'm_name'=>'deposit',

			'prefix'=>'tab_',

			'field' =>true,

			'map'=>array('pay_status'=>1,'user_account'=>$user['account']),//'wan001'

			'order'=>'create_time desc',

			'tmeplate_list'=>'record',

		);

		parent::lists($model,$p);

		//$this->display();

	}

	public function is_login(){
        if (session('member_auth.nickname') == "Uc用户") {
            $data['status'] = 1;
            $data['account'] = session('member_auth.account');
            return $this->ajaxReturn($data);
        } else {
            $mid = parent::is_login();
            if ($mid > 0) {
                $data = parent::entity($mid);
                $data['status'] = 1;
                return $this->ajaxReturn($data);
            } else {
                return $this->ajaxReturn(array('status' => 0, 'msg' => '服务器故障'));
            }
        }
		

	}


	/**

     * 注销当前用户

     * @return void

     */

    public function logout(){

        session('member_auth', null);

        session('member_auth_sign', null);

        session('[destroy]');

        $this->ajaxReturn(array('reurl'=>'media.php'));

    }



    /* 退出登录 */

    // public function logout(){

    //     if(is_login()){

    //         D('Member')->logout();

    //         session('[destroy]');

    //         $this->success('退出成功！', U('login'));

    //     } else {

    //         $this->redirect('login');

    //     }

    // }

    

	// 我的资料  lwx 2015-05-19

	public function profile(){

        $res = session("member_auth");

        $res = $res['mid'];

        if (IS_POST) {

            $flag = M('User','tab_')->save(array('id'=>$res,'nickname'=>I('post.nickname'),'qq'=>I('post.qq')));

            if ($flag>0) {

                

				$data['msg'] = '修改成功';

				$data['status']=1;

			} else {

				$data['msg'] = '修改失败';

				$data['status']=0;

			}

			echo json_encode($data);           

        } else {           

            $user = M('User','tab_')->where("id=$res")->find();

            $phone = substr($user['phone'],2,-2);

            $user['phone'] = str_replace($phone,'*******',$user['phone']);

            $rl = mb_substr($user['real_name'],0,1,'utf-8');

            $user['real_name']= str_replace($rl,'*',$user['real_name']);

            $idcard = substr($user['idcard'],3,-3);

            $user['idcard']=str_replace($idcard,'*********',$user['idcard']);

            $this->assign('up',$user);

            $this->assign('name',$user['account']);

            $this->display();           

        }

	}

    

    // 重置密码  lwx 2015-05-19

    public function resetpwd() {
        $t = I('t');
        $name = I('name');
        if (empty($t) || empty($name) || $t !== 'm') {
            $this->redirect('forget');
        }
        $mid = parent::is_login();
        if ($mid <1) {
            $this->redirect('forget');
        }
        $user = session('member_auth');
        if ($name !== $user['account']) {
            session('member_auth', null);
            session('member_auth_sign', null);
            session('[destroy]');$this->redirect('forget');
        }
        if (IS_POST) {
            if(C('UC_SET')==1){
                $uc=new Ucservice();
                $data_uc=$uc->get_uc($user['account']);
                if(is_array($data_uc)){
                  $uc_id=$uc->uc_edit($data_uc[1],"11",I('password'),"",1);
                }
            } 
            $this->pwd($user['mid'],I('password'));
        } else {            
            $user = M('User','tab_')->where("id=".$user['mid'])->find();
            if (!empty($user['phone'])) {
                $phone = substr($user['phone'],2,-2);
                $this->assign('phone',str_replace($phone,'*******',$user['phone']));
                $this->assign('ph',$user['phone']);                
            } 
            $this->assign('name',$user['account']);
            $this->display();
        }

    }

    

    public function findpwd() {
        $t = I('t');
        $name = I('name');
        if (empty($t) || empty($name) || $t !== 'f') {
            $this->redirect('forget');
        }
        $user = M('User','tab_')->where("account='$name'")->find();
        if (IS_POST) {
            $this->pwd($user['id'],I('password'));
        } else {                        
            if (!empty($user['phone'])) {
                $phone = substr($user['phone'],2,-2);
                $this->assign('phone',str_replace($phone,'*******',$user['phone']));
                $this->assign('ph',$user['phone']);                
            } 
            $this->assign('name',$user['account']);
            $this->display();             
        }
    }
    

    // 修改密码

    public function pwd($uid,$password) {
        $member = new MemberApi();
        $result = $member->updatePassword($uid,$password);
        if ($result) {
            $data['status']=1;
            $data['msg']='密码修改成功';
        } else {
            $data['status']=0;
            $data['msg']='密码修改失败';
        }
        echo json_encode($data);
    }
    

    // 推广员推广注册通道  lwx 2016-05-18

    public function preg() {
        $pid= I('pid');
        if (empty($pid)) $pid = 0;   
        $this->assign('pid',$pid);
        $this->display();        
    }
    
    public function plogin() {
        $this->display();           
    }
    

    public function safe() {
        $user = session("member_auth");
        if (IS_POST) {
            $pwd = I('pwd');
            $member = new MemberApi();
            $uc=new Ucservice();
            if(C('UC_SET')==1){
              $uidarray = $uc->uc_login($user['account'], $pwd,1);   
              if($uidarray>0){
                echo json_encode(array('status'=>1));
              }else{
                echo json_encode(array('status'=>0));
              }
            }else{
            $flag = $member->checkPassword($user['account'],$pwd);
            if ($flag) {
                echo json_encode(array('status'=>1));
            } else {
                echo json_encode(array('status'=>0));
            }
            }
        } else {
            $this->assign('name',$user['account']);
            $this->display();
        }

    }

    

    // 绑定手机 lwx
    public function phone() {
        if (IS_POST) {
			$telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
            $phone = $_POST['phone'];
			if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $phone)) {
				echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
			}
            $user = session("member_auth");
            $res = $user['mid'];
            M('User','tab_')->where("id=$res")->setField('phone',$phone);
            $flag = M('User','tab_')->where("id=$res and phone = $phone")->find();
            if ($flag) {
                $data['status']=1;
                $data['msg']='手机绑定成功';
            } else {
                $data['status']=0;
                $data['msg']='手机绑定失败';
            }
            session('telsvcode',null);unset($telsvcode);
            echo json_encode($data);
        } else {
            $res = session('member_auth.mid');
            
            $ph = M('User','tab_')->field("phone")->where("id=$res")->find();
            
            if (!empty($ph) && is_array($ph)) {
                $this->assign('phone',$ph['phone']);
            }

            $this->assign('name',session('member_auth.account'));
            $this->display();
        }
        
    }
    
    public function changeph() {
        if (IS_POST) {
            $telsvcode = session('telsvcode');

            $time = (time() - $telsvcode['time'])/60;

            if ($time>$telsvcode['delay']) {

                session('telsvcode',null);unset($telsvcode);

                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;

            }

            $phone = $_POST['phone'];

			if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $phone)) {

				echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;

			}
            $res = session("member_auth.mid");
            
            M('User','tab_')->where("id=$res")->setField('phone','');
            
            $flag = M('User','tab_')->where("id=$res and phone = $phone")->find();
            
            if (!$flag) {

                $data['status']=1;

                $data['msg']='手机解绑成功';

            } else {

                $data['status']=0;

                $data['msg']='手机解绑失败';

            }

            session('telsvcode',null);unset($telsvcode);

            echo json_encode($data);
            
        } else {
            echo json_encode(array('status'=>0,'msg'=>'服务器故障'));exit;
        }
    }

    

    // 绑定证件  lwx  2015-05-20

    public function card() {
        $user = session('member_auth');
        if (IS_POST) {
            $real_name =I('real_name');
            $idcard = I('idcard');
            if (empty($real_name) || empty($idcard)) {
                echo json_encode(array('status'=>0,'msg'=>'提交的数据有误'));
            }
            if(!is_array(find_uc_account(session('member_auth.account')))){
               echo json_encode(array('status'=>0,'msg'=>'Uc用户暂不支持')); exit;
            }
            $data['id']=$user['mid'];
			$data['real_name']=$real_name;
            $data['idcard']=$idcard;
            $flag = M('User','tab_')->save($data);
            $data='';
            if ($flag) {
                $data['status']=1;
                $data['msg']='认证成功';
            } else {
                $data['status']=0;
                $data['msg']='认证失败';
            }
            echo json_encode($data);
        } else {
            $user = M('User','tab_')->where("id=".$user['mid'])->find();
            if (!empty($user['real_name']) && !empty($user['idcard'])) {
                $this->assign('istrue','1');
            }
            $this->display();
        }       

    }

    

    public function sendvcode() {
        if (!IS_POST) {
            echo json_encode(array('status'=>0,'msg'=>'请按正确的流程'));exit;
        }
        $verify = new \Think\Verify();
		if(!$verify->check(I('verify'),I('vid'))){
            echo json_encode(array('status'=>2,'msg'=>'验证码不正确')); exit;
        }
        if(!is_array(find_uc_account(session('member_auth.account')))){
        echo json_encode(array('status'=>2,'msg'=>'Uc用户暂不支持')); exit;
        }
        $phone = I('phone');
        $this->telsvcode($phone);             
    }

    

    // 忘记密码 lwx 2015-05-19

    public function forget() {

        $mid = parent::is_login();

        if ($mid>0) {

            $this->redirect('Member/resetpwd/t/m/name/'.session('member_auth.account'));

        }

        if (IS_POST) {

            $account = I('account');

            $user = M('User','tab_')->where("account='$account'")->find();

            if (!empty($user) && is_array($user) && (1 == $user['lock_status'])) {

                $data['status']=1;

                $data['phone']=$user['phone'];

            } else {

                $data['status']=0;

            }   

            echo json_encode($data);

        } else {            

            $this->display();

        }

    }

    public function login(){
        if (empty($_POST['account']) || empty($_POST['password'])) {
            $this->ajaxReturn(array('status' => 0, 'msg' => '账号或密码不能为空'));
        }
        if (C('UC_SET') == 1) {
            $uc = new Ucservice();
            $uidarray = $uc->uc_login($_POST['account'], $_POST['password']);
            if (is_array($uidarray)) {
                $is_c = find_uc_account($_POST['account']);
                if (is_array($is_c)) {
                    $map['id'] = $is_c['id'];
                    M('user', 'tab_')->where($map)->setField('login_time', time());
                    $uidd = $is_c['id'];
                    $nickname = $is_c['account'];
                } else {
                    $uidd = $uidarray['uid'];
                    $nickname = "Uc用户";
                }
                $auth = array(
                    'mid' => $uidd,
                    'account' => $_POST['account'],
                    'nickname' => $nickname,
                    'balance' => empty($is_c['balance']) ? 0 : $is_c['balance'],
                    'last_login_time' => empty($is_c['login_time']) ? "*******" : $is_c['login_time']
                );
                session('member_auth', $auth);
                session('member_auth_sign', data_auth_sign($auth));
                $this->ajaxReturn(array('status' => 1, 'msg' => '登陆成功'));
            }
        } else {
            $data = array();
            $member = new MemberApi();
            $res = $member->otpLogin($_POST['account'], $_POST['password']);
            if ($res > 0) {
                parent::autoLogin($res);
                $this->ajaxReturn(array('status' => 1, 'msg' => '登陆成功'));
            } else {
                switch ($res) {
                    case -1:
                        $data = array('status' => 0, 'msg' => '用户不存在或被禁用,请联系客服');
                        break;
                    case -2:
                        $data = array('status' => 0, 'msg' => '密码错误');
                        break;
                    case -3:
                        $data = array('status' => -3);
                        break;
                    case -4:
                        $data = array('status' => -4, 'msg' => "动态密码错误");
                        break;
                    default:
                        $data = array('status' => 0, 'msg' => '未知错误');
                        break;
                }
                $this->ajaxReturn($data);
            }
        }

    }


    /**
    *注册后完成登陆
    */
    public function res_login(){
        parent::autoLogin($_POST['uid']);
        $this->ajaxReturn(array("status"=>0,"uid"=>$_POST['uid']));
    }

    public function register(){
        if(C("USER_ALLOW_REGISTER")==1){
             $verify = new \Think\Verify();
             if($verify->check(I('verify'))){
                if(empty($_POST['account']) || empty($_POST['password'])){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'账号或密码不能为空'));
                } else if(strlen($_POST['account'])>15||strlen($_POST['account'])<6){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名长度在6~15个字符'));
                }else if(!preg_match('/^[a-zA-Z0-9]{6,15}$/', $_POST['account'])){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名包含特殊字符'));
                }
            }
            else{
                return $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
            }
            if(C('UC_SET')==1){
            $uc = new Ucservice();
            $uc_id=$uc->uc_register($_POST['account'],$_POST['password'],"",0,"自然注册",0,"",1,1);
            }
            if($uc_id==-3){
                $msg ="账号已存在";
                return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
            }else{
            $member = new MemberApi();
            $pid = $_POST['pid'];
            $res = $member->register(trim($_POST['account']),$_POST['password']);
            if($res > 0 ){
                if ($pid) {
                    $promote = M("Promote","tab_")->where("id=$pid")->find();
                    $data=array('id'=>$res,'promote_id'=>$pid,'promote_account'=>$promote['account']);
                    $b = M('User','tab_')->save($data);
                    if (!$b) {
                        M('User','tab_')->save($data);
                    }
                }
                return $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功',"uid"=>$res));
            }
            else{
                $msg = $res == -1 ?"账号已存在":"注册失败";
                return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
            } 
            }
               
            }else{
                return $this->ajaxReturn(array('status'=>0,'msg'=>'未开放注册！！'));
            }
    }
  
    
    // 发送手机安全码

    public function telsvcode($phone=null,$delay=10,$flag=true) {
        if (empty($phone)) {
            echo json_encode(array('status'=>0));exit; 
        }
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        $r = M('Short_message')->add($result);
        if ($result['send_status'] != '000000') {
            echo json_encode(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }        
        $telsvcode['code']=$rand;
        $telsvcode['phone']=$phone;
        $telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
        session('telsvcode',$telsvcode);
        if ($flag) {
            echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收'));        
        } else
            echo json_encode(array('status'=>1,'msg'=>''));
    }

    // 短信验证
    public function checktelsvcode($phone,$vcode,$flag=true) {       
        $telsvcode = session('telsvcode');
        $time = (time() - $telsvcode['time'])/60;
        if ($time>$telsvcode['delay']) {
            session('telsvcode',null);unset($telsvcode);
            echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
        }
        if (!($telsvcode['code'] == $vcode) || !($telsvcode['phone'] == $phone)) {
            echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
        }
        session('telsvcode',null);
        unset($telsvcode); 
        if ($flag) {
            echo json_encode(array('status'=>1));
        }
    }

    public function result($phone,$vcode,$password,$pid=0) {
        $member = new MemberApi();
        $this->checktelsvcode($phone,$vcode,false);
        $flag = $member->checkUsername($phone);
        if (!$flag) {
            $data['msg']  = $this->getE(-11);
            $data['status'] =  0;
            $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));exit;
        }
        if(C('UC_SET')==1){
            $uc = new Ucservice();
            $uc_id=$uc->uc_register($phone,$password,"",0,"自然注册",0,"",1,2);
        if(is_numeric($uc_id)){
            $data['msg']="注册成功";
            $data['status']=1;
            $data['url']='';
            $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN')); 
            }
            }else{
            $uid = $member->register(trim($phone),trim($password));
         if($uid>0) {
            M('User','tab_')->save(array("id"=>$uid,"phone"=>$phone));
            if ($pid) {
                M('User','tab_')->where("id=$uid")->setField('promoteid',$pid);
            }
            $data['msg']="注册成功";
            $data['status']=1;
            $data['url']='';
        } else {
            $data['msg']  = '注册失败';
            $data['status'] =  0;
        }           
        $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN')); 
        }

    }

    

    public function telregister() {
		$data = array();
		if (IS_POST) {
            $member = new MemberApi();
			$telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
			if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $_POST['account'])) {
				echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
			}
            if(C('UC_SET')==1){
            $uc = new Ucservice();
            $uc_id=$uc->uc_register($_POST['account'],$_POST['password'],"",0,"自然注册",0,"",1,1);
            }
            if($uc_id==-3){
                return $this->ajaxReturn(array('status'=>0,'msg'=>'"账号已存在"'));
            }else{
            $member = new MemberApi();
            $pid = $_POST['pid'];
            $res =$member->register(trim($_POST['account']),trim($_POST['password']),0,$pid,$paccount['account'],$_POST['account']);
            if($res > 0 ){
                if ($pid) {
                    $promote = M("Promote","tab_")->where("id=$pid")->find();
                    $data=array('id'=>$res,'promote_id'=>$pid,'promote_account'=>$promote['account']);
                    $b = M('User','tab_')->save($data);
                    if (!$b) {
                        M('User','tab_')->save($data);
                    }
                }
                return $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功',"uid"=>$res));
            }
            else{
                $msg = $res == -1 ?"账号已存在":"注册失败";
                return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
            }
          
            $flag = $member->checkUsername($_POST['account']);
            if (!$flag) {
                $data['msg']  = $this->getE(-11);
				$data['status'] =  0;
                $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));exit;
            }
            $pid = $_POST['pid'];
            $paccount=M('Promote','tab_')->field('account')->where(array('id'=>$pid))->find();
			$uid = $member->register(trim($_POST['account']),trim($_POST['password']),0,$pid,$paccount['account'],$_POST['account']);
			if($uid>0) {
                M('User','tab_')->save(array("id"=>$uid,"phone"=>$_POST['account']));
                if ($pid) {
                    M('User','tab_')->where("id=$uid")->setField('promoteid',$pid);
                }
                $data['msg']="注册成功";
                $data['status']=1;
                $data['url']='';
                //$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
			} else {
				$data['msg']  = '注册失败';
				$data['status'] =  0;
			}
            session('telsvcode',null);unset($telsvcode);
            $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
        }
            
		} else {
			$this->redirect('Index/index');
		}		

	}

     public function reg_data(){
        $member = new MemberApi();
        $pid = $_POST['pid'];
        $res = $member->register(trim($_POST['account']),$_POST['password']);
        if($res > 0 ){
            if ($pid) {
                $promote = M("Promote","tab_")->where("id=$pid")->find();
                $data=array('id'=>$res,'promote_id'=>$pid,'promote_account'=>$promote['account']);
                $b = M('User','tab_')->save($data);
                if (!$b) {
                    M('User','tab_')->save($data);
                }
            }
            return $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功',"uid"=>$res));
        }
        else{
            $msg = $res == -1 ?"账号已存在":"注册失败";
            return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
        }
    }      

    /**

	* 验证用户名

	*/

	public function checkUser() {

		if (IS_POST) {

			$username = $_POST['username'];

			$len = strlen($username);

			if ($len !== mb_strlen($username)) {

				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));

			}

			if ($len<6 || $len >30) {

				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));

			}

			if(!preg_match("/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/u",$username)) {

				return $this->ajaxReturn(array('status'=>-21,'msg'=>$this->getE(-21)),C('DEFAULT_AJAX_RETURN'));

			}

            $member = new MemberApi();

			$flag = $member->checkUsername($username);

			if ($flag) {

				return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));

			} else {

				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-3)),C('DEFAULT_AJAX_RETURN'));

			}

		}

	}

    /**

    * 验证手机号码

    */

    public function checkPhone() {

		if (IS_POST) {
            C(api('Config/lists'));
			$username = $_POST['username'];

			$len = strlen($username);

			if ($len !== mb_strlen($username)) {

				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-9)),C('DEFAULT_AJAX_RETURN'));

			}

			if ($len !== 11) {

				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-12)),C('DEFAULT_AJAX_RETURN'));

			}

			if(!preg_match("/^1[358][0-9]{9}$/u",$username)) {

				return $this->ajaxReturn(array('status'=>-21,'msg'=>$this->getE(-12)),C('DEFAULT_AJAX_RETURN'));

			}

            $member = new MemberApi();

            if(C('UC_SET')==1){
                $uc=new Ucservice();
                $is_c=$uc->get_uc($username);
                if(is_array($is_c)){
                  return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-11)),C('DEFAULT_AJAX_RETURN'));

                }
            }

			$flag = $member->checkUsername($username);
			if ($flag) {
				return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));
			} else {
				return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-11)),C('DEFAULT_AJAX_RETURN'));

			}

		}

	}

    

    protected function getE($num="") {

		switch($num) {

			case -1:  $error = '用户名长度必须在6-30个字符以内！'; break;

			case -2:  $error = '用户名被禁止注册！'; break;

			case -3:  $error = '用户名被占用！'; break;

			case -4:  $error = '密码长度不合法'; break;

			case -5:  $error = '邮箱格式不正确！'; break;

			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;

			case -7:  $error = '邮箱被禁止注册！'; break;

			case -8:  $error = '邮箱被占用！'; break;

			case -9:  $error = '手机格式不正确！'; break;

			case -10: $error = '手机被禁止注册！'; break;

			case -11: $error = '手机号被占用！'; break;

            case -12: $error = '手机号码必须由11位数字组成';break;

			case -20: $error = '请填写正确的姓名';break;

			case -21: $error = '用户名必须由字母、数字或下划线组成,以字母开头';break;

			case -22: $error = '用户名必须由6~30位数字、字母或下划线组成';break;

			case -31: $error = '密码错误';break;

			case -32: $error = '用户不存在或被禁用';break;

			case -41: $error = '身份证无效';break;

			default:  $error = '未知错误';

		}

		return $error;

	}



	/**

	* 领取礼包

	*/

	public function getGameGift() {	

		$mid = parent::is_login();;

		if($mid==0){

			echo  json_encode(array('status'=>'0','msg'=>'请先登录'));

			exit();

		}
       
        if(session('member_auth.nickname')=="Uc用户"){
          echo  json_encode(array('status'=>'0','msg'=>'Uc用户暂不支持'));
          exit();
        }
		$list=M('record','tab_gift_');

		$is=$list->where(array('user_id'=>$mid,'gift_id'=>$giftid));

		if($is) {   
			    $map['user_id']=$mid;
			    $map['gift_id']=$_POST['giftid'];
			    $msg=$list->where($map)->find();
            if($msg){
				$data=$msg['novice'];
				echo  json_encode(array('status'=>'1','msg'=>'no','data'=>$data));
	        }
	        else{			
				$bag=M('giftbag','tab_');				
				$giftid= $_POST['giftid'];
				$ji=$bag->where(array("id"=>$giftid))->field("novice,end_time")->find();
				if(empty($ji['novice'])){
					echo json_encode(array('status'=>'1','msg'=>'noc'));
				}
				else{
                    if($ji['end_time']-time()<0){
                        echo json_encode(array('status'=>'1','msg'=>'not'));
                    }else{
                    $at=explode(",",$ji['novice']);
                    $gameid=$bag->where(array("id"=>$giftid))->field('game_id')->find();
                    $add['game_id']=$gameid['game_id'];
                    $add['game_name']=get_game_name($gameid['game_id']);
                    $add['gift_id']=$_POST['giftid'];
                    $add['gift_name']=$_POST['giftname'];
                    $add['status']=1;
                    $add['novice']=$at[0];
                    $add['user_id'] =$mid;
                    $add['user_account']=get_user_account($mid);
                    $add['create_time']=strtotime(date('Y-m-d h:i:s',time()));
                    $list->add($add);
                    $new=$at;
                    if(in_array($new[0],$new)){
                        $sd=array_search($new[0],$new);
                        unset($new[$sd]);
                    }
                    $act['novice']=implode(",", $new);
                    $bag->where("id=".$giftid)->save($act);
                    echo  json_encode(array('status'=>'1','msg'=>'ok','data'=>$at[0]));
                    }
		    	}   
		    } 
		}

	}


	public function verify($vid=''){
		$config = array(
			'seKey'     => 'ThinkPHP.CN',   //验证码加密密钥
			'fontSize'  => 16,              // 验证码字体大小(px)
			'imageH'    => 42,               // 验证码图片高度
			'imageW'    => 107,               // 验证码图片宽度
			'length'    => 4,               // 验证码位数
			'fontttf'   => '4.ttf',              // 验证码字体，不设置随机获取
		);
        ob_clean();
        $verify = new \Think\Verify($config);
        $verify->entry($vid);
	}





}

