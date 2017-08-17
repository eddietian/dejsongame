<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0026)game/ -->
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link href="<?php echo get_cover(C('PC_SET_ICO'),'path');?>" type="image/x-icon" rel="shortcut icon">
    <title><?php echo C('PC_SET_TITLE');?></title>
    <meta name="keywords" content="<?php echo C('WEB_SITE_KEYWORD');?>">
    <meta name="description" content="<?php echo C('WEB_SITE_DESCRIPTION');?>">

<!--    <?php if(CONTROLLER_NAME == 'Index' ): ?><title><?php echo seo_replace(C('media_index.seo_title'),'','media');?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_index.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_index.seo_description'),'','wap');?>">
    <?php elseif(CONTROLLER_NAME == 'Game' and ACTION_NAME == 'index' ): ?>
    <title><?php echo seo_replace(C('media_game_list.seo_title'),$data);?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_game_list.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_game_list.seo_description'),'','wap');?>">
    <?php elseif(CONTROLLER_NAME == 'Game' and ACTION_NAME == 'game_detail' ): ?>
    <title><?php echo seo_replace(C('media_game_detail.seo_title'),$data);?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_game_detail.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_game_detail.seo_description'),'','wap');?>">

    <?php elseif(CONTROLLER_NAME == 'Game' and ACTION_NAME == 'gift_list' ): ?>
    <title><?php echo seo_replace(C('media_gift_index.seo_title'),$vo);?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_gift_index.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_gift_index.seo_description'),'','wap');?>">
    <?php elseif(CONTROLLER_NAME == 'Article' and ACTION_NAME != 'index' ): ?>
    <title><?php echo seo_replace(C('media_news_list.seo_title'),$category);?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_news_list.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_news_list.seo_description'),'','wap');?>">
    <?php elseif(CONTROLLER_NAME == 'Activity' and ACTION_NAME != 'lists' ): ?>
    <title><?php echo seo_replace(C('media_activity_list.seo_title'),$category);?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_activity_list.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_activity_list.seo_description'),'','wap');?>">
    <?php else: ?>
    <title><?php echo seo_replace(C('media_index.seo_title'),'','wap');?></title>
    <meta name="keywords" content="<?php echo seo_replace(C('wap_index.seo_keyword'),'','wap');?>">
    <meta name="description" content="<?php echo seo_replace(C('wap_index.seo_description'),'','wap');?>"><?php endif; ?>-->


	<meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="mobile-agent" content="format=html5;url=">
    <meta http-equiv="mobile-agent" content="format=xhtml; url=">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" media="all" href="/Public/Media/css/base.css">
    <link rel="stylesheet" type="text/css" media="all" href="/Public/Media/css/index.css">
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="/Public/Media/js/function.js"></script>
    <script type="text/javascript" src="/Public/Media/js/vivo-common.js"></script>
	<script type="text/javascript" src="/Public/static/layer/layer.js" ></script>
</head>
<body>
<div class="topbar">
    <div class="wrap_w">
        <div class="topleft"><a href="<?php echo U('Game/game_list');?>">游戏中心</a> <span>|</span> <a href="<?php echo U('Game/gift_list');?>">礼包中心</a> </div>
        <div class="top">
            <!-- <div class="fl">
                <div id="xguc_login_script"></div>
            </div> -->
            <div class="login-register widthoutLogin">
              <div class="login-register-link logon-link hidden">
                <a href="#" class="trigger" tabindex="0" id="topShowName"></a>
                <a href="javascript:;" id="topLoginOut">退出登录</a>
              </div>
              <div class="login-register-link withoutLogon-link">
                <a href="javascript:;" class="top-login-link">亲，请登录</a>
                <a href="javascript:;" class="top-register-link">免费注册</a>
              </div>
            </div>
        </div>
    </div>
</div>

<div class="header">
    <div class="wrap_w clearfix">
        <div class="logo" style="background:none;">
            <a href="http://<?php echo C('PC_SET_LOGO_URL');?>"><img src="<?php echo get_cover(C('PC_SET_LOGO'),'path');?>" width="100%" height="100%" /></a>
        </div>
        <div class="header-search">
            <div class="searchbar">
                <form action="<?php echo U('game/game_list');?>" method="post">
					<input type="hidden" name="search_page" value="<?php echo CONTROLLER_NAME.'/'.ACTION_NAME;?>"/>
                    <input type="text"   class="searchstyle" name="search_key" value="" x-webkit-speech="" speech="">
                    <input type="submit" class="searchebtn" value="搜索">
                </form>
            </div>
            <!--  <div class="searchhot"><span>热门搜索词：</span>
				<?php if(is_array($aaa)): $i = 0; $__LIST__ = $aaa;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?><a href="/index.php/Search/index?keywords=<?php echo ($s["game_name"]); ?>"><?php echo ($s["game_name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div> -->
        </div>
        <div class="header-flash">
			<a  href="<?php echo ($single_img['url']); ?>"><img src="<?php echo ($single_img['data']); ?>"></a>
		</div>
    </div>
</div>

<div class="menu clearfix">
    <ul class="wrap_w">
        <li class="<?php if(CONTROLLER_NAME== Index): ?>cur<?php endif; ?>"><a href="<?php echo U('Index/index');?>">首  页</a></li>
        <li class="<?php if((ACTION_NAME == game_list) or (ACTION_NAME == game_detail) or ($search_page == game_list)): ?>cur<?php endif; ?>">
            <a href="<?php echo U('Game/game_list');?>">游戏中心</a>
        </li>
        <li class="<?php if((ACTION_NAME == gift_list) or ($search_page == gift_list)): ?>cur<?php endif; ?>">
            <a href="<?php echo U('Game/gift_list');?>">独家礼包</a>
        </li>
        <li class="<?php if(CONTROLLER_NAME== Article): ?>cur<?php endif; ?>"><a href="<?php echo U('Article/index?category=media');?>">新闻中心</a></li>
        <li class="<?php if(CONTROLLER_NAME== Service): ?>cur<?php endif; ?>"><a href="<?php echo U('Service/index');?>">客服中心</a></li>
    </ul>
</div>

<?php if(!(CONTROLLER_NAME== Index)): ?><link rel="stylesheet" type="text/css" media="all" href="/Public/Media/css/sub.css"><?php endif; ?>
<!-- 主体内容 -->

<div class="main wrap_w">
    <div class="col_1 clearfix">
        <div class="l">
            <div class="slide" id="slide">
                <ul class="slide-cont">
				<?php if(is_array($carousel)): $i = 0; $__LIST__ = $carousel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="slide-item" > 
                    <a href="<?php echo ($vo["url"]); ?>" target="_blank" title="<?php echo ($vo["title"]); ?>"> 
                        <img src="<?php echo (get_cover($vo["data"],'path')); ?>" alt="<?php echo ($vo["title"]); ?>">
                        <span class="slide-txt"> 
                            <span class="slide-tit"><?php echo ($vo["title"]); ?></span> 
                            <span class="slide-des"></span> 
                        </span> 
                        <span class="slide-mask"></span>
                    </a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>  
				</ul>
                <div class="slide-nav">
                    <a href="javascript:" class="">1</a>
                    <a href="javascript:" class="nownav">2</a>
                    <a href="javascript:" class="">3</a>
                    <a href="javascript:" class="">4</a>
                    <div class="nav-mask"></div>
                </div>
                <a href="javascript:;" title="" id="next"></a> 
                <a href="javascript:;" title="" id="prev"></a> 
            </div>
        </div>

        <div class="headlines"> 
			<?php $__CATE__ = D('Category')->getChildrenId(43);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,2); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><h3>
                <a href="<?php echo U('Article/detail?id='.$list['id']);?>" title="<?php echo ($list["title"]); ?>" target="_blank"><?php echo ($list["title"]); ?></a>
            </h3>
            <p>
                <span><?php echo ($list["description"]); ?>
                    <a href="<?php echo U('Article/detail?id='.$list['id']);?>">详情&gt;</a>
                </span>
            </p><?php endforeach; endif; else: echo "" ;endif; ?>
            <div class="hl3">
                <ul> 
					<?php $__CATE__ = D('Category')->getChildrenId(42);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,2); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><li>
                        <em class='ev'>公告</em>
                        <a href="<?php echo U('Article/detail?id='.$list['id']);?>" target="_blank" title="<?php echo ($list["title"]); ?>"><?php echo ($list["title"]); ?></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php $__CATE__ = D('Category')->getChildrenId(44);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,2); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><li>
                        <em class='ns'>活动</em>
                        <a href="<?php echo U('Article/detail?id='.$list['id']);?>" target="_blank" title="<?php echo ($list["title"]); ?>"><?php echo ($list["title"]); ?></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
        </div>

    </div>

    <!--推荐游戏-->
    <div class="col_2">

        <div class="title">
            <span>推荐游戏</span> <em><a href="<?php echo U('Game/game_list');?>">更多&gt;&gt;</a></em> 
        </div>
        <ul class="clearfix">
			<?php if(is_array($recommend)): $i = 0; $__LIST__ = $recommend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> 
                <a href="<?php echo U('Game/game_detail?id='.$vo['sibling_id'].'');?>" target="_blank">
                    <img src="<?php echo (get_cover($vo["cover"],'path')); ?>" class="gpic">
                </a>
                <p><?php echo preg_replace('/(\(.+)/i','',$vo['game_name']);?></p>
                <div class="img_hover"> 
                    <a href="<?php echo U('Game/game_detail?id='.$vo['sibling_id'].'');?>">
                        <div class="l">
                            <img src="<?php echo U('Game/dow_url_generate?game_id='.$vo['and_id']);?>">
                        </div>
                    </a>
                    <div class="r">
                        <a href="<?php echo U('Game/game_detail?id='.$vo['sibling_id'].'');?>">
                            <h3><?php echo preg_replace('/(\(.+)/i','',$vo['game_name']);?></h3>
                            <div class="info"></div>
                            <div class="star <?php echo ($vo["recommend_level"]); ?>">推荐:</div>
                        </a>
                        <?php if($vo["and_dow_address"] == "" && $vo["add_game_address"] == "" && $vo["ios_dow_address"] == "" && $vo["ios_game_address"] == "" ): ?><div style="color: white;"><br/>暂无下载</div><?php endif; ?>
                        <?php if($vo["and_dow_address"] == "" && $vo["add_game_address"] == ""): else: ?>
                            <div class="dl">
                                <a style="color: #0ab837;" href="<?php if($vo["dow_status"] == 0): ?>#<?php else: echo U('Down/down_file?game_id='.$vo['and_id'].'&type=1'); endif; ?>"> 安卓下载 </a><br/>
                            </div><?php endif; ?>
                        <?php if($vo["ios_dow_address"] == "" && $vo["ios_game_address"] == ""): else: ?>
                            <div class="dl">
                                <a style="color: #0ab837;" href="<?php if($vo["dow_status"] == 0): ?>#<?php else: echo U('Down/down_file?game_id='.$vo['ios_id'].'&type=2'); endif; ?>"> 苹果下载 </a>
                            </div><?php endif; ?>
                    </div>  
                    <div class="clear"></div>
                    <div class="notice"></div>
                </div>
                <div class="bg" style="opacity: 0.85;"></div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>



        <!--热门游戏-->

    <div class="col_3 clearfix">
        <div class="l">
            <div class="title"> <span>热门游戏</span> <em><a href="<?php echo U('Game/game_list');?>">更多&gt;&gt;</a></em> </div>
            <div class="mcs clearfix">
                <?php if(is_array($hot)): $i = 0; $__LIST__ = $hot;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl>
                    <dt>
                        <a href="<?php echo U('Game/game_detail?id='.$vo['sibling_id'].'');?>" target="_blank">
                            <img src="<?php echo (get_cover($vo["icon"],'path')); ?>">
                        </a>
                    </dt>
                    <dd>
                        <h3><a href="<?php echo U('Game/game_detail?id='.$vo['sibling_id'].'');?>" target="_blank"><?php echo preg_replace('/(\(.+)/i','',$vo['game_name']);?></a></h3>
                        <p class="gamestyle">
                            <span></span>
                            <span><?php echo ($vo['game_type_name']); ?></span>
                        </p>                    
                        <div class="gamedown">
                            <?php if($vo["and_dow_address"] == "" && $vo["add_game_address"] == "" && $vo["ios_dow_address"] == "" && $vo["ios_game_address"] == "" ): ?><a>暂无下载</a><?php endif; ?>
                            <?php if($vo["and_dow_address"] == "" && $vo["add_game_address"] == ""): else: ?>
                                <a  href="<?php if($vo["dow_status"] == 0): ?>#<?php else: echo U('Down/down_file?game_id='.$vo['and_id'].'&type=1'); endif; ?>"> 安卓下载 </a><?php endif; ?>
                            <?php if($vo["ios_dow_address"] == "" && $vo["ios_game_address"] == ""): else: ?>
                                <a  href="<?php if($vo["dow_status"] == 0): ?>#<?php else: echo U('Down/down_file?game_id='.$vo['ios_id'].'&type=2'); endif; ?>"> 苹果下载 </a><?php endif; ?>

                             <a href="<?php echo U('Game/gift_list?game_id='.$vo['id'].'');?>" class="gift">独家礼包</a>  
                        </div>
                    </dd>
                </dl><?php endforeach; endif; else: echo "" ;endif; ?>                            
            </div>
        </div>
        <div class="r">
            <div class="tabs">
                <ul class="tabNavigation clearfix">
                    <li class="big">礼包开服</li>
                    <li class="nor"><a href="#gift" class="on">游戏礼包</a></li>
                    <li class="nor"><a href="#kaifu">最新开服</a></li>
                </ul>
                <div class="tabcon" id="gift" style="display: block;">
                    <ul class="clearfix">
						<?php if(is_array($gift)): $i = 0; $__LIST__ = $gift;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="">
                            <div class="game-package-now">
                                <span><a href="" class="get">领取</a></span>
                                <a onclick="XGUC.get_game_gift(707, &#39; $vo['giftbag_name'] &#39;)" class="name">
                                     <?php echo ($vo["giftbag_name"]); ?>—<?php echo preg_replace('/(\(.+)/i','',$vo['game_name']);?>
                                </a>
                            </div>
                            <dl>
                                <dt>
                                    <a href="<?php echo U('Game/game_detail?id='.$vo['game_id'].'');?>" target="_blank">
                                        <img src="<?php echo (get_cover($vo["icon"],'path')); ?>">
                                    </a>
                                </dt>
                                <dd>
                                    <h3><a href="<?php echo U('Game/gift_list?game_id='.$vo['game_id'].'');?>" target="_blank"><?php echo ($vo['game_name']); ?></a></h3>
                                    <p><?php echo ($vo["giftbag_name"]); ?></p>
                                    <a class="package-btn" onclick="XGUC.get_game_gift(<?php echo ($vo['gift_id']); ?>, &#39;<?php echo ($vo['giftbag_name']); ?>&#39;)">领取</a> 
                                </dd>
                            </dl>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div class="tabcon" id="kaifu" style="display: none;">
                    <ul>
					<?php if(is_array($area)): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$at): $mod = ($i % 2 );++$i;?><li>
                            <dl>
                                <dt><a href="<?php echo U('Game/game_detail?id='.$at['game_id'].'');?>" target="_blank">
								<img src="<?php echo (get_cover($at["icon"],'path')); ?>"></a></dt>
                                <dd>
                                    <h3><a href="<?php echo U('Game/game_detail?id='.$at['game_id'].'');?>" target="_blank"><?php echo msubstr(substr(strip_tags($at['game_name']),0,-11),0,5,'utf-8',false);?></a></h3>
                                    <p>
                                        <?php echo ($at["server_name"]); ?><br>
                                        开服时间：<?php echo (date("m.d H:i",$at['start_time'])); ?>
									</p>
                                </dd>
                            </dl>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>                           
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--活动-->
    <div class="col_4 clearfix">
        <div class="l">
            <div class="title"> 
                <span>游戏活动</span> 
                <em><a href="<?php echo U('Article/lists/category/media_activity');?>" target="_blank">更多&gt;&gt;</a></em> 
            </div>
            <div class="lcons">
                <ul class="clearfix">
				<?php $__CATE__ = D('Category')->getChildrenId(44);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,3); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><li> <a href="<?php echo U('Article/detail?id='.$list['id']);?>" target="_blank">
					<img src="<?php echo (get_cover($list["cover_id"],'path')); ?>" class="gpic"></a>
                        <p><a href="<?php echo U('Article/detail?id='.$list['id']);?>" target="_blank"><?php echo ($list["title"]); ?></a></p>
                        <div class="cover"></div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
        </div>


        <div class="r">
            <div class="title"> <span>游戏客服</span></div>                    
            <div class="cons">
                <div class="mc">                    
                    <span><?php echo C('PC_SET_SERVER_TEL');?></span>
                    <p>工作时间：<?php echo C('PC_WORK_TIME');;?></p>
                </div>


                <div class="ot">
                    客服QQ：<?php echo C('PC_SET_SERVER_QQ');?><br> 
                    客服邮箱:<?php echo C('PC_SET_SERVER_EMAIL');?><br>
                    投诉邮箱:<?php echo C('PC_T_EMAIL');?><br>
                </div>
            </div>
        </div>
    </div>
</div>

<!--悬浮窗口-->
<div id="vivo-head">
	<div class="vivo-search">
      <div style="margin:0 auto; width:1000px; height:100px;"><div class="guide-app-link"><a href="<?php echo U('download');?>" target="_blank"><img src="http://pc.vlcms.com/Public/Media/images/foot-link_3625020.png" ></a></div></div>
		<div class="search-box">
			<a class="close"></a>
		</div>
	</div>
	<div class="vivo-nav cl" style="display:none">
		<div class="search-user">
			<a href="#" class="search"><b></b></a>
			<a href="#" class="user"><b></b></a>
		</div>
	</div>
</div>
    <script src="/Public/static/layer/extend/layer.ext.js" type="text/javascript"></script>
    <script>

        $(function(){
          if (IsPC()) {
          }else{
            window.location.href = "http://<?php echo ($_SERVER['SERVER_NAME']); ?>/mobile.php/";
          }
            var token = "";
            var flag = true;

            $('.title_bar_tab').on('click',function() {
                var that = $(this),target = that.attr('data-target');
                that.addClass('active').siblings().removeClass('active');
                $('#'+target).removeClass('hidden').siblings('.tabconent').addClass('hidden');
                if (flag && target == 'tabewm') {
                    QrLogin();
                    flag = false;
                }
            });
            function IsPC() {
            var userAgentInfo = navigator.userAgent;
            var Agents = ["Android", "iPhone",
                        "SymbianOS", "Windows Phone",
                        "iPad", "iPod"];
            var flag = true;
            for (var v = 0; v < Agents.length; v++) {
                if (userAgentInfo.indexOf(Agents[v]) > 0) {
                    flag = false;
                    break;
                }
            }
            return flag;
        }

            function QrLogin() {
                var ws = new WebSocket('ws://<?php echo ($_SERVER['HTTP_HOST']); ?>:1234');
                ws.onopen = function () {
                    $.ajax({
                        Type: 'POST',
                        dataType: 'json',
                        async: false,
                        url: "<?php echo U('QrLogin/getToken');?>",
                        success: function (res) {
                            token = res.token;
                            ws.send(token);
                            tabewm(token);
                        }
                    })
                };
                ws.onmessage = function (e) {
                    var res = e.data;
                    res = eval('(' + res + ')');
                    if (res.status == 1) {
                        $.ajax({
                            Type: 'POST',
                            dataType: 'json',
                            data: {token: res.token},
                            url: "<?php echo U('QrLogin/QrLogin');?>",
                            success: function (data) {
                                if (data.status == 1) {
                                    location.reload();
                                }
                            }
                        })
                    }
                };
            }

            function tabewm(token) {
                $.get('<?php echo U("QrLogin/inQrCode");?>',{token:token},function(data) {
                    $('#tabewm .tabewmsub').html(data);
                });
                setTimeout(function(){
                    QrLogin();
                },100000);

            }
        });

    </script>

<!--悬浮窗口结束-->


<div class="footer">
    <div class="area wrap_w">
        <div class="f-links"> <span>友情链接：</span>
	        <div class="roll">
	            <ul>
					<?php $_result=get_links();if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="<?php echo ($data["link_url"]); ?>" title="<?php echo ($data["title"]); ?>" ><?php echo ($data["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
	        </div>
        </div>
        <div class="infoot">
	        <!-- <div class="ifl" style="background:url(<?php echo get_cover(C('PC_SET_LOGO'),'path');?>) no-repeat 0 0;">  -->
	        <div class="ifl" style="background:none;">
                <img src="<?php echo get_cover(C('PC_SET_LOGO'),'path');?>" width="100%" height="100%" />
            </div>
	        <div class="ifc">
	            <p>
					<a href="<?php echo U('Article/news/type/about');?>" target="_blank" class="leftno">关于我们</a><span>|</span>
					<a href="<?php echo U('Article/news/type/collaborate');?>" target="_blank">商务合作</a><span>|</span>
					<a href="<?php echo U('Article/news/type/partner');?>" target="_blank">合作伙伴</a><span>|</span>
					<a href="<?php echo U('Article/news/type/supervise');?>" target="_blank">家长监督</a><span>|</span>
					<a href="<?php echo U('Service/index');?>" target="_blank"> 客服帮助</a><span>|</span>
					<!-- <a href="<?php echo U('About/index?type=74');?>" target="_blank"> 移动版</a> -->
				</p>
				<p>客服电话：<?php echo C('PC_SET_SERVER_TEL');?></p>
				<p>网站备案：<?php echo C('PC_SET_FOR_THE_RECORD');?>&nbsp;&nbsp;<?php if(C('PC_SET_FOR_THE_RECORD') == ''): ?>网络文化经营许可证编号：<?php echo C('PC_SET_LICENSE'); endif; ?></p>
				<p><?php echo C('PC_SET_COPYRIGHT');?></p>
	        </div>
	        <div class="ifr"  style="background:url(<?php echo get_cover(C('PC_SET_QRCODE'),'path');?>) no-repeat 0 0;background-size: 80px 80px">
	            <p>扫描二维码</p>
	            <p>关注官方微信</p>

	        </div>
        </div>
    </div>
</div>
<!-- 登陆弹窗 -->
<div class="g-login-pop g-pop " id="xglogin">
	<div class="m-mask"></div>
	<div class="m-box ">
		<a href="#" class="link-close"></a>
		<div class="form-box">	
			<form method="post" name="login" id="loginform" onsubmit="return false">
			<div class="title-bar">帐号登录</div>					
			<div id="errMsg" class="error-msg"></div>					
			<div class="input-optimize correct">						
				<input type="text" id="loginInputUname" name="account">						
				<i class="clear-text"></i>						
				<i class="icon-deco icon-acco"></i>						
				<div class="placeholder" style="display: none;">用户名/手机号</div>					
			</div>					
			<div class="input-optimize correct">						
				<input type="password" class="hidden">						
				<input type="password" id="loginPassword" name="password">						
				<i class="icon-deco icon-pass"></i>						
				<div class="placeholder" style="display: none;">密码</div>					
			</div>									
			<input type="submit" class="login-btn" value="登录" id="loginSubmit">
			<div class="bottom-bar">
                <a href="<?php echo U('Member/forget');?>">忘记密码</a>&nbsp;&nbsp;<a>|</a>&nbsp;&nbsp;
				<a href="#" id="goRegisterPop">注册新帐号</a>				
			</div>	
			</form>
		</div>	
	</div>
</div>
<!-- 登陆弹窗END -->

<!-- 注册弹窗 -->
<div class="g-register-pop g-pop" id="xgregister" >
	<div class="m-mask"></div>
	<div class="m-box m-register-box">
		<a href="#" class="link-close"></a>
		<h2 class="title-bar">欢迎注册溪谷游戏平台帐号<span id="notice"></span></h2>
		<div class="tab-trigger-bar">
			<a href="#" class="phone-trigger active" data-target="mPhoneRegisterPop">
				注册手机帐号
				<span class="arrow"></span>
			</a>
			<a href="#" class="user-trigger" data-target="mUsernameRegisterPop">
				注册用户名帐号
				<span class="arrow"></span>
			</a>
		</div>
		<div class="form-box">
			<!-- 手机注册 -->
			<div class="m-phone-register register-tab-box active" id="mPhoneRegisterPop">
				<form action="" id="mPhoneRegisterFormPop">
				<div>
					<label for="">手机号码</label>
					<div class="input-optimize">
						<input name="account" type="text" id="registerPhonePop">
						<i class="clear-text"></i>
						<div class="placeholder">请输入您的手机号码</div>
						<span class="error-msg"></span>
						<i class="icon-error"></i>
						<i class="icon-correct"></i>
					</div>
				</div>
				<div>
					<a href="#" class="get-checkcode disabled" id="getSafeCodePop">免费获取安全码</a>
                    <span class=""></span>
				</div>
				<div>
					<label for="">安全码</label>
					<div class="input-optimize">
						<input type="text" name="vcode" id="registerSafeCodePop">
                        <div class="placeholder">请输入安全码</div>
						<span class="error-msg"></span>
					</div>
				</div>
				<div>
					<label for="">密码</label>
					<div class="input-optimize">
					<input type="password" name="password" id="registerPhonePassPop">
					<div class="placeholder">6~30位数字、字母或特殊字符组成</div>
					<span class="error-msg"></span>
					</div>
				</div>
					<div>
					<a class="checkbox-optimize active">
					<i class="icon-agree" id="registerByPhoneAgreePop"></i>
					<input type="hidden" name="" value="">
					</a>
					<span href="#" id="registerByPhoneAgreeTxtPop" class="agree-txt active">我已阅读并同意《<a href="<?php echo U('Article/agreement');?>" target="_blank">溪谷手游用户注册协议</a>》
					</span>
					</div>
					<input type="submit" class="register-btn" value="注册" id="registerByPhoneSubmitPop">
				</form>
			</div>
			<!-- 手机注册END -->

			<!-- 账注册 -->
			<div class="m-username-register register-tab-box" id="mUsernameRegisterPop">
				<form action="" id="mNameRegisterFormPop">
					<div>
						<label for="">帐号</label>
						<div class="input-optimize">
							<input type="text" name="account" id="userNameByNamePop">
							<i class="icon-error"></i>
							<i class="icon-correct"></i>
							<div class="placeholder">6~30位数字、字母或下划线</div>
							<span class="error-msg"></span>
						</div>
					</div>
					<div>
						<label for="">设置密码</label>
						<div class="input-optimize">
							<input type="password" name="password" id="userPassPop" >
							<div class="placeholder">6~30位数字、字母或特殊字符组成</div>
							<span class="error-msg"></span>
						</div>
					</div>
					<div>
						<label for="">确认密码</label>
						<div class="input-optimize">
							<input type="password" name="repassword" id="userConfirmPasssPop">
							<span class="error-msg"></span>
						</div>
					</div>
					<div class="checkcodeInput">
						<label for="">验证码</label>
						<div class="input-optimize">
							<input type="text" name="verify" id="registerNameVcodePop">
							<img src="/index.php?s=/Media/Member/verify" alt="" class="checkcode">
							<span class="error-msg"></span>
						</div>
					</div>
					<div>
						<span class="checkbox-optimize active">
							<i class="icon-agree" id="registerByNameAgreePop"></i>
							<input type="hidden" name="" value="">
						</span>
						<span id="registerByNameAgreeTxtPop" class="agree-txt active">我已阅读并同意《<a href="<?php echo U('Article/agreement');?>" target="_blank">溪谷手游用户注册协议</a>》</span>
						<span class="agreeError"></span>
					</div>
					<input type="submit" class="register-btn" value="注册" id="registerByNameSubmitPop">
				</form>
			</div>
		</div>
		<div id="registerPopErr" class="registerpop-error"></div>
		<div class="box-bar">
			<span class="has-account">
				已有帐号？<a href="#" id="imeLogin" class="ime-login">立即登录</a>
			</span>
		</div>
	</div>
</div>
<!-- 注册弹窗END -->
<script src="/Public/Media/js/global.js"></script>
<script type="text/javascript" src="/Public/Media/js/jquery.zclip.min.js"></script>
</body>
</html>