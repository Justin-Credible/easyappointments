<?php 

namespace EA\Engine\Notifications;

use \EA\Engine\Types\Text;
use \EA\Engine\Types\Url;

/**
 * JGU: SMS Notifications Class
 * 
 * This class handles sending SMS notifications using www.twilio.com.
 * 
 * The SMS configuration settings are located at: /application/config/sms.php
 */
class SMS {

    /**
     * Framework Instance
     *
     * @var CI_Controller
     */
    protected $framework;

    /**
     * Contains SMS configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Class Constructor
     *
     * @param \CI_Controller $framework
     * @param array $config Contains the SMS configuration to be used.
     */
    public function __construct(\CI_Controller $framework, array $config) {
        $this->framework = $framework;
        $this->config = $config;
    }

    /**
     * Send an SMS with the appointment details.
     *
     * @param array $appointment Contains the appointment data.
     * @param array $provider Contains the provider data.
     * @param array $service Contains the service data.
     * @param array $customer Contains the customer data.
     * @param array $company Contains settings of the company. By the time the
     * "company_name", "company_link" and "company_email" values are required in the array.
     * @param \EA\Engine\Types\Text $title The SMS title may vary depending the receiver.
     * @param \EA\Engine\Types\Text $message The SMS message may vary depending the receiver.
     * @param \EA\Engine\Types\Url $appointmentLink This link is going to enable the receiver to make changes
     * to the appointment record.
     * @param string $recipientNumber The recipient mobile phone number.
     */
    public function sendAppointmentDetails(array $appointment, array $provider, array $service,
                                           $customer, array $company, Text $title, Text $message, Url $appointmentLink,
                                           string $recipientNumber) {

        $enableSMS = $this->config['sms_enabled'] === 'true';
        $url = $this->config['sms_endpoint_url'];
        $accountId = $this->config['sms_account_id'];
        $authToken = $this->config['sms_auth_token'];
        $sender = $this->config['sms_sender'];

        if (!$enableSMS || !$url || !$accountId || !$authToken || !$sender) {
            return;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_USERPWD, "$accountId:$authToken");

        $titleString = $title ? $title->get() : '';
        $messageString = $message ? $message->get() : '';
        $body = "$titleString\n$messageString";

        if ($appointment) {
            $appointment_start_date = date('m/d/Y g:i a', strtotime($appointment['start_datetime']));
            $appointment_end_date  = date('m/d/Y g:i a', strtotime($appointment['end_datetime']));

            $body .= "\n";
            $body .= "\nStart: $appointment_start_date";
            $body .= "\nEnd: $appointment_end_date";
        }

        if ($customer) {
            $customer_name = $customer['first_name'] . ' ' . $customer['last_name'];
            $customer_email = $customer['email'];
            $customer_phone = $customer['phone_number'];
            $appointment_notes = $appointment['notes'];

            $body .= "\n";
            $body .= "\nName: $customer_name";
            $body .= "\nE-mail: $customer_email";
            $body .= "\nPhone: $customer_phone";

            if ($appointment && $appointment_notes) {
                $body .= "\nNotes: $appointment_notes";
            }
        }

        if ($appointmentLink) {
            $urlString = $appointmentLink->get();
            $body .= "\n\n$urlString";
        }

        $params = [
            'To' => $recipientNumber,
            'From' => $sender,
            'Body' => $body,
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($responseCode < 200 || $responseCode >= 300) {
            throw new \RuntimeException("SMS could not been sent.\nURL: $url\nAccount ID: $accountId\nSender: $sender\nReceiver: $recipientNumber\nResponse Code:$responseCode\nResponse Body: $response\n\nCURL Error: $curlError");
        }
    }
}
