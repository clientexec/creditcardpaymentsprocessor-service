<?php
require_once 'modules/admin/models/ServicePlugin.php';
require_once 'modules/billing/models/BillingType.php';
require_once 'modules/billing/models/Invoice_EventLog.php';
require_once 'modules/billing/models/BillingGateway.php';

/**
* @package Plugins
*/
class PluginCreditcardpaymentsprocessor extends ServicePlugin
{
    protected $featureSet = 'billing';
    public $hasPendingItems = true;

    function getVariables()
    {
        $variables = array(
            /*T*/'Plugin Name'/*/T*/   => array(
                'type'          => 'hidden',
                'description'   => /*T*/''/*/T*/,
                'value'         => /*T*/'Credit Card Payments Processor'/*/T*/,
            ),
            /*T*/'Enabled'/*/T*/       => array(
                'type'          => 'yesno',
                'description'   => /*T*/'When enabled, will process your customer\'s credit cards for invoices that are due or past-due. This will only process your customers whose credit card is stored outside of ClientExec.'/*/T*/,
                'value'         => '0',
            ),
            /*T*/'Run schedule - Minute'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '30',
                'helpid'        => '8',
            ),
            /*T*/'Run schedule - Hour'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '*',
            ),
            /*T*/'Run schedule - Day'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '*',
            ),
            /*T*/'Run schedule - Month'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '*',
            ),
            /*T*/'Run schedule - Day of the week'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number in range 0-6 (0 is Sunday) or a 3 letter shortcut (e.g. sun)'/*/T*/,
                'value'         => '*',
            ),
        );

        return $variables;
    }

    function execute()
    {
        $messages = array();
        $numCustomers = 0;

        $billingGateway = new BillingGateway($this->user);
        $initial = 0;
        $passphrase = '';
        $billingGateway->process_invoice($initial, $passphrase);
        if (isset($this->session->all_invoices)){
              $numCustomers = count($this->session->all_invoices);
        }
        $billingGateway->send_process_invoice_summary("process");

        //$this->settings->updateValue("LastDateGenerateInvoices", time());

        array_unshift($messages, "$numCustomers customer(s) were charged");
        return $messages;
    }

    function pendingItems()
    {


        $returnArray = array();
        $returnArray['data'] = array();
        $returnArray['totalcount'] = count($returnArray['data']);
        $returnArray['headers'] = array (
            $this->user->lang('Customer'),
            $this->user->lang('Charge Date')
        );

        return $returnArray;
    }

    function output() { }

    function dashboard()
    {
        $row['customers'] = 0;
        return $this->user->lang('Number of customers to be charged: %d', $row['customers']);
    }
}
?>
