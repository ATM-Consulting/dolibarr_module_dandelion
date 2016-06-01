<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_dandelion.class.php
 * \ingroup dandelion
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsDandelion
 */
class ActionsDandelion
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		
		if ((in_array('productcard', explode(':', $parameters['context'])) 
			|| in_array('projectcard', explode(':', $parameters['context'])))
		
		&& $action === 'create')
		{
		  
		  	global $langs,$db,$user,$conf;
		  
		  	$langs->load('dandelion@dandelion');
		  	$table = $object->table_element;
		  
		  	if(in_array('projectcard', explode(':', $parameters['context']))){
		  		$prefix_list =  $conf->global->DANDELION_DEFAULT_PREFIX_PROJECT;
				$total_nb_char = $conf->global->DANDELION_TOTAL_NB_CHAR_PROJECT;
				$nb_min_char = (int)$conf->global->DANDELION_BASE_NB_CHAR_PROJECT;
			} 
			else {
				$prefix_list =  $conf->global->DANDELION_DEFAULT_PREFIX;
				$total_nb_char = $conf->global->DANDELION_TOTAL_NB_CHAR;
				$nb_min_char = (int)$conf->global->DANDELION_BASE_NB_CHAR;	
			}
		  
		    
		  
		  
		  	if(!empty($conf->global->DANDELION_DEFAULT_PREFIX)) {
		  		$TPrefix = explode(',',$prefix_list);
				dol_include_once('/core/lib/functions2.lib.php');
		  
		  		$tags = ' '.$langs->trans('NewRef').' : ';
		  	
			  	$TNextRef = array();
			  
			  	foreach($TPrefix as $prefix) {
			  		
					$mask = $prefix.'{'.str_pad('', $total_nb_char - strlen($prefix),'0').'}';
					//var_dump($mask,$table,$prefix);
					$TNextRef[] = get_next_value($db,$mask,$table,'ref');
					//var_dump($db->lastquery);
			  	}
				
				foreach($TNextRef as $ref) {
					$tags.='<a href="javascript:;" onclick="$input.val(\''.$ref.'\'); " class="nextref">'.$ref.'</a> ';
				}
				
				
			  	?>
			  	<style type="text/css">
			  		a.nextref {
			  			font-size: 9px;
			  			color:#fff;
			  			background-color: #000066;
			  			border-radius: 5px;
			  			padding:5px;
			  		}
			  		a.nextref:nth-child(odd) {
			  			background-color: #000099;	
			  		}
			  	</style><?php 	
		  	}
		  
		  	
		  	?>
		  	<script type ="text/javascript">
		  	$(document).ready(function() {
		  		$input = $('div.fiche input[name=ref]').first();
		  		<?php
		  		
					if(!empty($tags)) {
						echo '$input.after("'.addslashes($tags).'");';
						
					}		  		
		  		?>
		  		
		  		
		  		$input.autocomplete({
			      source: "<?php echo dol_buildpath('/dandelion/script/interface.php?get=nextref&table='.$table,1) ?>",
			      minLength: <?php echo $nb_min_char; ?>,
			      select: function( event, ui ) {
			       
			       	$(this).val(ui.item.value);
			       
			      }
			    });
		  	});
		  	</script>
		  	<?php
		  
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}
}