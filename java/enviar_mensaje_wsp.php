<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
require "../coneccion.php" ;
  mysqli_set_charset($conection, 'utf8'); //linea a colocar

  $query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
  $result_configuracion = mysqli_fetch_array($query_configuracioin);
  $ambito_area          =  $result_configuracion['ambito'];
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;

  if ($_POST['action'] == 'enviar_mensaje_wsp') {

    $numero_wsp = mysqli_real_escape_string($conection, $_POST['numero_wsp']);
    $mensaje = mysqli_real_escape_string($conection, $_POST['mensaje']);

                function getRealIP(){
                          if (isset($_SERVER["HTTP_CLIENT_IP"])){
                              return $_SERVER["HTTP_CLIENT_IP"];
                          }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                              return $_SERVER["HTTP_X_FORWARDED_FOR"];
                          }elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
                          {
                              return $_SERVER["HTTP_X_FORWARDED"];
                          }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
                          {
                              return $_SERVER["HTTP_FORWARDED_FOR"];
                          }elseif (isset($_SERVER["HTTP_FORWARDED"]))
                          {
                              return $_SERVER["HTTP_FORWARDED"];
                          }
                          else{
                              return $_SERVER["REMOTE_ADDR"];
                          }

                      }
                      if ($url =='http://localhost') {
                        $direccion_ip =  '186.42.10.32';
                      }else {
                        $direccion_ip = (getRealIP());
                      }

                      $datos = unserialize(file_get_contents('http://ip-api.com/php/'.$direccion_ip.''));

                       $pais            = $datos['country'];
                       $ciudad            = $datos['city'];
                       $provincia         = $datos['regionName'];
                       $fecha_actual = date('d-m-Y H:m:s', time());

                       $lon         = $datos['lon'];
                       $lat         = $datos['lat'];


                       function getDeviceDetails() {
                          $userAgent = $_SERVER['HTTP_USER_AGENT'];
                          $osPlatform = "Unknown OS Platform";
                          $osArray = array(
                              '/windows nt 10/i'     =>  'Windows 10',
                              '/windows nt 6.3/i'     =>  'Windows 8.1',
                              '/windows nt 6.2/i'     =>  'Windows 8',
                              '/windows nt 6.1/i'     =>  'Windows 7',
                              '/windows nt 6.0/i'     =>  'Windows Vista',
                              '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                              '/windows nt 5.1/i'     =>  'Windows XP',
                              '/windows xp/i'         =>  'Windows XP',
                              '/windows nt 5.0/i'     =>  'Windows 2000',
                              '/windows me/i'         =>  'Windows ME',
                              '/win98/i'              =>  'Windows 98',
                              '/win95/i'              =>  'Windows 95',
                              '/win16/i'              =>  'Windows 3.11',
                              '/macintosh|mac os x/i' =>  'Mac OS X',
                              '/mac_powerpc/i'        =>  'Mac OS 9',
                              '/linux/i'              =>  'Linux',
                              '/ubuntu/i'             =>  'Ubuntu',
                              '/iphone/i'             =>  'iPhone',
                              '/ipod/i'               =>  'iPod',
                              '/ipad/i'               =>  'iPad',
                              '/android/i'            =>  'Android',
                              '/blackberry/i'         =>  'BlackBerry',
                              '/webos/i'              =>  'Mobile'
                          );

                          foreach ($osArray as $regex => $value) {
                              if (preg_match($regex, $userAgent)) {
                                  $osPlatform = $value;
                              }
                          }

                          $browser = "Unknown Browser";
                          $browserArray = array(
                              '/msie/i'       => 'Internet Explorer',
                              '/firefox/i'    => 'Firefox',
                              '/safari/i'     => 'Safari',
                              '/chrome/i'     => 'Chrome',
                              '/edge/i'       => 'Edge',
                              '/opera/i'      => 'Opera',
                              '/netscape/i'   => 'Netscape',
                              '/maxthon/i'    => 'Maxthon',
                              '/konqueror/i'  => 'Konqueror',
                              '/mobile/i'     => 'Handheld Browser'
                          );

                          foreach ($browserArray as $regex => $value) {
                              if (preg_match($regex, $userAgent)) {
                                  $browser = $value;
                              }
                          }

                          return array(
                              'os' => $osPlatform,
                              'browser' => $browser
                          );
                      }

                      $deviceDetails = getDeviceDetails();

                      $sistema_oprativo = $deviceDetails['os'];
                      $buscador          = $deviceDetails['browser'];

                      $mensaje = "Mensaje genérico generado por el sistema de envío de guibis.com\n" .
                     "País: $pais\n" .
                     "Ciudad: $ciudad\n" .
                     "Provincia: $provincia\n" .
                     "Fecha: $fecha_actual\n" .
                     "SO: $sistema_oprativo\n" .
                     "browser: $buscador\n\n" .
                     "$mensaje\n\n" .
                     "https://guibis.com/mapa_envio_mensaje?lat=$lat&lon=$lon";


                // La URL de la API donde enviarás la solicitud POST
                $url = 'https://guibis.com/dev/message/';

                // Los datos que quieres enviar
                $datos = array(
                    'mensaje' => $mensaje,
                    'numero_wsp' => $numero_wsp, // Sustituye con el número real de WhatsApp.
                    'KEY' => 'F48ECE4E445FD02B738F96B8561E32B9',   // Sustituye con tu clave API real.
                    'callback' => 'envio_mensajes' // Sustituye con el callback real.
                );

                // Codifica los datos a JSON
                $datos_json = json_encode($datos);

                // Inicializa cURL
                $ch = curl_init($url);

                // Configura las opciones de cURL para enviar una solicitud POST con JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datos_json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($datos_json)
                ));

                // Envía la solicitud
                $response = curl_exec($ch);

                // Verifica si hubo un error en la solicitud
                if (curl_errno($ch)) {
                    throw new Exception(curl_error($ch));
                }

                // Cierra la sesión cURL
                curl_close($ch);

                // Decodifica la respuesta JSON y la imprime
                $response_data = json_decode($response, true);

                if (isset($response_data['noticia'])) {
                    if ($response_data['noticia'] == 'mensaje_enviado') {

                      $arrayName = array('respuesta' => 'mensaje_enviado_correctamente');
                      echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
                        // Mensaje enviado exitosamente
                        //echo "Mensaje enviado correctamente. Mensajes restantes: " . $response_data['mensajes_restantes'];
                    } elseif ($response_data['noticia'] == 'No se pudo enviar el mensaje') {

                      $arrayName = array('respuesta' => 'no_se_envio_mensaje');
                      echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
                        // No se pudo enviar el mensaje
                        //echo "No se pudo enviar el mensaje.";
                    } else {

                      $arrayName = array('respuesta' => 'respuesta_diferente','mensaje' =>$response_data['noticia']);
                      echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
                        // Otro tipo de noticia
                        //echo "Noticia: " . $response_data['noticia'];
                    }
                } else {
                    // Respuesta no contiene el campo 'noticia'
                    echo "Respuesta no reconocida: " . $response;

                    $arrayName = array('respuesta' => 'respuesta_diferente','mensaje' =>$response);
                    echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
                }





        }




 ?>
