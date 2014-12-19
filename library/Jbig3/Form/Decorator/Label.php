<?php

class Jbig3_Form_Decorator_Label extends Zend_Form_Decorator_Label {

    public function render($content){
        
        $element = $this->getElement();
        $view = $element->getView();
        
        if(NULL === $view) {
            return $content;
        }
        
        $label = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag = $this->getTag();
        $id = $this->getId();
        $class = $this->getClass();
        $options = $this->getOptions();
        
        unset($options['tagOptions']);
        
        if(empty($label) && empty($tag)) {
            return $content;
        }
        
        if(! empty($label)) {
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options);
        } else {
            $label = '&nbsp; :';
        }
        
        if(NULL !== $tag) {
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $options = array(
                'tag' => $tag, 
                'id' => $this->getElement()->getName() . '-label'
            );
            
            $tagOptions = $this->getOption('tagOptions');
            if(! empty($tagOptions)) {
                $options += $tagOptions;
            }
            
            $decorator->setOptions($options);
            
            $label = $decorator->render($label);
        }
        
        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}