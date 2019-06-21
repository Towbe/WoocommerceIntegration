<?php

require_once 'Destination.php';
require_once 'LinkitClient.php';
require_once 'LinkitLocation.php';

class LinkitJob
{
    public $id;
    public $service;
    public $destinations;
    public $cancelled;
    public $extras;
    public $clients;
    public $reference_id;
    public $phone_number;

    private function send_request($path, $method, $data) {
        $api_key = get_option('linkit_api_key', "");
        if ($api_key == "") {
            error_log('Linkit api key is not set');
            return null;
        }

        $curl = curl_init();

        $url = "https://api.towbe.com/v1/" . $path;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $api_key,
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            error_log($err);
            return null;
        }

        return $response;
    }

    public function create() {
        $serialized = $this->json_serialize();
        $result = $this->send_request("job-request", "PUT", $serialized);
        if ($result !== null) {
            return json_decode($result)->id;
        }
        return '';
    }

    public function cancel() {
        $result = $this->send_request("company/jobs/cancel", "POST", json_encode(array(
            "id" => $this->id,
        )));
    }

    private function json_serialize() {
        return json_encode($this);
    }
}
