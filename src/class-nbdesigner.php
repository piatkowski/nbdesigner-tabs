<?php

namespace NBDesignerTabs;

class NBDesigner {

	/**
	 * @var null|NBDesigner singleton instance
	 */
	private static $instance = null;

	/**
	 * @var ProductTab
	 */
	private $product;

	private function __construct() {
		//private contructor
	}

	static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
     * Initialize actions and filters
     *
	 * @return void
	 */
	function init() {
		add_action( 'nbd_extra_css', [ $this, 'add_css' ] );
		add_action( 'nbd_extra_js', [ $this, 'add_js' ] );
		add_action( 'template_redirect', [ $this, 'init_on_designer_page' ], 1 );
		add_action( 'nbd_editor_extra_tab_nav', [ $this, 'render_tab_nav' ] );
		add_action( 'nbd_editor_extra_tab_content', [ $this, 'render_tab_content' ] );
		add_action( 'nbd_after_option_product', [ $this, 'render_product_options' ], 1 );
		add_action( 'save_post_product', [ $this, 'save_product' ], 30 );
		add_action( 'after_nbd_save_cart_design', [ $this, 'save_cart_data' ], 10, 2 );
		add_action( 'after_nbd_save_customer_design', [ $this, 'save_customer_data' ], 10, 1 );
	}

	/**
     * Add css stylesheet to NBD Designer
     * NBD does not use enqueued styles and scripts
     *
	 * @return void
	 */
	function add_css() {
		$stylesheet_url = plugins_url( 'assets/css/tab.min.css', Plugin::get_plugin_path() );
		echo '<link rel="stylesheet" href="' . $stylesheet_url . '">';
	}

	/**
	 * Add JS script files to NBD Designer
	 * NBD does not use enqueued styles and scripts
	 *
	 * @return void
	 */
	function add_js() {
		$script_url = plugins_url( 'assets/js/tab.js', Plugin::get_plugin_path() );
		echo '<script src="' . $script_url . '" defer></script>';
	}

	/**
     * Creates ProductTab instance on NBD designer page
     *
	 * @return void
	 */
	function init_on_designer_page() {

		try {
			$is_ajax_designer = isset( $_GET['action'] ) && $_GET['action'] === 'nbdesigner_editor_html';
			if ( ! is_nbd_design_page() && ! $is_ajax_designer ) {
				return;
			}
			$id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
			if ( $id === 0 || get_post_type( $id ) !== 'product' ) {
				return;
			}

			$states = [];
			if ( ! empty( $_GET['nbd_item_key'] ) ) {
				$path     = NBDESIGNER_CUSTOMER_DIR . '/' . str_replace( '.', '', $_GET['nbd_item_key'] );
				$tab_file = $path . '/nbd_tabs.json';
				if ( file_exists( $tab_file ) ) {
					$states = json_decode( stripslashes( file_get_contents( $tab_file ) ), true );
				}
			}

			$this->product = new ProductTab( $id, $states );

		} catch ( \Exception $e ) {
			return;
		}
	}

	/**
     * Renders HTML for tabs navigation on designer page
     *
	 * @return void
	 */
	function render_tab_nav() {

		if ( ! $this->product ) {
			return;
		}
		foreach ( $this->product->get_tabs() as $tab ):
			?>
            <li
                    id="nav-tab-<?php echo $tab->get_id(); ?>"
                    data-tour="tab-<?php echo $tab->get_id(); ?>"
                    data-tour-priority="6"
                    class="tab animated slideInLeft animate900"
                    ng-click="disableDrawMode();disablePreventClickMode()"
            >
				<?php if ( ! $tab->get_thumbnail() ): ?>
                    <i class="icon-nbd icon-nbd-stack"></i>
				<?php else: ?>
                    <i class="icon-nbd icon-nbdt" style="background-image:url('<?php echo esc_url( $tab->get_thumbnail() ); ?>')"></i>
				<?php endif; ?>
                <span>
                    <?php echo esc_html( $tab->get_title() ); ?>
                </span>
            </li>
		<?php
		endforeach;

	}

	/**
	 * Renders HTML for tabs content on designer page
	 *
	 * @return void
	 */
	function render_tab_content() {
		if ( ! $this->product ) {
			return;
		}
		foreach ( $this->product->get_tabs() as $tab ):
			?>
            <div class="tab nbdesigner-tab" id="tab-tab-<?php echo esc_attr( $tab->get_id() ); ?>"
                 data-container="#tab-tab-<?php echo esc_attr( $tab->get_id() ); ?>"
                 data-tab-id="<?php echo base64_encode( $tab->get_id() . ':tab:' . wp_hash( $tab->get_id() . 'tab' ) ); ?>"
                 data-validation-message="<?php echo esc_attr( $tab->get_validation_message() ); ?>">
                <div class="tab-main tab-scroll">
                    <div class="nbdt__tab-description">
						<?php echo wp_kses_post( $tab->get_description() ); ?>
                    </div>
					<?php
					foreach ( $tab->get_fields() as $field ) {
						echo wp_kses_post( $field->render() );
					}
					?>
                </div>
            </div>
		<?php
		endforeach;
	}

	/**
	 * Renders HTML for admin backend
	 *
	 * @return void
	 */
	function render_product_options( $post_id ) {
		$tab_posts    = get_posts( [
			'post_type'   => Post::POST_TYPE,
			'post_status' => 'publish',
			'numberposts' => - 1
		] );
		$tab_post_ids = get_post_meta( $post_id, '_nbdt_product_tabs', true );
		if ( ! is_array( $tab_post_ids ) ) {
			$tab_post_ids = [];
		}
		?>
        <div>
            <h3>Włącz zakładki dla produktu</h3>
			<?php
			foreach ( $tab_posts as $tab_post ) {
				$selected = in_array( $tab_post->ID, $tab_post_ids );
				?>
                <p class="nbd-option-top">
                    <label>
                        <input type="checkbox" name="_nbdt_product_tabs[]"
                               value="<?php echo $tab_post->ID; ?>"<?php checked( $selected ); ?> autocomplete="off">
                        <span><?php echo esc_html( $tab_post->post_title ); ?> (#<?php echo $tab_post->ID; ?>) </span>
                    </label>
                </p>
				<?php
			}
			?>
        </div>
		<?php
	}

	/**
     * Custom hook to save tab post meta on admin backend
     *
	 * @param $post_id
	 *
	 * @return void
	 */
	function save_product( $post_id ) {
		if ( isset( $_POST['_nbdt_product_tabs'] ) && is_array( $_POST['_nbdt_product_tabs'] ) ) {
			$tab_post_ids = [];
			foreach ( $_POST['_nbdt_product_tabs'] as $tab_post_id ) {
				if ( (int) $tab_post_id > 0 && get_post_type( (int) $tab_post_id ) === Post::POST_TYPE ) {
					$tab_post_ids[] = (int) $tab_post_id;
				}
			}
			update_post_meta( $post_id, '_nbdt_product_tabs', $tab_post_ids );
		}
	}

	/**
     * Get encoded value and check value integrity
     *
	 * @param $base64
	 *
	 * @return array|false
	 */
	private function get_safe_value( $base64 ) {
		list( $id, $value, $hash ) = explode( ':', base64_decode( $base64 ) . ':' );
		if ( wp_hash( $id . $value ) === $hash ) {
			return [
				0 => $id,
				1 => $value
			];
		}

		return false;
	}

	/**
     * Save tabs state if the project is in the Cart
     *
	 * @param $post
	 * @param $result
	 *
	 * @return void
	 */
	function save_cart_data( $post, $result ) {
		if ( ! empty( $result['cart_nbd_item_key'] ) ) {
			$this->save_to_json( $result['cart_nbd_item_key'] );
		}
	}

	/**
	 * Save tabs state if the project is in the Order
	 *
	 * @param $post
	 * @param $result
	 *
	 * @return void
	 */
	function save_customer_data( $result ) {
		if ( ! empty( $result['folder'] ) ) {
			$this->save_to_json( $result['folder'] );
		}
	}

	/**
     * Save state to json file under design folder (nbd)
     *
	 * @param $nbd_item_key
	 *
	 * @return void
	 */
	private function save_to_json( $nbd_item_key ) {
		$customer_data = [];
		if ( ! empty( $_POST['nbdt'] ) ) {
			$nbd_tabs = json_decode( stripslashes( $_POST['nbdt'] ), true );
			foreach ( $nbd_tabs as $tab_id_hashed => $tab ) {
				$tab_id = $this->get_safe_value( $tab_id_hashed );
				if ( $tab_id === false ) {
					return;
				}
				if ( ! is_array( $tab ) ) {
					continue;
				}
				foreach ( $tab as $hashed_value ) {
					$value = $this->get_safe_value( $hashed_value );
					if ( $value === false ) {
						return;
					}
					$customer_data[ $tab_id[0] ][ $value[0] ] = $value[1];
				}

			}
		}
		$path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
		file_put_contents( $path . '/nbd_tabs.json', json_encode( $customer_data ) );
	}

}