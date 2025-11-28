<?php

class CashfreeHelper
{
    private $appId;
    private $secretKey;
    private $apiVersion = '2022-09-01';
    private $baseUrl;

    public function __construct()
    {
        // The keys are located on the Desktop as per user environment
        $configFile = 'C:/Users/Nirbhay Zala/Desktop/config/keys.php';
        
        if (file_exists($configFile)) {
            $config = include($configFile);
            if (isset($config['cashfree'])) {
                $this->appId = $config['cashfree']['app_id'];
                $this->secretKey = $config['cashfree']['secret_key'];
                $this->baseUrl = $config['cashfree']['base_url'];
            } else {
                throw new Exception("Cashfree configuration not found in keys.php");
            }
        } else {
            // Fallback: try checking if it's in the standard config directory just in case
            $fallbackConfig = __DIR__ . '/../config/keys.php';
            if (file_exists($fallbackConfig)) {
                $config = include($fallbackConfig);
                $this->appId = $config['cashfree']['app_id'];
                $this->secretKey = $config['cashfree']['secret_key'];
                $this->baseUrl = $config['cashfree']['base_url'];
            } else {
                throw new Exception("Configuration file not found at: " . $configFile);
            }
        }
    }

    public function createOrder($orderId, $amount, $customerDetails, $returnUrl)
    {
        $url = $this->baseUrl . "/orders";

        $headers = [
            "Content-Type: application/json",
            "x-api-version: " . $this->apiVersion,
            "x-client-id: " . $this->appId,
            "x-client-secret: " . $this->secretKey
        ];

        $data = [
            "order_id" => (string)$orderId,
            "order_amount" => (float)$amount,
            "order_currency" => "INR",
            "customer_details" => [
                "customer_id" => (string)$customerDetails['id'],
                "customer_name" => $customerDetails['name'],
                "customer_email" => $customerDetails['email'],
                "customer_phone" => (string)$customerDetails['phone']
            ],
            "order_meta" => [
                "return_url" => $returnUrl
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Cashfree Error: ' . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }

    public function verifyPayment($orderId)
    {
        $url = $this->baseUrl . "/orders/" . $orderId;

        $headers = [
            "Content-Type: application/json",
            "x-api-version: " . $this->apiVersion,
            "x-client-id: " . $this->appId,
            "x-client-secret: " . $this->secretKey
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Cashfree Verification Error: ' . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }
}
