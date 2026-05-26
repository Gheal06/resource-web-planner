<?php
require_once __DIR__ . "/../models/UserModel.php";

class GravatarService {
  private $userModel;

  public function __construct($connection) {
    $this->userModel = new UserModel($connection);
  }

  public function getEmailHashForUser($username) {
    $user = $this->userModel->findByUsername($username);
    if (!$user) {
      return null;
    }
    for ($stripped_email = '', $i = 0; $i < strlen($user['email']) && $user['email'][$i] !== '@'; $i++) {
      if ($user['email'][$i] !== ' ') {
        $stripped_email .= $user['email'][$i];
      }
    }
    // return $stripped_email;
    return md5(strtolower(trim($user['email'])));
  }
  public function getGravatarUrl($username, $size = 80) {
    $emailHash = $this->getEmailHashForUser($username);
    if (!$emailHash) {
      return null;
    }
    return "https://www.gravatar.com/avatar/{$emailHash}?s={$size}&d=identicon";
  }
}


?>