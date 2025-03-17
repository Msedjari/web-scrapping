<?php
  SessionController::userLogout();
// Redirigir a la página de inicio de sesión después de procesar el formulario
header("Location: /login");
exit;