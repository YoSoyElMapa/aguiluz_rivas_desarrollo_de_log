<?php
    ini_set('display_error',1);
    ini_set('display_startup_error',1);
    include(__DIR__ . '/../inc/funciones.inc.php');
    include(__DIR__ . '/ips.php');

    $metodo_permitido = "POST";
    $archivo = "logs/log.log";
    $metodo_autorizado = "localhost";
    $dominio_autorizado = "http://" . $_SERVER["HTTP_HOST"];
    $ip = ip_in_ranges($_SERVER["REMOTE_ADDR"],$rango);
    $txt_usuario_autorizado = "admin";
    $txt_password_autorizado = "admin";

    if(array_key_exists("HTTP_REFERER",$_SERVER)){
        //VIENE DE UNA PAGINA DENTRO DEL SISTEMA



        if(strpos($_SERVER["HTTP_REFERER"]??"NO_REFERER",$dominio_autorizado) !== false) {
            //EL REFERER DE DONDE VIENE LA PETICION ESTA AUTORIZADO

            if($ip === true){
                //LA DIRECCION IP DEL USUARIO SI ESTA AUTORIZADA

                //SE VERIFICA QUE EL USUARIO HAYA ENVIADO UNA PETICION AUTORIZADA
                if($_SERVER["REQUEST_METHOD"] == $metodo_permitido){
                    //EL METODO ENVIADO POR EL USUARIO SI ESTA AUTORIZADO

                    //LIMPIEZA DE VALORES  QUE VIENEN DESDE EL FORMULARIO
                    $valor_campo_usuario = ( (array_key_exists("txt_user",$_POST)) ? htmlspecialchars(stripslashes(trim($_POST["txt_user"])),ENT_QUOTES) : "");
                    $valor_campo_password = ( (array_key_exists("txt_pass",$_POST)) ? htmlspecialchars(stripslashes(trim($_POST["txt_pass"])),ENT_QUOTES) : "");


                    //SE VERIFICA QUE LOS VALORES DE LOS CAMPOS SEAN DIFERENTES DE VACIO
                    if( ($valor_campo_usuario!=""|| strlen($valor_campo_usuario) > 0 ) and ($valor_campo_password!="" || strlen($valor_campo_password) > 0) ){
                        //LA VARIABLES SI TIENEN VALORES

                        $usuario = preg_match('/^[a-zA-Z0-9]{1,10}+$/',$valor_campo_usuario);//SE VERIFICA CON UN PATRON SI EL VALOR DEL CAMPO "usuario" CUMPLE CON LAS CONDICIONES
                        //ACEPTABLES (SE ACEPTAN, NUMEROS, LETRAS MAYUSCULAS Y LETRAS MINUSCULAS, EN UN MAXIMO DE 10 CARACTERES Y UN MINIMO DE 1 CARACTER)
                        $password = preg_match('/^[a-zA-Z0-9]{1,10}+$/',$valor_campo_password);//SE VERIFICA CON UN PATRON SI EL VALOR DEL CAMPO "usuario" CUMPLE CON LAS CONDICIONES
                        //ACEPTABLES (SE ACEPTAN, NUMEROS, LETRAS MAYUSCULAS Y LETRAS MINUSCULAS, EN UN MAXIMO DE 10 CARACTERES Y UN MINIMO DE 1 CARACTER)

                        //SE VERIFICA QUE LOS RESULTADOS DEL PATRON SEAN EXCLUSIVAEMENTE SATISFACTORIOS
                        if($usuario !== false and $usuario !== 0 and $password !== false and $password !== 0){
                            //EL USUARIO Y LA CONRASEÑA SI POSEEN VALORES ACEPTADOS

                            if($valor_campo_usuario === $txt_usuario_autorizado and $valor_campo_password === $txt_password_autorizado){
                                //EL USUARIO INGRESO LAS CREDENCIALES CORRECTAS
                                echo("HOLA MUNDO");
                                crear_editar_log($archivo, "EL CLIENTE INICIO SESION SATISFACTORIAMENTE",1,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                            }else{
                                //EL USUARIO NO INGRESO LAS CREDENCIALES CORRECTAS
                                crear_editar_log($archivo, "CREDENCIALES INCORRECTAS ENVIADAS HACIA //",$_SERVER["HTTP_HOST"],$_SERVER["HTTP_REQUEST_URI"],2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                                header("HTTP/1.1 301 Moved Permanently");
                                header("Location: ../?status=7");
                            }
                        }else{
                            //LOS VALORES INGRESADOS EN LOS CAMPOS POSEEN CARACTERES NO SOPORTADOS
                            crear_editar_log($archivo, "ENVIO DE DATOS DEL FORMULARIO CON CARACTERES NO SOPORTADOS",3,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ../?status=6");
                        }
                    }else{
                        //LAS VARIABLES ESTAN VACIAS
                        crear_editar_log($archivo, "ENVIO DE CAMPOS VACIOS AL SERVIDOR",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ../?status=5");
                    }

                }else{
                    //EL METODO ENVIADO POR EL USUARIO NO ESTA AUTORIZADO
                    crear_editar_log($archivo, "ENVIO DE METODO NO AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=4");
                }
            }else{
                //LA DIRECCION IP DEL USUARIO NO ESTA AUTORIZADA
                crear_editar_log($archivo, "DIRECCION IP NO AUTORIZADA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=3");
            }
        }else{
            //EL REFERER DE DONDE VIENE LA PETICION ES DE UN ORIGEN DESCONOCIDO
            crear_editar_log($archivo, "HA INTENTADO SUPLANTAR UN REFERER QUE NO ESTA AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=2");
            }
    }else{
        //EL USUARIO DIGITO LA URL DESDE EL NAVEGADOR SIN PASAR POR EL FORMULARIO
        crear_editar_log($archivo, "EL USUARIO HA INTENTADO INGRESAR AL SISTEMA DE UNA MANERA INCORRECTA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"]??"NO_REFERER",$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=1");
    }
?>