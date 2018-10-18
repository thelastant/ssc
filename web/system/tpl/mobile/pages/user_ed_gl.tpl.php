<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<?php
include_once("./live/config.php");

$Action=$_REQUEST['Action'];
$usernamesc = $uidc['username'];

if($usernamesc==''){
echo "<script language=\"javascript\">alert('请登录后操作');window.location.href='/';</script>";exit;
}
?>
<user_ed_gl class="app-page">
    <div class="settings">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">额度管理</h1>
            </div>
        </header>
        <style>
            .mark {
                display: none
            }
        </style>
<script language="javascript">
jq();
function jq()
{
document.getElementById("dqmoney").value="正在读取...";
$.get("/live/api.php?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}

function chg(obj)
{
if(obj.options[obj.selectedIndex].value =="ibc"){
document.getElementById("dqmoney").value="正在读取...";
$.get("/live/api.php?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}else{
document.getElementById("dqmoney").value="正在读取...";
$.get("/live/api.php?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}
}
</script>
        <div id="payOutApp" class="content container" style="background: #fff;">
            <div class="ey-panel-content">
                <div class="well">
                    <form action="?Action=save" class="form form-inline" method="post">
                        <div class="form-group">
                            <label for="" class="control-label">操作金额</label>
                            <input type="text" class="form-control" name="amount" value="" placeholder="请输入[操作金额]">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">操作方式</label>
                            <select name="transtype" id="">
                                <option value="OUT">平台转出金额</option>
								<option value="IN">金额转入平台</option>
                            </select>
                        </div>
						<div class="form-group">
                            <label for="" class="control-label">操作平台</label>
                            <select name="xt" onchange="chg(this)" id="">
                                <option value="ibc">沙巴体育</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">当前金额</label>
                            <input class="form-control" type="text" name="dqmoney" id="dqmoney" readonly="readonly">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn primary btn-block">提交</button>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="table-box">
                        <div class="row table-head">
                            <div class="phone-3 column">类型</div>
                            <div class="phone-3 column">平台</div>
                            <div class="phone-3 column">金额</div>
                            <div class="phone-3 column">时间</div>
                        </div>
<?php
$search=" WHERE uid='$uidsc' ";
$return=mysql_query("SELECT *  FROM `api_log` $search order by id desc  limit 100");
while($var=mysql_fetch_array($return)){
?>
                        <div class="row table-body">
                            <div class="phone-3 column">
                                <p><?php if($var['type']=='1'){echo '转入';}else{echo '转出';}?></p>
                            </div>
							<div class="phone-3 column">
                                <p><?=$var['xt']?></p>
                            </div>
							<div class="phone-3 column">
                                <p class="text-success"><?=$var['coin']?></p>
                            </div>
                            <div class="phone-3 column">
                                <p><?=date('m-d H:i:s', $var['time'])?></p>
                            </div>
                        </div>
<?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</user_ed_gl>
