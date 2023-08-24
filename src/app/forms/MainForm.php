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
        
        //var_dump($elements[1]->children[1]->text);
    }

    /**
     * @event addNickBtn.action 
     */
    function doAddNickBtnAction(UXEvent $e = null)
    {    
        //// Adding element
        
        // Edit
        $nickEdit = new UXTextField();
        $nickEdit->width = 448;
        $nickEdit->height = 32;
        $nickEdit->promptText = 'Никнейм';
        
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

    /**
     * @event startBtn.action 
     */
    function doStartBtnAction(UXEvent $e = null)
    {    
        // Parse elements
        $elements = $this->Box->children;
        
        foreach ($elements as $elem) {
            var_dump($elem->children[0]->text);
        }
    }

    /**
     * @event removeAllBtn.action 
     */
    function doRemoveAllBtnAction(UXEvent $e = null)
    {    
        $this->Box->children->clear();
    }




}
