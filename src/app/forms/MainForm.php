<?php
namespace app\forms;

use std, gui, framework, app;


class MainForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->logoLabel->font = UXFont::load('res://.data/font/UniSans.ttf', 32);
        $this->subtitleLabel->font = UXFont::load('res://.data/font/UniSans.ttf', 20);
        
        // Setup VerticalBox
        $VBox = $this->Box = new UXVBox();
        $VBox->spacing = 8;
        $VBox->padding = 16;
        $this->container->content->add($VBox);
        
        $this->checkChangingButtons();
        
        //var_dump($elements[1]->children[1]->text);
    }

    /**
     * @event addNickBtn.action 
     */
    function doAddNickBtnAction(UXEvent $e = null)
    {    
        $this->addElement();
    }

    /**
     * @event startBtn.action 
     */
    function doStartBtnAction(UXEvent $e = null)
    {    
        global $working, $names, $nameIndex, $serverIDs, $auth, $enableTime, $updateSpeed;
        
        // Parse elements
        $elements = $this->Box->children;
        $names = [];
        $nameIndex = 0;
        $serverIDs = explode(',', str_replace(' ', '', $this->serverIdEdit->text));
        $auth = $this->authEdit->text;
        $enableTime = null;
        
        foreach ($elements as $elem) {
            $names[] = $elem->children[0]->text;
        }
        
        Logger::info('Nicknames: '. implode(', ', $names));
        
        $updateSpeed = intval($this->intervalField->text) * 1000;
        $this->request->interval = $updateSpeed;
        
        if (!$working) {
            $working = true;
            $this->doRequestAction();
            $this->request->enabled = true;
            $e->sender->text = 'Стоп';
            $e->sender->classesString = 'button danger';
            $icon = new UXImage('res://.data/img/pause.png');
            $e->sender->graphic = new UXImageView($icon);
        } else {
            $working = false;
            $this->request->enabled = false;
            $e->sender->text = 'Старт';
            $e->sender->classesString = 'button success';
            $icon = new UXImage('res://.data/img/play.png');
            $e->sender->graphic = new UXImageView($icon);
        }
        
        $this->panel->enabled = !$working;
        $this->addNickBtn->enabled = !$working;
        $this->importBtn->enabled = !$working;
        $this->exportBtn->enabled = !$working;
        $this->removeAllBtn->enabled = !$working;
        $this->decrementBtn->enabled = !$working;
        $this->intervalLabel->enabled = !$working;
        $this->intervalField->enabled = !$working;
        $this->incrementBtn->enabled = !$working;
        $this->idLabel->enabled = !$working;
        $this->serverIdEdit->enabled = !$working;
        $this->authLabel->enabled = !$working;
        $this->authEdit->enabled = !$working;
        
        if (!$working) {
            $this->checkChangingButtons();
        }
    }

    /**
     * @event removeAllBtn.action 
     */
    function doRemoveAllBtnAction(UXEvent $e = null)
    {    
        $this->Box->children->clear();
    }
    
    function addElement($nick = null) {
    
        //// Adding element
        
        // Edit
        $nickEdit = new UXTextField();
        $nickEdit->width = 448;
        $nickEdit->height = 32;
        $nickEdit->promptText = 'Никнейм';
        if ($nick) {
            $nickEdit->text = $nick;
        }
        
        // Remove Button
        $remBtn = new UXButton('Удалить');
        $remBtn->width = 104;
        $remBtn->height = 32;
        $remBtn->style = '-fx-font-size: 14;';
        $remBtn->classesString = 'button danger';
        $remBtn->cursor = 'HAND';
        
        // Final Nick Panel
        $HBox = new UXHBox([$nickEdit, $remBtn]);
        $HBox->style = "
            -fx-background-color: #313338; -fx-background-radius: 8; -fx-border-style: none; -fx-border-radius: 8;
            -fx-effect: dropshadow( gaussian, #00000033, 10, 0, 0, 0); -fx-border-width: 0; -fx-border-color: #313338;
        ";
        $HBox->alignment = 'CENTER';
        $HBox->spacing = 8;
        $HBox->padding = 8;
        
        $remBtn->on('action', function ($e) use ($HBox) {
            $this->Box->remove($HBox);
            $this->logoLabel->requestFocus();
        });
        
        $this->Box->add($HBox);
        
    }

    function checkChangingButtons() {
        if (intval($this->intervalField->text) > 1) {
            $this->decrementBtn->enabled = true;
        } else {
            $this->decrementBtn->enabled = false;
        }
        if (intval($this->intervalField->text) < 999999) {
            $this->incrementBtn->enabled = true;
        } else {
            $this->incrementBtn->enabled = false;
        }
    }
    
    function showCD($text) {
        if ($this->cdLabel->opacity <= 0) {
            $this->cdLabel->position = [16, 112];
            $this->cdLabel->text = $text;
            $this->cdLabel->opacity = 1;
            waitAsync(3000, function () {
                Animation::fadeOut($this->cdLabel, 1000);
            });
            waitAsync(4000, function () {
                $this->cdLabel->position = [672, 112];
            });
        }
    }

    /**
     * @event decrementBtn.action 
     */
    function doDecrementBtnAction(UXEvent $e = null)
    {    
        $this->intervalField->text = intval($this->intervalField->text) - 1;
        $this->checkChangingButtons();
    }

    /**
     * @event incrementBtn.action 
     */
    function doIncrementBtnAction(UXEvent $e = null)
    {    
        $this->intervalField->text = intval($this->intervalField->text) + 1;
        $this->checkChangingButtons();
    }

    /**
     * @event intervalField.keyUp 
     */
    function doIntervalFieldKeyUp(UXKeyEvent $e = null)
    {    
        $this->checkChangingButtons();
    }

    /**
     * @event importBtn.action 
     */
    function doImportBtnAction(UXEvent $e = null)
    {    
        $this->mainPanel->enabled = false;
        $file = $this->importer->execute();
        if ($file) {
            $this->ini->path = $file;
            $this->ini->load();
            $data = $this->ini->toArray();
            foreach ($data as $key => $param) {
                if ($key === 'Settings') {
                    $this->intervalField->text = $param['interval'];
                    $this->serverIdEdit->text = $param['ids'];
                    $this->authEdit->text = $param['auth'];
                } else {
                    $this->addElement($param['field']);
                }
            }
        }
        $this->mainPanel->enabled = true;
    }


}
