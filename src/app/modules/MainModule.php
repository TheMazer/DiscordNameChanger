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
        $reqThread = new Thread(function () use ($e) {
            global $names, $nameIndex, $serverIDs, $auth, $enableTime, $updateSpeed;

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
                                'http://138.124.60.166/discordNicknameChanger.php?'.
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
                            } else {
                                uiLater(function () use ($responseData) {
                                    $this->form('MainForm')->showCD($responseData['message']);
                                });
                            }
                        }
                    } else {
                        $url =
                            'http://138.124.60.166/discordNicknameChanger.php?'.
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
                        } else {
                            uiLater(function () use ($responseData) {
                                $this->form('MainForm')->showCD($responseData['message']);
                            });
                        }
                        
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
