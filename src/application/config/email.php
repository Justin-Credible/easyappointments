<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com'; 
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent'] = 'Easy!Appointments'; 
$config['protocol'] = 'smtp'; // or 'smtp'
$config['mailtype'] = 'html'; // or 'text'
// HACK: JGU: Read e-mail configuration from Azure environment variables.
$config['smtp_host'] = getenv('email_smtp_host');
$config['smtp_user'] = getenv('email_smtp_user');
$config['smtp_pass'] = getenv('email_smtp_pass');
$config['smtp_crypto'] = getenv('email_smtp_crypto'); // 'ssl' or 'tls'
$config['smtp_port'] = getenv('email_smtp_port');
