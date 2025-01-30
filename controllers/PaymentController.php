<?php

require_once 'models/Payment.php';
require_once 'config/midtrans.php';
require_once 'vendor/autoload.php'; // Pastikan autoloading tersedia

use Ramsey\Uuid\Uuid;

class PaymentController
{
    private $paymentModel;

    public function __construct($pdo)
    {
        $this->paymentModel = new Payment($pdo);
    }

    public function createPayment($data)
    {
        date_default_timezone_set('Asia/Jakarta');

        // Generate UUID sebagai order_id
        if (empty($data['order_id'])) {
            $data['order_id'] = Uuid::uuid4()->toString(); // Menggunakan UUID versi 4
        }

        if (empty($data['transaction_time'])) {
            $data['transaction_time'] = date('Y-m-d H:i:s');
        }

        // Data pelanggan
        $customerDetails = [
            'name' => $data['customer']['name'] ?? null, // Menggunakan "name" saja
            'email' => $data['customer']['email'] ?? null,
            'phone' => $data['customer']['phone'] ?? null,
        ];

        $paymentData = [
            'booking_id' => $data['booking_id'],
            'order_id' => $data['order_id'],
            'status' => $data['status'],
            'gross_amount' => $data['gross_amount'],
            'payment_type' => $data['payment_type'],
            'transaction_time' => $data['transaction_time'],
        ];

        $paymentId = $this->paymentModel->create($paymentData);

        if ($paymentId) {
            $transactionDetails = [
                'order_id' => $data['order_id'],
                'gross_amount' => $data['gross_amount'],
            ];

            // Menambahkan informasi pelanggan ke transaksi Midtrans
            $transaction = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
            ];

            try {
                // Generate Snap Token and Snap URL
                $snapToken = \Midtrans\Snap::getSnapToken($transaction);
                $snapUrl = \Midtrans\Snap::createTransaction($transaction)->redirect_url;

                echo json_encode([
                    "message" => "Payment successfully created.",
                    "payment_id" => $paymentId,
                    "order_id" => $data['order_id'], // UUID order_id
                    "snap_token" => $snapToken,
                    "snap_url" => $snapUrl, // Include snap_url
                    "customer" => $customerDetails, // Include customer details in response
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    "message" => "Failed to generate snap token or URL.",
                    "error" => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode(["message" => "Failed to create payment."]);
        }
    }


    public function midtransNotification($data)
    {
        error_log(print_r($data, true));

        $orderId = $data['order_id'];
        $status = $data['transaction_status'];

        error_log("Order ID: $orderId");
        error_log("Transaction Status: $status");

        $payment = $this->paymentModel->getByOrderId($orderId);
        if (!$payment) {
            echo json_encode(["message" => "Order not found in the database."]);
            return;
        }

        if ($status === 'settlement') {
            $updatedRows = $this->paymentModel->updateStatus($orderId, 'settlement');

            if ($updatedRows > 0) {
                echo json_encode(["message" => "Payment status updated to settlement."]);
            } else {
                echo json_encode(["message" => "Failed to update payment status."]);
            }
        } else {
            echo json_encode(["message" => "Non-settlement status received."]);
        }
    }

    public function getPaymentByOrderId($orderId)
    {
        $payment = $this->paymentModel->getByOrderId($orderId);

        if ($payment) {
            echo json_encode($payment);
        } else {
            echo json_encode(["message" => "Payment not found."]);
        }
    }
}
