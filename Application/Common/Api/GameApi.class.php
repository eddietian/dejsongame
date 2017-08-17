<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class GameApi {
	public function game_login($uid,$game_id,$pid=0){

		$this->add_user_login_record($uid,$game_id);
		$this->set_user_promote($uid,$game_id);
		$this->add_user_play($uid,$game_id,$pid);
	 	$game = M('GameSet',"tab_");
		$map['game_id'] = $game_id;
		$game_data = $game->where($map)->find();
	    $data['user_id']    = $uid;//uid同步平台uid唯一值,
	    $data['game_appid'] = $game_data['game_pay_appid'];//同步平台账号
	    $data['email']      = $game_data['agent_id'];//同步平台账号
	    $data['new_time']   = time();
	    $data['key']		= $game_data['game_key'];
	    $data['sign']       = MD5(http_build_query($data));
	    unset($data['key']);
	    //md5("user_id={$data['user_id']}&game_appid={$game_data['game_pay_appid']}&email={$data['email']}&new_time={$data['new_time']}&key=mengchuang");
	    $_loginUrl = $game_data['login_notify_url']."?".http_build_query($data);
	    return $_loginUrl;
	}

	public function game_pay_notify($param=null){
		$game = M('GameSet',"tab_");
		$map['game_id'] = $param['game_id'];
		$game_data = $game->where($map)->find();
		if(empty($game_data)){ $this->error_record("未找到指定游戏数据"); return false;}
		if(empty($game_data['pay_notify_url'])){$this->error_record("未设置游戏支付通知地址"); return false;}
		//$md5_sign = md5($param['out_trade_no'].$param['price']."1".$param['extend']."mengchuang");
		$data = array(
			"source"       => "梦创科技",
			"trade_no"     => $param['extend'],
			"out_trade_no" => $param['pay_order_number'],
			"amount"       => $param['pay_amount'] * 100,
			"game_appid"   => $game_data['game_pay_appid'],
			"key"		   => $game_data['game_key'],	
		);
		$data["sign"] = MD5(urldecode(http_build_query($data)));
		unset($data['key']);
		$_payUrl = $game_data['pay_notify_url']."?".http_build_query($data);
		$result = $this->post($_payUrl);
		return $result;
	}
	//登陆记录
	public function add_user_login_record($uid,$game_id)
	{
		$user=get_user_entity($uid);
		$game=game_entity_data($game_id);
		$data['game_id']=$game_id;
		$data['game_name']=$game['game_name'];
		$data['user_id']=$uid;
		$data['user_account']=$user['account'];
		$data['user_nickname']=$user['nickname'];
		$data['login_time']=time();
		$data['login_ip']=get_client_ip();
		$data['type']=1;
		M("UserLoginRecord","tab_")->add($data);
	}
	//玩家记录
	 public function add_user_play($user_id,$game_id,$pid=0){
        //实例化 play
        $user_play = M("play","tab_user_");
        $play_map['user_id'] = $user_id;
        $play_map['game_id'] = $game_id;
        $user=get_user_entity($user_id);
        $game=game_entity_data($game_id);
        $pid=$user['promote_id'];//如果1推广员的用户走的2推广员的链接，这条记录还应该是1推广员的
        $play = $user_play->where($play_map)->find();
        if(empty($play)){
            $play_data["user_id"] = $user_id;
            $play_data["user_account"] =$user['account'];
            $play_data["user_nickname"] =$user['nickname'];
            $play_data["game_appid"] = $game['game_appid'];
            $play_data["game_id"] = $game['id'];
            $play_data["game_name"] = $game['game_name'];
            $play_data["area_id"] = 0;
            $play_data["area_name"] = "";
            $play_data["role_id"] = 0;
            $play_data["role_name"] = "";
            $play_data["role_level"] = 0;
            $play_data["balance"] = 0;
            $play_data["promote_id"]=$user['promote_id'];//推广id
            $play_data["promote_account"] = $user['promote_account'];//推广姓名
            $user_play->add($play_data);
        }

    }

    //判断推广注册的用户有没有玩游戏 如没有吧当前游戏写入
    public  function set_user_promote($uid,$game_id){
    	$map['id']=$uid;
    	$user=M("User","tab_")->where($map)->find();
    	if($user['promote_id']>0&&$user['fgame_id']==0){
    		M("user","tab_")->where($map)->setField(array('fgame_id'=>$game_id,'fgame_name'=>get_game_name($game_id)));	
   		}
    }


	public function error_record($msg=""){
		\Think\Log::record($msg);
	}

	/**
	*post提交数据
    */
    protected function post($url){
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
    }

}