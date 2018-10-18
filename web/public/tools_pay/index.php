<?php
$sign = $_POST['sign'];
$merchant_code = $_POST['merchant_code'];
$bank_code = $_POST['bank_code'];
$order_no = $_POST['order_no'];
$order_amount = $_POST['order_amount'];
$service_type = $_POST['service_type'];
$input_charset = $_POST['input_charset'];
$notify_url = $_POST['notify_url'];
$interface_version = $_POST['interface_version'];

$sign_type = $_POST['sign_type'];
$order_time = $_POST['order_time'];
$product_name = $_POST['product_name'];

$client_ip = $_POST['client_ip'];
$extend_param = $_POST['extend_param'];
$extra_return_param = $_POST['extra_return_param'];

$pay_type = $_POST['pay_type'];
$product_code = $_POST['product_code'];
$product_desc = $_POST['product_desc'];
$return_url = $_POST['return_url'];
$product_num = $_POST['product_num'];
$show_url = $_POST['show_url'];
$redo_flag = $_POST['redo_flag'];
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body onLoad="document.dinpayForm.submit();">
<form name="dinpayForm" method="post" action="https://pay.dinpay.com/gateway?input_charset=UTF-8">
    <input type="hidden" name="sign" value="<?php echo $sign ?>"/>
    <input type="hidden" name="merchant_code" value="<?php echo $merchant_code ?>"/>
    <input type="hidden" name="bank_code" value="<?php echo $bank_code ?>"/>
    <input type="hidden" name="order_no" value="<?php echo $order_no ?>"/>
    <input type="hidden" name="order_amount" value="<?php echo $order_amount ?>"/>
    <input type="hidden" name="service_type" value="<?php echo $service_type ?>"/>
    <input type="hidden" name="input_charset" value="<?php echo $input_charset ?>"/>
    <input type="hidden" name="notify_url" value="<?php echo $notify_url ?>">
    <input type="hidden" name="interface_version" value="<?php echo $interface_version ?>"/>
    <input type="hidden" name="sign_type" value="<?php echo $sign_type ?>"/>
    <input type="hidden" name="order_time" value="<?php echo $order_time ?>"/>
    <input type="hidden" name="product_name" value="<?php echo $product_name ?>"/>
    <input Type="hidden" Name="client_ip" value="<?php echo $client_ip ?>"/>
    <input Type="hidden" Name="extend_param" value="<?php echo $extend_param ?>"/>
    <input Type="hidden" Name="extra_return_param" value="<?php echo $extra_return_param ?>"/>
    <input Type="hidden" Name="pay_type" value="<?php echo $pay_type ?>"/>
    <input Type="hidden" Name="product_code" value="<?php echo $product_code ?>"/>
    <input Type="hidden" Name="product_desc" value="<?php echo $product_desc ?>"/>
    <input Type="hidden" Name="product_num" value="<?php echo $product_num ?>"/>
    <input Type="hidden" Name="return_url" value="<?php echo $return_url ?>"/>
    <input Type="hidden" Name="show_url" value="<?php echo $show_url ?>"/>
    <input Type="hidden" Name="redo_flag" value="<?php echo $redo_flag ?>"/>
</form>
</body>
</html>
