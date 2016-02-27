<?php

class PaypalGateway
{

  protected $params;
  protected $controller;

  public function __construct($params = array())
  {
    $this->params = array_merge(sfConfig::get('app_Payment_PaypalGateway'), $params);
    $this->controller = sfContext::getInstance()->getController();
  }

  public function isSuccess($data)
  {
    return $data['VK_SERVICE'] == '1101';
  }

  /*
   * Add Mac data field
   */

  public function addMac($data)
  {
    return $data;
  }

  /**
   *
   * @return String, payment form submit url
   */
  public function getSubmitUrl()
  {
    return $this->controller->genUrl('@payment\paypalSet');
  }

  public function formatFormField($name, $value)
  {
    return '<input type="hidden" value="' . $value . '" name="' . $name . '">';
  }

  private function doHttpPost($methodName_, $nvpStr_)
  {
    // Set up your API credentials, PayPal end point, and API version.
    $API_UserName = $this->params['username'];
    $API_Password = $this->params['password'];
    $API_Signature = $this->params['signature'];
    $API_Endpoint = $this->params['set_url'];

    $version = urlencode('63.0');

    // Set the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turn off the server and peer verification (TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the API operation, version, and API signature in the request.
    $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

    // Set the request as a POST FIELD for curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Get response from the server.
    $httpResponse = curl_exec($ch);

    if (!$httpResponse)
    {
      exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
    }

    // Extract the response details.
    $httpResponseAr = explode("&", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value)
    {
      $tmpAr = explode("=", $value);
      if (sizeof($tmpAr) > 1)
      {
        $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
      }
    }

    if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr))
    {
      exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    }

    return $httpParsedResponseAr;
  }

  public function setExpessCheckout()
  {
    // Set request-specific fields.
    $paymentAmount = urlencode($this->params['amount']);
    $currencyID = urlencode('USD'); // or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
    $paymentType = urlencode('Sale'); // or 'Sale' or 'Order'

    $returnURL = $this->params['success_url'];
    $cancelURL = $this->params['cancel_url'];
    $solutionType = urlencode('Sole');
    $landingPage = urlencode('Billing');
    $paymentDesc = 'Sensorsix Membership payments '.$this->params['amount'].' USD';
    
    // Add request-specific fields to the request string.
    $nvpStr = "&PAYMENTREQUEST_0_AMT=$paymentAmount&ReturnUrl=$returnURL&CANCELURL=$cancelURL&PAYMENTREQUEST_0_PAYMENTACTION=$paymentType&PAYMENTREQUEST_0_CURRENCYCODE=$currencyID&SOLUTIONTYPE=$solutionType&LANDINGPAGE=$landingPage&PAYMENTREQUEST_0_DESC=$paymentDesc&NOSHIPPING=1";

    // Execute the API operation; see the PPHttpPost function above.
    $httpParsedResponseAr = $this->doHttpPost('SetExpressCheckout', $nvpStr);

    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
    {
      // Redirect to paypal.com.
      $this->params['token'] = urldecode($httpParsedResponseAr["TOKEN"]);
      $payPalURL = $this->params['do_url'] . "&token=".$this->params['token'];
      return $payPalURL;
    }
    else
    {
      throw new sfException('Paypal error: ' . urldecode($httpParsedResponseAr['L_LONGMESSAGE0']));
    }
  }
  
  public function doExpressCheckout()
  {
    // Set request-specific fields.
    $payerID = urlencode($this->params['payer_id']);
    $token = urlencode($this->params['token']);

    $paymentType = urlencode("Sale"); // or 'Sale' or 'Order'
    $paymentAmount = urlencode($this->params['amount']);
    $currencyID = urlencode("USD");// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
    // Add request-specific fields to the request string.
    $nvpStr = "&TOKEN=$token&PAYERID=$payerID&PAYMENTREQUEST_0_PAYMENTACTION=$paymentType&PAYMENTREQUEST_0_AMT=$paymentAmount&PAYMENTREQUEST_0_CURRENCYCODE=$currencyID";

    // Execute the API operation; see the PPHttpPost function above.
    $httpParsedResponseAr = $this->doHttpPost('DoExpressCheckoutPayment', $nvpStr);

    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
    {
      return $httpParsedResponseAr;
    }
    else
    {
      return false;
    }
  }

}