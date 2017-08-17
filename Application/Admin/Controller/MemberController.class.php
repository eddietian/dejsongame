<?php

namespace Admin\Controller;
use User\Api\MemberApi as MemberApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class MemberController extends ThinkController {

    /**
    *平台用户信息
    */
    public function user_info($p=0){
        $hav = '';
        if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
//                    $map['promote_id']=array("elt",0);
                    $hav = 'tab_user.promote_id = 0';
                    unset($_REQUEST['promote_name']);
                }else{
//                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                    $hav = 'tab_user.promote_id = '.get_promote_id(I('promote_name'));
                    unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['account'])){
//            $map['tab_user.account'] = array('like','%'.$_REQUEST['account'].'%');
            empty($hav) || $hav .= ' AND ';
            $hav .= "tab_user.account like '%".I('account')."%'";
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['register_way'])){
//            $map['register_way'] = $_REQUEST['register_way'];
            empty($hav) || $hav .= ' AND ';
            $hav .= 'tab_user.register_way ='.I('register_way');
            unset($_REQUEST['register_way']);
        }
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
            empty($hav) || $hav .= ' AND ';
            $hav .= 'tab_user.register_time BETWEEN '.strtotime(I('time-start')).' AND '.strtotime(I('time-end'));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            empty($hav) || $hav .= ' AND ';
            $hav .= 'tab_user.register_time BETWEEN '.strtotime(I('start')).' AND '.strtotime(I('end'));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(!empty(I('line_day'))){
            $day = strtotime(date('Y-m-d')) - intval(I('line_day')) * 86400;
            empty($hav) || $hav .= ' AND ';
            $hav .= $day.'> tab_user.login_time';
        }
        if(!empty(I('recharge_total'))){
            empty($hav) || $hav .= ' AND ';
            $hav .= 'recharge_total > '.I('recharge_total');
        }
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row = 10;
        //排序
        if(I('total_status') == 1){
            $order = 'recharge_total,tab_user.id';
        }elseif(I('total_status') == 2){
            $order = 'recharge_total desc,tab_user.id desc';
        }else{
            $order = 'tab_user.id desc';
        }
//        $count = M('user','tab_')->where($map)->count();
        //数据
        $data = M('user','tab_')->field('tab_user.*,IFNULL( IFNULL(sum(b.pay_amount),0)+IFNULL(sum(c.pay_amount),0),0) AS recharge_total')
            ->join('left join tab_spend AS c ON c.user_id = tab_user.id  AND c.pay_status = 1 AND c.pay_way != 0')
            ->join('left join tab_deposit AS b ON tab_user.id = b.user_id AND b.pay_status = 1')
            ->group('tab_user.id')
            ->having($hav)
            ->page($page,$row)
            ->order($order)
            ->select();
        //计数
        $count_sql = M('user','tab_')->field('tab_user.*,IFNULL( IFNULL(sum(b.pay_amount),0)+IFNULL(sum(c.pay_amount),0),0) AS recharge_total')
            ->join('left join tab_deposit AS b ON tab_user.id = b.user_id AND b.pay_status = 1')
            ->join('left join tab_spend AS c ON c.user_id = tab_user.id  AND c.pay_status = 1 AND c.pay_way != 0')
            ->group('tab_user.id')
            ->having($hav)
            ->order('tab_user.id desc')
            ->select(false);
        $count_sql = 'select count(*) as s from'.$count_sql.' a';
        $count = M()->query($count_sql);
//        var_dump($count_sql,$count);
        $count = $count['0']['s'];

        $model = M('Model')->getByName('user');
        //分页
        if($count > 10){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->assign('model', $model);
        $this->meta_title = '用户信息列表';
        $this->display();
    }

    /**
    *用户登陆记录
    */
    public function login_record($p=1){
        if(isset($_REQUEST['game_name'])){
            $map['game_id']=get_game_id($_REQUEST['game_name']);
            unset($_REQUEST['game_name']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['login_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['login_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['account'])){
            $map['user_account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        $extend=array();
        $extend['map']=$map;
        parent::lists("UserLoginRecord",$p,$extend['map']);
    }

    
    public function del($model = null, $ids=null){
        $map=array();
        if(isset($_REQUEST['id'])){
            $map['id']=$_REQUEST['id'];
            $data=M('user_login_record','tab_')
            ->where($map)->delete();
            $this->success('删除成功！',U('login_record'),2);
        }else{
            $this->error('请选择要操作的数据！');
        }
    }
    public function delprovide($ids)
    {
      $list=M("user_login_record","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
        $delete=$list->where($map)->delete();
        if($delete){
            $this->success("批量删除成功！",U("login_record"));
        }else{
        $this->error("批量删除失败！",U("login_record"));
        }
    }
    public function edit($id=null){
    	if(IS_POST){
            $member = new MemberApi();
            $data = $_REQUEST;
    		if(empty($data['password'])){unset($data['password']);}
            $res = $member->updateInfo($data);
            if($res !== false){
               if(C('UC_SET')==1&&!empty($data['password'])){
                $data_uc=$uc->get_uc(get_user_account($id));
                if(is_array($data_uc)){
                  $uc_id=$uc->uc_edit($data_uc[1],"11",$data['password'],"",1);
                }
            }   
                $this->success('修改成功',U('user_info'));
            }
            else{
                $this->error('修改失败');
            }
    		
    	}
    	else{
    		$user = A('User','Event');
    		$data=$user->user_entity($id);
            $this->assign('data',$data);
    		$this->display();
    	}
    	
    }
    public function chax($p=1)
    {
        $map['user_account']=get_user_account($_REQUEST['id']);
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row    = 10;
        //$new_model = D($name);
        $data = M("spend","tab_")
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count =M("spend","tab_")->where($map)->count();
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->display();
    }
    public function denglu($p=1){
        $map['user_id']=$_REQUEST['id'];
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row    = 10;
        //$new_model = D($name);
        $data = M("user_login_record","tab_")
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count =M("user_login_record","tab_")->where($map)->count();
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->display();
    }
    public function bind_balance($p=1){
        $map['user_id']=$_REQUEST['id'];
        $data = M("user_play","tab_")
            // 查询条件
            ->where($map)
            ->group('user_account,game_name')
            /* 执行查询 */
            ->select();
        $this->assign('list_data', $data);
        $this->display();
    }

}
