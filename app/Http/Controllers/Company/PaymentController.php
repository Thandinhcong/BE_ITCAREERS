<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\HistoryPayment;
use App\Models\Invoice;
use App\Models\Packages;
use App\Models\ProfileOpen;
use App\Models\Vnpay_payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private $data;
    private $vnp_TmnCode = 'MSJEPJ3Z';
    private $vnp_HashSecret = 'SIXNKWGJKGANJAGNYZCGLDDRASWRIIIU';
    public function __construct()
    {
        $this->data = [];
    }
    public function getListPackage()
    {
        $this->data['package'] = Packages::where([['status', '=', 1], ['type_account', '=', 0]])
            ->get()->toArray();
        // status 0 là ứng viên 1 là công ty
        if (!$this->data['package']) {
            return response()->json([
                'status' => false,
                'package' => $this->data['package']
            ], 404);
        }
        return response()->json([
            'status' => true,
            'package' => $this->data['package']
        ], 200);
    }
    public function insertInvoice(Request $request)
    {
        $id = random_int(1, 1000000);
        $user_id = Auth::user()->id;
        $package_id = $request->package_id;
        $package = Packages::find($package_id);
        $data_invoice = [
            'id' => $id,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'status' => 0,
            'amount' => $package->price,
            'total' => 1
        ];
        $invoice = Invoice::create($data_invoice);
        $this->data['invoice'] = Invoice::with('package')
            ->select(
                'invoices.id as invoice_id',
                'invoices.user_id',
                'invoices.package_id',
                'invoices.status',
                'invoices.amount',
                'invoices.total',
                'invoices.created_at',
                'invoices.updated_at',
            )
            ->where('id', $invoice->id)
            ->first();
        return response()->json([
            'status' => true,
            'invoice' => $this->data['invoice']
        ], 200);
    }

    public function payment(Request $request)
    {
        $host = $request->getHttpHost();
        // $vnp_Returnurl = url('') . "/api/company/vnpay_return";
        // $vnp_Returnurl = "http://127.0.0.1:5173/business/deposit";
        $vnp_Returnurl = "http://localhost:5173/business/deposit";

        $vnp_TmnCode = $this->vnp_TmnCode; //Website ID in VNPAY System
        $vnp_HashSecret = $this->vnp_HashSecret; //Secret key
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
        $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";


        $Amount = intval($request->amount);

        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
        $vnp_TxnRef = $request->invoice_id;
        $vnp_OrderInfo = "Thanh toan hoa don:" . $request->invoice_id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $Amount * 100; //
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );
        if (isset($request->redirect)) {
            return redirect()->away($vnp_Url);
        } else {
            return response()->json([
                $returnData
            ], 200);
        }
    }

    public function vnpay_return(Request $request)
    {
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $url_ipn = url('') . str_replace('vnpay_return', 'vnpay_ipn', $request->getRequestUri());
        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                return response()->json([
                    'status' => true,
                    'message' => 'GD Thành công!',
                    // redirect()->away($url_ipn),
                    'data' => $url_ipn
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'GD không thành công!',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Chu kỳ không hợp lệ!',
            ], 400);
        }
        return response()->json([
            'status' => false,
            'message' => 'Lỗi không xác định',
        ], 400);
    }

    public function vnpay_ipn(Request $request)
    {
        $inputData = array();
        $returnData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount'] / 100; // Số tiền thanh toán VNPAY phản hồi

        $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        $orderId = $inputData['vnp_TxnRef'];
        try {
            //Check Orderid
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                $invoice = Invoice::with('package')->find($orderId);
                if ($invoice != NULL) {
                    if ($invoice["amount"] == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                    {
                        if ($invoice["status"] == NULL && $invoice["status"] == 0) {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $Status = 1; // Trạng thái thanh toán thành công
                            } else {
                                $Status = 2; // Trạng thái thanh toán thất bại / lỗi
                            }
                            $invoice->update(['status' => $Status]);
                            $company = Company::where('id', Auth::user()->id)->first();
                            $company->coin = $company->coin + $invoice->package->coin;
                            $company->save();
                            updateProcess(Auth::user()->id, "Thực hiện nạp {$invoice->package->coin} coin vào tài khoản", $invoice->package->coin, 0, 0);
                            $vnpay_payment = Vnpay_payment::create($request->all());
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Giao dịch thành công!';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Đơn đặt hàng đã được xác nhận';
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Số tiền không hợp lệ';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Không tồn tại hóa đơn';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Chữ ký không hợp lệ';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Lỗi không xác định';
        }
        if ($returnData['RspCode'] != 00) {
            return response()->json([
                'status' => false,
                'message' => $returnData['Message'],
            ], 400);
        } else {
            return response()->json([
                'status' => true,
                'message' => $returnData['Message'],
            ], 200);
        }
    }
    public function historyPayment()
    {
        $this->data['history_deposit'] = DB::table('history_payments')
            ->where('user_id', '=', Auth::user()->id)
            ->where('note', 'like', '%' . 'coin vào tài khoản' . '%')
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 0)
            ->orderBy('created_at', 'DESC')
            ->get();
        $this->data['profileHistory'] = DB::table('profile_open')
            ->where('company_id', Auth::user()->id)
            ->leftJoin('profile', 'profile.id', '=', 'profile_open.profile_id')
            ->select(
                'profile_open.id as user_id',
                DB::raw('CONCAT("Mở khóa liên hệ ứng viên ", profile.name, " với phí liên hệ ", profile_open.coin, " coin") as note'),
                DB::raw('1 as type_coin'),
                DB::raw('0 as type_account'),
                'profile_open.created_at',
                'profile_open.coin'
            )
            ->orderBy('profile_open.created_at', 'DESC')
            ->get();


        $postHistory = DB::table('history_payments')
            ->where('user_id', '=', Auth::user()->id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->select(
                DB::raw('null as id'),
                'note',
                'coin',
                'type_coin',
                'type_account',
                DB::raw('null as title'),
                DB::raw('null as name'),
                'created_at',
            )
            ->orderBy('created_at', 'DESC');


        $feedbackHistory = DB::table('history_payments')
            ->where('user_id', '=', Auth::user()->id)
            ->where('note', 'like', '%' . 'Feedback' . '%')
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 0)
            ->select(
                DB::raw('null as id'),
                'note',
                'coin',
                'type_coin',
                'type_account',
                DB::raw('null as title'),
                DB::raw('null as name'),
                'created_at',
            )
            ->orderBy('created_at', 'DESC');

        $mergedHistory = $postHistory->union($feedbackHistory)->get();

        $this->data['history_payment'] = $mergedHistory
            ->map(function ($item) {
                return collect($item)
                    ->reject(function ($value) {
                        return $value === null;
                    })
                    ->toArray();
            })
            ->sortByDesc('created_at')
            ->values()
            ->all();
        // if ($this->data['history_deposit']->count() == 0 && $this->data['history_payment']->count() == 0) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Bạn chưa thực hiện giao dịch nào',
        //     ], 400);
        // }
        return response()->json([
            'status' => true,
'message' => 'Giao dịch đã thực hiện: ',
            'data' => $this->data,

        ], 200);
    }
    public function refund()
    {
        $vnp_TmnCode = $this->vnp_TmnCode; //Website ID in VNPAY System
        $vnp_HashSecret = $this->vnp_HashSecret; //Secret key
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";

        $amount = ($_POST["amount"]) * 100;
        $ipaddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => '2.1.0',
            "vnp_TransactionType" => $_POST["trantype"],
            "vnp_Command" => "refund",
            "vnp_CreateBy" => $_POST["mail"],
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_TxnRef" => $_POST["orderid"],
            "vnp_Amount" => $amount,
            "vnp_OrderInfo" => 'Noi dung thanh toan',
            "vnp_TransDate" => $_POST['paymentdate'],
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_IpAddr" => $ipaddr
        );
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_apiUrl = $vnp_apiUrl . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_apiUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $ch = curl_init($vnp_apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        echo $data;
    }
    public function statistics()
    {
        $job_list = DB::table('job_post')
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->whereIn('job_post.status', [1, 0])
            ->join('district', 'job_post.area_id', '=', 'district.id')
            ->join('province', 'district.province_id', '=', 'province.id')
            ->groupBy('province.id')
            ->orderByDesc('job_count')
            ->select(
                'province.id',
                'province.province',
                DB::raw('count(*) as  job_count'),
            )->limit(3)->get();

        if ($job_list != []) {
            return response()->json([
                'status' => 200,
                'job_list' => $job_list
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'mesage' => 'không có bản ghi nào',
                'job_list' => []
            ], 404);
        }
    }
}
