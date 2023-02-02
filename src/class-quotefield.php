<?php

namespace NBDesignerTabs;

class QuoteField extends Field {

	/**
	 * @var string
	 */
	private $title = '';

	/**
	 * @var string
	 */
	private $content = '';

	/**
	 * @throws \Exception
	 */
	function __construct( $id, $data ) {
		parent::__construct( $id, $data );
		foreach ( [ 'title', 'content' ] as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$this->{$key} = $data[ $key ];
			}
		}
	}

	/**
     * Render HTML for quote field
     *
	 * @return void
	 */
	function render() {
		?>
        <div class="nbdt__quote-container" data-id="<?php echo esc_attr( $this->id ); ?>">
            <p class="nbdt__quote-title"><?php echo esc_html( $this->title ); ?></p>
            <p class="nbdt__quote-content"><?php echo esc_html( $this->content ); ?></p>
        </div>
		<?php
	}
}