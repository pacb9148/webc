
<?php

header("Content-type: text/html; charset=utf8");

    function form_mail($sPara, $sAsunto, $sTexto, $sDe)
    {
        $bHayFicheros = 0;
        $sCabeceraTexto = "";
        $sAdjuntos = "";
        $sCuerpo = utf8_decode($sTexto);
        $sSeparador = uniqid("_Separador-de-datos_");

        $sCabeceras = "MIME-version: 1.0\n";

        // Recogemos los campos del formulario 
        foreach ($_POST as $snombre => $sValor)
            $sCuerpo = $sCuerpo."\n".$snombre." = ".$sValor;

        // Recorremos los Ficheros 
        foreach ($_FILES as $vAdjunto)
        {
            if ($bHayFicheros == 0)
            {
                // Hay ficheros 
                $bHayFicheros = 1;
                // Cabeceras generales del mail 
                $sCabeceras .= "Content-type: multipart/mixed;";
                $sCabeceras .= "boundary=\"".$sSeparador."\"\n";
                // Cabeceras del texto 
                $sCabeceraTexto = "--".$sSeparador."\n";
                $sCabeceraTexto .= "Content-type: text/plain;charset=iso-8859-1\n";
                $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n\n";
                $sCuerpo = $sCabeceraTexto.$sCuerpo;
            }

            // Se añade el fichero 
            if ($vAdjunto["size"] > 0)
            {
                $sAdjuntos .= "\n\n--".$sSeparador."\n";
                $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";
                $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
                $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";

                $oFichero = fopen($vAdjunto["tmp_name"], 'rb');
                $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"]));
                $sAdjuntos .= chunk_split(base64_encode($sContenido));

                fclose($oFichero);
            }

        }

         // Si hay ficheros se añaden al cuerpo 
        if ($bHayFicheros)
            $sCuerpo .= $sAdjuntos."\n\n--".$sSeparador."--\n";

        // Se añade la cabecera de destinatario 
        if ($sDe)$sCabeceras .= "From:".$sDe."\n";

        // Por último se envia el mail 
        return(mail ($sPara, $sAsunto, $sCuerpo, $sCabeceras));
    }

    //Ejemplo de como usar: 
    if (form_mail("support@grupocostain.com",
                                "Remitente - Contacto via formulario web",
                                "Los datos introducidos en el formulario son:\n",
                                $_POST["email"]))
    echo "<script language='javascript'> alert('Tu mensaje se ha enviado, muchas gracias.'); 
            window.location.href = 'http://grupocostain.com';
            </script>";

?>
