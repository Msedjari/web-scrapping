<?php
use App\Controller\DatabaseController; // Importamos el controlador de la base de datos


class SessionController
{
    private $connection; // Declaramos una propiedad privada para la conexión

    public function __construct(){
        $db = DatabaseController::getInstance(); // Obtenemos la instancia única del controlador de la base de datos
        $this->connection = $db->getConnection(); // Asignamos la conexión a la propiedad
    }

    public static function userSignUp($username, $email, $password){
       $instance = new self(); // Creamos una nueva instancia de SessionController
       if ($instance->exist($username, $email)){ // Verificamos si el usuario ya existe
           return ; // Si existe, no hacemos nada
       } else{
            try {
                $sql = "INSERT INTO User (username, email, password, token) VALUES (:username, :email, :password, :token)"; // Definimos la consulta SQL para insertar un nuevo usuario
                $statement = $instance->connection->prepare($sql); // Preparamos la consulta
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hasheamos la contraseña
                $token = bin2hex(random_bytes(16)); // Generamos un token aleatorio
                $statement->bindValue(':username', $username); // Asignamos el valor del nombre de usuario
                $statement->bindValue(':email', $email); // Asignamos el valor del email
                $statement->bindValue(':password', $hashed_password); // Asignamos el valor de la contraseña hasheada
                $statement->bindValue(':token', $token); // Asignamos el valor del token
                $statement->execute(); // Ejecutamos la consulta

                return true; // Retornamos true si la inserción fue exitosa
            } catch (PDOException $e) {
                return false; // Retornamos false si hubo un error
            }                                                                                   
       }
    }

    public static function userLogin($username , $password){
        $instance = new self(); // Creamos una nueva instancia de SessionController
        if (!$instance->exist($username)) { // Verificamos si el usuario no existe
            return false; // Retornamos false si el usuario no existe
        } else {
            try {
                $sql = "SELECT * FROM User WHERE username = :username"; // Definimos la consulta SQL para seleccionar el usuario
                $statement = $instance->connection->prepare($sql); // Preparamos la consulta
                $statement->bindValue(':username', $username); // Asignamos el valor del nombre de usuario
                $statement->setFetchMode(PDO::FETCH_OBJ); // Establecemos el modo de obtención de resultados como objeto
                $statement->execute(); // Ejecutamos la consulta
        
                $user = $statement->fetch(); // Obtenemos el resultado de la consulta

                if ($user && password_verify($password, $user->password)){ // Verificamos si el usuario existe y la contraseña es correcta
                    session_start(); // Iniciamos la sesión
                    $_SESSION['id'] = $user->id; // Asignamos el ID del usuario a la sesión
                    $_SESSION['username'] = $username; // Asignamos el nombre de usuario a la sesión

                    self::generateSessionToken($user); // Generamos un token de sesión

                    $jwt = $instance->createJWT(); // Creamos un JWT
                    self::createSecureCookie("jwt", $jwt, time() + (86400 * 30), "/"); // Creamos una cookie segura con el JWT

                    return true; // Retornamos true si el inicio de sesión fue exitoso
                } else {
                    return false; // Retornamos false si la contraseña es incorrecta
                }
            } catch(PDOException $error) {
                echo $sql . "<br>" . $error->getMessage(); // Mostramos el error
                return false; // Retornamos false si hubo un error
            }
        }
    }

    public static function userLogout() {
        session_start(); // Iniciamos la sesión
        session_destroy(); // Destruimos la sesión
        setcookie("token", "", time() - 3600, "/"); // Eliminamos la cookie del token
        setcookie("jwt", "", time() - 3600, "/"); // Eliminamos la cookie del JWT
    }

    public static function generateSessionToken($user){
        if(isset($_SESSION['id'])){ // Verificamos si la sesión está iniciada
            $token = bin2hex(random_bytes(16)); // Generamos un token aleatorio
            setcookie("token", $token, time() + (86400 * 30), "/"); // Creamos una cookie con el token

            //Guardar el token en la base de datos
            $instance = new self(); // Creamos una nueva instancia de SessionController
            $statement = $instance->connection->prepare("UPDATE User SET token = :token WHERE id = :id"); // Preparamos la consulta SQL para actualizar el token del usuario
            $statement->bindValue(':token', $token); // Asignamos el valor del token
            $statement->bindValue(':id', $user->id); // Asignamos el valor del ID del usuario
            $statement->execute(); // Ejecutamos la consulta
            return true; // Retornamos true si la actualización fue exitosa
        } else {
            return false; // Retornamos false si la sesión no está iniciada
        }
    }

    public static function exist($username, $email = null) {
        $instance = new self(); // Creamos una nueva instancia de SessionController
        if($email == null){ // Verificamos si el email es nulo
            try{
                $sql = "SELECT * FROM User WHERE username = :username"; // Definimos la consulta SQL para seleccionar el usuario por nombre de usuario
                $statement = $instance->connection->prepare($sql); // Preparamos la consulta
                $statement->bindValue(':username', $username); // Asignamos el valor del nombre de usuario
                $statement->setFetchMode(PDO::FETCH_OBJ); // Establecemos el modo de obtención de resultados como objeto
                $statement->execute(); // Ejecutamos la consulta

                $result = $statement->fetch(); // Obtenemos el resultado de la consulta
                return !$result ? false : true; // Retornamos true si el usuario existe, false si no existe

            } catch(PDOException $error){
                echo $sql . "<br>" . $error->getMessage(); // Mostramos el error
            }
        } else {
            try {
                $sql = "SELECT * FROM User WHERE username = :username OR email = :email"; // Definimos la consulta SQL para seleccionar el usuario por nombre de usuario o email
                $statement = $instance->connection->prepare($sql); // Preparamos la consulta
                $statement->bindValue(':username', $username); // Asignamos el valor del nombre de usuario
                $statement->bindValue(':email', $email); // Asignamos el valor del email
                $statement->setFetchMode(PDO::FETCH_OBJ); // Establecemos el modo de obtención de resultados como objeto
                $statement->execute(); // Ejecutamos la consulta

                $result = $statement->fetch(); // Obtenemos el resultado de la consulta
                return !$result ? false : true; // Retornamos true si el usuario existe, false si no existe
            } catch (PDOException $error) {
                echo $sql . "<br>" . $error->getMessage(); // Mostramos el error
            } 
        }
    }

    public static function verifyTokenCookie(){
        if(isset($_COOKIE['token'])){ // Verificamos si la cookie del token está establecida
            $token = $_COOKIE['token']; // Obtenemos el valor del token de la cookie
            $instance = new self(); // Creamos una nueva instancia de SessionController
            $statement = $instance->connection->prepare("SELECT * FROM User WHERE token = :token"); // Preparamos la consulta SQL para seleccionar el usuario por token
            $statement->bindValue(':token', $token); // Asignamos el valor del token
            $statement->setFetchMode(PDO::FETCH_OBJ); // Establecemos el modo de obtención de resultados como objeto
            $statement->execute(); // Ejecutamos la consulta
            $user = $statement->fetch(); // Obtenemos el resultado de la consulta
            
            if($user){ // Verificamos si el usuario existe
                $_SESSION['id'] = $user->id; // Asignamos el ID del usuario a la sesión
                $_SESSION['username'] = $user->username; // Asignamos el nombre de usuario a la sesión
                return true; // Retornamos true si el token es válido
            } else {
                setCookie("token", "", time() - 3600, "/"); // Eliminamos la cookie del token
                echo "Token no encontrado"; // Mostramos un mensaje de error
                return false; // Retornamos false si el token no es válido
            }
        } else {
            return false; // Retornamos false si la cookie del token no está establecida
        }
    }

    public static function verifyJWTCookie(){
        if(isset($_COOKIE['jwt'])){ // Verificamos si la cookie del JWT está establecida
            $jwt = $_COOKIE['jwt']; // Obtenemos el valor del JWT de la cookie
            $instance = new self(); // Creamos una nueva instancia de SessionController
            $statement = $instance->connection->prepare("SELECT id, username FROM User WHERE jwt = :jwt"); // Preparamos la consulta SQL para seleccionar el usuario por JWT
            $statement->bindValue(':jwt', $jwt); // Asignamos el valor del JWT
            $statement->setFetchMode(PDO::FETCH_OBJ); // Establecemos el modo de obtención de resultados como objeto
            $statement->execute(); // Ejecutamos la consulta
            $user = $statement->fetch(); // Obtenemos el resultado de la consulta
          
            if($user){ // Verificamos si el usuario existe
                $_SESSION['id'] = $user->id; // Asignamos el ID del usuario a la sesión
                $_SESSION['username'] = $user->username; // Asignamos el nombre de usuario a la sesión
                return true; // Retornamos true si el JWT es válido
            } else {
                setCookie("jwt", "", time() - 3600, "/"); // Eliminamos la cookie del JWT
                echo "JWT invalido"; // Mostramos un mensaje de error
                return false; // Retornamos false si el JWT no es válido
            }
        } else {
            return false; // Retornamos false si la cookie del JWT no está establecida
        }
    }

    public static function isLoggedIn(){
        return self::verifyTokenCookie() && self::verifyJWTCookie(); // Verificamos si el usuario está logueado comprobando las cookies del token y JWT
    }

    public function createJWT(){
        if (isset($_SESSION['id'])){ // Verificamos si la sesión está iniciada
            $header = ['alg' => 'HS256' , 'typ' => 'JWT']; // Definimos el encabezado del JWT
            
            $payload = [
                'id' => $_SESSION['id'], // Asignamos el ID del usuario al payload
                'username' => $_SESSION['username'], // Asignamos el nombre de usuario al payload
                'exp' => time() + (86400 * 30) // Asignamos la fecha de expiración al payload
            ];

            $jwt = self::generateJWT($header, $payload , self::getSecretKey()); // Generamos el JWT

            $instance = new self(); // Creamos una nueva instancia de SessionController
            $statement = $instance->connection->prepare("UPDATE User SET jwt = :jwt WHERE id = :id"); // Preparamos la consulta SQL para actualizar el JWT del usuario
            $statement->bindValue(':jwt', $jwt); // Asignamos el valor del JWT
            $statement->bindValue(':id', $_SESSION['id']); // Asignamos el valor del ID del usuario
            $statement->execute(); // Ejecutamos la consulta

            return $jwt; // Retornamos el JWT
        } else {
            return null; // Retornamos null si la sesión no está iniciada
        }
    }

    public static function getSecretKey(){
        return 'your_secret_key'; // Retornamos la clave secreta para el JWT
    }    

    public static function generateJWT($header, $payload, $secret_key){
        $header_encoded = self::base64URLEncode(json_encode($header)); // Codificamos el encabezado en base64
        $payload_encoded = self::base64URLEncode(json_encode($payload)); // Codificamos el payload en base64

        $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret_key, true); // Generamos la firma del JWT
        $signature_encoded = base64_encode($signature); // Codificamos la firma en base64

        return "$header_encoded.$payload_encoded.$signature_encoded"; // Retornamos el JWT completo
    }

    public static function verifyJWT($jwt, $secret_key){
        list($header_encoded, $payload_encoded, $signature_encoded) = explode('.', $jwt); // Dividimos el JWT en sus partes
        $signature = base64_encode(hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret_key, true)); // Generamos la firma del JWT

        if($signature !== $signature_encoded){ // Verificamos si la firma es válida
            return false; // Retornamos false si la firma no es válida
        }

        $payload = json_decode(base64_decode($payload_encoded), true); // Decodificamos el payload
        
        if(isset($payload['exp']) && $payload['exp'] < time()){ // Verificamos si el JWT ha expirado
            return false; // Retornamos false si el JWT ha expirado
        }
        return true; // Retornamos true si el JWT es válido
    }

    public static function base64URLEncode($data){
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data)); // Codificamos los datos en base64 y reemplazamos los caracteres no seguros para URL
    }

    public static function createSecureCookie($cookieName, $cookieValue, $expirationTime, $path){
        $domain = ''; // Definimos el dominio de la cookie
        $secure = false; // Definimos si la cookie es segura
        $httponly = true; // Definimos si la cookie es accesible solo a través de HTTP
        setcookie($cookieName, $cookieValue, $expirationTime, $path, $domain, $secure, $httponly); // Creamos la cookie segura
    }
}