<?php

public class AdminService {
    private $adminModel;
    private $userModel;

    public function __construct($connection) {
        $this->adminModel = new AdminModel($connection);
        $this->userModel = new UserModel($connection);
    }

    public function getAllAdmins() {
        return $this->adminModel->getAllAdmins();
    }

    public function deleteAdmin($user_id) {
        return $this->adminModel->deleteAdmin($user_id);
    }

    public function addAdmin($user_id) {
        return $this->adminModel->addAdmin($user_id);
    }

    public function send_OTC_to_email($user_email){
        $user = $this->userModel->findByEmail($user_email);
        if (!$user) return array('success' => false, 'message' => 'No account with this email.');
    }

    public function reset_database() {
      shell_exec('../../admin_scripts/initdb.sh defarg');
      return array('success' => true, 'message' => 'Database reset successfully.');
    }

?>