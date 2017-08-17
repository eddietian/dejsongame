<?php

namespace Admin\Controller;

use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class StatController extends ThinkController
{

    // /**
    // *日常统计
    // */
    public function daily($value = '')
    {
        $stat = A('Stat', 'Event');
        $stat->spend_statistics();
        $stat->register_statistics();
        $stat->spend_statistics_year();
        $stat->register_statistics_year();
        $this->meta_title = '日常统计';
        $stat->display();
    }

    /**
     *支付方式统计
     */
    public function pay_way($type = null)
    {
        $pay_way = A('Payway', 'Event');
        switch ($type) {
            case '0':
                $pay_way->this_month();
                break;
            case '1':
                $pay_way->last_month();
                break;
            case '2':
                $pay_way->this_week();
                break;
            case '3':
                $pay_way->last_week();
                break;
            default:
                $pay_way->this_year();
                break;
        }
        $this->meta_title = '来款统计';

        $this->display();
    }

    //登录统计
    public function cpa_login()
    {
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $fields = array("promote_id");
        $key = "game_name";
        if (isset($_REQUEST['game_name'])) {
            if ($_REQUEST['game_name'] == '全部') {
                unset($_REQUEST['game_name']);
            } else {
                $map['tab_game.game_appid'] = get_game_appid($_REQUEST['game_name']);
                unset($_REQUEST['game_name']);
            }
        }
        if (isset($_REQUEST['promote_name'])) {
            if ($_REQUEST['promote_name'] == '全部') {
                unset($_REQUEST['promote_name']);
            } else if ($_REQUEST['promote_name'] == '自然注册') {
                $map['promote_id'] = array("elt", 0);
                unset($_REQUEST['promote_name']);
            } else {
                $map['promote_id'] = get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if (isset($_REQUEST[$key])) {
            $map[$key] = array('like', '%' . $_GET[$key] . '%');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach ($_REQUEST as $name => $val) {
            if (in_array($name, $fields)) {
                $map[$name] = $val;
            }
        }
        if (isset($_REQUEST['game_id'])) {
            $map["tab_game.id"] = $_REQUEST['game_id'];
            unset($_REQUEST['game_id']);
        }
        $row = 10;
        $data = M('user_play', 'tab_')
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_user_play.game_appid,count(tab_user_play.game_appid) as count,tab_game.id,promote_id")
            ->join("tab_game on tab_user_play.game_appid = tab_game.game_appid")
            // 查询条件
            ->group("tab_game.game_appid,promote_id")
            ->where($map)
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        $count = M('user_play', 'tab_')
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_user_play.game_appid,count(tab_user_play.game_appid) as count,tab_game.id,promote_id")
            ->join("tab_game on tab_user_play.game_appid = tab_game.game_appid")
            // 查询条件
            ->group("tab_game.game_appid,promote_id")
            ->where($map)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count = count($count);//D("play")->where($map)->count();
        //分页
        if ($count > $row) {
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('game_name', I("game_name"));
        $this->assign("reco", I("time"));
        $this->assign("list_data", $data);
        $this->assign("guild", I("account"));
        $this->meta_title = 'CPS登陆统计';
        $this->display();
    }
}
