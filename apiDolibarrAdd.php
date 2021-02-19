<?php

////////////////////////////////////////
//PROCESO DE INSERCION DE DATOS
////////////////////////////////////////

//echo '<script>console.log(' . json_encode(strtotime("now"), JSON_HEX_TAG) . ');</script>';  
//if(isset($_POST['functionname']))
//{

//echo '<script>console.log(' . json_encode(strtotime("now"), JSON_HEX_TAG) . ')</script>'; 
//echo '<script>console.log(' . date("Y-m-d H:i:s") . ')</script>'; 

date_default_timezone_set('America/Mexico_City');

$dateSisIni = str_replace(' ', '%20', date("Y-m-d H", strtotime('-200 hours')));
$dateSisFin = str_replace(' ', '%20', date("Y-m-d H"));

//$pruebaleft = str_pad("003", 7, "0", STR_PAD_LEFT);

/////////////////////
//OBTENER INFORMACION DE LAS APIS DE DOLIBARR
$dolNameKey = "DOLAPIKEY";
$dolApiKey = "PDrive640440!";

$dolNameKeyPipe = "api_token";
$dolApiKeyPipe = "6beabb33d84b7f9faefc97973c50993846a258f6";

$urlProducts = "https://www.bks.com.mx/bcorp/api/index.php/products?limit=3000&sqlfilters=te.acpd%3D0%20and%20t.tosell%3D1%20and%20t.finished%3D1%20and%20t.fk_product_type%3D0%20and%20t.entity%3D1%20and%20te.mgpd%3D1"; //sqlfilters=t.fk_product_type%3D0%20and%20t.tosell%3D1%20and%20t.finished%20%3D%201%20and%20te.acpd%20%3D%200";//and%20t.datec%20%3E%3D%20'" . $dateSisIni . "'%20and%20t.datec%20%3C%3D%20'" . $dateSisFin . "'";
$urlProductsPut = "https://www.bks.com.mx/bcorp/api/index.php/products/";
$urlProductsPipe = "https://api.pipedrive.com/v1/products?start=0&" . $dolNameKeyPipe . "=" . $dolApiKeyPipe;
$urlCategories = "https://www.bks.com.mx/bcorp/api/index.php/categories/";

$urlProposals = "https://www.bks.com.mx/bcorp/api/index.php/proposals?limit=3000&sqlfilters=t.rowid%20%3D%20100"; //sqlfilters=te.acpd%3D0%20and%20t.fk_statut%3E0%20and%20t.entity%3D1"; //sqlfilters=t.rowid%20%3D%20100";
$urlProposalsPut = "https://www.bks.com.mx/bcorp/api/index.php/proposals/";
$urlProposalsPipe = "https://api.pipedrive.com/v1/deals?start=0&" . $dolNameKeyPipe . "=" . $dolApiKeyPipe;
$urlProposalsContact = "https://www.bks.com.mx/bcorp/api/index.php/contacts/";
$urlProposalsThird = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties/";
$urlProposalsProductPipe = "https://api.pipedrive.com/v1/deals/";
$urlProposalsProductDoli = "https://www.bks.com.mx/bcorp/api/index.php/products/";

$urlContacts = "https://www.bks.com.mx/bcorp/api/index.php/contacts?limit=3000&sqlfilters=te.acpd%3D0%20and%20t.statut%3D1%20and%20t.entity%3D1%20and%20te.mgpd%3D1";
$urlContactsPut = "https://www.bks.com.mx/bcorp/api/index.php/contacts/";
$urlContactsPipe = "https://api.pipedrive.com/v1/persons?start=0&" . $dolNameKeyPipe . "=" . $dolApiKeyPipe;
$urlContactsThird = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties/";

$urlThirdparties = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties?limit=3000&sqlfilters=te.acpd%3D0%20and%20t.status%3D1%20and%20t.entity%3D1%20and%20te.mgpd%3D1%20and%20(t.client%3D1%20or%20t.client%3D2%20or%20t.client%3D3)";
$urlThirdpartiesPut = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties/";
$urlThirdpartiesPipe = "https://api.pipedrive.com/v1/organizations?start=0&" . $dolNameKeyPipe . "=" . $dolApiKeyPipe;

$tituloCorreoThird = "Agregar - Carga masiva - Organizaciones";
$tituloCorreoContact = "Agregar - Carga masiva - Contactos";
$tituloCorreoProduct = "Agregar - Carga masiva - Productos";
$tituloCorreoProposal = "Agregar - Carga masiva - Propuestas";
$deCorreo = "alan.amst@gmail.com";
$paraCorreo = "alan_solo@hotmail.com";

$lengthCodeClient = 7;

///////////////////////////////////
//FUNCION API DOLIBARR POST PUT GET
function callAPI($method, $nameKey, $apikey, $url, $data = false)
{
    $curl = curl_init();
    $httpheader = [$nameKey . ':' . $apikey];

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

            break;
        case "PUT":

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

////////////////////////////////////
//FUNCION API PIPEDRIVE POST PUT GET
function callAPIPipe($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

            break;
        case "PUT":

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}


///////////////////////////////////
//ENVIAR CORREO
function EnvioCorreo($de, $para, $titulo, $mensaje)
{
    $to      = $de;
    $subject = $titulo;
    //$message = 'Hola mundo';
    /*
            $headers = 'From: alan_solo@hotmail.com' . "\r\n" .
                'Reply-To: alan_solo@hotmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                */
    $headers = "Content-Type: text/html; charset=UTF-8\r\n";

    $message = '<!doctype html>' .
        '<html lang="en">' .
        '<head>' .
        '<!-- Required meta tags -->' .
        '<meta charset="utf-8">' .
        '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">' .
        //'<title>Hello, world!</title>' .
        //$titulo .
        '</head>' .
        '<body style="background-color:#CAE2F9;">' .
        '<div>' .
        '<h3>' . $titulo . '</h3>' .
        '</div>' .
        '</hr>' .
        '<div style="color:red">' .
        '<h2>Lista de errores:</h2>' .
        //'<li>This is a danger alert—check it out!</li>' .
        $mensaje .
        '</div>' .
        '</hr>' .

        '</body>' .
        '</html>';

    mail($para, $subject, $message, $headers);
}


/////////////////////////////////////
/////////////////////////////////////
//PROCESO ORGANIZACION
/////////////////////////////////////
/////////////////////////////////////

//EnvioCorreo();
////////////////
//LISTA ORGANIZACION
$listThirds = CallAPI("GET", $dolNameKey, $dolApiKey, $urlThirdparties);
$listThirds = json_decode($listThirds, true);

//////////////////////////////
//RECORRER LA LISTA ORGANIZACION
if (!isset($listThirds["error"]) && count($listThirds) > 0) {
    $mensajeErrorThird = "";

    foreach ($listThirds as $posicion => $thirds) {
        try {
            if (isset($thirds["code_client"]) && !empty($thirds["code_client"]) && strlen($thirds["code_client"]) == $lengthCodeClient) {
                $idThirds = $thirds["id"];
                //$idThirdsPipe = $thirds["array_options"]["options_idpd"];

                $fechaCreacionThird = "";
                if (!empty($thirds["date_creation"])) {
                    $fechaCreacionThird = date("Y-m-d H:i:s", $thirds["date_creation"]);
                }

                $fechaModificacionThird = "";
                if (!empty($thirds["date_modification"])) {
                    $fechaModificacionThird = date("Y-m-d H:i:s", $thirds["date_modification"]);
                }

                ///////////////////////////////
                //INFORMACION COMPLETA ARRAY OPTIONS
                $arrayOptionsThird["array_options"] = $thirds["array_options"];

                ////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataThirdsPipe = array(
                    //THIRD
                    "a5b878fafdf36082b8b9387c1cd678c77af84626" => $idThirds, //rowid
                    "name" => $thirds["name"], //name
                    //"data" => $thirds["name_alias"],
                    //"additional_data" => $thirds["address"],
                    "51979244a94759ff79197fd019fae361413b4d51" => $thirds["entity"], //entity
                    "c965c74fa89644ca278dea84b00eb1f82c9adbdd" => $thirds["status"], //statut
                    "d1f61afdaaf556c4906c3f26ca35366c25bb66ef" => $fechaModificacionThird, //tms hora
                    "bc7f3571c5968bac787a4ca25187f1504fdfcd8e" => $fechaModificacionThird, //tms
                    "a664fc5944ee09fff86d24c96364118758a65f92" => $fechaCreacionThird, //datec
                    "63597182cc3eb28528aa28984d6878878f544c42" => $fechaCreacionThird, //datec hora
                    //"adb9f47c492dd7d1c07ca4ded5abce0a25a2cbcc" => $thirds["status"], //status
                    "9f730ad1c7187bf79750376c59dd25389349db24" => $thirds["code_client"], //code_client
                    "address" => $thirds["address"], //address
                    "9f7ead56b4ca4c1597516d0ba343be8b5fddedc5" => $thirds["zip"], //zip
                    "ec58bc081f8ec7538463d9045a2de33fda90d04e" => $thirds["town"], //town
                    "48cc12957fe8754bab8c3065c25c2f28755b7091" => $thirds["state_id"], //fk_departement
                    "307103485f11067f7d163bbef81f9ede4cba91bd" => $thirds["country_id"], //fk_pays
                    "73a25af23abad8331a4d5252a3981bc6c2f6a479" => $thirds["phone"], //phone
                    "e71d46aad8b066bbb72aa4cb623d11d93ce00db9" => $thirds["fax"], //fax
                    "c80bd3163b99f7a9350b29b463c49f3a0d7ecab6" => $thirds["url"], //url
                    "8dd4eb019733a1b33a6d77a93d41cf6a918439c1" => $thirds["email"], //email
                    //"8615bcb454c9ad446fa1d9d886570d8573d16dac" => $thirds["id"], //socialnetworks
                    "4db4bcacc6ccef512c45453a0537cd8ca3749262" => $thirds["array_options"]["options_whatsapp"], //whatsapp
                    "fbc988be0c2eb230c3d9ddf43fb44be78666b7f4" => $thirds["effectif_id"], //fk_effectif
                    "6c7788b8bc65ef185667c405ab835ccea2b1cd9d" => $thirds["idprof1"], //siren
                    "560630ca23f0cb5bcb0a57f71b7129faeede3390" => $thirds["client"], //client
                    "2163d516bfe8c16cf57ad0bd66bd76c15658784e" => $thirds["fk_prospectlevel"], //fk_prospectlevel
                    "36da2dc1bdd120c1eaf0062c4ed339c903d0a465" => $thirds["price_level"], //price_level
                    //EXTRAFIELDS
                    //"bdc929758c85468150246fcfa2455ef1c21528bd" => $thirds["id"], //tms
                    //"315ab29dbe061afc0ca3329c176359f8cb4107de" => $thirds["array_options"]["options_fac_pub_gral"],//fk_object
                    "7bd5c41419aa372165525f38f82706f7fa82c46e" => $thirds["array_options"]["options_contado"], //contado
                    "8c071979cefde4d3bbd71c89029b2845617b2aa8" => $thirds["array_options"]["options_fac_pub_gral"], //fac_pub_gral
                    "c80bd3163b99f7a9350b29b463c49f3a0d7ecab6" => $thirds["array_options"]["options_usocfdi"], //usocfdi
                    "5948ffaaed0953101f50977396fb7487ed30bb69" => $thirds["array_options"]["options_formapagcfdi"], //formapagcfdi
                    //"e43a68d42badedf6dbbcf3b058b65ca9c698978a" => $thirds["array_options"]["options_whatsapp"], //whatsapp
                    "2f80a5416a66075c3944006c9bca4f80f13852f5" => $thirds["array_options"]["options_potencial"], //potencial
                    "e67d60f2202535ba7fcdc68d52d4946ab298f23e" => $thirds["array_options"]["options_negocio"], //negocio
                    "7c482bf133444c4ab1bcaf76665437ddc55a5914" => $thirds["array_options"]["options_territorio"], //territorio
                    "bc2831691a7c5970bc65505a055676b08b2a3464" => $thirds["country"], //pais
                    "707a16c175da0aa667f7a90778937a5c25ec30d8" => $thirds["state"],
                );

                /*
                $resThirdsPipe = CallAPIPipe("POST", $urlThirdpartiesPipe, $dataThirdsPipe);
                $resThirdsPipe = json_decode($resThirdsPipe, true);



                if (isset($resThirdsPipe["success"]) && $resThirdsPipe["success"]) {

                    $arrayOptionsThird["array_options"]["options_acpd"] = "1";
                    $arrayOptionsThird["array_options"]["options_idpd"] = strval($resThirdsPipe["data"]["id"]);

                    if (!isset($arrayOptionsThird["array_options"]["options_cp_fedex"]) || empty($arrayOptionsThird["array_options"]["options_cp_fedex"])) {
                        $arrayOptionsThird["array_options"]["options_cp_fedex"] = "0";
                    }

                    if (!isset($arrayOptionsThird["array_options"]["options_vts_pg"]) || empty($arrayOptionsThird["array_options"]["options_vts_pg"])) {
                        $arrayOptionsThird["array_options"]["options_vts_pg"] = "0";
                    }

                    if (!isset($arrayOptionsThird["array_options"]["options_formapagcfdi"]) || empty($arrayOptionsThird["array_options"]["options_formapagcfdi"])) {
                        $arrayOptionsThird["array_options"]["options_formapagcfdi"] = "PUE";
                    }

                    if (!isset($arrayOptionsThird["array_options"]["options_usocfdi"]) || empty($arrayOptionsThird["array_options"]["options_usocfdi"])) {
                        $arrayOptionsThird["array_options"]["options_usocfdi"] = "G01";
                    }

                    if (!isset($arrayOptionsThird["array_options"]["options_pub_gral"]) || empty($arrayOptionsThird["array_options"]["options_pub_gral"])) {
                        $arrayOptionsThird["array_options"]["options_pub_gral"] = "NO";
                    }


                    $listThirdEdit = callAPI("PUT", $dolNameKey, $dolApiKey, $urlThirdpartiesPut . $idThirds, json_encode($arrayOptionsThird));
                    $listThirdEdit = json_decode($listThirdEdit, true);
                } else {
                    $mensajeErrorThird .= "<li>Id: " . $idThirds . ", Nombre: " . $thirds["name"] . ", Error: " . $resThirdsPipe["error"] . "</li>";
                }
                */
            }
        } catch (Exception $e) {
            $mensajeErrorThird .= "<li>Id: " . $idThirds . ", Nombre: " . $thirds["name"] . ", Error: " . $e->getMessage() . "</li>";
        }
    }

    ////////////////////////////
    //ENVIO DE CORREO ORGANIZACION       
    if (!empty($mensajeErrorThird)) {
        EnvioCorreo($deCorreo, $paraCorreo, $tituloCorreoThird, $mensajeErrorThird);
    }
}



/////////////////////////////////////
/////////////////////////////////////
//PROCESO CONTACTOS
/////////////////////////////////////
/////////////////////////////////////

////////////////
//LISTA CONTACTOS
$listContacts = CallAPI("GET", $dolNameKey, $dolApiKey, $urlContacts);
$listContacts = json_decode($listContacts, true);

//////////////////////////////
//RECORRER LA LISTA CONTACTOS
if (!isset($listContacts["error"]) && count($listContacts) > 0) {
    $mensajeErrorContact = "";

    foreach ($listContacts as $posicion => $contact) {
        try {
            if (isset($contact["socid"]) && !empty($contact["socid"])) {
                $idContact = $contact["id"];
                $idContactThird = $contact["socid"];
                //$idContactPipe = $contact["array_options"]["options_idpd"];

                $fechaCreacionContact = "";
                if (!empty($contact["date_creation"])) {
                    $fechaCreacionContact = date("Y-m-d H:i:s", $contact["date_creation"]);
                }

                $fechaModificacionContact = "";
                if (!empty($contact["date_modification"])) {
                    $fechaModificacionContact = date("Y-m-d H:i:s", $contact["date_modification"]);
                }

                $jabber = "";
                if (isset($contact["socialnetworks"]["jabber"])) {
                    $jabber = $contact["socialnetworks"]["jabber"];
                }

                $whatsapp = "";
                if (isset($contact["socialnetworks"]["whatsapp"])) {
                    $whatsapp = $contact["socialnetworks"]["whatsapp"];
                }

                $numExterior = "";
                if (isset($contact["array_options"]["options_numexterior"])) {
                    $numExterior = $contact["array_options"]["options_numexterior"];
                }

                $numInterior = "";
                if (isset($contact["array_options"]["options_numinterior"])) {
                    $numInterior = $contact["array_options"]["options_numinterior"];
                }

                $tipreg = "";
                if (isset($contact["array_options"]["options_tipreg"])) {
                    $tipreg = $contact["array_options"]["options_tipreg"];
                }

                $domicilioEntrega = "";
                if (isset($contact["array_options"]["options_domicilioentrega"])) {
                    $domicilioEntrega = $contact["array_options"]["options_domicilioentrega"];
                }

                $funcionContacto = "";
                if (isset($contact["array_options"]["options_funcioncontacto"])) {
                    $funcionContacto = $contact["array_options"]["options_funcioncontacto"];
                }


                ///////////////////////////////
                //INFORMACION COMPLETA ARRAY OPTIONS
                $arrayOptionsContact["array_options"] = $contact["array_options"];


                ///////////////////////////////////////////////
                //OBTENER ID ORGANIZACION DOLIBARR DE PIPEDRIVE
                $idContactThirdPipe = 0;


                $listContactsThird = CallAPI("GET", $dolNameKey, $dolApiKey, $urlContactsThird . $idContactThird);
                $listContactsThird = json_decode($listContactsThird, true);


                if (count($listContactsThird)) {
                    if (isset($listContactsThird["array_options"]["options_idpd"])) {
                        $idContactThirdPipe = $listContactsThird["array_options"]["options_idpd"];
                    } else {
                        $mensajeErrorContact .= "<li>Id: " . $idContact . ", Nombre: " . $contact["firstname"] . " " . $contact["lastname"] . ", Error: No se encontro la organizacion con la que esta relacionada en Dolibarr." . "</li>";
                    }
                } else {
                    $mensajeErrorContact .= "<li>Id: " . $idContact . ", Nombre: " . $contact["firstname"] . " " . $contact["lastname"] . ", Error: No se encontro la organizacion con la que esta relacionada en Dolibarr." . "</li>";
                }



                ///////////////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataContactPipe = array(
                    //PEOPLE
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $idContact, //rowid
                    "org_id" => $idContactThirdPipe,
                    "name" => $contact["firstname"] . " " . $contact["lastname"],
                    "2fb92dd064d0c7eb0414fff27723102eff1d03a7" => $fechaCreacionContact, //datec
                    "a44bbf0f64bc2330f9259bd00591d4b75a574d6a" => $fechaCreacionContact, //datec hora
                    "65ea6ea2752b4815401b01d7ae81fe2dc157f2a5" => $fechaModificacionContact, //tms hora
                    "a9de03ddf89df42aa7c3adf6c88bd0f886d21f77" => $fechaModificacionContact, //tms
                    "1f5d9bd66cb2c18ea2864f9a42c024b729a9368f" => $contact["socid"], //fk_soc
                    "452a78b6083aa680044ca988b68794f0436d8574" => $contact["entity"], //entity
                    "983a21fcd8278f56b9d8ccee69a64b510da09c31" => $contact["civility"], //civility
                    "56fceb93ac6090ef277dbb634f543a501f604184" => $contact["lastname"], //lastname
                    "d8c4b1171cb0f9326af453f1260f57594d9b813f" => $contact["firstname"], //firstname
                    "5cff9ed6829a1633630893bb67caed4c10956426" => $contact["address"], //address
                    "b722390bed15829401be9226b2b49273c9934245" => $contact["zip"], //zip
                    "82954b2cf81f1ddf3172a242f7d543fd0d473575" => $contact["town"], //town
                    "ac64bcd4c256755cf27a5c0780d50d6c3f284eaf" => $contact["state_id"], //fk_departement
                    "2379a4c3df490dc0eb1dd83ff19d6d996c752d34" => $contact["country_id"], //fk_pays
                    "aa2899a40b23b7edbfc64b42d3ad9d6cf9411775" => $contact["birthday"], //birthday
                    "b0396742da038967d4f2029c68ada251be25323f" => $contact["poste"], //poste
                    "phone" => $contact["phone_pro"], //phone
                    "b64091221561eb8a9dda32704dd29dbfee795290" => $contact["phone_perso"], //phone_perso
                    "b1708fa7673797cf973aae82c838d564fbe1a5df" => $contact["phone_mobile"], //phone_mobile
                    "email" => $contact["email"], //email
                    "1241f63687d014706519202472a700321cd39265" => $jabber, //socialnetworks
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $whatsapp, //whatsapp
                    "ac60b777b44d44ecd19698a7ddccf705abcc5840" => $contact["statut"], //statut
                    //"4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $contact["id"], //bks_whatsapp_compras
                    //OPTIONS
                    //"a7b2c05b5fa3d4a1bf7448982a2685470c788109" => $contact["id"],//fk_object
                    "dc2ae964f76df57566517377f50f75c2d71136be" => $numExterior, //numexterior
                    "b75d613546a5bc4445a359940fdcf3ecec932fc6" => $numInterior, //numinterior
                    "721dfb5509bc763410c50fb5c803c5621842afbf" => $tipreg, //tipreg
                    "9af3b6cc7800a99aa399f649a176f1a61f75dc64" => $domicilioEntrega, //domicilioentrega
                    "6d8245c2bff0524719ac3ccb960dea3c7a034905" => $funcionContacto, //funcioncontacto
                    "5ca9136f19cff56456ded71423526651502674c0" => $contact["country"], //pais
                    "7a50b3f83c0fa869106ac8d09820b2389fe71cd3" => $contact["state"], //estado
                );

                /*
                $resContactPipe = CallAPIPipe("POST", $urlContactsPipe, $dataContactPipe);
                $resContactPipe = json_decode($resContactPipe, true);



                if (isset($resContactPipe["success"]) && $resContactPipe["success"]) {
                    $arrayOptionsContact["array_options"]["options_acpd"] = "1";
                    $arrayOptionsContact["array_options"]["options_idpd"] = strval($resContactPipe["data"]["id"]);

                    if (!isset($arrayOptionsContact["array_options"]["options_tipreg"]) || empty($arrayOptionsContact["array_options"]["options_tipreg"])) {
                        $arrayOptionsContact["array_options"]["options_tipreg"] = "0";
                    }

                    if (!isset($arrayOptionsContact["array_options"]["options_domicilioentrega"]) || empty($arrayOptionsContact["array_options"]["options_domicilioentrega"])) {
                        $arrayOptionsContact["array_options"]["options_domicilioentrega"] = "0";
                    }

                    if (!isset($arrayOptionsContact["array_options"]["options_numexterior"]) || empty($arrayOptionsContact["array_options"]["options_numexterior"])) {
                        $arrayOptionsContact["array_options"]["options_numexterior"] = "0";
                    }

                    if (!isset($arrayOptionsContact["array_options"]["options_funcioncontacto"]) || empty($arrayOptionsContact["array_options"]["options_funcioncontacto"])) {
                        $arrayOptionsContact["array_options"]["options_funcioncontacto"] = "0";
                    }


                    $listContactsEdit = callAPI("PUT", $dolNameKey, $dolApiKey, $urlContactsPut . $idContact, json_encode($arrayOptionsContact));
                    $listContactsEdit = json_decode($listContactsEdit, true);
                } else {
                    $mensajeErrorContact .= "<li>Id: " . $idContact . ", Nombre: " . $contact["firstname"] . " " . $contact["lastname"] . ", Error: " . $resContactPipe["error"] . "</li>";
                }
                */
            }
        } catch (Exception $e) {
            $mensajeErrorContact .= "<li>Id: " . $idContact . ", Nombre: " . $contact["firstname"] . " " . $contact["lastname"] . ", Error: " . $e->getMessage() . "</li>";
        }
    }

    ////////////////////////////
    //ENVIO DE CORREO CONTACTO           
    if (!empty($mensajeErrorContact)) {
        EnvioCorreo($deCorreo, $paraCorreo, $tituloCorreoContact, $mensajeErrorContact);
    }
}



////////////////////////////////////
////////////////////////////////////
//PROCESO DE PRODUCTOS
///////////////////////////////////
///////////////////////////////////

///////////////
//LISTA PRODUCTOS
$listProducts = CallAPI("GET", $dolNameKey, $dolApiKey, $urlProducts);
$listProducts = json_decode($listProducts, true);

/////////////////////////////
//RECORRER LA LISTA PRODUCTOS
if (!isset($listProducts["error"]) && count($listProducts) > 0) {
    $mensajeErrorProduct = "";

    foreach ($listProducts as $posicion => $product) {
        try {
            $idProduct = $product["id"];
            //$idProductPipe = $product["array_options"]["options_idpd"];

            ////////////////
            //LABEL
            if (count($product["multilangs"]) > 0) {
                $labelName = $product["multilangs"]["es_MX"]["label"];
            } else {
                $labelName = $product["label"];
            }

            $precio = "0";
            if (isset($product["multiprices"]["6"])) {
                $precio = $product["multiprices"]["6"];
            }

            $precioTtc = "0";
            if (isset($product["multiprices_ttc"]["6"])) {
                $precioTtc = $product["multiprices_ttc"]["6"];
            }

            $precioTvaTx = "0";
            if (isset($product["multiprices_tva_tx"]["6"])) {
                $precioTvaTx = $product["multiprices_tva_tx"]["6"];
            }

            $precioArray = array(
                "0" => array(
                    "price" => $precio,
                    "currency" => "MXN"
                )
            );


            ////////////////////////////////
            //INFORMACION COMPLETA ARRAY OPTIONS
            $arrayOptionsProduct["array_options"] = $product["array_options"];


            $dateCreation = $product["date_creation"];
            $dateModification = $product["date_modification"];

            $categories = "";


            if (isset($product["array_options"]["options_categoria_0"])) {
                if ($product["array_options"]["options_categoria_0"] != "466") {
                    $idCategories = $product["array_options"]["options_categoria_0"];

                    if ($idCategories != "") {
                        ///////////////////////////
                        //OBTENER CATEGORIAS
                        $listCategories = CallAPI("GET", $dolNameKey, $dolApiKey, $urlCategories . $idCategories);
                        $listCategories = json_decode($listCategories, true);

                        if (count($listCategories) > 0) {
                            if (isset($listCategories["label"])) {
                                $categories = $listCategories["label"];
                            }
                        }
                    }
                }
            }


            ////////////////////////
            //INSERTAR INFORMACION EN PIPEDRIVE
            $dataProductPipe = array(
                //PRODUCT
                "efa2d1011ae8a446b016ddc8f43f9b74ab24b2c8" => $idProduct, //rowid
                "4574b2a2a5d31600ad7c41a7d3b53c9ed4eba257" => $product["ref"], //ref
                //"category" => $categories,
                "category" => $categories,
                "description" => $product["description"],
                "code" => $product["ref"],
                "prices" => $precioArray,
                "4574b2a2a5d31600ad7c41a7d3b53c9ed4eba257" => $product["entity"], //entity
                "667d28f3f7acddfef2c391b79cbf4927ef4f40fa" => $dateCreation, //datec
                "8a5fae642d1a386f7ec040149dfafa52033f13af" => $dateCreation, //datec hora
                "8d651acb6ec5c71913565820c4f9cad89eb076b8" => $dateModification, //tms hora
                "64c083a41879cb285a588c5bd620875144d6fb8c" => $dateModification, //tms
                "0a3c71abc646caeccc1274efe5b6a7159ecd8ccc" => $labelName, //label
                "name" => $labelName, //labal--name
                "144d081f4593a8eddb3756f8d930b78335f13fed" => $precio, //price
                "5a4f875741977481f60d818395519c7eaaa32a77" => $product["price_ttc"], //price_ttc
                "edf719089524c06d999fa667a3c3d5f5b63258c0" => $product["tva_tx"], //tva_tx
                //"f2df8a9f5bb0afb0c1b0b1f22605a50d3b738545" => $product[""],//tosell
                //"cc3001feeb6b06aadf3c7ab4c321b4621100bcf1" => $product[""],//fk_product_type
                "a07575affd01d486f415d792ddb71092a5cd0719" => $product["finished"], //finished
                //PRODUCT_EXTRAFIELDS
                //"877190fea56b587c0f603f3adb55a5ef88d680a3" => $product[""],//tms
                //"877190fea56b587c0f603f3adb55a5ef88d680a3" => $product[""],//fk_object
                "877190fea56b587c0f603f3adb55a5ef88d680a3" => $product["array_options"]["options_mostrar_ps"], //mostrar_ps
                "a7b2c05b5fa3d4a1bf7448982a2685470c788109" => $product["array_options"]["options_marca"], //marca
                "d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["array_options"]["options_cmarca"], //cmarca
                //PRICE
                //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//entity
                //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//tms
                //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//fk_product
                //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//date_price
                "c67b38b8d081214657b5796d07fc8abcc905b1c4" => $precio, //price_level
                "426cf856dfd49d5a8dced934f0aad537004507a6" => $precio, //price
                "0c0977153cbfd183926f5f7b17a8cc35c5f3bc84" => $precioTtc, //price_tcc
                "07990431d3b67630d4472b688981dc810dd42dca" => $precioTvaTx, //tva_tx
                //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["array_options"]["6"],//tosell
                "tax" => 0,
                "active_flag" => "1",
                "selectable" => "1",
                "visible_to" => "1",
                "2a0937c76ea75bbe78829aa3f9f17402831035a1" => $product["entity"],
                "f2df8a9f5bb0afb0c1b0b1f22605a50d3b738545" => $product["status"],
                "cc3001feeb6b06aadf3c7ab4c321b4621100bcf1" => $product["type"],
            );

            /*
            $resProductPipe = CallAPIPipe("POST", $urlProductsPipe, $dataProductPipe);
            $resProductPipe = json_decode($resProductPipe, true);



            if (isset($resProductPipe["success"]) && $resProductPipe["success"]) {
                $arrayOptionsProduct["array_options"]["options_acpd"] = "1";
                $arrayOptionsProduct["array_options"]["options_idpd"] = strval($resProductPipe["data"]["id"]);
                //$arrayOptionsProduct["array_options"]["options_umed"] = "0";

                if (!isset($arrayOptionsProduct["array_options"]["options_umed"]) || empty($arrayOptionsProduct["array_options"]["options_umed"])) {
                    $arrayOptionsProduct["array_options"]["options_umed"] = "H87";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_claveprodserv"]) || empty($arrayOptionsProduct["array_options"]["options_claveprodserv"])) {
                    $arrayOptionsProduct["array_options"]["options_claveprodserv"] = "0";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_mostrar_rr"]) || empty($arrayOptionsProduct["array_options"]["options_mostrar_rr"])) {
                    $arrayOptionsProduct["array_options"]["options_mostrar_rr"] = "NO";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_mostrar_ps"]) || empty($arrayOptionsProduct["array_options"]["options_mostrar_ps"])) {
                    $arrayOptionsProduct["array_options"]["options_mostrar_ps"] = "NO";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_cupdescto"]) || empty($arrayOptionsProduct["array_options"]["options_cupdescto"])) {
                    $arrayOptionsProduct["array_options"]["options_cupdescto"] = "0";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_mas_vendidos"]) || empty($arrayOptionsProduct["array_options"]["options_mas_vendidos"])) {
                    $arrayOptionsProduct["array_options"]["options_mas_vendidos"] = "NO";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_unids_empaque"]) || empty($arrayOptionsProduct["array_options"]["options_unids_empaque"])) {
                    $arrayOptionsProduct["array_options"]["options_unids_empaque"] = "1";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_prom_exclusive"]) || empty($arrayOptionsProduct["array_options"]["options_prom_exclusive"])) {
                    $arrayOptionsProduct["array_options"]["options_prom_exclusive"] = "NO";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_cmarca"]) || empty($arrayOptionsProduct["array_options"]["options_cmarca"])) {
                    $arrayOptionsProduct["array_options"]["options_cmarca"] = "436";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_0"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_0"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_0"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_1"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_1"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_1"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_2"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_2"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_2"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_3"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_3"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_3"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_4"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_4"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_4"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_5"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_5"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_5"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_6"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_6"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_6"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_7"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_7"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_7"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_8"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_8"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_8"] = "466";
                }

                if (!isset($arrayOptionsProduct["array_options"]["options_categoria_9"]) || empty($arrayOptionsProduct["array_options"]["options_categoria_9"])) {
                    $arrayOptionsProduct["array_options"]["options_categoria_9"] = "466";
                }

                $listProductsEdit = callAPI("PUT", $dolNameKey, $dolApiKey, $urlProductsPut . $idProduct, json_encode($arrayOptionsProduct));
                $listProductsEdit = json_decode($listProductsEdit, true);
            } else {
                $mensajeErrorProduct .= "<li>Id: " . $idProduct . ", Nombre: " . $labelName . ", Error: " . $resProductPipe["error"] . "</li>";
            }
            */
        } catch (Exception $e) {
            $mensajeErrorProduct .= "<li>Id: " . $idProduct . ", Nombre: " . $labelName . ", Error: " . $e->getMessage() . "</li>";
        }
    }

    ////////////////////////////
    //ENVIO DE CORREO PRODUCTO           
    if (!empty($mensajeErrorProduct)) {
        EnvioCorreo($deCorreo, $paraCorreo, $tituloCorreoProduct, $mensajeErrorProduct);
    }
}



/////////////////////////////////////
/////////////////////////////////////
//PROCESO PROPUESTAS
/////////////////////////////////////
/////////////////////////////////////

///////////////
//LISTA PROPUESTAS
$listProposals = CallAPI("GET", $dolNameKey, $dolApiKey, $urlProposals);
$listProposals = json_decode($listProposals, true);

//////////////////////////////
//RECORRER LA LISTA PROPUESTAS
if (!isset($listProposals["error"]) && count($listProposals) > 0) {
    $mensajeErrorProposal = "";

    foreach ($listProposals as $posicion => $proposal) {
        try {
            $atencion = "";
            $whatsapp = "";

            $idProposals = $proposal["id"];
            //$idProposalPipe = $proposal["array_options"]["options_idpd"];

            if (count($proposal["array_options"]) > 0) {
                $atencion = $proposal["array_options"]["options_atencion"];
                $whatsapp = $proposal["array_options"]["options_whatsapp"];
            }

            $fkPropal = "";
            if (isset($proposal["lines"]["0"]["fk_propal"])) {
                $fkPropal = $proposal["lines"]["0"]["fk_propal"]; //fk_propal
            }

            $fkProduct = "";
            if (isset($proposal["lines"]["0"]["fk_product"])) {
                $fkProduct = $proposal["lines"]["0"]["fk_product"]; //fk_product
            }

            $labelProposal = "";
            if (isset($proposal["lines"]["0"]["label"])) {
                $labelProposal = $proposal["lines"]["0"]["label"]; //label
            }

            $qtyProposal = "";
            if (isset($proposal["lines"]["0"]["qty"])) {
                $qtyProposal = $proposal["lines"]["0"]["qty"]; //qty
            }

            $subpriceProposal = "";
            if (isset($proposal["lines"]["0"]["subprice"])) {
                $subpriceProposal = $proposal["lines"]["0"]["subprice"]; //subprice
            }

            $totalHt = "";
            if (isset($proposal["lines"]["0"]["total_ht"])) {
                $totalHt = $proposal["lines"]["0"]["total_ht"]; //total_ht
            }

            $totalTva = "";
            if (isset($proposal["lines"]["0"]["total_tva"])) {
                $totalTva = $proposal["lines"]["0"]["total_tva"]; //total_tva
            }

            $totalTtc = "";
            if (isset($proposal["lines"]["0"]["total_ttc"])) {
                $totalTtc = $proposal["lines"]["0"]["total_ttc"]; //total_ttc
            }


            ////////////////////////////////
            //INFORMACION COMPLETA ARRAY OPTIONS
            $arrayOptionsProposal["array_options"] = $proposal["array_options"];

            $fechaModificacionPro = "";
            if (!empty($proposal["date_modification"])) {
                $fechaModificacionPro = date("Y-m-d H:i:s", $proposal["date_modification"]); //tms
            }

            $fechaCreacionPro = "";
            if (!empty($proposal["date_creation"])) {
                $fechaCreacionPro = date("Y-m-d H:i:s", $proposal["date_creation"]); //datec
            }

            $fechaPPro = "";
            if (!empty($proposal["datep"])) {
                $fechaPPro = date("Y-m-d H:i:s", $proposal["datep"]); //datep
            }

            $fechaValidacionPro = "";
            if (!empty($proposal["date_validation"])) {
                $fechaValidacionPro = date("Y-m-d H:i:s", $proposal["date_validation"]); //date_valid
            }


            $idProposalsThird = $proposal["socid"];

            ///////////////////////////
            //OBTENER ORGANIZACION

            $listProposalsThird = CallAPI("GET", $dolNameKey, $dolApiKey, $urlProposalsThird . $idProposalsThird);
            $listProposalsThird = json_decode($listProposalsThird, true);

            $idProposalsThirdPipe = "0";
            $nameProposalsThird = "";


            if (count($listProposalsThird) > 0) {
                if (isset($listProposalsThird["array_options"]["options_idpd"])) {
                    $idProposalsThirdPipe = $listProposalsThird["array_options"]["options_idpd"];
                } else {
                    $mensajeErrorProposal .= "<li>Id: " . $idProposals . ", SocId: " . $proposal["socid"] . ", Error: No se encontro la organizacion con la que esta relacionada en Dolibarr." . "</li>";
                    continue;
                }

                if (isset($listProposalsThird["name"])) {
                    $nameProposalsThird = $listProposalsThird["name"];
                }
            } else {
                $mensajeErrorProposal .= "<li>Id: " . $idProposals . ", SocId: " . $proposal["socid"] . ", Error: No se encontro la organizacion con la que esta relacionada en Dolibarr." . "</li>";
                continue;
            }



            ///////////////////////////////////
            //INSERTAR INFORMACION EN PIPEDRIVE
            $dataProposalPipe = array(
                //PROPUESTA
                "9adbe002f78c76d02fdfdb5ab22639edcf133eb5" => $idProposals, //rowid
                "title" => $nameProposalsThird, //product_label
                "org_id" => $idProposalsThirdPipe, //
                "c5ee5d9a7c2bc1a84088598280967a9684b1911c" => $proposal["ref"], //ref
                "1f08ddcab9a5207f30d9b3d263eed03ce4361149" => $proposal["entity"], //entity
                "3faf533af1c25471b3817f4f9a95174f656b6758" => $proposal["ref_client"], //
                "217630d19dcbf95b3a8567dd09f616a8732af4f6" => $proposal["socid"], //fk_soc
                "70b4f66a13cbd29b6602c5efcc557bcb3bb5118d" => $fechaModificacionPro, //tms hora
                "c6bb035b883a48f49c78bad244af5587be802110" => $fechaModificacionPro, //tms
                "c7899f46a01a1117b7434052ce5cc0ce2a015b74" => $fechaCreacionPro, //datec
                "900233043c34059044ec0f4196c96bb5c42beb70" => $fechaCreacionPro, //datec hora
                "0d17f17aee61957cab31fcb1c8cb8db48184d79e" => $fechaPPro, //datep
                "28c532d3390d1c339b6f092ff4ea14b59b18c8b8" => $fechaPPro, //datep hora
                "09affa29be3524c10847664291c84263cebfa637" => $fechaValidacionPro, //date_valid
                "a12770a08dfbee833d05d2356302362150a22583" => $fechaValidacionPro, //date_valid hora
                //"43ef2531f3ed66eebae4beb2528708118eecfcdb" => $proposal[""],//date_cloture
                "9b6d03f2d1f70f438a95d62207fad0c2749853c2" => $proposal["statut"], //fk_statut
                "ef02c68c540c0bfc9b2652407181afa00adf9296" => $proposal["total_ht"], //total_ht
                "5d3c5c50edd469bd90d4945cf6365db5b5adc3c5" => $proposal["total_tva"], //tva
                "040b1aa2a15992dd5f50ff8fc2ff3d54237a2f05" => $proposal["total"], //total
                "ee0dec7b00f830cfde5bc63a23fb0aba16ebbd8e" => $proposal["note_public"], //note_public
                "value" => $totalTtc,
                "currency" => "MXN",
                //DET
                //"ee0dec7b00f830cfde5bc63a23fb0aba16ebbd8e" => $fkPropal, //fk_propal
                //"f37396f51747eb8015c0b6a34d44bda962fcc72c" => $fkProduct, //fk_product
                //"ce61c4579d6a182bfc57f59a5b73c2667ab00bad" => $labelProposal, //label
                //"f108f69b67d007c7a339c649c3c7edc4b64d9f18" => $qtyProposal, //qty
                //"45f9c92d468d095c07162f03128cabde2bf51838" => $subpriceProposal, //subprice
                //"c45bdcbdc7bbe32dc29c7ae62a279025581f7900" => $totalHt, //total_ht
                //"7df3c0cc94f51b93980c1524dc3327eb830c120d" => $totalTva, //total_tva
                //"e9d30656b481b0084db83b133ed93d0a4fcd626f" => $totalTtc, //total_ttc
                //EXTRAFIELDS
                //"" => $proposal[""],//fk_object
                "4cc7be96ca62920cd72457841836080058636fa0" => $atencion, //atencion
                "0a21a3feaf958ebd02bcb697bacd8afabd2f89d4" => $whatsapp, //whatsapp
            );


            $resProposalPipe = CallAPIPipe("POST", $urlProposalsPipe, $dataProposalPipe);
            $resProposalPipe = json_decode($resProposalPipe, true);



            if (isset($resProposalPipe["success"]) && $resProposalPipe["success"]) {
                $keyPipeDrive = "?" . $dolNameKeyPipe . "=" . $dolApiKeyPipe;

                $idProposalsPipe = $resProposalPipe["data"]["id"];

                //////////////////////////
                //AGREGAR PRODUCTOS

                foreach ($proposal["lines"] as $posicionProduct => $proposalProduct) {
                    $idProductDoli = $proposalProduct["fk_product"];
                    ///////////////////////
                    //OBTENER PRODUCTOS
                    $listProposalsProductDoli = CallAPI("GET", $dolNameKey, $dolApiKey, $urlProposalsProductDoli . $idProductDoli);
                    $listProposalsProductDoli = json_decode($listProposalsProductDoli, true);

                    $idProductPipe = "";

                    if (count($listProposalsProductDoli) > 0) {
                        if (isset($listProposalsProductDoli["array_options"]["options_idpd"])) {
                            $idProductPipe = $listProposalsProductDoli["array_options"]["options_idpd"];
                        }
                    }

                    $subprice = 0;
                    if (!empty($proposalProduct["subprice"])) {
                        $subprice = $proposalProduct["subprice"];
                    }

                    $tvaTax = 0;
                    if (!empty($proposalProduct["tva_tx"])) {
                        $tvaTax = $proposalProduct["tva_tx"];
                    }

                    $priceIva = 0;
                    try {
                        $priceIva = $subprice * (1 + ($tvaTax / 100));
                    } catch (Exception $exPre) {
                        $priceIva = 0;
                    }

                    $dataProposalProductPipe = array(
                        "product_id" => $idProductPipe,
                        "item_price" => $priceIva, //$proposalProduct["subprice"],
                        "quantity" => $proposalProduct["qty"],
                        "tax" => $proposalProduct["tva_tx"],
                    );

                    $listProposalsProduct = CallAPIPipe("POST", $urlProposalsProductPipe . $idProposalsPipe . "/products" . $keyPipeDrive, $dataProposalProductPipe);
                    $listProposalsProduct = json_decode($listProposalsProduct, true);
                }


                $arrayOptionsProposal["array_options"]["options_acpd"] = "1";
                $arrayOptionsProposal["array_options"]["options_idpd"] = strval($idProposalsPipe);

                $listProposalsEdit = callAPI("PUT", $dolNameKey, $dolApiKey, $urlProposalsPut . $idProposals, json_encode($arrayOptionsProposal));
                $listProposalsEdit = json_decode($listProposalsEdit, true);
            } else {
                $mensajeErrorProposal .= "<li>Id: " . $idProposals . ", SocId: " . $proposal["socid"] . ", Error: " . $resProposalPipe["error"] . "</li>";
            }
        } catch (Exception $e) {
            $mensajeErrorProposal .= "<li>Id: " . $idProposals . ", SocId: " . $proposal["socid"] . ", Error: " . $e->getMessage() . "</li>";
        }
    }

    ////////////////////////////
    //ENVIO DE CORREO PROPUESTA
    if (!empty($mensajeErrorProposal)) {
        EnvioCorreo($deCorreo, $paraCorreo, $tituloCorreoProposal, $mensajeErrorProposal);
    }
}


echo json_encode("OK");
