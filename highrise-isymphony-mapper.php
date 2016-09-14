<?php
/*
 * The file integrates HelpSpot's Live Lookup with 37Signals Highrise CRM tool
 * PHP Versions required: 5+
 * PHP Modules required: curl
 */
/* MODIFY THESE FOR YOUR HIGHRISE ACCOUNT */
$highrise_url = 'https://i9technologies.highrisehq.com'; //Your Highrise URL, something like http://userscape.highrisehq.com
$api_token = 'b9ed71b998a8b31f99f5c083726f07d6'; //You can find this in your Highrise account in the "my info" screen
/****************** NO MODIFICATIONS REQUIRED BELOW HERE ******************/
/* GET HIGHRISE XML */
//Highrise currently doesn't have a contact search, so we need to pull in all the contacts and search ourselves
$curl = curl_init($highrise_url.'/people.xml');
//Return XML don't output it
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
//Set Basic auth information
curl_setopt($curl,CURLOPT_USERPWD,$api_token.':x'); //Username (api token, fake password as per Highrise api)
//Don't verify for SSL if you have an SSL Highrise account
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
//Call Highrise
$xml = curl_exec($curl);
curl_close($curl);
//Parse the XML returned from Highrise
$people = new SimpleXMLElement($xml);
/* SEARCH PEOPLE BY PHONE NUMBER */
$matches = array();
foreach($people->person AS $person){
        foreach($person->{'contact-data'} AS $contactData){
                foreach($contactData->{'phone-numbers'} AS $phoneNumbers){
                        if($phoneNumbers->{'phone-number'}->number == $_GET['phone-number']){
                                // takes the first match, this could be altered to handle multiple matches if desired
				$matches = $person;
                                break;
                        }
                }
        }
}
//If there was a match, redirect to the URL
if($matches){
        header("Location: " . $highrise_url . "/people/" . $matches->{'id'} . "-" . $matches->{'first-name'} . "-" . $matches->{'last-name'});
}
?>
