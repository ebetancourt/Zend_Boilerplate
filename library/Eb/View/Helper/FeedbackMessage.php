<?php
class Eb_View_Helper_FeedbackMessage extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function feedbackMessage($message = '', $classStyle = 'error', $id = 'feedback-msg')
    {
        if (empty($message)){
            return '';
        }

        $returnVal = "<div id=\"{$id}\" class=\"feedback-message {$classStyle}-feedback\">$message</div>";
        return $returnVal;
    }
}