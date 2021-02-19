<?php  

    ////////////////////////////////////////
    //PROCESO DE ACTUALIZACION DE DATOS
    ////////////////////////////////////////

    //echo '<script>console.log(' . json_encode(strtotime("now"), JSON_HEX_TAG) . ');</script>';  
    //if(isset($_POST['functionname']))
    //{

        //echo '<script>console.log(' . json_encode(strtotime("now"), JSON_HEX_TAG) . ')</script>'; 
        //echo '<script>console.log(' . date("Y-m-d H:i:s") . ')</script>'; 
        
        $dateSisIni = str_replace(' ', '%20', date("Y-m-d H", strtotime('-200 hours')));
        $dateSisFin = str_replace(' ', '%20', date("Y-m-d H"));

        /////////////////////
        //OBTENER INFORMACION DE LAS APIS DE DOLIBARR
        $dolNameKey = "DOLAPIKEY";
        $dolApiKey = "PDrive640440!";

        $dolNameKeyPipe = "api_token";
        $dolApiKeyPipe = "6beabb33d84b7f9faefc97973c50993846a258f6";

        $urlKeyPipe = "?" . $dolNameKeyPipe."=".$dolApiKeyPipe;

        $urlProducts = "https://www.bks.com.mx/bcorp/api/index.php/products?sqlfilters=t.fk_product_type%3D0%20and%20t.tosell%3D1%20and%20t.finished%20%3D%201%20and%20te.acpd%20%3D%200";//and%20t.datec%20%3E%3D%20'" . $dateSisIni . "'%20and%20t.datec%20%3C%3D%20'" . $dateSisFin . "'";
        $urlProductsPut = "https://www.bks.com.mx/bcorp/api/index.php/products/";
        $urlProductsEditPipe = "https://api.pipedrive.com/v1/products/";//.$dolNameKeyPipe."=".$dolApiKeyPipe;

        $urlProposals = "https://www.bks.com.mx/bcorp/api/index.php/proposals?sqlfilters=t.rowid%20%3D%203076";//te.acpd%20%3D%200";
        $urlProposalsPut = "https://www.bks.com.mx/bcorp/api/index.php/proposals/";
        $urlProposalsEditPipe = "https://api.pipedrive.com/v1/deals/";//.$dolNameKeyPipe."=".$dolApiKeyPipe;

        $urlContacts = "https://www.bks.com.mx/bcorp/api/index.php/contacts?sqlfilters=te.acpd%20%3D%200";
        $urlContactsPut = "https://www.bks.com.mx/bcorp/api/index.php/contacts/";
        $urlContactsEditPipe = "https://api.pipedrive.com/v1/persons/";//.$dolNameKeyPipe."=".$dolApiKeyPipe;

        $urlThirdparties = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties?sqlfilters=te.acpd%20%3D%200";
        $urlThirdpartiesPut = "https://www.bks.com.mx/bcorp/api/index.php/thirdparties/";
        $urlThirdpartiesEditPipe = "https://api.pipedrive.com/v1/organizations/";//.$dolNameKeyPipe."=".$dolApiKeyPipe;
        
        ///////////////////////////////////
        //FUNCION API DOLIBARR POST PUT GET
        function callAPI($method, $nameKey, $apikey, $url, $data = false)
        {
            $curl = curl_init();
            $httpheader = [$nameKey.':'.$apikey];

            switch ($method)
            {
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
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

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

            switch ($method)
            {
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
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

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
        if(count($listProducts) > 0)
        {
            foreach($listProducts as $posicion=>$product)
            {
                $idProduct = $product["id"];
                $idProductPipe = $product["array_options"]["options_idpd"];

                ////////////////
                //LABEL
                if(count($product["multilangs"]) > 0)
                {
                    $labelName = $product["multilangs"]["es_MX"]["label"];
                }
                else
                {
                    $labelName = $product["label"];
                }
                
                $precioArray = array(
                    "0" => array(
                        "price" => $product["multiprices"]["6"],
                        "currency" => "MXN"
                    )
                );

                ////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataProductPipe = array(
                    //PRODUCT
                    "efa2d1011ae8a446b016ddc8f43f9b74ab24b2c8" => $idProduct,//rowid
                    "4574b2a2a5d31600ad7c41a7d3b53c9ed4eba257" => $product["ref"],//ref
                    "code" => $product["ref"],
                    "prices" => $precioArray,
                    "4574b2a2a5d31600ad7c41a7d3b53c9ed4eba257" => $product["entity"],//entity
                    "667d28f3f7acddfef2c391b79cbf4927ef4f40fa" => $product["date_creation"],//datec
                    "69ac0647b4a609bc631bc58517fa3a69d256b9c4" => $product["date_modification"],//tms
                    "0a3c71abc646caeccc1274efe5b6a7159ecd8ccc" => $labelName,//label
                    "name" => $labelName,//labal--name
                    "144d081f4593a8eddb3756f8d930b78335f13fed" => $product["multiprices"]["6"],//price
                    "5a4f875741977481f60d818395519c7eaaa32a77" => $product["price_ttc"],//price_ttc
                    "edf719089524c06d999fa667a3c3d5f5b63258c0" => $product["tva_tx"],//tva_tx
                    //"f2df8a9f5bb0afb0c1b0b1f22605a50d3b738545" => $product[""],//tosell
                    //"cc3001feeb6b06aadf3c7ab4c321b4621100bcf1" => $product[""],//fk_product_type
                    "a07575affd01d486f415d792ddb71092a5cd0719" => $product["finished"],//finished
                    //PRODUCT_EXTRAFIELDS
                    //"877190fea56b587c0f603f3adb55a5ef88d680a3" => $product[""],//tms
                    //"877190fea56b587c0f603f3adb55a5ef88d680a3" => $product[""],//fk_object
                    "877190fea56b587c0f603f3adb55a5ef88d680a3" => $product["array_options"]["options_mostrar_ps"],//mostrar_ps
                    "a7b2c05b5fa3d4a1bf7448982a2685470c788109" => $product["array_options"]["options_mostrar_ps"],//marca
                    "d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["array_options"]["options_cmarca"],//cmarca
                    //PRICE
                    //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//entity
                    //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//tms
                    //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//fk_product
                    //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["options_cmarca"],//date_price
                    "c67b38b8d081214657b5796d07fc8abcc905b1c4" => $product["multiprices"]["6"],//price_level
                    "426cf856dfd49d5a8dced934f0aad537004507a6" => $product["multiprices"]["6"],//price
                    "0c0977153cbfd183926f5f7b17a8cc35c5f3bc84" => $product["multiprices_ttc"]["6"],//price_tcc
                    "07990431d3b67630d4472b688981dc810dd42dca" => $product["multiprices_tva_tx"]["6"],//tva_tx
                    //"d2fe9f71d968c17ad0617402b67b03999b33027e" => $product["array_options"]["6"],//tosell
                    "tax" => 0,
                    "active_flag" => "1",
                    "selectable" => "1",
                    "visible_to" => "1",
                );
                
                $resProductPipe = CallAPIPipe("PUT", $urlProductsEditPipe . $idProductPipe . $urlKeyPipe, $dataProductPipe);
                $resProductPipe = json_decode($resProductPipe, true);

                /*
                if(isset($resProductPipe["success"]) && $resProductPipe["success"])
                {
                    $dataProductPut = array( 
                        "array_options" => array( 
                            "options_acpd" => "1"
                        )
                    );

                    $listProducts = callAPI("PUT", $dolNameKey, $dolApiKey, $urlProductsPut . $idProduct, json_encode($dataProductPut));
                    $listProducts = json_decode($listProducts, true);
                }
                */
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
        if(count($listProposals) > 0)
        {
            foreach($listProposals as $posicion=>$proposal)
            {
                $atencion = "";
                $whatsapp = "";

                $idProposals = $proposal["id"]; 
                $idProposalsPipe = $proposal["array_options"]["options_idpd"];

                if(count($proposal["array_options"]) > 0)
                {
                    $atencion = $proposal["array_options"]["options_atencion"];
                    $whatsapp = $proposal["array_options"]["options_whatsapp"];
                }

                $fkPropal = "";
                if(isset($proposal["lines"]["0"]["fk_propal"]))
                {
                    $fkPropal = $proposal["lines"]["0"]["fk_propal"];//fk_propal
                }
                
                $fkProduct = "";
                if(isset($proposal["lines"]["0"]["fk_product"]))
                {
                    $fkProduct = $proposal["lines"]["0"]["fk_product"];//fk_product
                }

                $labelProposal = "";
                if(isset($proposal["lines"]["0"]["label"]))
                {
                    $labelProposal = $proposal["lines"]["0"]["label"];//label
                }

                $qtyProposal = "";
                if(isset($proposal["lines"]["0"]["qty"]))
                {
                    $qtyProposal = $proposal["lines"]["0"]["qty"];//qty
                }

                $subpriceProposal = "";
                if(isset($proposal["lines"]["0"]["subprice"]))
                {
                    $subpriceProposal = $proposal["lines"]["0"]["subprice"];//subprice
                }

                $totalHt = "";
                if(isset($proposal["lines"]["0"]["total_ht"]))
                {
                    $totalHt = $proposal["lines"]["0"]["total_ht"];//total_ht
                }

                $totalTva = "";
                if(isset($proposal["lines"]["0"]["total_tva"]))
                {
                    $totalTva = $proposal["lines"]["0"]["total_tva"];//total_tva
                }

                $totalTtc= "";
                if(isset($proposal["lines"]["0"]["total_ttc"]))
                {
                    $totalTtc = $proposal["lines"]["0"]["total_ttc"];//total_ttc
                }

                ////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataProposalPipe = array(
                    //PROPUESTA
                    "9adbe002f78c76d02fdfdb5ab22639edcf133eb5" => $idProposals,//rowid
                    "title" => $proposal["socid"], //product_label
                    "c5ee5d9a7c2bc1a84088598280967a9684b1911c" => $proposal["ref"],//ref
                    "1f08ddcab9a5207f30d9b3d263eed03ce4361149" => $proposal["entity"],//entity
                    "3faf533af1c25471b3817f4f9a95174f656b6758" => $proposal["ref_client"],//
                    "217630d19dcbf95b3a8567dd09f616a8732af4f6" => $proposal["socid"],//fk_soc
                    "e1ffc131d965be4f1f8627b4a8d4ca48c5599cdd" => $proposal["date_modification"],//tms
                    "c7899f46a01a1117b7434052ce5cc0ce2a015b74" => $proposal["date_creation"],//datec
                    "0d17f17aee61957cab31fcb1c8cb8db48184d79e" => $proposal["datep"],//datep
                    "09affa29be3524c10847664291c84263cebfa637" => $proposal["date_validation"],//date_valid
                    //"43ef2531f3ed66eebae4beb2528708118eecfcdb" => $proposal[""],//date_cloture
                    "9b6d03f2d1f70f438a95d62207fad0c2749853c2" => $proposal["statut"],//fk_statut
                    "0f0027971034d082db83a7a0a36554cf1d194661" => $proposal["total_ht"],//total_ht
                    "5d3c5c50edd469bd90d4945cf6365db5b5adc3c5" => $proposal["tva"],//tva
                    "5d3c5c50edd469bd90d4945cf6365db5b5adc3c5" => $proposal["total"],//total
                    "ee0dec7b00f830cfde5bc63a23fb0aba16ebbd8e" => $proposal["note_public"],//note_public
                    "value" => $totalTtc,
                    "currency" => "MXN",
                    //DET
                    "ee0dec7b00f830cfde5bc63a23fb0aba16ebbd8e" => $fkPropal,//fk_propal
                    "f37396f51747eb8015c0b6a34d44bda962fcc72c" => $fkProduct,//fk_product
                    "ce61c4579d6a182bfc57f59a5b73c2667ab00bad" => $labelProposal,//label
                    "f108f69b67d007c7a339c649c3c7edc4b64d9f18" => $qtyProposal,//qty
                    "45f9c92d468d095c07162f03128cabde2bf51838" => $subpriceProposal,//subprice
                    "c45bdcbdc7bbe32dc29c7ae62a279025581f7900" => $totalHt,//total_ht
                    "7df3c0cc94f51b93980c1524dc3327eb830c120d" => $totalTva,//total_tva
                    "e9d30656b481b0084db83b133ed93d0a4fcd626f" => $totalTtc,//total_ttc
                    //EXTRAFIELDS
                    //"" => $proposal[""],//fk_object
                    "4cc7be96ca62920cd72457841836080058636fa0" => $atencion,//atencion
                    "0a21a3feaf958ebd02bcb697bacd8afabd2f89d4" => $whatsapp,//whatsapp
                );

                
                //$resProposalPipe = CallAPIPipe("POST", $urlProposalsEditPipe . $idProposalsPipe . $urlKeyPipe, $dataProposalPipe);
                //$resProposalPipe = json_decode($resProposalPipe, true);

                /*
                if(isset($resProposalPipe["success"]) && $resProposalPipe["success"])
                {
                    $dataProposalsPut = array( 
                        "array_options" => array( 
                            "options_acpd" => "1"
                        )
                    );

                    $listProposals = callAPI("PUT", $dolNameKey, $dolApiKey, $urlProposalsPut . $idProposals, json_encode($dataProposalsPut));
                    $listProposals = json_decode($listProposals, true);
                }
                */

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
        if(count($listContacts) > 0)
        {
            foreach($listContacts as $posicion=>$contact)
            {
                $idContact = $contact["id"];
                $idContactPipe = $contact["array_options"]["options_idpd"];

                if(isset($contact["socialnetworks"]["jabber"]))
                {
                    $jabber = $contact["socialnetworks"]["jabber"];
                }

                if(isset($contact["socialnetworks"]["whatsapp"]))
                {
                    $whatsapp = $contact["socialnetworks"]["whatsapp"];
                }

                ////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataContactPipe = array(
                    //PEOPLE
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $idContact, //rowid
                    "name" => $contact["socname"],
                    "0f4cba8a92f3a33a7a88a6b5b06873f97bf0fe21" => $contact["date_creation"], //datec
                    "6616da6d79aaf8d69db84da89edcc52cdf728a27" => $contact["date_modification"], //tms
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $contact["socid"], //fk_soc
                    "452a78b6083aa680044ca988b68794f0436d8574" => $contact["entity"], //entity
                    "983a21fcd8278f56b9d8ccee69a64b510da09c31" => $contact["civility"], //civility
                    "56fceb93ac6090ef277dbb634f543a501f604184" => $contact["lastname"], //lastname
                    "d8c4b1171cb0f9326af453f1260f57594d9b813f" => $contact["firstname"], //firstname
                    "5cff9ed6829a1633630893bb67caed4c10956426" => $contact["address"], //address
                    "b722390bed15829401be9226b2b49273c9934245" => $contact["zip"], //zip
                    "82954b2cf81f1ddf3172a242f7d543fd0d473575" => $contact["town"], //town
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $contact["state_id"], //fk_departement
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $contact["country_id"], //fk_pays
                    "aa2899a40b23b7edbfc64b42d3ad9d6cf9411775" => $contact["birthday"], //birthday
                    "b0396742da038967d4f2029c68ada251be25323f" => $contact["poste"], //poste
                    "5cbcec0a769302a02051a9401367df9fc317f7f3" => $contact["phone_pro"], //phone
                    "b64091221561eb8a9dda32704dd29dbfee795290" => $contact["phone_perso"], //phone_perso
                    "b1708fa7673797cf973aae82c838d564fbe1a5df" => $contact["phone_mobile"], //phone_mobile
                    "d063eaa5c7b526628034afea6f99934a9fd92dca" => $contact["email"], //email
                    "1241f63687d014706519202472a700321cd39265" => $jabber, //socialnetworks
                    "4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $whatsapp, //whatsapp
                    "ac60b777b44d44ecd19698a7ddccf705abcc5840" => $contact["statut"], //statut
                    //"4323d27c4ed30d1a92398d7d73618d6bfb248f88" => $contact["id"], //bks_whatsapp_compras
                    //OPTIONS
                    //"a7b2c05b5fa3d4a1bf7448982a2685470c788109" => $contact["id"],//fk_object
                    "dc2ae964f76df57566517377f50f75c2d71136be" => $contact["array_options"]["options_numexterior"],//numexterior
                    "b75d613546a5bc4445a359940fdcf3ecec932fc6" => $contact["array_options"]["options_numinterior"],//numinterior
                    "721dfb5509bc763410c50fb5c803c5621842afbf" => $contact["array_options"]["options_tipreg"],//tipreg
                    "9af3b6cc7800a99aa399f649a176f1a61f75dc64" => $contact["array_options"]["options_domicilioentrega"],//domicilioentrega
                    "6d8245c2bff0524719ac3ccb960dea3c7a034905" => $contact["array_options"]["options_funcioncontacto"],//funcioncontacto
                );

                
                //$resContactPipe = CallAPIPipe("POST", $$urlContactsEditPipe . $idContactPipe . $urlKeyPipe, $dataContactPipe);
                //$resContactPipe = json_decode($resContactPipe, true);

                /*
                if(isset($resContactPipe["success"]) && $resContactPipe["success"])
                {
                    $dataContactsPut = array( 
                        "array_options" => array( 
                            "options_acpd" => "1"
                        )
                    );

                    $listContacts = callAPI("PUT", $dolNameKey, $dolApiKey, $urlContactsPut . $idContact, json_encode($dataContactsPut));
                    $listContacts = json_decode($listContacts, true);
                }
                */
            }
        }   
        


        
        /////////////////////////////////////
        /////////////////////////////////////
        //PROCESO ORGANIZACION
        /////////////////////////////////////
        /////////////////////////////////////

        ////////////////
        //LISTA ORGANIZACION
        $listThirds = CallAPI("GET", $dolNameKey, $dolApiKey, $urlThirdparties);
        $listThirds = json_decode($listThirds, true);

         //////////////////////////////
        //RECORRER LA LISTA ORGANIZACION
        if(count($listThirds) > 0)
        {
            foreach($listThirds as $posicion=>$thirds)
            {

                $idThirds = $thirds["id"];
                $idThirdsPipe = $thirds["array_options"]["options_idpd"];

                ////////////////////////
                //INSERTAR INFORMACION EN PIPEDRIVE
                $dataThirdsPipe = array(
                    //THIRD
                    "a5b878fafdf36082b8b9387c1cd678c77af84626" => $idThirds, //rowid
                    "name" => $thirds["name"],//name
                    //"data" => $thirds["name_alias"],
                    //"additional_data" => $thirds["address"],
                    "51979244a94759ff79197fd019fae361413b4d51" => $thirds["entity"], //entity
                    "c965c74fa89644ca278dea84b00eb1f82c9adbdd" => $thirds["statut"], //statut
                    "dfaf06ab5fc9764b709cc2c263a27408db10a127" => $thirds["date_modification"], //tms
                    "a664fc5944ee09fff86d24c96364118758a65f92" => $thirds["date_creation"], //datec
                    "adb9f47c492dd7d1c07ca4ded5abce0a25a2cbcc" => $thirds["status"], //status
                    "280f12779f74fb8d1abae1fb55081b1ca5335ab8" => $thirds["code_client"], //code_client
                    "217b864729b4a944afcabb1b0ab96bdc447967f3" => $thirds["address"], //address
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
                    "bdc929758c85468150246fcfa2455ef1c21528bd" => $thirds["id"], //tms
                    //"315ab29dbe061afc0ca3329c176359f8cb4107de" => $thirds["array_options"]["options_fac_pub_gral"],//fk_object
                    "7bd5c41419aa372165525f38f82706f7fa82c46e" => $thirds["array_options"]["options_contado"],//contado
                    "8c071979cefde4d3bbd71c89029b2845617b2aa8" => $thirds["array_options"]["options_fac_pub_gral"], //fac_pub_gral
                    "c80bd3163b99f7a9350b29b463c49f3a0d7ecab6" => $thirds["array_options"]["options_usocfdi"], //usocfdi
                    "5948ffaaed0953101f50977396fb7487ed30bb69" => $thirds["array_options"]["options_formapagcfdi"], //formapagcfdi
                    "e43a68d42badedf6dbbcf3b058b65ca9c698978a" => $thirds["array_options"]["options_whatsapp"], //whatsapp
                    "2f80a5416a66075c3944006c9bca4f80f13852f5" => $thirds["array_options"]["options_potencial"], //potencial
                    "e67d60f2202535ba7fcdc68d52d4946ab298f23e" => $thirds["array_options"]["options_negocio"], //negocio
                    "7c482bf133444c4ab1bcaf76665437ddc55a5914" => $thirds["array_options"]["options_territorio"], //territorio
                );
                
                //$resThirdsPipe = CallAPIPipe("POST", $urlThirdpartiesEditPipe . $idThirdsPipe . $urlContactsEditPipe, $dataThirdsPipe);
                //$resThirdsPipe = json_decode($resThirdsPipe, true);

                /*
                if(isset($resThirdsPipe["success"]) && $resThirdsPipe["success"])
                {
                    $dataThirdsPut = array( 
                        "array_options" => array( 
                            "options_acpd" => "1"
                        )
                    );

                    $listContacts = callAPI("PUT", $dolNameKey, $dolApiKey, $urlThirdpartiesPut . $idThirds, json_encode($dataThirdsPut));
                    $listContacts = json_decode($listContacts, true);
                }
                */
            }
        }
        

        echo json_encode("OK");
    //}
?>