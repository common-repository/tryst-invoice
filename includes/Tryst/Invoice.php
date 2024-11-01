<?php

namespace Tryst;

use Tryst\Contracts\Mail;

class Invoice implements Mail {
    protected $post, $timestamp, $meta, $media_files, $member, $domain, $email_key;
    
    public function __construct($invoice_id = null, $meta = null, $member = null, $media_files = []){
        global $tryst_plugin;
        $this->post = $invoice_id != null ? get_post($invoice_id) : null;
        
        //new register
        if($meta != null){

            
        array_walk($meta, function($val) {
            if(!is_scalar($val))
            return $val;
            return sanitize_text_field($val);
        });

        
            $this->meta = self::getFilteredMeta($meta);
        } 
        
        //register exist, query from db
        if($meta == null && $this->post != null){
            $this->meta = $this->load_meta();        
        }
        
        $this->member = $member;
        
        $this->media_files = $media_files;
        
        if($this->member == null && $this->post != null){
            $this->setMember(Member::find($this->getMeta('user_id')));
        } 
        
    }
    
    
    public static function findByFormKey($key){
        global $wpdb;
        
        $meta = current(
            $wpdb->get_results( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='invoice_key' AND meta_value = '$key'", OBJECT )
        );
        
        return self::getInstance($meta->post_id);
    }
    
    public static function getInstance($invoice_id = null, $meta = null, $member = null, $media_files = []) {
        return new static($invoice_id, $meta, $member, $media_files);
    }
    
    public function getFormKey(){
        global $wpdb;
        
        $post_id = $this->post->ID;
        
        $meta = current($wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = $post_id AND meta_key = 'invoice_key' ", OBJECT ));
        
        if(empty($meta))
        return null;
        
        return $meta->meta_value;
    }
    
    
    public function save(){
        
        // Create post object
        $post_data = array(
            'post_title'    => sprintf(__('Invoice request from %s', 'tryst-invoice'), $this->getMember()->getUser()->first_name),
            'post_content'  => '',
            'post_type'		=> 'invoice',
            'post_status'   => 'private',
            'post_author'   => 1
        );
        
        $id =  wp_insert_post( $post_data );
        
        
        if($id){
            
            //save member id with the invoice
            $this->setMeta('user_id', $this->getMember()->getUser()->ID);
            
            $this->setMeta('invoice_key', wp_hash($this->getMember()->getUser()->ID.strtotime('now')));

            
            foreach($this->getMeta() as $k => $v){
                delete_post_meta($id, $k);
                add_post_meta($id, $k, $v);
            }
            
            $this->post = get_post($id);
        }
        
        return $this;
        
    }
    
    public function getId(){
        return $this->post->ID;
    }
    
    public function setTimestamp($timestamp){
        $this->timestamp = new \DateTime($timestamp);
    }
    
    public function getTimestamp($load_meta = false){
        return $this->timestamp;        
    }
    
    public function setMedia($media_items){
        array_push($this->media, $media_items);
        return $this->media;
    }
    
    public function getMedia(){
        return $this->media;
    }
    
    public function thumbnail(){
        $image = plugin_dir_url( __FILE__ ) . '../../public/img/invoice-1.png';
        $thumbnail = get_the_post_thumbnail_url($this->post);
        return $thumbnail == false ? $image : $thumbnail;
    }
    
    public function card(){
        $markup = '<div class="col-md-3"><a href="'.get_page_link().'?invoice_id='.$this->post->ID.'"><figure class="row" data-id="'.$this->post->ID.'">
        <div class="col-4"><img src="'.$this->thumbnail().'" class="img-fluid"></div>
        <div class="col-8">'.$this->post->post_title.'</div>
        </figure>
        </a></div>';
        
        return $markup;
    }
    
    public function load_media_files(){
        $this->setMedia(get_field('file_1'));
        $this->setMedia(get_field('file_2'));
        $this->setMedia(get_field('file_3'));
        $this->setMedia(get_field('file_4'));
        $this->setMedia(get_field('file_5'));
        
        return $this->getMedia();
    }
    
    public function load_meta(){
        return get_post_meta($this->post->ID);
    }
    
    /* 
    * data to be shown on single-invoice
    */
    public function get_content(){
        return $this->post->post_content;
    }
    
    /**
    * Get the value of post
    */ 
    public function getPost()
    {
        return $this->post;
    }
    
    
    
    /**
    * Set the value of meta from form post values
    *
    * @return  self
    */ 
    public function getFilteredMeta($meta)
    {
        //todo: read this from get_options
        $allowed = ['user_id' => true, 'amount' => true, 'message' => true];
        
        $filtered = array_intersect_key($meta, $allowed);
        
        return $filtered;
    }
    
    
    /**
    * Get the value of member
    */ 
    public function getMember()
    {
        return $this->member;
    }
    
    
    /**
    * Set the value of member
    *
    * @return  self
    */ 
    public function setMember($member)
    {
        $this->member = $member;
        
        return $this;
    }
    

    
    public function setMeta($key, $value){
        $this->meta[$key] = $value;
        return $this;
    }
    
    public function getMeta($key = null)
    {
        if($key != null){            
            if(isset($this->meta[$key])){
                if(is_array($this->meta[$key])){
                    return current($this->meta[$key]);
                } else {
                    return $this->meta[$key];
                }
            } else {
                return null;
            }
        }
        
        return $this->meta;
    }
    
    /*
    * return UL HTML element with all information from object for intro
    */
    public function getEmailIntro(){
        $markup = '<p>'. sprintf(__('Hello %s, this is the invoice request receipt.', 'tryst-invoice'), $this->getMember()->getName()).'</p>';
        return $markup;
    }
    
    
    /*
    * return UL HTML element with all information from object
    */
    public function getEmailForm(){
        global $post;
        $options = get_option('tryst_option');
        $markup = '<ul>';
        $markup .= '<li><strong>'.__('Name', 'tryst-invoice').'</strong>: '.$this->getMember()->getName().'</li>';
        $markup .= '<li><strong>'.__('Phone', 'tryst').'</strong>: '.$this->getMember()->getPhone().'</li>';
        $markup .= '<li><strong>'.__('Message', 'tryst-invoice').'</strong>: '.$this->getMeta('message').'</li>';
        
        if(!empty($options['tryst_mail']))
        $markup .= '<li><strong>'.__('Recepcionist E-mail', 'tryst').'</strong>: '.$options['tryst_mail'].'</li>';
        
        $markup .= '<li><strong>'.__('Full information', 'tryst').'</strong>: <a href="'.get_permalink().'?tryst_invoice_hash='.$this->getFormKey().'">'.__('Click to open view on the site', 'tryst').'</a></li>';
        $markup .= '</ul>';
        return $markup;
    }
    
    /*
    * return UL HTML element with all information from object for footer
    */
    public function getEmailFooter(){
        $markup = '<p>'.__('We will get back in touch soon', 'tryst-invoice').'</p>';
        $options = get_option('tryst_option');
        $markup .= $options['email_footer'];
        return $markup;
    }
    
    public function getEmailFilePath(){
        //send mail
        return realpath(dirname(__FILE__)).'/../../public/templates/E-mail/invoice.html';
    }
    
    public function getEmailTitle(){
        
        global $tryst_plugin;
        
        switch($this->getEmailKey()){
            case "request":
            return sprintf(__('Invoice requested at %s', 'tryst-invoice'), get_bloginfo('name'));
            break;
            case "release":
            return sprintf(__('Invoice released at %s', 'tryst-invoice'), get_bloginfo('name'));
            break;
        }
        
        return __('Invoice', 'tryst-invoice');
    }
    
    public function setEmailKey($key){
        $this->email_key = $key;
    }
    
    public function getEmailKey(){
        return $this->email_key;
    }
}