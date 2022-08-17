<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail 
{
    private $api_key = '113b9a2146dcff4f5fb5886a52d59372';
    private $api_key_secret = '7aacd5cfcad356b95100c0a9ed55e03e';

    public function send($to_email,$to_name,$subject,$contents)
    {
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
       
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "martial.4@hotmail.fr",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' =>3983526,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'contents' => $contents
                    ],
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();

            }
        }

?>