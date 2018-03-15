<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\OrderPaid;
use Config;
use Illuminate\Http\Request;

// Used to process plans
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;


class PaypalController extends Controller
{
    //
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;

    // Create a new instance with our paypal credentials
    public function __construct()
    {
        // Detect if we are running in live mode or sandbox
        if (config('paypal.settings.mode') == 'live') {
            $this->client_id = config('paypal.live_client_id');
            $this->secret = config('paypal.live_secret');
        } else {
            $this->client_id = config('paypal.sandbox_client_id');
            $this->secret = config('paypal.sandbox_secret');
        }
        // Set the Paypal API Context/Credentials
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    public function postPayment()
    {

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();
        $item_1->setName('Item 1')
            ->setCurrency('USD')
            ->setQuantity(2)
            ->setPrice('15');

        $item_2 = new Item();
        $item_2->setName('Item 2')
            ->setCurrency('USD')
            ->setQuantity(4)
            ->setPrice('7');

        $item_3 = new Item();
        $item_3->setName('Item 3')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice('20');

        // add item to list
        $item_list = new ItemList();
        $item_list->setItems(array($item_1, $item_2, $item_3));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(78);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('payment.status'))
            ->setCancelUrl(route('payment.status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (Config::get('app.debug')) {
                echo "Exception: " . $ex->getMessage() . PHP_EOL;
                $err_data = json_decode($ex->getData(), true);
                exit;
            } else {
                die('Some error occur, sorry for inconvenient');
            }
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        // add payment ID to session
        \session()->put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            // redirect to paypal
            return redirect()->away($redirect_url);
        }

        return redirect('/')
            ->with('error', 'Unknown error occurred');
    }

    public function getPaymentStatus()
    {
        // Get the payment ID before session clear
        $payment_id = \session()->get('paypal_payment_id');

        // clear the session payment ID
        \session()->forget('paypal_payment_id');

        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
            return redirect()->back()
                ->with(['status' => 'Payment failed']);
        }

        $payment = Payment::get($payment_id, $this->apiContext);

        // PaymentExecution object includes information necessary
        // to execute a PayPal account payment.
        // The payer_id is added to the request query parameters
        // when the user is redirected from paypal back to your site
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));

        //Execute the payment
        $result = $payment->execute($execution, $this->apiContext);

        if ($result->getState() == 'approved') { // payment made
            $user = Auth::user();
            $order = Order::find(4);
            $user->notify(new OrderPaid($order));
            $order->status = 1;
            $order->save();
            return redirect()->back()
                ->with(['status' => 'Payment success']);
        }
        return redirect()->back()
            ->with(['status' => 'Payment failed']);
    }
}
