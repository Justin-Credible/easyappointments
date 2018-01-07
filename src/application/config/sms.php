<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// JGU: Read SMS (www.twilio.com) configuration from Azure environment variables.
$config['sms_enabled'] = getenv('sms_enabled');
$config['sms_endpoint_url'] = getenv('sms_endpoint_url');
$config['sms_account_id'] = getenv('sms_account_id');
$config['sms_auth_token'] = getenv('sms_auth_token');
$config['sms_sender'] = getenv('sms_sender');
