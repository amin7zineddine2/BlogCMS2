<?php
class Database {
    private $conn;
    
    public function __construct() {
    try {
    $db_host = 'localhost';
    $db_port = '1521';
    $db_service = 'XE'; 
    $db_user = 'Dev Test 2';   
    $db_pass = 'aminblayza';

    $conn = oci_connect(
        $db_user,
        $db_pass,
        "//{$db_host}:{$db_port}/{$db_service}",
        'AL32UTF8'
    );

    if (!$conn) {
        $e = oci_error();
        throw new Exception($e['message']);
    }

    oci_set_module_name($conn, 'BlogCMS');
    oci_set_client_identifier($conn, $db_user);

} catch (Exception $e) {
    die("Oracle connection failed: " . $e->getMessage());
}
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function executeQuery($sql, $params = []) {
        $stid = oci_parse($this->conn, $sql);
        
        if (!$stid) {
            $e = oci_error($this->conn);
            throw new Exception("Erreur de préparation Oracle: " . $e['message']);
        }
        
        foreach ($params as $key => $value) {
            oci_bind_by_name($stid, $key, $params[$key]);
        }
        
        if (!oci_execute($stid)) {
            $e = oci_error($stid);
            throw new Exception("Erreur d'exécution Oracle: " . $e['message']);
        }
        
        $results = [];
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $results[] = $row;
        }
        
        oci_free_statement($stid);
        return $results;
    }
    
    public function executeNonQuery($sql, $params = []) {
        $stid = oci_parse($this->conn, $sql);
        
        if (!$stid) {
            $e = oci_error($this->conn);
            throw new Exception("Erreur de préparation Oracle: " . $e['message']);
        }
        
        foreach ($params as $key => $value) {
            oci_bind_by_name($stid, $key, $params[$key]);
        }
        
        if (!oci_execute($stid)) {
            $e = oci_error($stid);
            throw new Exception("Erreur d'exécution Oracle: " . $e['message']);
        }
        
        $rowCount = oci_num_rows($stid);
        oci_free_statement($stid);
        
        return $rowCount;
    }
    
    public function getNextSequenceValue($sequenceName) {
        $sql = "SELECT $sequenceName.NEXTVAL FROM DUAL";
        $result = $this->executeQuery($sql);
        return $result[0]['NEXTVAL'];
    }
    
    public function __destruct() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }
}

$db = new Database();
?>