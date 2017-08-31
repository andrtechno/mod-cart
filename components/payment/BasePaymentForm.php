<?php

/**
 * Base class for configuration payment forms.
 * Renders form without <form> tags.
 */
class BasePaymentForm extends CMSForm {

    public function render() {
        $this->renderBegin();
        $form = $this->renderBody();
        $this->renderEnd();

        return $form;
    }

}