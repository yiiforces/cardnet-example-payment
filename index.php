<?php
	$apiUrl     = 'https://lab.cardnet.com.do/servicios/tokens/v1/';
	$publicKey  = 'mfH9CqiAFjFQh_gQR_1TQG_I56ONV7HQ';
	$privateKey = '9kYH2uY5zoTD-WBMEoc0KNRQYrC7crPRJ7zPegg3suXguw_8L-rZDQ__';
	$order      = uniqid('order-');
	$amount     = rand(1, 9999) . '.' . rand(0,99);
	$user       = [
		'id'        => rand(10000, 999999),
		'name'      => 'Flavio Salas',
		'email'     => uniqid('email_') . '@gmail.com',
	];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Test Cardnet Payment</title>
	<script src="./assets/jquery.js" ></script>
	<script src="./assets/bootstrap.min.js"></script>
	<script src="<?= sprintf('%sScripts/PWCheckout.js?key=%s', $apiUrl, $publicKey); ?>"></script>
	<link rel="stylesheet" href="./assets/bootstrap.min.css">
</head>
<body>
	<script type="text/javascript">
		(function(){
			document.addEventListener("readystatechange", function(e){
				if(e.target.readyState === "complete")
				{
					var flagInitAjaxRequest = false;

					const NODE = $('#cardnet-container');
					NODE.fadeIn();

					if(typeof PWCheckout == "undefined")
					{
						NODE.html("No se ha cargado la libreria de pagos para cardnet. La pagina sera refrescada...");
						setTimeout(function(){
							window.location.reload(true);
						}, 1500);

						return;
					}

					var params = {
						"token"   : null,
						"order"   : "<?= $order ?>",
						"amount"  : "<?= $amount ?>",
						"user"    : <?= json_encode($user) ?>,
					};

					PWCheckout.SetProperties({
						"name"          : "AppName",
						"email"         : params.user.email,
						"image"         : "http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>assets/logo.png",
						"button_label"  : "Pagar #monto#",
						"description"   : "Descripcion de compra",
						"currency"      : "DOP",
						"amount"        : params.amount,
						"lang"          : "ESP",
						"form_id"       : "form-cardnet-checkout",
						"checkout_card" : "1",
						"empty"         : "false",
						"autoSubmit"    : "false",
					});

					PWCheckout.Bind("tokenCreated", function(token){
						flagInitAjaxRequest = true;
						params.token        = token;

						var modal = NODE.find('.modal').first();
						modal.find(".modal-title").text("test orden nro " + params.order );
						modal.find(".modal-footer").hide();
						modal.find(".modal-body").html("");
						modal.find(".modal-body").css({
							"background-image" 		: "url(./assets/loader.gif)",
							"background-position"	: "center",
							"background-repeat"		: "no-repeat",
						});

						modal.on("hidden.bs.modal", function(){
							window.location.reload(true);
						})

						modal.modal({backdrop: "static"});

						setTimeout(function(){
							$.ajax({
								url  : "curl.php",
								data : params,
								type : "POST",
							})
							.done(function(data){
								modal.find(".modal-body")
									.css("background-image", "none")
									.html(data);

								modal.find(".modal-footer").show();
							})
							.fail(function(data){
								console.log(data);
								alert("error");
								//window.location.reload(true);
							})
						}, 750);
					});

					PWCheckout.Bind("closed", function(){
						setTimeout(function(){
							if(flagInitAjaxRequest == false)
								window.location.reload(true);
						}, 255);
					});

					PWCheckout.AddActionButton("payment-btn");
				}
			});
		})();
	</script>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>Cardnet Test Payment</h1>
				<hr>
				<div id="cardnet-container" class="container-fluid" style="display:none">

					<div class="modal fade" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title"></h4>
								</div>
								<div class="modal-body" style="min-height:250px; max-height:450px; overflow:auto"></div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 text-center">
							<form  id="form-cardnet-checkout"  onsubmit="event.preventDefault()">
								<button id="payment-btn">Test Payment</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>


