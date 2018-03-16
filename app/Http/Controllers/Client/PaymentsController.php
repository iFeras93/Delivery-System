<?php

namespace App\Http\Controllers\Client;

use App\Models\Order;
use App\Models\Setting;
use App\Notifications\OrderPaid;
use Bodunde\GoogleGeocoder\Geocoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Notification;
use App\Models\Transaction as MyTransaction;

/* Paypal Libraries*/

use Mockery\Exception;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Refund;
use PayPal\Api\Sale;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PPConnectionException;
use PayPal\Rest\ApiContext;

class PaymentsController extends Controller
{
    //init variable for paypal
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;

    private $order;

    // Create a new instance with our paypal credentials
    public function __construct(Request $request)
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

        // get order using id
        $this->order = Order::find($request->input('order_id'));
    }


    /**
     * Store a details of payment with paypal.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPayment()
    {

        // Create new payer and method
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('client.payment.status'))
            ->setCancelUrl(route('client.payment.status'));


        // list of items
        $listOfItem = [];
        // fetching products from order and add to listOfItem
        foreach ($this->order->products as $product) {
            $item = new Item();
            $item->setName($product->title)
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice($product->price);
            //push every item in to array
            array_push($listOfItem, $item);
        }

        // Set item list
        $itemList = new ItemList();
        $itemList->setItems($listOfItem);

        // for shipping
        $shipping_price = $this->order->distance_price;

        // Set payment details
        $details = new Details();
        $details->setShipping($shipping_price)
            ->setSubtotal($this->order->product_price);

        // Set payment amount
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($this->order->total_price)
            ->setDetails($details);

        // Set transaction object
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // Create the full payment object
        $payment = new Payment();
        $payment->setIntent("order")
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));


        try {
            // create payment in paypal
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

        // fetching payment response
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

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
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


            // Update Order Status
            $order_id = session()->get('order_id');
            $user = Auth::user();
            $c_order = Order::find($order_id);
            $c_order->status = 1;
            $c_order->save();

            // create and save new transaction
            $order_transaction = new MyTransaction();
            $order_transaction->transaction_id = $payment->getId();
            $order_transaction->order_id = $c_order->id;
            $order_transaction->status = "paid";
            $order_transaction->total_price = $c_order->total_price;
            $order_transaction->save();

            //try to send mail notification to user pay with paypal
            try {
                $user->notify(new OrderPaid($c_order));
            } catch (Exception $exception) {
                return redirect()->back()
                    ->with(['status' => 'Payment failed']);
            }
            return redirect()->back()
                ->with(['status' => 'Payment success']);
        }
        return redirect()->back()
            ->with(['status' => 'Payment failed']);

        session()->forget('order_id');
    }


}
