<?php

namespace Media\Controller;
use OT\DataDictionary;
use User\Api\MemberApi;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends BaseController {

	//系统首页
    public function index(){
        $this->nav();
        $this->slide();
    	$this->recommend();
    	$this->hot();
    	$this->gift();
    	$this->area();
        $this->display();
    }

    public function nav() {
        $navinfo = $this->getHtmlContent(array("HOME_NAV_NEWGAME", "HOME_NAV_ANDROIDGAME"));

        if (empty($navinfo)) {
            return false;
        }

        $gameinfoids = array();
        //拿到gameid
        foreach ($navinfo as $key => $v) {
            $ids = explode(",", $v['content']);
            $navinfo[$key]['ids'] = $ids;
            $gameinfoids = array_merge($gameinfoids,$ids);
        }

        //拿到游戏信息
        $gameinfo = $this->getGameByGameids($gameinfoids);

        foreach ($navinfo as $key => $v) {
            foreach ($v['ids'] as $kk => $gameid) {
                $navinfo[$key]['ids'][$kk] = $gameinfo[$gameid];
            }
        }

        $this->assign("nav_newgame", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 0, 10));
        $this->assign("nav_newgame_other", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 10, 20));


        $this->assign("nav_androidgame", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 0, 10));
        $this->assign("nav_androidgame_other", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 10, 20));


        $this->assign("nav_iosgame", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 0, 10));
        $this->assign("nav_iosgame_other", array_slice($navinfo['HOME_NAV_NEWGAME']['ids'], 10, 20));



    }


    public function slide(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 1; #首页轮播图广告id
        $carousel = $adv->where($map)->order('sort ASC')->select();
        $this->assign("carousel",$carousel);
    }

    /***
	*推荐游戏
    */
    public function recommend(){

    	$model = array(
    		'm_name'=>'Game',
    		'prefix'=>'tab_',
    		'map'   =>array('game_status'=>1,'recommend_status'=>1),
    		'field' =>true,
    		'order' =>'sort asc',
                'group' =>'sibling_id',
    		'limit' =>4,
    	);
    	$reco = parent::list_data($model);
        $reco=game_merge($reco,$model['map']);
    	$this->assign('recommend',$reco);
    }

    /***
	*热门游戏
    */
    public function hot(){
    	$model = array(
    		'm_name'=>'Game',
    		'prefix'=>'tab_',
    		'map'   =>array('game_status'=>1,'recommend_status'=>2),
    		'field' =>true,
    		'order' =>'sort asc',
                'group' =>'sibling_id',
    		'limit' =>9,
    	);
    	$hot = parent::list_data($model);
        $hot = game_merge($hot,$model['map']);
    	$this->assign('hot',$hot);
    }

    /***
	*游戏礼包
    */
    public function gift(){
    	$model = array(
    		'm_name'=>'Giftbag',
    		'prefix'=>'tab_',
    		'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,giftbag_type,tab_game.icon,tab_giftbag.create_time',
    		'join'	=>'tab_game on tab_giftbag.game_id = tab_game.id',
    		'map'   =>array('game_status'=>1),
    		'order' =>'create_time desc',
    		'limit' =>9,
    	);
    	$gift = parent::join_data($model);
    	$this->assign('gift',$gift);
    }

    /***
	*游戏区服
    */
    public function area(){
    	$model = array(
            'm_name'=>'server',
            'prefix'=>'tab_',
            'field' =>'tab_server.*,tab_game.icon,tab_game.cover',
            'join'	=>'tab_game on tab_server.game_id = tab_game.id',
            'map'   =>array('game_status'=>1),
            'order' =>'create_time desc',
            'limit' =>9,
    	);
    	$area = parent::join_data($model);
    	$this->assign('area',$area);
    }

    public function download(){
        $map1['name'] = '联运APP';
        $app=M('app','tab_')->where($map1)->find();
        $this->assign('app',$app);
         $cate_id  = 1;
         $group_id = 5;
          $type   =   C('CONFIG_GROUP_LIST');
        $map['status'] = 1;
        $map['category'] = $cate_id;
        $map['group'] = $group_id;
        $list   =   M("Config")->where($map)->field('id,name,title,extra,value,remark,type')->order('sort')->select();
/*        header("Content-type:text/html;charset=utf-8");
        var_dump($list);
        exit;*/
        $picture=M('picture','sys_');
        $map1['id']=C("DL_RIGHT");
        $pic5=$picture->where($map1)->find();
        $map2['id']=C("DL_TITLE");
        $pic2=$picture->where($map2)->find();
        $this->assign('pic5',$pic5);
        $this->assign('pic2',$pic2);
        $this->assign('list',$list);
        $this->display();
    }

    public function qrcode($url='pc.vlcms.com',$level=3,$size=4){
        $map['name'] = '联运APP';
        $data_url = M('app','tab_')->field('file_url')->where($map)->find();
        $data_url = substr($data_url['file_url'],1);
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别 
        $matrixPointSize = intval($size);//生成图片大小 
        // $url = "http://".$_SERVER['HTTP_HOST']."/Uploads/APP/xgsy.apk";
        $url = "http://".$_SERVER['HTTP_HOST'].$data_url;
        //var_dump($url);exit;
        //生成二维码图片 
        //echo $_SERVER['REQUEST_URI'];
        $object = new \QRcode();
        echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   
    }

}