<?php

class SessionController
{
    private $conn;

    public function __construct($conn){
        $db = DatabaseController::getInstance();
        $this->conn = $db->getConnection();
    }

    public static function userSignUp($username, $email, $password){
       if ((new self) -> exist($username,$email)){
           return ;
       } else{
            try {
                $sql = $this->conn->prepare("INSERT INTO  (username, email, password, token) VALUES (:username, :email, :password,:token)");
                $statement = (new self)->connection->prepare($sql);
                $statement->bindValue(':username', $username);
                $statement->bindValue(':email', $email);
                $statement->bindValue(':password', $hashed_password);
                $statement->bindValue(':token', $token);
                $statement->setFetchMode(PDO::FETCH_OBJ);
                $statement->execute();

                return true;
            } catch (PDOException $e) {
                return false;
            }                                                                                   
       }
    }

    public static function userLogin($username , $password){
        if (!(new self)->exist($username)) {
            //echo "Username does not exists";
            return false;
        } else {
            try {
        $sql = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        
        $statement = (new self)->connection->prepare($sql);
        $statement->bindValue(':username', $username);
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $statement->execute();
        
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->password)){
            session_start();
            $_SESSION['id'] = $user->id;
            $_SESSION['username'] = $username;


            self::generateSessionToken($user);

            SessionController::createSecureCookie("jwt",self::createJWT(),time()+ (86400 * 30), "/");

            return true;
        } else {

            return false;
        }
            }
            catch(PDOException $error) {
                echo $sql . "<br>" . $error->getMessage();
                return false;
            }
        }
        }
        public static function userLogout() {
            session_start();
            session_destroy();
            setcookie("token", "", time() - 3600, "/"); // Eliminar cookie
            setcookie("jwt", "", time() - 3600, "/"); // Eliminar cookie
        }

        public static function generateSessionToken($user){
            if(isset($_SESSION['id'])){
                $token = bin2hex(random_bytes(16));
                setcookie("token", $token, time() + (86400 * 30), "/"); // 86400 = 1 day

                //Guardar el token en la base de datos
                $statement = (new self)->conn->prepare("UPDATE User SET token = :token WHERE id = :id");
                $statement->bindValue(':token', $token);
                $statement->bindValue(':id', $user->id);

                $statement->execute();
                return true;
            }
            else{
                return false;
            }
        }

        public static function exist($username,$email = null) {
            if($email == null){
                try{
                    $sql = "SELECT * FROM users WHERE username = :username";
                    $statement = (new self)->conn->prepare($sql);
                    $statement->bindValue(':username', $username);
                    $statement->setFetchMode(PDO::FETCH_OBJ);
                    $statement->execute();

                    $result = $stmt->fetch();
                    return !$result ? false : true;

                }
                catch(PDOException $error){
                    echo $sql . "<br>" . $error->getMessage();
                }
            } else {
                try {
                    $sql = "SELECT * FROM User WHERE username = :username OR email = :email";
                    $statement = (new self)->conn->prepare($sql);
                    $statement->bindValue(':username', $username);
                    $statement->bindValue(':email', $email);
                    $statement->setFetchMode(PDO::FETCH_OBJ);
                    $statement->execute();

                    $result = $stmt->fetch();
                    return !$result ? false : true;
                } catch (PDOException $error) {
                    echo $sql . "<br>" . $error->getMessage();

                } 
            }
        }
        public static function verifyTokenCookie(){
            
            if(isset($_COOKIE['token'])){
                
                $token = $_COOKIE['token'];
                
                $statement = "SELECT * FROM User WHERE token = :token";
                $statement = (new self)->conn->prepare($sql);
                $statement->bindValue(':token', $token);
                $statement->setFetchMode(PDO::FETCH_OBJ);
                $statement->execute();
                $user = $stmt->fetch();
                
                if($user){
                    $_SESSION['id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    return true;
                }
                else{

                    setCookie("token", "", time() - 3600, "/"); // Eliminar cookie
                    echo "Token no encontrado";
                    return false;
                }
            }
            else{
                return false;
            }
        }

        public static function verifyJWTCookie(){
            if(isset($_COOKIE['jwt'])){
                $jwt = $_COOKIE['jwt'];

                $statement = (new self)->connction->prepare("SELECT id,username FROM User WHERE jwt = :jwt");
                $statement->bindValue(':jwt', $jwt);
                $statement->setFetchMode(PDO::FETCH_OBJ);
                $statement->execute();
                $user = $stmt->fetch();
              
                if($user){
                    $_SESSION['id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    return true;
                }
                else{
                    setCookie("jwt", "", time() - 3600, "/"); // Eliminar cookie
                    echo "JWT invalido";
                    return false;
                }
            }
            else{
                return false;
            }
        }

        public static function isLoggedIn(){
            return self::verifyTokenCookie() && self::verifyJWTCookie();
        }

        public function createJWT(){
            if (isset($_SESSION['id'])){
                $header = ['alg' => 'HS256' , 'typ' => 'JWT'];
                
                $payload = [
                    'id' => $_SESSION['id'],
                    'username' => $_SESSION['username'],
                    'exp' => time() + (86400 * 30)
                ];

                $jwt = self::generateJWT($header, $payload , self::getSecretKey());

                $statement = (new self)->conn->prepare("UPDATE User SET jwt = :jwt WHERE id = :id");
                $statement->bindValue(':jwt', $jwt);
                $statement->bindValue(':id', $_SESSION['id']);
                $statement->execute();

                return $jwt;
            }
            else{
                return null;
            }
        }

        public static function getSecretKey(){
            
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '../');
            $dotenv->load();

            return $_ENV['SECRET_KEY'];
        }    

        public static function generateJWT($header, $payload, $secret_key){
            $header_encoded = self::base64URLEncode(json_encode($header));
            $payload_encoded = self::base64URLEncode(json_encode($payload));


            $signature = hash_hmac('sha256',"$header_encoded.$payload_encoded",$secret_key,true);
            $signature = base64_encode($signature);

            return "$header_encoded.$payload_encoded.$signature_encoded";
        }

        public static function verifyJWT($jwt,$secret_key){
            list($header_encoded, $payload_encoded, $signature_encoded) = explode('.',$jwt);
            $signature = self::base64URLDecode(hash_mac('sha256',"$header_encoded.$payload_encoded",$secret_key,true));

            if($signature !== $signature_encoded){
                return false;
            }

            $payload = json_decode(base64_decode($payload_encoded),true);
            
            if(isset($payload['exp']) && $payload['exp'] < time()){
                return false;
            }
            return true;
        }

        public static function base64URLEncode($data){
            return str_replace(['+','/','='], ['-','_',''], base64_encode($data));
        }

        public static function craeteSecureCookie($cookieName, $cookieValue, $expirationTime, $path){

            $domain = '';

            $secure = false;

            $httponly = true;

            setcookie($cookieName, $cookieValue, $expirationTime, $path, $domain, $secure, $httponly);
        }
}