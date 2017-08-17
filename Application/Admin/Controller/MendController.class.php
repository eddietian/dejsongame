<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class MendController extends ThinkController {
    
    public function lists($p=1){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        parent::lists("user",$p,$map);
    }

    public function edit($id = null)
    {
        if (IS_POST) {
            // if(empty($_POST['prmoote_id_to'])){$this->error('请选择推广员');}
            if ($_POST['promote_id'] == $_POST['prmoote_id_to']) {
                $this->error('没有更换操作');
            }
            $create = $_REQUEST;
            $map['id'] = $create['user_id'];
            $map_['user_id'] = $create['user_id'];
            $data['promote_id'] = $create['prmoote_id_to'];
            $data['promote_account'] = get_promote_name($create['prmoote_id_to']);
            $user_data = $data;
            $promote = M('promote', 'tab_')->where(array("id"=>I('prmoote_id_to')))->find();
            if ($promote['parent_id'] != "0") {
                $promote = M('promote', 'tab_')->where(array("id"=>$promote['parent_id']))->find();
            }
            $user_data['parent_id'] = $promote['id'];
            if(empty($promote)){
                $user_data['parent_id'] = 0;
            }
            $user = M('user', 'tab_')->where($map)->save($user_data);
            $user_ = M('UserPlay', 'tab_')->where($map_)->save($data);
            $spend = M('Spend', 'tab_')->where($map_)->where(array('is_check'=>array('in','1,2')))->save($data);
            $depost = M('Deposit', 'tab_')->where($map_)->save($data);
            $depost = M('Bind_spend', 'tab_')->where($map_)->save($data);
            $create['user_account'] = get_user_account($create['user_id']);
            $create['promote_account'] = get_promote_name($create['promote_id']);
            $create['promote_id_to'] = $create['prmoote_id_to'];
            $create['promote_account_to'] = get_promote_name($create['prmoote_id_to']);
            $create['create_time'] = time();
            $create['op_id'] = UID;
            $create['op_account'] = session('user_auth.username');
            $mend = M('mend', 'tab_')->add($create);
            if ($mend) {
                $this->success('补链成功', U('lists'), 2);
            }
        } else {
            $user = A('User', 'Event');
            $user_data = $user->user_entity($id);
            $user_data || $this->error("用户数据异常");
            $this->assign("data", $user_data);
            $this->meta_title = '编辑推广补链';
            $this->display();
        }
    }
    public function record($p=0)
    {
        if(isset($_REQUEST['promote_id'])){
            $map['promote_id']=$_REQUEST['promote_id'];
            unset($_REQUEST['promote_id']);
        }
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        parent::lists("Mend",$p,$map);
    }

}