<?php
 class ApiView {
    public function response($data, $status = 200) {
    header("Content-Type: application/json");
    header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));

    $json = json_encode($data);

    if ($json === false) {
        echo json_encode([
            'error' => json_last_error_msg()
        ]);
        return;
    }

    echo $json;
    exit;
}

    private function _requestStatus($code){
        $status = array(
          200 => "OK",
          201 => "Created",
          400 => "Bad request",
          401 => "Unauthorized",
          404 => "Not found",
          409 => "Conflict",
          500 => "Internal Server Error"
        );
        return (isset($status[$code])) ? $status[$code] : $status[500];
      }
  
 }