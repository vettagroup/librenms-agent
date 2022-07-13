<?php

$mbmailq = shell_exec('mb-mailq');

$mbmailqlines = preg_split('/\r\n|\r|\n/', $mbmailq);

$mbmailqdata = array(
    "postfix" => array(),
    "mailborder" => array()
);

$service = null;

foreach ($mbmailqlines as $line){
    if(preg_match("/\*\sPostfix\sMTA\*/i", $line)){
        $service = "postfix";
    } elseif (preg_match("/\*\sMailborder\s\*/i", $line)){
        $service = "mailborder";
    }
    if(empty($service)){
        echo "No service found, something broke
        ";
        die;
    }
    if($service == "postfix"){
        if(preg_match("/\*\s+Incoming\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['incoming'] = $matches[1];
        } elseif (preg_match("/\*\s+Deferred\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['deferred'] = $matches[1];
        } elseif (preg_match("/\*\s+Bounced\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['bounced'] = $matches[1];
        } elseif (preg_match("/\*\s+Corrupt\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['corrupt'] = $matches[1];
        } elseif (preg_match("/\*\s+Active\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['active'] = $matches[1];
        } elseif (preg_match("/\*\s+Hold\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['postfix']['hold'] = $matches[1];
        }
    } elseif ($service == "mailborder"){
        if(preg_match("/\*\s+Corrupt\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['corrupt'] = $matches[1];
        } elseif (preg_match("/\*\s+Deferred\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['deferred'] = $matches[1];
        } elseif (preg_match("/\*\s+Delay\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['delay'] = $matches[1];
        } elseif (preg_match("/\*\s+Jailed\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['jailed'] = $matches[1];
        } elseif (preg_match("/\*\s+Milter\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['milter'] = $matches[1];
        } elseif (preg_match("/\*\s+Stage\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['stage'] = $matches[1];
        } elseif (preg_match("/\*\s+Scan\:\s(\d+)/", $line, $matches)){
            $mbmailqdata['mailborder']['scan'] = $matches[1];
        }
    }
}

$postfixvalues = $mbmailqdata['postfix']['incoming'] . "," . $mbmailqdata['postfix']['deferred'] . "," . $mbmailqdata['postfix']['bounced'] . "," . $mbmailqdata['postfix']['corrupt'] . "," . $mbmailqdata['postfix']['active'] . "," . $mbmailqdata['postfix']['hold'];
$mailbordervalues = $mbmailqdata['mailborder']['corrupt'] . "," . $mbmailqdata['mailborder']['deferred'] . "," . $mbmailqdata['mailborder']['delay'] . "," . $mbmailqdata['mailborder']['jailed'] . "," . $mbmailqdata['mailborder']['milter'] . "," . $mbmailqdata['mailborder']['stage'] . "," . $mbmailqdata['mailborder']['scan'];

echo $postfixvalues . "," . $mailbordervalues . "
";

?>
