<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

class FFG_Persona_Assignment_Admin {

    private static $instance;

    private $field_info;

    private $path;

    private $url;

    private $persona_mapping_field_meta;

    private function __construct() {

        $this->path = plugin_dir_path( __FILE__ );
		$this->url  = plugin_dir_url( __FILE__ );


       // add_action( 'xprofile_field_additional_options', array( $this, 'render' ), 1000);
        add_action( 'xprofile_field_after_save', array( $this, 'save_persona_mapping' ) );
        

    }

    /**
	 * Get the singleton.
	 *
	 * @return FFG_Persona_Assignment_Admin
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    public function render(BP_XProfile_Field $field ) {
        ?>
        
        <div class="postbox" id="xprofile-field-mapping">   
        <h3> <?php _ex( 'Persona Mapping', 'Persona Mapping section title in the admin', 'persona-mapping-field-for-bp' ); ?></h3> 
        <?php
            $options = $field->get_children();
            if ($options) {
                foreach($options as $childoptions) {
                    echo "<span>".$childoptions->name."</span>";
                    echo "<input type='text' id='".$childoptions->name."' name='".$childoptions->name."'><br />"; 
   
                }
            }
            ?>
            </div>
            <?php
    }

    public function save_persona_mapping($field) {
        $options = $field->get_children();
        $persona_mapping_field_meta = array();
        $tempvalue = "";
        if ($options) {
            foreach($options as $childoptions) {
                $tempconcat = $childoptions->name."#".$_POST[$childoptions->name];
                array_push($persona_mapping_field_meta, $tempconcat);
            }
            bp_xprofile_update_field_meta( $field->id, 'ffg-persona-mapping-meta', implode(',', $persona_mapping_field_meta) );
        }
    }


}

FFG_Persona_Assignment_Admin::get_instance();
