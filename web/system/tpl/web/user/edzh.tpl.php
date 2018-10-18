<?php
include_once("./live/config.php");

$Action=$_REQUEST['Action'];
$usernamesc = $uidc['username'];

if($usernamesc==''){
echo "<script language=\"javascript\">alert('请登录后操作');window.location.href='/';</script>";exit;
}

$uapiresult=mysql_query("select * from api_user where type='ibc' and uid='$uidsc'");
$uapi=mysql_fetch_array($uapiresult);
if($uapi['id']==''){
$Hostnames = "http://api.kemairui.cn/Ibc";
$CreateMember=curl_file_get_contents($Hostnames.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CreateMember&username='.$usernamesc);//exit($CreateMember);
$CreateMember = json_decode($CreateMember,true);
if($CreateMember['result']=="true"){
mysql_query("INSERT INTO `api_user` (`uid`, `type`, `time`) VALUES ('$uidsc','ibc','$begtime')");
}
}

if($Action=='dqje'){
if($_GET['xt']=='bb'){
$url1 = curl_file_get_contents($Host.'bbin?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CheckUsrBalance&username='.$usernamesc);//exit($url1);
$result1 = json_decode($url1,true);
$bb_money=$result1['data']['data'][0]['Balance'];
if($bb_money<=0){
	$bb_money=$bb_money;
}elseif ($bb_money==''){
	$bb_money='获取失败';
}
echo $bb_money;
}else if($_GET['xt']=='ibc'){
$url2 = curl_file_get_contents($Host.'ibc?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CheckUserBalance&username='.$usernamesc);//exit($url2);
$result2 = json_decode($url2,true);
$ibc_money=$result2['data']['Data'][0]['balance'];
if($ibc_money<=0){
	$ibc_money=$ibc_money;
}elseif ($ibc_money==''){
	$ibc_money='获取失败';
}
echo $ibc_money;
}else if($_GET['xt']=='ag'){
$url3 = file_get_contents($Host.'ag?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=GetBalance&member_id='.$usernamesc);//exit($url3);
$result3 = json_decode($url3,true);
$ag_money=$result3['data'];
if($ag_money<=0){
	$ag_money=$ag_money;
}elseif ($ag_money==''){
	$ag_money='获取失败';
}
echo $ag_money;
}else if($_GET['xt']=='mg'){
$url4 = file_get_contents($Host.'mg?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=GetBalance&username='.$usernamesc);//exit($url3);
$result4 = json_decode($url4,true);
$mg_money=$result4['Balance'];
if($mg_money<=0){
	$mg_money=$mg_money;
}elseif ($mg_money==''){
	$mg_money='获取失败';
}
echo $mg_money;
}
exit;
}

if($Action=='save'){
$transtype=$_REQUEST['transtype'];
$xt=$_REQUEST['xt'];
$amount=$_REQUEST['amount'];
if(!is_numeric($amount)){
	echo "<script language=\"javascript\">alert('操作金额必须是数字');history.go(-1);</script>";exit;
}

if($xt=="bb"){
	$xts="bbin";
}else{
	$xts=$xt;
}
$Hostname=$Host.$xts;

if($xt=="bb"){
$function="Transfer";
$huiyuanid="username";
$zhuanchuedu="remit";
$zhuanzhangfangshi="action";

if($transtype=="IN"){
$transtype="IN";
$jj="1";
}elseif($transtype=="OUT"){
$transtype="OUT";
$jj="0";
}

}elseif($xt=="pt"){
$function="Transfer";
$huiyuanid="player_name";
$zhuanchuedu="remit";
$zhuanzhangfangshi="action";

if($transtype=="IN"){
$transtype="IN";
$jj="1";
}elseif($transtype=="OUT"){
$transtype="OUT";
$jj="0";
}

}elseif($xt=="mg"){
$function="Transfer";
$huiyuanid="username";
$zhuanchuedu="amount";
$zhuanzhangfangshi="action";

if($transtype=="IN"){
$transtype="in";
$jj="1";
}elseif($transtype=="OUT"){
$transtype="out";
$jj="0";
}

}elseif($xt=="ag"){
$function="PrepareTransferCredit";
$huiyuanid="member_id";
$zhuanchuedu="amount";
$zhuanzhangfangshi="direction";

if($transtype=="IN"){
$transtype="1";
$jj="1";
}elseif($transtype=="OUT"){
$transtype="0";
$jj="0";
}

}elseif($xt=="ibc"){
$function="FundTransfer";
$huiyuanid="username";
$zhuanchuedu="amt";
$zhuanzhangfangshi="type";

if($transtype=="IN"){
$transtype="in";
$jj="1";
}elseif($transtype=="OUT"){
$transtype="out";
$jj="0";
}

}

if($amount<10){
	echo "<script language=\"javascript\">alert('操作金额不可小于10');window.location.href='edzh';</script>";exit;
}

if($amount>10000000){
	echo "<script language=\"javascript\">alert('操作金额不可大于10000000');window.location.href='edzh';</script>";exit;
}

if($jj=="0"){
if($xt=="bb"){
$url1 = curl_file_get_contents($Host.'bbin?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CheckUsrBalance&username='.$usernamesc);//exit($url1);
$result1 = json_decode($url1,true);
$bb_money=$result1['data']['data'][0]['Balance'];
$zze_money=$bb_money;
}elseif($xt=="ibc"){
$url2 = curl_file_get_contents($Host.'ibc?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CheckUserBalance&username='.$usernamesc);//exit($url1.$url2);
$result2 = json_decode($url2,true);
$ibc_money=$result2['data']['Data'][0]['balance'];
$zze_money=$ibc_money;
}

if($zze_money=='' and $zze_money!=0){
	echo "<script language=\"javascript\">alert('获取额度失败');window.location.href='edzh';</script>";exit;
}

$zjes=$zze_money-$amount;
if($zjes<0){
	echo "<script language=\"javascript\">alert('".$xt."额度不足');window.location.href='edzh';</script>";exit;
}

}else{
$zjes=$uidc['coin']-$amount;
if($zjes<0){
	echo "<script language=\"javascript\">alert('余额不足');window.location.href='edzh';</script>";exit;
}
}


$url = file_get_contents($Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function='.$function.'&'.$huiyuanid.'='.$usernamesc.'&'.$zhuanchuedu.'='.$amount.'&'.$zhuanzhangfangshi.'='.$transtype);
$result = json_decode($url,true);
if($result['result']=="true"){
	
	if($jj=="0"){
		mysql_query("UPDATE ".$prename."members SET coin=coin+$amount WHERE uid='$uidc[uid]'");
		mysql_query("insert into `api_log` set 
`uid`='$uidc[uid]',
`type`='$jj',
`xt`='$xt',
`coin`='$amount',
`usercoin`='$uidc[coin]',
`time`='$begtime',
`info`='".$xt."转出额度转换'
");
	}elseif($jj=="1"){
		mysql_query("UPDATE ".$prename."members SET coin=coin-$amount WHERE uid='$uidc[uid]'");
		mysql_query("insert into `api_log` set 
`uid`='$uidc[uid]',
`type`='$jj',
`xt`='$xt',
`coin`='$amount',
`usercoin`='$uidc[coin]',
`time`='$begtime',
`info`='".$xt."转入额度转换'
");
	}
echo "<script language=\"javascript\">alert('操作成功');window.location.href='edzh';</script>";exit;
}


echo "<script language=\"javascript\">alert('操作失败');window.location.href='edzh';</script>";exit;

}
?>
<div id="user-setting-dom" class="common container">
    <div class="head">
        <div class="name icon-user">额度管理</div>
    </div>
<style>
.common .body table {
    color: #1d1d1d;
    background-color: rgba(255, 255, 255, 0.94);
    border-left: 0px solid #ddd;
    border-bottom: 0px solid #ddd;
}
table {
    border-spacing: 0;
    border-collapse: inherit;
}
</style>
<script language="javascript">
jq();
function jq()
{
document.getElementById("dqmoney").value="正在读取...";
document.getElementById("online_channelyh").style.display="";
$.get("/user/edzh?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}

function chg(obj)
{
if(obj.options[obj.selectedIndex].value =="ibc"){
document.getElementById("dqmoney").value="正在读取...";
$.get("/game/stat/edzh?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}else{
document.getElementById("dqmoney").value="正在读取...";
$.get("/game/stat/edzh?Action=dqje&xt=ibc",function(data){
	document.getElementById("dqmoney").value=data;
});
}
}
</script>
    <div class="body">
	<form method="POST" action="?Action=save" class="body password">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 30%;text-align: right;">操作金额：</td>
                <td style="width: 70%;text-align: left;"><input type="text" name="amount" placeholder="请输入[操作金额]"></td>
            </tr>
			
			<tr>
                <td style="width: 30%;text-align: right;">操作方式：</td>
                <td style="width: 70%;text-align: left;"><select name="transtype" class="form-control" style="width: 200px;height: 39px;border: 1px solid #ddd;background-color: #f9f9f9;color: #666;">
			<option value="OUT">平台转出金额</option>
			<option value="IN">金额转入平台</option>
			</select></td>
            </tr>
			
			<tr>
                <td style="width: 30%;text-align: right;">操作平台：</td>
                <td style="width: 70%;text-align: left;"><select name="xt" onchange="chg(this)" class="form-control" style="width: 200px;height: 39px;border: 1px solid #ddd;background-color: #f9f9f9;color: #666;">
			<option value="ibc">沙巴体育</option>
			</select></td>
            </tr>
			
			<tr>
                <td style="width: 30%;text-align: right;">当前金额：</td>
                <td style="width: 70%;text-align: left;"><input type="text" name="dqmoney" id="dqmoney" readonly="readonly"></td>
            </tr>
			
			<tr>
                <td style="width: 30%;text-align: right;"></td>
                <td style="width: 70%;text-align: left;"><button type="submit" class="btn btn-blue" style="float: left;">提交</button></td>
            </tr>
        </table>
	</form>
	<div class="body password">
	<table class="table table-bordered table-hover">
<thead>
<tr class="text-center">
	<td>
		转换类型
	</td>
	<td>
		转换系统
	</td>
	<td>
		流动金额
	</td>
	<td>
		变化前余额
	</td>
	<td>
		转换时间
	</td>
</tr>
</thead>
<tbody>
<?php
$search=" WHERE uid='$uidsc' ";
$return=mysql_query("SELECT *  FROM `api_log` $search order by id desc  limit 100");
while($var=mysql_fetch_array($return)){
?>
<tr class="text-center">
	<td>
		<span><?php if($var['type']=='1'){echo '转入';}else{echo '转出';}?></span>
	</td>
	<td>
		<span><?=$var['xt']?></span>
	</td>
	<td>
		<span><?=$var['coin']?></span>
	</td>
	<td>
		<span><?=$var['usercoin']?></span>
	</td>
	<td>
		<span><?=date('m-d H:i:s', $var['time'])?></span>
	</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
    </div>
    <style>
        .layer-window {
            height: 520px;
            width: 400px;

        }

        .layer-window-content {
            padding: 15px;
        }

        .layer-window-header {
            color: #fff;
            height: 60px;
            background: rgba(0, 0, 0, 0.75);
            border-bottom: 3px solid #337ab7;
        }

        .layer-window-header h3 {
            line-height: 60px;
            margin: 0;
            padding-left: 20px;
            border-bottom: none;
        }

        .bank-item {
            margin-bottom: 15px;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .add-bank {
            cursor: pointer;
        }
    </style>
</div>