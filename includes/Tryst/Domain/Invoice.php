<?php

namespace Tryst\Domain;

use Tryst\Invoice as I;

class Invoice extends I {

    public function __construct($invoice_id = null, $meta = null, $member = null, $media_files = []){
        
        array_walk($meta, function($val) {
            if(!is_scalar($val))
            return $val;
            return sanitize_text_field($val);
        });
        
        parent::__construct($invoice_id, $meta, $member, $media_files);  
        
        
        if($meta != null)
        $this->meta = self::getFilteredMeta($meta);

        return $this;
    }
       
    /**
    * Set the value of meta from form post values
    *
    * @return  self
    */ 
    public function getFilteredMeta($meta)
    {
        //todo: read this from get_options
        $allowed = ['user_id' => true, 'guide' => true, 'amount' => true, 'message' => true];
              
        $filtered = array_intersect_key($meta, $allowed);
        
        if($this->getMeta() != null)
        $filtered = array_merge($filtered, $this->getMeta());
        
        return $filtered;
    }
    
    
    /*
    * return UL HTML element with all information from object
    */
    public function getEmailForm(){
        global $post;
        $options = get_option('tryst_option');
        $markup = '<ul>';
        $markup .= '<li><strong>Tipo de guia</strong>: '.$this->getMeta('guide').'</li>';
        $markup .= '<li><strong>'.__('Name', 'tryst-invoice').'</strong>: '.$this->getMember()->getName().'</li>';
        $markup .= '<li><strong>'.__('Phone', 'tryst').'</strong>: '.$this->getMember()->getPhone().'</li>';
        $markup .= '<li><strong>Valor</strong>: '.$this->getMeta('amount').'</li>';
        $markup .= '<li><strong>Contribuinte</strong>: '.$this->getMember()->getMeta('contributor').'</li>';
        $markup .= '<li><strong>'.__('Message', 'tryst-invoice').'</strong>: '.$this->getMeta('message').'</li>';
        
        $markup .= '<li><strong>'.__('Recepcionist E-mail', 'tryst').'</strong>: '.$this->getMeta('guide').'</li>';
        
        $markup .= '<li><strong>'.__('Full information', 'tryst').'</strong>: <a href="'.get_permalink().'?tryst_invoice_hash='.$this->getFormKey().'">'.__('Click to open view on the site', 'tryst').'</a></li>';
        $markup .= '</ul>';
        return $markup;
    }

        
    public function save(){

        $data_save = parent::save();

        $id = $this->getId();

        IF(empty($id))
        return null;

        foreach($this->getMeta() as $k => $v){
                delete_post_meta($id, $k);
                add_post_meta($id, $k, $v);
        }       

        var_dump($this->getMeta());
        die;
        
        return $this;
        
    }
    
  
}