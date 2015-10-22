#!/usr/bin/php
<?php

$adress_mail = "";
$mdp = "";

$token = "";
$channel = "";
$username = "Bot";
$slack_url = "https://slack.com/api/chat.postMessage?";

// Utilisation de SSL sans vérifiacation du certificat (optionnel)
  $imap = imap_open("{mail.domaine.com:993/ssl/novalidate-cert}INBOX", $adress_mail, $mdp);
  if (FALSE === $imap) {
      die('La connexion a echoue  . Verifiez vos parametres!');
  }
  else
  {
        $result = imap_search($imap, "UNSEEN");
        $count = count($result);
        if ($result == FALSE)
        {
                exit();
        }
        $i = 0;
        while ($i <= $count - 1)
        {
                $msgno = $result[$i];
                $headerText = imap_fetchHeader($imap, $msgno);
                $headers = imap_rfc822_parse_headers($headerText);
                $corps = imap_fetchbody($imap, $msgno, 1);
                $from = $headers->from;
                $corps = modif($corps);
                slack($token, $channel, "Message de:" . $from[0]->personal . " [" . $from[0]->mailbox . "@" . $from[0]->host . "] \n\n\n\n" . $corps . "\n\n\n",$username, $slack_url);
                $i = $i + 1;
        }
 }
imap_close($imap);

function slack($token, $channel, $text, $username, $slack_url)
{
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $slack_url . "token=" . $token . "&channel=" . $channel . "&text=" . urlencode($text) . "&username=" . $username . "&pretty=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, TRUE); // false pour le protocole GET et true pour POST
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
}
?>
