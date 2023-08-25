<?php
namespace app\modules;

use std, gui, framework, app;


class MainModule extends AbstractModule
{

    /**
     * @event request.action 
     */
    function doRequestAction(ScriptEvent $e = null)
    {    
        /*$data = '{"global_name":"!Tractor 228"}';
        $url = 'https://discord.com/api/v9/users/@me';
        $headers = [
            'Content-Type: application/json',
            'Authorization: NTcyMDUxMjg5MDg5OTAwNTQ1.GWF5Fa.ja_3lr3xDhl_esg4erSPpy4ER3XulEBzqKuk4k'
        ];
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        var_dump($response);
        curl_close($curl);*/
        
        /*$ch = curl_init('https://discord.com/api/v9/users/@me');
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_PATCH, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"global_name":"!Tractor 228"}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json', 'Authorization: NTcyMDUxMjg5MDg5OTAwNTQ1.GWF5Fa.ja_3lr3xDhl_esg4erSPpy4ER3XulEBzqKuk4k']);
        
        var_dump(curl_exec($ch));*/
        
        /*$httpClient = new HttpClient();
        $httpClient->headers = [
            'Content-Type: application/json',
            'Authorization: NTcyMDUxMjg5MDg5OTAwNTQ1.GWF5Fa.ja_3lr3xDhl_esg4erSPpy4ER3XulEBzqKuk4k'
        ];
        var_dump( $httpClient->patch('https://discord.com/api/v9/users/@me', '{"global_name":"!Tractor 228"}') );
        
        $conn = new URLConnection('https://discord.com/api/v9/users/@me');
        $conn->setRequestProperty("X-HTTP-Method-Override", "PATCH");
        $conn->setRequestProperty();
        $conn->requestMethod = 'POST';*/
        $reqThread = new Thread(function () use ($e) {
            var_dump('____________ New Changing ____________');
        
            global $names, $nameIndex, $serverIDs, $auth, $enableTime, $updateSpeed;
            //Logger::warn('Still sleeping... | '. time(). ' / '. $enableTime);

            if (!$enableTime) {
            
                if ( substr($names[$nameIndex], 0, strlen("/sleep")) === "/sleep" ) {
                    $sleepDur = intval(substr($names[$nameIndex], strlen("/sleep"), strlen($names[$nameIndex])-1)) - 1;
                    $enableTime = time() + $sleepDur;
                    $e->sender->interval = 1000;
                    Logger::info('Sleeping '. $sleepDur. ' seconds');
                } else {
                    
                    if ($serverIDs) {
                        foreach ($serverIDs as $serverID) {
                            $serverUrl = '&serverId='. $serverID;
                            $url =
                                'https://esrkk.sparkdev.space/terminalApp/scripts/requests/reqSettings.php?'.
                                'name='. urlencode($names[$nameIndex]). '&'.
                                'auth='. $auth.
                                $serverUrl;
                            var_dump($url);
                            
                            $result = file_get_contents($url);
                            var_dump('Response: '. $result);
                            
                            $responseData = json_decode($result, true);
                            if ($responseData['message'] == 'You are being rate limited.') {
                                uiLater(function () use ($responseData) {
                                    $this->form('MainForm')->showCD('Discord отправил вас в Cooldown'. "\n". 'Осталось: '. strval(intval($responseData['retry_after'])).
                                    ' сек.'. "\n". 'Осторожнее!');
                                });
                            } elseif ($responseData['message'] == '401: Unauthorized') {
                                uiLater(function () use ($responseData) {
                                    $this->form('MainForm')->showCD('Неправильная авторизация!');
                                });
                            } elseif ($responseData['code'] == '10004') {
                            uiLater(function () use ($responseData) {
                                $this->form('MainForm')->showCD('Неизвестный ID сервера(ов)!');
                            });
                        }
                        }
                    } else {
                        $url =
                            'https://esrkk.sparkdev.space/terminalApp/scripts/requests/reqSettings.php?'.
                            'name='. urlencode($names[$nameIndex]). '&'.
                            'auth='. $auth;
                        var_dump($url);
                        
                        $result = file_get_contents($url);
                        var_dump('Response: '. $result);
                        
                        $responseData = json_decode($result, true);
                        if ($responseData['message'] == 'You are being rate limited.') {
                            uiLater(function () use ($responseData) {
                                $this->form('MainForm')->showCD('Discord отправил вас в Cooldown'. "\n". 'Осталось: '. strval(intval($responseData['retry_after'])).
                                ' сек.'. "\n". 'Осторожнее!');
                            });
                        } elseif ($responseData['message'] == '401: Unauthorized') {
                            uiLater(function () use ($responseData) {
                                $this->form('MainForm')->showCD('Неправильная авторизация!');
                            });
                        } elseif ($responseData['code'] == '10004') {
                            uiLater(function () use ($responseData) {
                                $this->form('MainForm')->showCD('Неизвестный ID сервера(ов)!');
                            });
                        }
                        // $result = '{"message":"You are being rate limited.","retry_after":177.213,"global":false}';
                        // alert(json_decode($result, true)['message']);
                    }
                
                    $nameIndex++;
                
                    if ($nameIndex > count($names) - 1) {
                        $nameIndex = 0;
                    }

                }
            } else {
                if (time() >= $enableTime) {
                    $e->sender->interval = $updateSpeed;
                    $enableTime = null;
                    $nameIndex++;
                    
                    if ($nameIndex > count($names) - 1) {
                        $nameIndex = 0;
                    }
                }
            }
        });
        
        $reqThread->start();
    }

}
