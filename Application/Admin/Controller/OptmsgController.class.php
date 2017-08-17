<?php

namespace Admin\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class OptmsgController extends ThinkController {
    
    /**
	*列表
    */
    public function lists($p=0){
        parent::lists("optmsg",$p);
    }

}
