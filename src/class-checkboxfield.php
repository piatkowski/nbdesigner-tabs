<?php

namespace NBDesignerTabs;

class CheckboxField extends Field {

	/**
	 * @var mixed|string
	 */
	private $label = '';

	/**
	 * @var string
	 */
	private $value = '';

	/**
	 * @throws \Exception
	 */
	function __construct( $id, $data ) {
		parent::__construct( $id, $data );
		if ( isset( $data['label'] ) ) {
			$this->label = $data['label'];
			$this->value = base64_encode( $this->id . ':' . $this->label . ':' . wp_hash( $this->id . $this->label ) );
		}
	}

	/**
     * Render field HTML code
     *
	 * @return void
	 */
	function render() {
		?>
        <div class="nbdt__checkbox-container" data-id="<?php echo esc_attr( $this->id ); ?>">
            <label>
                <input type="checkbox" value="<?php echo esc_attr( $this->value ); ?>"
                       autocomplete="off"<?php checked( $this->state === $this->label ) ?>> <?php echo esc_html( $this->label ); ?>
            </label>
        </div>
		<?php
	}
}