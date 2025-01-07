<?php
class ServiceController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Service($pdo);
    }

    public function create($data) {
        // Check if data is sent via form-data
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $photo = $_FILES['photo']['name']; // Assuming photo is uploaded as a file
            // Handle file upload
            move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $photo);
        } else {
            // For raw JSON data
            $name = $data['name'];
            $price = $data['price'];
            $description = $data['description'];
            $photo = $data['photo'];
        }
    
        $result = $this->model->create($name, $price, $description, $photo);
    
        if ($result) {
            echo json_encode(["success" => true, "message" => "Service successfully added."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add service."]);
        }
    }
    
    

    public function read($id) {
        $service = $this->model->read($id);
        if ($service) {
            echo json_encode($service);
        } else {
            echo json_encode(['message' => 'Service not found']);
        }
    }

    public function update($id, $data) {
        if ($this->model->update($id, $data['name'], $data['price'], $data['description'], $data['photo'])) {
            echo json_encode(["message" => "Service successfully updated."]);
        } else {
            echo json_encode(["message" => "Failed to update service."]);
        }
    }
    

    public function delete($id) {
        if ($this->model->delete($id)) {
            echo json_encode(["message" => "Service successfully deleted."]);
        } else {
            echo json_encode(["message" => "Failed to delete service."]);
        }
    }
    

    public function listAll() {
        $services = $this->model->listAll();
        if (count($services) > 0) {
            echo json_encode($services);
        } else {
            echo json_encode(['message' => 'No services found']);
        }
    }
}

?>