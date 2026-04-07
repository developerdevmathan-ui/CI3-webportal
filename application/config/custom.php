<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['stripe_secret'] = env_value('STRIPE_SECRET', 'sk_test_xxx');
$config['stripe_publishable'] = env_value('STRIPE_PUBLISHABLE', 'pk_test_xxx');
$config['stripe_webhook_secret'] = env_value('STRIPE_WEBHOOK_SECRET', 'whsec_xxx');
$config['stripe_currency'] = strtolower(env_value('STRIPE_CURRENCY', 'usd'));
