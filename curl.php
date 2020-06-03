<?php
	$apiUrl     = 'https://lab.cardnet.com.do/servicios/tokens/v1/';
	$publicKey  = 'mfH9CqiAFjFQh_gQR_1TQG_I56ONV7HQ';
	$privateKey = '9kYH2uY5zoTD-WBMEoc0KNRQYrC7crPRJ7zPegg3suXguw_8L-rZDQ__';

	$amount     = $_POST['amount']  ?? '1';
	$realAmount = number_format($amount, 2, '.', '');
	$amount     = preg_replace('<\.|,>', '', $realAmount);

	$dataPost = [
		'TrxToken'       => $_POST['token']['TokenId'],
		'Order'          => $_POST['order']  ,
		'Amount'         => $amount  ,
		'Currency'       => 'DOP',
		'UniqueID'       => md5($_POST['order']) ,
		'AdditionalData' => json_encode($_POST['user']),
		'CustomerIP'     => $_SERVER['REMOTE_ADDR'],
		'DataDO'         => ['Invoice' => $_POST['order'] ,'Tax' => '000'],
		'Capture'        => 'true',
		'Tip' 			 => '000',
	];


    try{
    	file_put_contents('./logs/send-data.json', json_encode($dataPost));
    }
    catch(\Exception $e){

    }



	$curl = curl_init();
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataPost) );
    curl_setopt($curl, CURLOPT_URL, $apiUrl . 'api/purchase');
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Access-Control-Allow-Origin: https://www.cms-do.net/',
        'Authorization: Basic ' . $privateKey,
        'Content-Type: application/json',
        'Accept:application/json',
    ]);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($curl);
    curl_close($curl);

    if(!$result)
    	exit('<h2 class="text-center">Connection Failure</h2>');

    try{
    	file_put_contents('./logs/response-data.json', $result);
    }
    catch(\Exception $e){
    }

    // response data html table:
    $token    = $_POST['token'];
    $result   = json_decode($result, true);
    $Response = $result['Response'];

	$order = [];
	$order['id']    = $_POST['order'];
	$order['user']  = $_POST['user'];
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h2>Result Payment</h2>
			<div class="table-responsive">
				<table class="table table-bordered">
					<!--token-->
					<thead>
						<tr>
							<th colspan="2">Cardnet into Token</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>key</th>
							<th>value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>ApiUrl</td>
							<td><?= $apiUrl ?></td>
						</tr>
						<tr>
							<td>Public key</td>
							<td><?= $publicKey ?></td>
						</tr>

						<tr>
							<td>Private key</td>
							<td><?= $privateKey ?></td>
						</tr>
						<tr>
							<td>TokenId</td>
							<td><?= @$token['TokenId']; ?></td>
						</tr>
						<tr>
							<td>Created</td>
							<td><?= @$token['Created']; ?></td>
						</tr>
						<tr>
							<td>Brand</td>
							<td><?= @$token['Brand']; ?></td>
						</tr>
						<tr>
							<td>Last4</td>
							<td><?= @$token['Last4']; ?></td>
						</tr>
						<tr>
							<td>CardExpMonth</td>
							<td><?= @$token['CardExpMonth']; ?></td>
						</tr>
						<tr>
							<td>CardExpYear</td>
							<td><?= @$token['CardExpYear']; ?></td>
						</tr>
					</tbody>
					<!--info order-->
					<thead>
						<tr>
							<th colspan="2">Ecommerce Info order</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>key</th>
							<th>value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Order Id</td>
							<td><?= @$order['id']; ?></td>
						</tr>
						<tr>
							<td>Ecommerce user id</td>
							<td><?= @$order['user']['id']; ?></td>
						</tr>
						<tr>
							<td>Ecommerce user name</td>
							<td><?= @$order['user']['name']; ?></td>
						</tr>
						<tr>
							<td>Ecommerce user email</td>
							<td><?= @$order['user']['email']; ?></td>
						</tr>
					</tbody>

					<!--Response payment-->
					<thead>
						<tr>
							<th colspan="2">Cardnet Response payment</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>key</th>
							<th>value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>PurchaseId</td>
							<td><?= @$Response['PurchaseId']; ?></td>
						</tr>
						<tr>
							<td>Order</td>
							<td><?= @$Response['Order']; ?></td>
						</tr>
						<tr>
							<td>TransactionID</td>
							<td><?= @$Response['Transaction']['TransactionID']; ?></td>
						</tr>
						<tr>
							<td>Transaction Created</td>
							<td><?= @$Response['Transaction']['Created']; ?></td>
						</tr>
						<tr>
							<td>Transaction Status</td>
							<td><?= @$Response['Transaction']['Status']; ?></td>
						</tr>
						<tr>
							<td>Transaction Description</td>
							<td><?= @$Response['Transaction']['Description']; ?></td>
						</tr>
						<tr>
							<td>Transaction ApprovalCode</td>
							<td><?= @$Response['Transaction']['ApprovalCode']; ?></td>
						</tr>
					</tbody>

					<!--Response payment info-->
					<thead>
						<tr>
							<th colspan="2">Payment info</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>key</th>
							<th>value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td >Currency</td>
							<td><?= @$Response['Currency']; ?></td>
						</tr>
						<tr>
							<td >Amount</td>
							<td><?= @$Response['Amount'] . " (real amount ". $realAmount ." )"; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>





<?php
	/*
	return;
    echo '<pre>';
    echo '<hr> Data Response:';
    print_r(json_decode($result, true));

    echo '<hr> Data post:';
    print_r($_POST);
    echo '</pre>';
	*/
