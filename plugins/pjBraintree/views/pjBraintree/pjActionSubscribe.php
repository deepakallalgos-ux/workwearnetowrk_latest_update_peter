<?php
$tpl['arr']['amount'] = number_format($tpl['arr']['amount'], 2, '.', '');
$tmp = $tpl['arr']['amount'].$tpl['arr']['custom'].$tpl['arr']['notify_url'].$tpl['arr']['private_key'];
$hash = hash('sha256', $tmp);
?>
<form method="post" action="" style="display: inline" name="<?php echo $tpl['arr']['name']; ?>" id="<?php echo $tpl['arr']['id']; ?>">
	<input type="hidden" name="amount" value="<?php echo $tpl['arr']['amount']; ?>">
	<input type="hidden" name="custom" value="<?php echo $tpl['arr']['custom']; ?>">
	<input type="hidden" name="notify_url" value="<?php echo $tpl['arr']['notify_url']; ?>">
	<input type="hidden" name="cancel_url" value="<?php echo $tpl['arr']['cancel_url']; ?>">
	<input type="hidden" name="locale" value="<?php echo $tpl['arr']['locale']; ?>">
	<input type="hidden" name="first_name" value="<?php echo pjSanitize::html(@$tpl['arr']['first_name']); ?>">
	<input type="hidden" name="last_name" value="<?php echo pjSanitize::html(@$tpl['arr']['last_name']); ?>">
	<input type="hidden" name="is_subscription" value="1">
	<input type="hidden" name="hash" value="<?php echo $hash; ?>">
	<?php
	if (isset($tpl['arr']['submit']))
	{
        ?><button type="submit" class="<?php echo @$tpl['arr']['submit_class']; ?>"><?php echo htmlspecialchars(@$tpl['arr']['submit']); ?></button><?php
	}
	?>
</form>

<div class="mb-modal" id="modalBraintree">
    <div class="mb-dialog">
        <div class="mb-content">
            <div class="mb-header">
                <div class="mb-title">Braintree</div>
                <button type="button" class="mb-close"><span>x</span></button>
            </div>
            <div class="mb-body"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo PJ_INSTALL_URL . $controller->getConst('PLUGIN_CSS_PATH'); ?>app.css?v=1.2.0">
<script src="<?php echo PJ_INSTALL_URL . $controller->getConst('PLUGIN_JS_PATH'); ?>braintree.js?v=1.2.0"></script>
<script src="https://js.braintreegateway.com/web/dropin/1.36.0/js/dropin.min.js"></script>
<script>
(function() {
	var session_id,
		isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
		getSessionId = function () {
			return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
		},
		createSessionId = function () {
			if (getSessionId() === "") {
				sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
			}
		};

	if (isSafari) {
		createSessionId();
		session_id = getSessionId();
	} else {
		session_id = "";
	}

	function onSubmit(event) {
        event.preventDefault();
        var fd = new FormData(this),
            modal = document.querySelector("#modalBraintree"),
            modalInst = new BraintreeModal(modal);

        fetch("<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjBraintree&action=pjActionGetToken&session_id=" + session_id, {
            credentials: "include",
            method: "POST",
            body: fd
        }).then(function(response) {
            if (response.ok) {
                return response.text();
            }
            return Promise.reject(response);
        }).then(function(data) {
            modal.querySelector(".mb-body").innerHTML = data;
            modalInst.show();
        }).catch(function(error) {
            console.warn(error);
        });
        return false;
    }

    function onShown() {
        var modal = this,
            modalInst = new BraintreeModal(modal),
            body = modal.querySelector(".mb-body");

        var form = document.querySelector("#payment-form");
        braintree.dropin.create({
            authorization: modal.querySelector("#braintree-client-token").value,
            selector: "#bt-dropin"
        }, function (createErr, instance) {
            if (createErr) {
                console.log('Create Error', createErr);

                var submit = form.querySelector("button[type='submit']");
                if (submit) {
                    submit.disabled = true;
                }

                return;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                instance.requestPaymentMethod(function (err, payload) {
                    if (err) {
                        console.log('Request Payment Method Error', err);
                        return;
                    }

                    var submit = form.querySelector("button[type='submit']");
                    if (submit) {
                        submit.disabled = true;
                    }

                    // Add the nonce to the form and submit
                    document.querySelector('#nonce').value = payload.nonce;

                    var fd = new FormData(form);
                    fetch("<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjBraintree&action=pjActionGetCustomer&session_id=" + session_id, {
                        credentials: "include",
                        method: "POST",
                        body: fd
                    }).then(function(response) {
                        if (response.ok) {
                            return response.json();
                        }
                        return Promise.reject(response);
                    }).then(function(data) {

                        if (!(data && data.status)) {
                            return;
                        }

                        if (data.status === "OK") {

                            modalInst.hide();
                            window.location.href = data.url;

                        } else if (data.status === "FAIL") {

                            modalInst.hide();

                            var url = new URL("<?php echo PJ_INSTALL_URL; ?>index.php");
                            url.searchParams.set("controller", "pjBraintree");
                            url.searchParams.set("action", "pjActionGetTransaction");
                            url.searchParams.set("session_id", session_id);
                            url.searchParams.set("id", data.id);
                            url.searchParams.set("notify_url", data.notify_url);
                            url.searchParams.set("custom", data.custom);
                            url.searchParams.set("hash", data.hash);

                            fetch(url).then(function(response) {
                                if (response.ok) {
                                    return response.text();
                                }
                                return Promise.reject(response);
                            }).then(function(data) {
                                body.innerHTML = data;
                                modalInst.show();
                            }).catch(function(error) {
                                console.warn(error);
                            });

                        } else if (data.status === "ERR") {

                            modalInst.hide();
                            fetch("<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjBraintree&action=pjActionGetToken&session_id=" + session_id + "&tm=" + data.tm, {
                                credentials: "include",
                                method: "POST"
                            }).then(function(response) {
                                if (response.ok) {
                                    return response.text();
                                }
                                return Promise.reject(response);
                            }).then(function(data) {
                                body.innerHTML = data;
                                modalInst.show();
                            }).catch(function(error) {
                                console.warn(error);
                            });
                        }
                    }).catch(function(error) {
                        console.warn(error);
                    });
                });
            });
        });

        new BraintreeDemo({
            formID: 'payment-form'
        });
    }

    var form = document.querySelector("#pjOnlinePaymentForm_braintree");
    if (form) {
        form.addEventListener("submit", onSubmit);
    }

    var modal = document.querySelector("#modalBraintree");
    if (modal) {
        modal.addEventListener("shown", onShown);
    }

})();
</script>