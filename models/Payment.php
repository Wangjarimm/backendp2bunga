<?php

class Payment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($data)
    {
        $sql = "INSERT INTO payments (booking_id, order_id, status, gross_amount, payment_type, transaction_time) 
                VALUES (:booking_id, :order_id, :status, :gross_amount, :payment_type, :transaction_time)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':booking_id' => $data['booking_id'],
            ':order_id' => $data['order_id'],
            ':status' => $data['status'],
            ':gross_amount' => $data['gross_amount'],
            ':payment_type' => $data['payment_type'],
            ':transaction_time' => $data['transaction_time'],
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updateStatus($orderId, $status)
    {
        error_log("Updating status for order_id: $orderId to $status");
    
        $sql = "UPDATE payments SET status = :status WHERE order_id = :order_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':order_id' => $orderId,
        ]);
    
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            error_log("Successfully updated status for order_id: $orderId");
        } else {
            error_log("No rows updated for order_id: $orderId. Please check if order exists.");
        }
    
        return $rowCount;
    }

    public function getByOrderId($orderId)
    {
        $sql = "SELECT * FROM payments WHERE order_id = :order_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetch();
    }
}
