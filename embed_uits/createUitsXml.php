<?php
/*
* TO create the UITS formate xml
*/

$uits_xml_header_start = '<?xml version="1.0" encoding="utf-8"?>
<uits:UITS xmlns:uits="http://www.udirector.net/schemas/2009/uits/1.1">';

//metadata start
$metadata ="<metadata>
<nonce>".$nonce."</nonce>
<Distributor>".$distributor."</Distributor>
<Time>".$time."</Time>
<ProductID type=".$productID_type." completed=".$purchase_completed.">".$productID."</ProductID>
<AssetID type=".$assetID_type.">".$assetID."</AssetID>
<TID type=".$transactionID_type.">".$transactionID."</TID>
<UID version=".$userID_version.">".$userID."</UID>
<Media algorithm=".$media_hash_algorithm.">".$media_hash."</Media>
<URL type=".$url_type.">".$url."</URL>
<PA>".$parentalAdvisory."</PA>
<Copyright value=".$copyright.">".$copyright."</Copyright>
</metadata>";
//metadata end

//signature start
$config = array(
    "digest_alg" => "RSA2048",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);
//openssl_pkey_new - generates a new private and public key pair.
$pkey_config_result =  openssl_pkey_new($config);

//openssl_pkey_export — Gets an exportable representation of a key into a string
$details = openssl_pkey_export($pkey_config_result, $privKey);

//openssl_pkey_get_details — Returns an array with the key details
$pubKey = openssl_pkey_get_details($pkey_config_result);

$pubKey = $pubKey["key"];
$d2 = hash('sha256', $metadata);
//openssl_public_encrypt - Encrypts data with public key 
openssl_public_encrypt($d2, $encrypted, $pubKey);
$signature = base64_encode($encrypted);
$signature_keyid = hash('sha1', $pubKey);
$signature_algorithm = 'RSA2048';
$signature_canonicalization ='none';

$signature_tag ="<signature algorithm=".$signature_algorithm." canonicalization=".$signature_canonicalization." keyID=".$signature_keyid.">".$signature."</signature>";

//signature end
$uits_xml_header_end ="</uits:UITS>";

//Combined the xml nodes
$data_uits = $uits_xml_header_start.$metadata.$signature_tag.$uits_xml_header_end;

?>