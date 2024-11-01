    <div class="row">
        <div class="form-group col-12 my-0">
        <?php 
			$options = get_option('tryst_option');
			$emails = $options['guide_list'];
		?>
            <?php if(!empty($invoice)): ?>
            <p>Tipo de guia: <?php echo isset($invoice) && isset($emails[$invoice->getMeta('guide')]) ? $emails[$invoice->getMeta('guide')].' – '.$invoice->getMeta('guide') : null; ?></p>
            <p>Contribuinte: <?php echo !empty($invoice->getMember()) ? $invoice->getMember()->getMeta('contributor') : 'Indefinido'; ?></p>
            <?php else: ?>
            <label for="contributor" class="mt-2">Tipo de contribuinte</label>
            <select name="member[contributor]" id="contributor" class="form-control">
                <option>Empregado</option>
                <option>Empresa</option>
            </select>
            <label for="guide">Tipo de guia</label>
            <select name="invoice[guide]" id="guide" class="form-control">
            <option value="">Selecione o tipo</option>
            <?php 
			foreach($emails as $e => $setor) 
                echo '<option class="w-employee" value="'.$e.'">'.$setor.'</option>';
			?>
            </select>
            <!-- end !empty invoice-->
            <?php endif; ?>
        </div>
    </div>
