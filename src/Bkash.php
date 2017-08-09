<?php

namespace Mehedi\Bkash;

use Exception;

class Bkash
{
    /**
     * Bkash api base url.
     *
     * @var string
     */
    protected $base_url = 'http://www.bkashcluster.com:9080/dreamwave/merchant/trxcheck/sendmsg';
    
    /**
     * Check bkash payment transaction id.
     *
     * @param string $transactionId
     * @throws Exception
     */
    public function check($transactionId)
    {
        $data = [
            'user' => config('bkash.username'),
            'pass' => config('bkash.password'),
            'msisdn' => config('bkash.mobile'),
            'trxid' => $transactionId,
        ];

        return $this->validateResponse(
            $this->callApi($data)
        );
    }

    /**
     * @param array $data
     */
    protected function callApi($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    /**
     * @param array $response
     * @throws Exception
     */
    protected function validateResponse($response)
    {
        switch ($response->trxStatus) {
            case '0010':
            case '0011':
                throw new Exception('Transaction is pending, please try again later.');
                break;

            case '0100':
                throw new Exception('Transaction ID is valid but transaction has been reversed.');
                break;

            case '0111':
                throw new Exception('Transaction is failed.');
                break;

            case '1001':
                throw new Exception('Invalid MSISDN input. Try with correct mobile no.');
                break;

            case '1002':
                throw new Exception('Invalid transaction ID.');
                break;

            case '1003':
                throw new Exception('Authorization Error, please contact site admin.');
                break;

            case '1004':
                throw new Exception('Transaction ID not found.');
                break;

            case '9999':
                throw new Exception('System error, could not process request. Please contact site admin.');
                break;

            case '0000':
                return $response;
        }
    }
}
