<html>

<head>
    <title>demo pay - by ksher</title>
</head>
<style>
    .header {
        width: 100%;
        font-size: 20px;
        font-weight: normal
    }

    .pay {
        width: 100%;
        display: block;
        float: left
    }

    .pay form {
        float: left;
        width: 80%
    }

    .group {
        width: 80%;
        margin: 15px;
        float: left
    }
    
    .group label {
        width: 25%;
        float: left;
        display: block;
    }

    .group div {
        float: left;
        display: block
    }
</style>

<body>
    <div class="pay">
        <div class="header">function:native_pay (C scan B) </div>
        <label>before genrate C scan B API, please check </label><a href=http://api.ksher.net/KsherAPI/dev/account_wallet_support.html>account wallet support </a>
        <form name="pay_form" action="./demo_pay.php" method="post">
            <div class="group">
                <label>mch_order_no</label>
                <div><input type="text" name="mch_order_no" value="<?php echo date("YmdHis", time()) . rand(100000, 999999); ?>" /></div>
            </div>
            <div class="group">
                <label>total_fee</label>
                <div><input type="text" name="total_fee" value="<?php echo 1; ?>" /></div>
            </div>
            <div class="group">
                <label>fee_type</label>
                <div>
                    <select name="fee_type">
                        <option value="THB">THB</option>
                    </select>
                </div>
            </div>
            <div class="group">
                <label>channel</label>
                <div>
                    <select name="channel">
                        <option value=promptpay>promptpay</option>
                        <option value=alipay>alipay</option>
                        <option value=wechat>wechat</option>
                        <option value=airpay>airpay</option>
                        <option value=truemoney>truemoney</option>
                    </select>
                </div>
            </div>
            <div class="group">
                <label>&nbsp;</label>
                <input type='hidden' name='action' value='native_pay' />
                <div><input type="submit" value="submit" /> </div>
            </div>
        </form>
    </div>


    <div class="pay">
        <div class="header">function:gateway_pay (Website)</div>
        <form name="pay_form" action="./demo_pay.php" method="post">
            <div class="group">
                <label>product_name</label>
                <div><input type="text" name="product_name" value="<?php echo 'ชื่อสินค้า'; ?>" /></div>
            </div>
            <div class="group">
                <label>mch_order_no</label>
                <div><input type="text" name="mch_order_no" value="<?php echo date("YmdHis", time()) . rand(100000, 999999); ?>" /></div>
            </div>
            <div class="group">
                <label>total_fee</label>
                <div><input type="text" name="total_fee" value="<?php echo 1; ?>" /></div>
            </div>
            <div class="group">
                <label>fee_type</label>
                <div>
                    <select name="fee_type">
                        <option value="THB">THB</option>
                    </select>
                </div>
            </div>
            <div class="group">
                <label>&nbsp;</label>
                <input type='hidden' name='action' value='gateway_pay' />
                <div><input type="submit" value="submit" /> </div>
            </div>
        </form>
    </div>

</body>

</html>

<script type="text/javascript">
    function apiSelectChange() {
        if (document.getElementById("receiver_type").value == 'BANK')
                {
                    document.getElementById("Menu_receiver_bank_code").style.display = "block";
                }
                else{
                    document.getElementById("Menu_receiver_bank_code").style.display = "none";
                }
            }
</script>