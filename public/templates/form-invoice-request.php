<form action="<?php echo get_page_link(); ?>" method="POST" class="validate my-4 <?php if(isset($_GET['tryst_invoice_hash'])): ?> readonly <?php endif; ?>">
    <?php wp_nonce_field( 'invoice', 'invoice_nonce_field' ); ?>
    <input type="hidden" name="invoice">
    <?php $user = wp_get_current_user(); ?>
    <?php if(!empty($user)): ?>
    <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>">
    <?php endif; ?>
    <?php global $tryst_plugin; ?>
    <?php if(isset($invoice) && !empty($invoice->getPost())): ?>
    <div class="alert  row bg-light">
        <div class="col-md-4 d-flex">
            <figure class="d-block m-auto align-self-center">
                <img src="<?php echo $tryst_plugin->path.'public/img/keys-small.png'; ?>" alt="calendar-invoice" >
            </figure>
        </div>
        <div class="col-md-8">
            <p><?php _e('Please save your access credentials to login the site:', 'tryst'); ?>
                <br>
                <strong>Login:</strong> <?php echo $invoice->getMember()->getUser()->user_login; ?>
                <br>
                <strong>Pass:</strong> <?php echo $invoice->getMember()->getPasswordDescription(); ?>
            </p>
        </div>
    </div>
    <?php endif; ?>        
    <div class="row">
        <div class="col-12">
            <h6 class="mt-3 t-section">Dados do boleto</h6>
        </div>
    </div>
    <!-- DOMAIN SUPPORT -->
    <?php 
    if($tryst_plugin != null && !empty($tryst_plugin->getNamespace()) && file_exists(plugin_dir_path( __FILE__ ).'Domain/form-invoice-request.php')){
        include plugin_dir_path( __FILE__ ).'Domain/form-invoice-request.php';					
    }
    ?>
    <!-- END DOMAIN SUPPORT -->
    <?php ?>
    <div class="form-group row">
        <div class="col-12">
            <label for="amount">Valor a pagar</label>
            <input type="tel" class="form-control f-invoice validate[required, min[0], custom[real]]" name="invoice[amount]" id="amount" value="<?php echo !empty($invoice) ? $invoice->getMeta('amount') : ''; ?>">         
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <label for="message">Mensagem</label>
            <textarea class="form-control f-invoice" name="invoice[message]" id="message"><?php echo !empty($invoice) ? $invoice->getMeta('message') : ''; ?></textarea>  
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h6 class="mt-3 t-section w-employee"><?php _e('Worker data', 'tryst'); ?></h6>
            <h6 class="mt-3 t-section w-company d-none"><?php _e('Company data', 'tryst'); ?></h6>
        </div>
    </div>
    <div class="form-group row">
        <!-- TRYST MEMBER SUPPORT -->
        <?php 
        global $tryst_plugin;
        $options = get_option('tryst_option');
        if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
            include $tryst_plugin->getExtensionPath('member').'/public/templates/'.$options['form_country'].'/fields/base-fields.php';
            $file_domain_fields = $tryst_plugin->getExtensionPath('member')."/public/templates/".$options['form_country']."/Domain/fields/base-fields.php";
            if(class_exists("\\Tryst\\Domain\\Main") && file_exists($file_domain_fields)){
                include $file_domain_fields;        
            }     
        }
        ?>
    </div>
    <?php
    if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
        include $tryst_plugin->getExtensionPath('member').'/public/templates/'.$options['form_country'].'/fields/form-address-fields.php';
        $file_domain_fields = $tryst_plugin->getExtensionPath('member')."/public/templates/".$options['form_country']."/Domain/fields/form-address-fields.php";
        if(class_exists("\\Tryst\\Domain\\Main") && file_exists($file_domain_fields)){
            include $file_domain_fields;        
        }     
    }
    ?>
    <!-- TRYST MEMBER SUPPORT -->
    <?php if(empty($invoice)): ?>
    <div class="text-center mt-4">
        <label for="f-security-code"><?php _e('Security code', 'tryst') ?></label>
    </div>
    <div class="row">
        <div class="col-4 offset-md-1 col-md-4 text-right">
            <span class="badge badge-info t-code"><?php echo substr(strtotime('now'), -3); ?></span>
        </div>
        <div class="col-8 col-md-4">
            <input type="hidden" name="security_code" value="<?php echo substr(strtotime('now'), -3); ?>">
            <input required type="text" class="form-control validate[required]" name="security_code_repeat" id="f-security-code" placeholder="<?php echo __('Type number', 'tryst').' '.substr(strtotime('now'), -3); ?>"> 
            <small class="form-text text-muted"><?php _e('Simple captcha to help us avoid robot spam', 'tryst')?></small>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <input type="submit" value="<?php _e('Send', 'tryst'); ?>" class="pure-button pure-button-primary btn-primary btn float-right"> 
        </div>
    </div>
    <?php else: ?>
    <div class="row mt-4">
        <div class="col-12">
            <button type="button" class="btn btn-secondary mx-auto my-4 d-block" data-toggle="modal" data-target="#email_repeat"><?php printf(__('Send remind e-mail to %s', 'tryst-invoice'), $invoice->getMember()->getEmail()); ?></button>
        </div>
    </div>
    <?php endif; ?>  
</form>
<?php if(!empty($invoice)): ?>
<!-- mail send modal -->
<!-- The Modal -->
<div class="modal" id="email_repeat">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">E-mail</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo get_page_link(); ?>" method="POST" class="validate">
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" value="<?php echo $invoice->getFormKey(); ?>" name="tryst_invoice_hash">
                    <input type="hidden" value="true" name="invoice_mail_repeat">
                    <div class="text-center mt-4">
                        <label for="f-security-code"><?php _e('Security code', 'tryst') ?></label>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <span class="badge badge-info t-code"><?php echo substr(strtotime('now'), -3); ?></span>
                        </div>
                        <div class="col-8">
                            <input type="hidden" name="security_code" value="<?php echo substr(strtotime('now'), -3); ?>">
                            <input required type="text" class="form-control" name="security_code_repeat" id="f-security-code" placeholder="<?php echo __('Type number', 'tryst').' '.substr(strtotime('now'), -3); ?>"> 
                            <small class="form-text text-muted"><?php _e('Simple captcha to help us avoid robot spam', 'tryst')?></small>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success float-right">OK</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">X</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
