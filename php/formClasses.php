<?php

class Form {

	private $form_name;
	private $db_name;
	private $note_email;
	private $conf_email;
	private $form_email;
	private $data;
	private $form_html;
	private $form_js;
	private $form_js_required;
	private $form_css;
	private $form_php;
	private $form_php_email;
	private $form_php_required;
	private $element_list = [];


	public function __construct($data, $form_name, $db_name, $note_email, $conf_email){
		$this->data = $data;
		$this->form_name = $this->scrub_name($form_name);
		$this->db_name = $this->scrub_name($db_name);
		$this->note_email = $note_email;
		$this->conf_email = $conf_email;
		$this->create_form();
		if($this->conf_email == 'yes' && empty($this->form_email)){
			$response["success"] = false;
			$response["text"] = "Confirmation email set to yes. This requires a Text Input Element with the name email";
			die(json_encode($response));
		}
		$this->form_js = $this->create_js();
		$this->form_php = $this->create_php();
	}

	private function create_form(){
		$this->form_html .= $this->start_html();
		foreach($this->data as $row){
			$this->form_html .= $this->start_row_html();
			foreach($row->row as $containers){
				$this->form_html .= $this->start_container_html($containers->len);
				foreach($containers->containers as $element){
					if($element->data !== null){
						$element_class = $this->select_type($element);
						$this->element_list[] = $element_class;
						$this->form_html .= $element_class->html;
						$this->form_js_required .= $element_class->is_required();
						if($element_class->get_id() == 'email'){
							$this->form_email = true;
						}
					}
				}
				$this->form_html .= $this->end_div_html();
			}
			$this->form_html .= $this->end_div_html();
		}
		$this->form_html .= $this->end_html();
	}

	private function create_js(){
		return 

"(function($) {
	$(function() {
		setEvents();
	});
	
	function setEvents(){
		$('#fb-form').on('submit', function(e) {
			var event = e.originalEvent;
			event.preventDefault ? e.preventDefault() : event.returnValue = false;
			$(this).ajaxSubmit({
				url:		   'php/main.php',
				type:		   'POST',
				dataType:	   'JSON',
				beforeSubmit:  showRequest,
				success:       showResponse,
				error:		   showError
			});
			return false;
		});

		$('input, select').off('keydown');
		
		$('input, select').on('keydown', function(e) {
			if (e.keyCode == 13) {
				return false;
			}
		});
	}

	function showRequest(formData, jqForm, options){
		resetErrors();
		var noError = true;
		$('input[type=submit]').prop('disabled', true);

		" .

		$this->form_js_required
		
		. "

		if(noError){
			createMessage('success', 'Please wait form is being submitted');
		} else {
			$('input[type=submit]').prop('disabled', false);
		}
		return noError;
	}

	function showResponse(response, status, xhr, \$form){
		if(response.success){
			$('#fb-form')[0].reset();
			createMessage('success', response.text);
		} else {
			createMessage('error', response.text);
		}
		$('input[type=submit]').prop('disabled', false);
	}

	function showError(jqXHR, textStatus, errorThrown) {
		$('input[type=submit]').prop('disabled', false);
		createMessage('error', 'Problem submitting the form : ' +  jqXHR + ' | ' + textStatus + ' | ' + errorThrown);
	}

	function resetErrors(){
		$('.errorLabel').text('');
		$('.errorBorder').removeClass('errorBorder');
	}

	function checkInput(input, error){
		var errors = false;
		$(input).each(function(index, element){
			if($.trim($(element).val()) == ''){
				errors = true;
				$(element).addClass('errorBorder');
				$(element).parent().next('.errorLabel').text(error);
			}
		});
		return errors;
	}

	function setError(response){
		if(typeof response == 'string'){
			createMessage('error', response);
		} else {
			createMessage('error', 'Unknown Error Occured')
		}
	}

	function createMessage(type, message){
		var alert_type = null;
		var alert_msg = null;
		var html = null;
		if(type == 'success'){
			alert_type = 'success';
			alert_msg = 'Success!';
		} else {
			alert_type = 'danger';
			alert_msg = 'Error!';
		}

		html = ' \
		<div class=\"col-sm-6 col-sm-offset-3\"> \
			<div class=\"alert alert-' + alert_type + ' fade in\"> \
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a> \
				<strong>' + alert_msg + ' </strong><p class=\"inline\">' + message + '<p> \
			</div> \
		</div>';


		$('#message-div').html(html);
		window.scrollTo(0,0);
	}

})( jQuery );";
	}
	
	private function create_php(){
									$php =
'<?php
';
									if($this->db_name !== NULL){ $php .= '
	require "databaseQuery.php";
	
	//Create MySQL Database Object
	$db = new pdo_mysql();

	';
									$php .= 
	$this->get_post_data();
									}
									if($this->db_name !== NULL){ $php .= '
	//SQL Statements
	try {
		$result = $db->db_query("CREATE DATABASE IF NOT EXISTS ' . $this->db_name . '");
	} catch (PDOException $ex) {
		echo $ex->getMessage();
	}

	try {
		$db->db_new_connection("' . $this->db_name . '");
	} catch (PDOException $ex) {
		echo $ex->getMessage();
	}

	' . $this->get_sql_statements() . '
						
	';
									}
									
									if($this->note_email === NULL){ $php .= '
	$response["success"] = true;
	$response["text"] = "Successfully Submitted Form";
	die(json_encode($response));	
	';
									}
									
									if($this->note_email !== NULL){ $php .= '
	//Create and Send Email
	require_once "email.php";
	';
										$this->form_php_email = $this->create_php_email();
									}
	
									$php .= '
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	';
	
	return $php;
	}
	
	private function create_php_email(){
									$email = 
'<?php

	require "PHPMailer/PHPMailerAutoload.php";
	require "sendMail.php";
';
									if($this->conf_email !== NULL){ $email .= "
	\$addressArray = array(\$email, '{$this->note_email}'); ";
									} else { $email .= "
	\$addressArray = array('{$this->note_email}'); ";
									}
									
									$email .= '
	$subject = "' . $this->form_name . ' Confirmation Email";
	
	';	
	
	
	$email .= '
	$body = \'' .  
	$this->get_email_header();
	
	
	$email .= '
	<body>
		<table class="body">
			<tr>
				<td class="center" align="center" valign="top">
					<center>
						<table class="row header">
							<tr>
								<td class="center" align="center">
									<center>
										<table class="container">
											<tr>
												<td class="wrapper last">
													<table class="twelve columns">
														<tr>
															<td class="six sub-columns">
																<img src="http://placehold.it/200x50" />
															</td>
															<td class="six sub-columns last" align="right" style="text-align:right; vertical-align:middle;">
																<span class="template-label">HERO</span>
															</td>
															<td class="expander"></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</center>
								</td>
							</tr>
						</table>
						<br />
						<table class="container">
							<tr>
								<td>
	
									<table class="row">
										<tr>
											<td class="wrapper last">
												<table class="twelve columns">
													<tr>
														<td>
															<h1>Hi, Elijah Baily</h1>
															<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et.</p>
															<img width="580" height="300" src="http://placehold.it/580x300" />
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table class="row callout">
										<tr>
											<td class="wrapper last">
												<table class="twelve columns">
													<tr>
														<td class="panel">
															<p>Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae. <a href="#">Click it! »</a>
															</p>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table class="row">
										<tr>
											<td class="wrapper last">
												<table class="twelve columns">
													<tr>
														<td>
															<h3>Title Ipsum <small>This is a note.</small></h3>
															<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table class="row">
										<tr>
											<td class="wrapper last">
												<table class="three columns">
													<tr>
														<td>
															<table class="button">
																<tr>
																	<td>
																		<a href="#">Click Me!</a>
																	</td>
																</tr>
															</table>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table class="row footer">
										<tr>
											<td class="wrapper">
												<table class="six columns">
													<tr>
														<td class="left-text-pad">
															<h5>Connect With Us:</h5>
															<table class="tiny-button facebook">
																<tr>
																	<td>
																		<a href="#">Facebook</a>
																	</td>
																</tr>
															</table>
															<br />
															<table class="tiny-button twitter">
																<tr>
																	<td>
																		<a href="#">Twitter</a>
																	</td>
																</tr>
															</table>
															<br />
															<table class="tiny-button google-plus">
																<tr>
																	<td>
																		<a href="#">Google +</a>
																	</td>
																</tr>
															</table>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
											<td class="wrapper last">
												<table class="six columns">
													<tr>
														<td class="last right-text-pad">
															<h5>Contact Info:</h5>
															<p>Phone: 408.341.0600</p>
															<p>Email: <a href="/cdn-cgi/l/email-protection#8be3f8eee7efe4e5cbfff9eae5ffe4f9a5e8e4e6"><span class="__cf_email__" data-cfemail="e8809b8d848c8786a89c9a89869c879ac68b8785">[email&#160;protected]</span><script data-cfhash="f9e31" type="text/javascript">
	/* <![CDATA[ */!function(){try{var t="currentScript"in document?document.currentScript:function(){for(var t=document.getElementsByTagName("script"),e=t.length;e--;)if(t[e].getAttribute("data-cfhash"))return t[e]}();if(t&&t.previousSibling){var e,r,n,i,c=t.previousSibling,a=c.getAttribute("data-cfemail");if(a){for(e="",r=parseInt(a.substr(0,2),16),n=2;a.length-n;n+=2)i=parseInt(a.substr(n,2),16)^r,e+=String.fromCharCode(i);e=document.createTextNode(e),c.parentNode.replaceChild(e,c)}t.parentNode.removeChild(t);}}catch(u){}}()/* ]]> */</script></a>
															</p>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table class="row">
										<tr>
											<td class="wrapper last">
												<table class="twelve columns">
													<tr>
														<td align="center">
															<center>
																<p style="text-align:center;"><a href="#">Terms</a> | <a href="#">Privacy</a> | <a href="#">Unsubscribe</a>
																</p>
															</center>
														</td>
														<td class="expander"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
	
								</td>
							</tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			/* <![CDATA[ */
			(function() {
				try {
					var s, a, i, j, r, c, l = document.getElementsByTagName("a"),
						t = document.createElement("textarea");
					for (i = 0; l.length - i; i++) {
						try {
							a = l[i].getAttribute("href");
							if (a && a.indexOf("/cdn-cgi/l/email-protection") > -1 && (a.length > 28)) {
								s = "";
								j = 27 + 1 + a.indexOf("/cdn-cgi/l/email-protection");
								if (a.length > j) {
									r = parseInt(a.substr(j, 2), 16);
									for (j += 2; a.length > j && a.substr(j, 1) != "X"; j += 2) {
										c = parseInt(a.substr(j, 2), 16) ^ r;
										s += String.fromCharCode(c);
									}
									j += 1;
									s += a.substr(j, a.length - j);
								}
								t.innerHTML = s.replace(/</g, "&lt;").replace(/>/g, "&gt;");
								l[i].setAttribute("href", "mailto:" + t.value);
							}
						} catch (e) {}
					}
				} catch (e) {}
			})();
			/* ]]> */
		</script>
	</body>
	
	</html>\';';
	
									$email .= '
									
	$success = sendEmail($addressArray, $subject, $body, "");
	
	if($success === true){
		$response["success"] = true;
		$response["text"] = "Successfully Submitted Form";
		echo json_encode($response);
	} else {
		$response["success"] = false;
		$response["text"] = "Form Submitted. Error Sending Email.";
		echo json_encode($response);
	}';
	
	return $email;
		
	}
	
	private function get_email_header(){
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width" />
		<style>
			#outlook a {
				padding: 0;
			}
			body {
				width: 100%!important;
				min-width: 100%;
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
				margin: 0;
				padding: 0;
			}
			.ExternalClass {
				width: 100%;
			}
			.ExternalClass,
			.ExternalClass p,
			.ExternalClass span,
			.ExternalClass font,
			.ExternalClass td,
			.ExternalClass div {
				line-height: 100%;
			}
			#backgroundTable {
				margin: 0;
				padding: 0;
				width: 100%!important;
				line-height: 100%!important;
			}
			img {
				outline: none;
				text-decoration: none;
				-ms-interpolation-mode: bicubic;
				width: auto;
				max-width: 100%;
				float: left;
				clear: both;
				display: block;
			}
			center {
				width: 100%;
				min-width: 580px;
			}
			a img {
				border: none;
			}
			p {
				margin: 0 0 0 10px;
			}
			table {
				border-spacing: 0;
				border-collapse: collapse;
			}
			td {
				word-break: break-word;
				-webkit-hyphens: auto;
				-moz-hyphens: auto;
				hyphens: auto;
				border-collapse: collapse!important;
			}
			table,
			tr,
			td {
				padding: 0;
				vertical-align: top;
				text-align: left;
			}
			hr {
				color: #d9d9d9;
				background-color: #d9d9d9;
				height: 1px;
				border: none;
			}
			table.body {
				height: 100%;
				width: 100%;
			}
			table.container {
				width: 580px;
				margin: 0 auto;
				text-align: inherit;
			}
			table.row {
				padding: 0px;
				width: 100%;
				position: relative;
			}
			table.container table.row {
				display: block;
			}
			td.wrapper {
				padding: 10px 20px 0px 0px;
				position: relative;
			}
			table.columns,
			table.column {
				margin: 0 auto;
			}
			table.columns td,
			table.column td {
				padding: 0px 0px 10px;
			}
			table.columns td.sub-columns,
			table.column td.sub-columns,
			table.columns td.sub-column,
			table.column td.sub-column {
				padding-right: 10px;
			}
			td.sub-column,
			td.sub-columns {
				min-width: 0px;
			}
			table.row td.last,
			table.container td.last {
				padding-right: 0px;
			}
			table.one {
				width: 30px;
			}
			table.two {
				width: 80px;
			}
			table.three {
				width: 130px;
			}
			table.four {
				width: 180px;
			}
			table.five {
				width: 230px;
			}
			table.six {
				width: 280px;
			}
			table.seven {
				width: 330px;
			}
			table.eight {
				width: 380px;
			}
			table.nine {
				width: 430px;
			}
			table.ten {
				width: 480px;
			}
			table.eleven {
				width: 530px;
			}
			table.twelve {
				width: 580px;
			}
			table.one center {
				min-width: 30px;
			}
			table.two center {
				min-width: 80px;
			}
			table.three center {
				min-width: 130px;
			}
			table.four center {
				min-width: 180px;
			}
			table.five center {
				min-width: 230px;
			}
			table.six center {
				min-width: 280px;
			}
			table.seven center {
				min-width: 330px;
			}
			table.eight center {
				min-width: 380px;
			}
			table.nine center {
				min-width: 430px;
			}
			table.ten center {
				min-width: 480px;
			}
			table.eleven center {
				min-width: 530px;
			}
			table.twelve center {
				min-width: 580px;
			}
			table.one .panel center {
				min-width: 10px;
			}
			table.two .panel center {
				min-width: 60px;
			}
			table.three .panel center {
				min-width: 110px;
			}
			table.four .panel center {
				min-width: 160px;
			}
			table.five .panel center {
				min-width: 210px;
			}
			table.six .panel center {
				min-width: 260px;
			}
			table.seven .panel center {
				min-width: 310px;
			}
			table.eight .panel center {
				min-width: 360px;
			}
			table.nine .panel center {
				min-width: 410px;
			}
			table.ten .panel center {
				min-width: 460px;
			}
			table.eleven .panel center {
				min-width: 510px;
			}
			table.twelve .panel center {
				min-width: 560px;
			}
			.body .columns td.one,
			.body .column td.one {
				width: 8.333333%;
			}
			.body .columns td.two,
			.body .column td.two {
				width: 16.666666%;
			}
			.body .columns td.three,
			.body .column td.three {
				width: 25%;
			}
			.body .columns td.four,
			.body .column td.four {
				width: 33.333333%;
			}
			.body .columns td.five,
			.body .column td.five {
				width: 41.666666%;
			}
			.body .columns td.six,
			.body .column td.six {
				width: 50%;
			}
			.body .columns td.seven,
			.body .column td.seven {
				width: 58.333333%;
			}
			.body .columns td.eight,
			.body .column td.eight {
				width: 66.666666%;
			}
			.body .columns td.nine,
			.body .column td.nine {
				width: 75%;
			}
			.body .columns td.ten,
			.body .column td.ten {
				width: 83.333333%;
			}
			.body .columns td.eleven,
			.body .column td.eleven {
				width: 91.666666%;
			}
			.body .columns td.twelve,
			.body .column td.twelve {
				width: 100%;
			}
			td.offset-by-one {
				padding-left: 50px;
			}
			td.offset-by-two {
				padding-left: 100px;
			}
			td.offset-by-three {
				padding-left: 150px;
			}
			td.offset-by-four {
				padding-left: 200px;
			}
			td.offset-by-five {
				padding-left: 250px;
			}
			td.offset-by-six {
				padding-left: 300px;
			}
			td.offset-by-seven {
				padding-left: 350px;
			}
			td.offset-by-eight {
				padding-left: 400px;
			}
			td.offset-by-nine {
				padding-left: 450px;
			}
			td.offset-by-ten {
				padding-left: 500px;
			}
			td.offset-by-eleven {
				padding-left: 550px;
			}
			td.expander {
				visibility: hidden;
				width: 0px;
				padding: 0!important;
			}
			table.columns .text-pad,
			table.column .text-pad {
				padding-left: 10px;
				padding-right: 10px;
			}
			table.columns .left-text-pad,
			table.columns .text-pad-left,
			table.column .left-text-pad,
			table.column .text-pad-left {
				padding-left: 10px;
			}
			table.columns .right-text-pad,
			table.columns .text-pad-right,
			table.column .right-text-pad,
			table.column .text-pad-right {
				padding-right: 10px;
			}
			.block-grid {
				width: 100%;
				max-width: 580px;
			}
			.block-grid td {
				display: inline-block;
				padding: 10px;
			}
			.two-up td {
				width: 270px;
			}
			.three-up td {
				width: 173px;
			}
			.four-up td {
				width: 125px;
			}
			.five-up td {
				width: 96px;
			}
			.six-up td {
				width: 76px;
			}
			.seven-up td {
				width: 62px;
			}
			.eight-up td {
				width: 52px;
			}
			table.center,
			td.center {
				text-align: center;
			}
			h1.center,
			h2.center,
			h3.center,
			h4.center,
			h5.center,
			h6.center {
				text-align: center;
			}
			span.center {
				display: block;
				width: 100%;
				text-align: center;
			}
			img.center {
				margin: 0 auto;
				float: none;
			}
			.show-for-small,
			.hide-for-desktop {
				display: none;
			}
			body,
			table.body,
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			p,
			td {
				color: #222222;
				font-family: "Helvetica", "Arial", sans-serif;
				font-weight: normal;
				padding: 0;
				margin: 0;
				text-align: left;
				line-height: 1.3;
			}
			h1,
			h2,
			h3,
			h4,
			h5,
			h6 {
				word-break: normal;
			}
			h1 {
				font-size: 40px;
			}
			h2 {
				font-size: 36px;
			}
			h3 {
				font-size: 32px;
			}
			h4 {
				font-size: 28px;
			}
			h5 {
				font-size: 24px;
			}
			h6 {
				font-size: 20px;
			}
			body,
			table.body,
			p,
			td {
				font-size: 14px;
				line-height: 19px;
			}
			p.lead,
			p.lede,
			p.leed {
				font-size: 18px;
				line-height: 21px;
			}
			p {
				margin-bottom: 10px;
			}
			small {
				font-size: 10px;
			}
			a {
				color: #2ba6cb;
				text-decoration: none;
			}
			a:hover {
				color: #2795b6!important;
			}
			a:active {
				color: #2795b6!important;
			}
			a:visited {
				color: #2ba6cb!important;
			}
			h1 a,
			h2 a,
			h3 a,
			h4 a,
			h5 a,
			h6 a {
				color: #2ba6cb;
			}
			h1 a:active,
			h2 a:active,
			h3 a:active,
			h4 a:active,
			h5 a:active,
			h6 a:active {
				color: #2ba6cb!important;
			}
			h1 a:visited,
			h2 a:visited,
			h3 a:visited,
			h4 a:visited,
			h5 a:visited,
			h6 a:visited {
				color: #2ba6cb!important;
			}
			.panel {
				background: #f2f2f2;
				border: 1px solid #d9d9d9;
				padding: 10px!important;
			}
			.sub-grid table {
				width: 100%;
			}
			.sub-grid td.sub-columns {
				padding-bottom: 0;
			}
			table.button,
			table.tiny-button,
			table.small-button,
			table.medium-button,
			table.large-button {
				width: 100%;
				overflow: hidden;
			}
			table.button td,
			table.tiny-button td,
			table.small-button td,
			table.medium-button td,
			table.large-button td {
				display: block;
				width: auto!important;
				text-align: center;
				background: #2ba6cb;
				border: 1px solid #2284a1;
				color: #ffffff;
				padding: 8px 0;
			}
			table.tiny-button td {
				padding: 5px 0 4px;
			}
			table.small-button td {
				padding: 8px 0 7px;
			}
			table.medium-button td {
				padding: 12px 0 10px;
			}
			table.large-button td {
				padding: 21px 0 18px;
			}
			table.button td a,
			table.tiny-button td a,
			table.small-button td a,
			table.medium-button td a,
			table.large-button td a {
				font-weight: bold;
				text-decoration: none;
				font-family: Helvetica, Arial, sans-serif;
				color: #ffffff;
				font-size: 16px;
			}
			table.tiny-button td a {
				font-size: 12px;
				font-weight: normal;
			}
			table.small-button td a {
				font-size: 16px;
			}
			table.medium-button td a {
				font-size: 20px;
			}
			table.large-button td a {
				font-size: 24px;
			}
			table.button:hover td,
			table.button:visited td,
			table.button:active td {
				background: #2795b6!important;
			}
			table.button:hover td a,
			table.button:visited td a,
			table.button:active td a {
				color: #fff!important;
			}
			table.button:hover td,
			table.tiny-button:hover td,
			table.small-button:hover td,
			table.medium-button:hover td,
			table.large-button:hover td {
				background: #2795b6!important;
			}
			table.button:hover td a,
			table.button:active td a,
			table.button td a:visited,
			table.tiny-button:hover td a,
			table.tiny-button:active td a,
			table.tiny-button td a:visited,
			table.small-button:hover td a,
			table.small-button:active td a,
			table.small-button td a:visited,
			table.medium-button:hover td a,
			table.medium-button:active td a,
			table.medium-button td a:visited,
			table.large-button:hover td a,
			table.large-button:active td a,
			table.large-button td a:visited {
				color: #ffffff!important;
			}
			table.secondary td {
				background: #e9e9e9;
				border-color: #d0d0d0;
				color: #555;
			}
			table.secondary td a {
				color: #555;
			}
			table.secondary:hover td {
				background: #d0d0d0!important;
				color: #555;
			}
			table.secondary:hover td a,
			table.secondary td a:visited,
			table.secondary:active td a {
				color: #555!important;
			}
			table.success td {
				background: #5da423;
				border-color: #457a1a;
			}
			table.success:hover td {
				background: #457a1a!important;
			}
			table.alert td {
				background: #c60f13;
				border-color: #970b0e;
			}
			table.alert:hover td {
				background: #970b0e!important;
			}
			table.radius td {
				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				border-radius: 3px;
			}
			table.round td {
				-webkit-border-radius: 500px;
				-moz-border-radius: 500px;
				border-radius: 500px;
			}
			body.outlook p {
				display: inline!important;
			}
			@media only screen and (max-width: 600px) {
				table[class="body"] img {
					width: auto!important;
					height: auto!important;
				}
				table[class="body"] center {
					min-width: 0!important;
				}
				table[class="body"] .container {
					width: 95%!important;
				}
				table[class="body"] .row {
					width: 100%!important;
					display: block!important;
				}
				table[class="body"] .wrapper {
					display: block!important;
					padding-right: 0!important;
				}
				table[class="body"] .columns,
				table[class="body"] .column {
					table-layout: fixed!important;
					float: none!important;
					width: 100%!important;
					padding-right: 0px!important;
					padding-left: 0px!important;
					display: block!important;
				}
				table[class="body"] .wrapper.first .columns,
				table[class="body"] .wrapper.first .column {
					display: table!important;
				}
				table[class="body"] table.columns td,
				table[class="body"] table.column td {
					width: 100%!important;
				}
				table[class="body"] .columns td.one,
				table[class="body"] .column td.one {
					width: 8.333333%!important;
				}
				table[class="body"] .columns td.two,
				table[class="body"] .column td.two {
					width: 16.666666%!important;
				}
				table[class="body"] .columns td.three,
				table[class="body"] .column td.three {
					width: 25%!important;
				}
				table[class="body"] .columns td.four,
				table[class="body"] .column td.four {
					width: 33.333333%!important;
				}
				table[class="body"] .columns td.five,
				table[class="body"] .column td.five {
					width: 41.666666%!important;
				}
				table[class="body"] .columns td.six,
				table[class="body"] .column td.six {
					width: 50%!important;
				}
				table[class="body"] .columns td.seven,
				table[class="body"] .column td.seven {
					width: 58.333333%!important;
				}
				table[class="body"] .columns td.eight,
				table[class="body"] .column td.eight {
					width: 66.666666%!important;
				}
				table[class="body"] .columns td.nine,
				table[class="body"] .column td.nine {
					width: 75%!important;
				}
				table[class="body"] .columns td.ten,
				table[class="body"] .column td.ten {
					width: 83.333333%!important;
				}
				table[class="body"] .columns td.eleven,
				table[class="body"] .column td.eleven {
					width: 91.666666%!important;
				}
				table[class="body"] .columns td.twelve,
				table[class="body"] .column td.twelve {
					width: 100%!important;
				}
				table[class="body"] td.offset-by-one,
				table[class="body"] td.offset-by-two,
				table[class="body"] td.offset-by-three,
				table[class="body"] td.offset-by-four,
				table[class="body"] td.offset-by-five,
				table[class="body"] td.offset-by-six,
				table[class="body"] td.offset-by-seven,
				table[class="body"] td.offset-by-eight,
				table[class="body"] td.offset-by-nine,
				table[class="body"] td.offset-by-ten,
				table[class="body"] td.offset-by-eleven {
					padding-left: 0!important;
				}
				table[class="body"] table.columns td.expander {
					width: 1px!important;
				}
				table[class="body"] .right-text-pad,
				table[class="body"] .text-pad-right {
					padding-left: 10px!important;
				}
				table[class="body"] .left-text-pad,
				table[class="body"] .text-pad-left {
					padding-right: 10px!important;
				}
				table[class="body"] .hide-for-small,
				table[class="body"] .show-for-desktop {
					display: none!important;
				}
				table[class="body"] .show-for-small,
				table[class="body"] .hide-for-desktop {
					display: inherit!important;
				}
			}
		</style>
		<style>
			table.facebook td {
				background: #3b5998;
				border-color: #2d4473;
			}
			table.facebook:hover td {
				background: #2d4473!important;
			}
			table.twitter td {
				background: #00acee;
				border-color: #0087bb;
			}
			table.twitter:hover td {
				background: #0087bb!important;
			}
			table.google-plus td {
				background-color: #DB4A39;
				border-color: #CC0000;
			}
			table.google-plus:hover td {
				background: #CC0000!important;
			}
			.template-label {
				color: #ffffff;
				font-weight: bold;
				font-size: 11px;
			}
			.callout .panel {
				background: #ECF8FF;
				border-color: #b9e5ff;
			}
			.header {
				background: #999999;
			}
			.footer .wrapper {
				background: #ebebeb;
			}
			.footer h5 {
				padding-bottom: 10px;
			}
			table.columns .text-pad {
				padding-left: 10px;
				padding-right: 10px;
			}
			table.columns .left-text-pad {
				padding-left: 10px;
			}
			table.columns .right-text-pad {
				padding-right: 10px;
			}
			@media only screen and (max-width: 600px) {
				table[class="body"] .right-text-pad {
					padding-left: 10px!important;
				}
				table[class="body"] .left-text-pad {
					padding-right: 10px!important;
				}
			}
		</style>
		<style>
			table.facebook td {
				background: #3b5998;
				border-color: #2d4473;
			}
			table.facebook:hover td {
				background: #2d4473!important;
			}
			table.twitter td {
				background: #00acee;
				border-color: #0087bb;
			}
			table.twitter:hover td {
				background: #0087bb!important;
			}
			table.google-plus td {
				background-color: #DB4A39;
				border-color: #CC0000;
			}
			table.google-plus:hover td {
				background: #CC0000!important;
			}
			.template-label {
				color: #ffffff;
				font-weight: bold;
				font-size: 11px;
			}
			.callout .panel {
				background: #ECF8FF;
				border-color: #b9e5ff;
			}
			.header {
				background: #999999;
			}
			.footer .wrapper {
				background: #ebebeb;
			}
			.footer h5 {
				padding-bottom: 10px;
			}
			table.columns .text-pad {
				padding-left: 10px;
				padding-right: 10px;
			}
			table.columns .left-text-pad {
				padding-left: 10px;
			}
			table.columns .right-text-pad {
				padding-right: 10px;
			}
			@media only screen and (max-width: 600px) {
				table[class="body"] .right-text-pad {
					padding-left: 10px!important;
				}
				table[class="body"] .left-text-pad {
					padding-right: 10px!important;
				}
			}
		</style>
	</head>';
	}
	
	
	private function get_post_data(){
		$req_data = "";
		$non_req_data = "";
		$array_check = "";
		foreach($this->element_list as $element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				if($element->get_required() == false){
					$non_req_data .= 
		'$' . $eID . ' = empty($_POST["' . $element->get_id() . '"]) ? NULL : $_POST["' . $element->get_id() . '"];
	';
				} else {
					$req_data .=
		'empty($_POST["' . $element->get_id() . '"]) ? missing_data() : $' . $eID . ' = $_POST["' . $element->get_id() . '"];
	';	
				}
				if(is_a($element, 'Checkbox_Input') || is_a($element, 'Radio_Input')){
					$array_check .= 
		'if(is_array($' . $element->get_id() . ')){
		$' . $eID . ' = implode(",", $' . $eID . ');
	}';
				}
			}
		}
		
		$data = 
	'//Non Required Form Data
	' . $non_req_data . '
	//Required Form Data
	' . $req_data . '
	//Array Concat
	' . $array_check;
	
		return $data;
	}
	
	private function get_sql_statements(){
		$sql_statement = 
	'try {
		$db->db_query("INSERT INTO ' . $this->form_name;
		

		$sql_columns = " (submission_date, ";
		$sql_values = "VALUES (CURDATE(), ";
		$create_values = "(id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, ";
		$params = 'array(';
		
		$count = 1;
		foreach($this->element_list as $key=>$element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				$sql_columns .= $eID . ', ';
				$sql_values .= ':input' . $count . ', ';
				$create_values .= $eID . " {$element->db_type}, ";
				$params .= '":input' . $count . '" => $' . $eID . ', ';
				$count = $count + 1;
			}
		}
		
		$sql_columns = substr($sql_columns, 0, -2);
		$sql_values = substr($sql_values, 0, -2);
		$create_values = substr($create_values, 0, -2);
		$params = substr($params, 0, -2);
		
		$sql_columns .= ") ";
		$sql_values .= ')", ';
		$create_values .= ")";
		$params .= "));";
		
		$create_statement = 
	'try {
		$db->db_query("CREATE TABLE IF NOT EXISTS ' . $this->form_name . ' ' . $create_values . '");
	} catch (PDOException $ex) {
		echo $ex->getMessage();
	}
	
	';
		
		
		$sql_statement .= $sql_columns . $sql_values;
		
		$sql_statement .= $params . '
	} catch (PDOException $ex) {
		echo $ex->getMessage();
	}
	
	';
		
		return $create_statement . $sql_statement;
	}
	
	private function scrub_name($name){
		$name = preg_replace('/[^.[:alnum:]_-]/','_',trim($name));
		$name = preg_replace('/\.*$/','',$name);
		
		return $name;
	}
	
	private function start_html(){
		return
"<!DOCTYPE html>
	<head>
		<link href='css/bootstrap.min.css' rel='stylesheet'>
		<link href='css/main.css' rel='stylesheet'>
	</head>
	<body>
		<div id='message-div' class='row'></div>
		<form id='fb-form'>
";
	}
	
	private function end_html(){
		return
"
		</form>
		<script src='js/jquery-1.11.3.min.js'></script>
		<script src='js/jquery.form.min.js'></script>
		<script src='js/main.js'></script>
		<script src='js/bootstrap.min.js'></script>
	</body>
</html>";	
	}

	private function start_row_html(){
		return "
				<div id='formDiv' class='row'>";
	}

	private function start_container_html($length){
		return "
					<div class='col-sm-{$length} column'>";
	}
	private function end_div_html(){
		return "
					</div>";
	}

	private function select_type($element){
	    switch($element->type) {
	        case 'text':
	            $e = new Text_Input($element->data);
	            break;
	        case 'select':
	            $e = new Select_Input($element->data);
	            break;
	        case 'checkbox':
	            $e = new Checkbox_Input($element->data);
	            break;
	        case 'radio':
	            $e = new Radio_Input($element->data);
	            break;
	        case 'textarea':
	            $e = new Textarea_Input($element->data);
	            break;
	        case 'submit':
	            $e = new Submit_Element($element->data);
	            break;
	        case 'textElement':
	            $e = new Text_Element($element->data);
	            break;
	        default:
	            return null;
	    }
	    return $e;
	}

	public function get_html(){
		return $this->form_html;
	}

	public function get_js(){
		return $this->form_js;
	}
	
	public function get_php(){
		return $this->form_php;	
	}
	
	public function get_php_email(){
		return $this->form_php_email;	
	}
}

class Element {
	protected $id;
	protected $classes;
	protected $label;
	protected $required;
	protected $value;
	protected $user_element;
	public $html;
	
	public function is_required(){
		$class_list = explode(' ', $this->classes);

		foreach($class_list as $class){
			if($class == 'required'){
				$this->required = true;
				return 		
		"if(checkInput('#{$this->id}', 'Required Input')){
			noError = false;
		}
		";
			}
		}
		$this->required = false;
		return "";
	}
	
	public function get_id(){
		return $this->id;	
	}
	
	public function get_label(){
		return $this->label;	
	}
	
	public function get_value(){
		return $this->value;	
	}
	
	public function get_required(){
		return $this->required;	
	}
	
	public function get_classes(){
		return $this->classes;	
	}
	
	public function get_user_element(){
		return $this->user_element;	
	}
}

class Text_Input extends Element {
	private $placeholder;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;
		$this->user_element = true;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<input type='text' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->value}' placeholder='{$this->placeholder}'>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Select_Input extends Element {
	private $options;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->label = $data->label;
		$this->options = $data->options;
		$this->user_element = true;

		$this->create_element();

	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<select id='{$this->id}' name='{$this->id}' class='{$this->classes}'>
									{$this->create_options($this->options)}
								</select>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";

	}

	function create_options($options){
		$options_html = null;
		foreach($options as $option){
			$selected = ($option->selected === false) ? null : ' selected';
			$disabled = ($option->disabled === false) ? null : ' disabled'; 
			$options_html .= "<option name='{$option->text}' value='{$this->value}'{$selected}{$disabled}>{$option->text}</option>
									";
		}

		return $options_html;
	}
}

class Checkbox_Input extends Element {
	private $group_name;
	private $checkboxes;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label 		= $data->label;
		$this->id 			= str_replace('[]', '', $data->group_name);
		$this->group_name 	= str_replace('[]', '', $data->group_name);
		$this->checkboxes 	= $data->checkboxes;
		$this->user_element = true;

		$this->create_element();
	}

	function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								{$this->create_checkboxes($this->checkboxes)}
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}

	function create_checkboxes($checkboxes){
		$checkboxes_html = null;
		foreach($checkboxes as $checkbox){ 
			$checkboxes_html .= "<label class='{$checkbox->classes}'>
									<input type='checkbox' id='{$checkbox->id}' name='{$this->group_name}[]' value='{$checkbox->value}'> {$checkbox->name}
								</label>
								";
		}

		return $checkboxes_html;
	}
}

class Radio_Input extends Element {
	private $group_name;
	private $radios;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label 		= $data->label;
		$this->id 			= str_replace('[]', '', $data->group_name);
		$this->group_name 	= str_replace('[]', '', $data->group_name);
		$this->radios 		= $data->radio;
		$this->user_element = true;

		$this->create_element();
	}

	function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								{$this->create_radios()}
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}

	function create_radios(){
		$radios_html = null;
		foreach($this->radios as $radio){ 
			$radios_html .= "<label class='{$radio->classes}'>
									<input type='radio' id='{$radio->id}' name='{$this->group_name}[]' value='{$radio->value}'> {$radio->name}
								</label>
								";
		}

		return $radios_html;
	}
}

class Textarea_Input extends Element {
	private $text;
	private $placeholder;
	public $db_type = "text";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;
		$this->user_element = true;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<textarea rows='4' id='{$this->id}' name='{$this->id}' class='{$this->classes}' placeholder='{$this->placeholder}'>{$this->text}</textarea>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Text_Element extends Element {
	private $text;
	private $type;

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->type = $data->type;
		$this->user_element = false;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<div class='col-sm-12'>
								<{$this->type} id='{$this->id}' name='{$this->id}' class='{$this->classes}'>{$this->text}</{$this->type}>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Submit_Element extends Element {

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->user_element = false;
		
		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<input type='submit' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->value}'>
						</div>";
	}
}

?>