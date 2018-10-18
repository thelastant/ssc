<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>测试接口</title>
</head>
<?php
include('config.php');
include('Des3.class.php');
$des = new DES3($config['encKey']);
$rec = '';
if (isset($_GET['rec'])) {
    $rec = $_GET['rec'];
}
if ($rec == 'pay') {
    write_log('准备支付');
    $pay = array();
    $pay['merNo'] = $config['merNo']; #商户号
    $pay['netway'] = 'WX';  #WX 或者 ZFB
    $pay['random'] = (string)rand(1000, 9999);  #4位随机数    必须是文本型
    $pay['orderNum'] = date('YmdHis') . rand(1000, 9999);  #商户订单号
    $pay['amount'] = "10";  #默认分为单位 转换成元需要 * 100   必须是文本型
    $pay['goodsName'] = '测试支付';  #商品名称
    $pay['callBackUrl'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?rec=callback';  #通知地址 可以写成固定
    $pay['callBackViewUrl'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?rec=View';  #前台跳转 可以写成固定
    ksort($pay); #排列数组 将数组已a-z排序
    $sign = md5(Util::json_encode($pay) . $config['signKey']); #生成签名

    $pay['sign'] = strtoupper($sign); #设置签名
    $data = Util::json_encode($pay); #将数组转换为JSON格式
    write_log('通知地址：' . $pay['callBackUrl']);
    write_log('提交支付订单：' . $pay['orderNum']);

    $post = array('data' => $data);

    $return = wx_post($config['payUrl'], $post); #提交订单数据
    $row = json_decode($return, true); #将返回json数据转换为数组
    if ($row['stateCode'] !== '00') {
        write_log('系统错误,错误号：' . $row['stateCode'] . '错误描述：' . $row['msg']);
        echo '系统维护中.';
        exit();
    } else {
        if (is_sign($row, $config['signKey'])) { #验证返回签名数据
            $qrcodeUrl = $row['qrcodeUrl'];
            $orderNum = $row['orderNum'];
            $msg = $row['msg'];
            write_log('创建订单成功!订单号：' . $orderNum . '系统消息：' . $msg);
            header("location:" . 'qrcode.php?code=' . $qrcodeUrl);
            exit();
        }
    }
}

if ($rec == 'callback') { #订单通知
    write_log('接收到后台通知');
    $data = $_POST['data'];
    $arr = json_decode($data, 320);
    if (is_sign($arr, $config['signKey'])) {
        write_log('通知签名验证成功');

        $amount = (int)$arr['amount'];
        $amount = $amount / 100;
        $goodsName = $arr['goodsName'];
        $orderNum = $arr['orderNum'];
        $payDate = $arr['payDate'];
        $payResult = $arr['payResult'];
        if ($payResult == '00') {
            write_log('支付成功...订单号：' . $orderNum . ' 商品名称:' . $goodsName . ' 支付金额：' . $amount);
            echo '0'; #数据验证完成必须输出0告诉系统 通知完成。
            exit();
        }

    } else {
        write_log('通知签名验证失败');
    }

}

if ($rec == 'View') { #前台跳转
    echo '前台跳转';

}

if ($rec == 'remit') {
    write_log('准备代付');
    $pay = array();
    $pay['merNo'] = $config['merNo']; #商户号
    $pay['orderNum'] = date('YmdHis') . rand(1000, 9999);  #商户订单号
    $pay['amount'] = $des->encrypt("100");  #默认分为单位 转换成元需要 * 100 并且进行3DES加密
    $pay['bankCode'] = 'ICBC';  #银行名称代码 比如 爱存不存的ICBC
    $pay['bankAccountName'] = $des->encrypt('梁铭光');;  #结算姓名3DES加密
    $pay['bankAccountNo'] = $des->encrypt('6212261405007142466');;  #结算卡号3DES加密
    $pay['callBackUrl'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?rec=remit_callback';  #通知地址 可以写成固定

    ksort($pay); #排列数组 将数组已a-z排序
    $sign = md5(Util::json_encode($pay) . $config['signKey']); #生成签名
    $pay['sign'] = strtoupper($sign); #设置签名
    $data = Util::json_encode($pay); #将数组转换为JSON格式


    write_log('通知地址：' . $pay['callBackUrl']);
    write_log('提交代付订单：' . $pay['orderNum']);
    $post = array('data' => $data);
    $return = wx_post($config['remitUrl'], $post); #提交订单数据
    $row = json_decode($return, true); #将返回json数据转换为数组
    if ($row['stateCode'] !== '00') {
        write_log('系统错误,错误号：' . $row['stateCode'] . '错误描述：' . $row['msg']);
        echo '系统维护中.';
        exit();
    } else {
        if (is_sign($row, $config['signKey'])) { #验证返回签名数据
            if ($row['stateCode'] == '00') {
                $stateCode = $row['stateCode'];
                $msg = $row['msg'];
                $orderNum = $row['orderNum'];
                $amount = $row['amount'];
                $amount = $amount / 100;
                $string = '创建代付成功!订单号：' . $orderNum . ' 系统消息：' . $msg . ' 代付金额：' . $amount;
                write_log($string);
                echo $string;
                exit();
            }
        } else {
            write_log('返回签名验证失败!');

        }

    }

}

if ($rec == 'remit_callback') { #代付通知
    write_log('接收到代付后台通知');
    $data = $_POST['data'];
    $arr = json_decode($data, 320);
    if (is_sign($arr, $config['signKey'])) {
        write_log('代付通知签名验证成功');
        $remitResult = $arr['remitResult'];
        if ($remitResult == '00') {
            $amount = (int)$arr['amount'];
            $amount = $amount / 100;
            $orderNum = $arr['orderNum'];
            $remitDate = $arr['remitDate'];
            write_log('代付成功...订单号：' . $orderNum . ' 代付金额：' . $amount);
            echo '0'; #数据验证完成必须输出0告诉系统 通知完成。
            exit();
        } else {
            write_log('代付错误...');
        }

    } else {
        write_log('通知签名验证失败');
    }
}
function is_sign($row, $signKey)
{ #效验服务器返回数据
    $r_sign = $row['sign']; #保留签名数据
    $arr = array();
    foreach ($row as $key => $v) {
        if ($key !== 'sign') { #删除签名
            $arr[$key] = $v;
        }
    }
    ksort($arr);
    $sign = strtoupper(md5(Util::json_encode($arr) . $signKey)); #生成签名
    if ($sign == $r_sign) {
        return true;
    } else {
        return false;
    }

}

function wx_post($url, $data)
{ #POST访问
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    return $tmpInfo;
}


function write_log($str)
{ #输出LOG日志
    $str = date('Y-m-d H:i:s') . ' ' . $str . "\r\n";
    file_put_contents("test.log", $str, FILE_APPEND);
}


class Util
{
    static function json_encode($input)
    {
        if (is_string($input)) {
            $text = $input;
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            return '"' . $text . '"';
        } else if (is_array($input) || is_object($input)) {
            $arr = array();
            $is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
            foreach ($input as $k => $v) {
                if ($is_obj) {
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                } else {
                    $arr[] = self::json_encode($v);
                }
            }
            if ($is_obj) {
                return '{' . join(',', $arr) . '}';
            } else {
                return '[' . join(',', $arr) . ']';
            }
        } else {
            return $input . '';
        }
    }
}

?>

<body>
<a href="?rec=pay">充值接口</a>
<a href="?rec=remit">代付接口</a>
</body>
</html>
