<?php
/**
 * Plugin Name: PMPro Sortable Member List
 * Plugin URL: https://github.com/pbrocks/pmpro-sortable-members-list
 * Description: An example of using native WP admin tables by extending the WP_List_Table class to display data in WP dashboard
 * Author: pbrocks
 * Author URL: https://github.com/pbrocks/
 */

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

if ( is_admin() ) {
	new PMPro_Member_List_Table();
}

/**
 * PMPro_Member_List_Table class will create the page to load the table
 */
class PMPro_Member_List_Table {

	/**
	 * Constructor will create the menu item
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_pmpro_sortable_list_page' ) );
		add_action( 'admin_head', array( $this, 'add_admin_css' ) );
	}

	/**
	 * Menu item will allow us to load the page to display the table
	 */
	public function add_pmpro_sortable_list_page() {
		$hook_suffix = add_submenu_page( 'pmpro-membershiplevels', 'Sortable Members', 'Sortable Members', 'manage_options', 'pmpro-list-table.php', array( $this, 'list_table_page' ) );
		add_action( "load-$hook_suffix", array( $this, 'member_screen_options' ) );
		add_action( "load-$hook_suffix", array( $this, 'sortable_help_tabs' ) );
	}

	public function member_screen_options() {
		global $pmpro_sortable_list;
		$option = 'per_page';
		$args = array(
			'label' => 'Subscribers',
			'default' => 10,
			'option' => 'members_per_page',
		);
		add_screen_option( $option, $args );
		$pmpro_sortable_list = new PMPro_Member_List();
	}
	
	public function sortable_help_tabs() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'      => 'sortable_overview',
				'title'   => __( 'Sortable Overview', 'pmpro-sortable-members' ),
				'content' => '<p>' . __( 'Overview of your plugin or theme here', 'pmpro-sortable-members' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'sortable_faq',
				'title'   => __( 'Sortable FAQ', 'pmpro-sortable-members' ),
				'content' => '<p>' . __( 'Frequently asked questions and their answers here', 'pmpro-sortable-members' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'sortable_support',
				'title'   => __( 'Sortable Support', 'pmpro-sortable-members' ),
				'content' => '<p>' . __( 'For support, visit the <a href="https://www.paidmembershipspro.com/forums/forum/members-forum/" target="_blank">Support Forums</a>', 'pmpro-sortable-members' ) . '</p>',
			)
		);

		$screen->set_help_sidebar( '<p>' . __( 'This is the content you will be adding to the sidebar.', 'pmpro-sortable-members' ) . '</p>' );
	}

	/**
	 * Display the list table page
	 *
	 * @return Void
	 */
	public function list_table_page() {
		global $pmpro_sortable_list;

		$pmpro_sortable_list->prepare_items();
		?>
			<div class="wrap">
				<div id="icon-users" class="icon32"></div>
				<h2>PMPro Sortable Member List</h2>
				<?php $pmpro_sortable_list->display(); ?>
			</div>
		<?php
	}
	/**
	 * WP_List_Table CSS
	 *
	 * Adds CSS on the list table page.
	 */
	public static function add_admin_css() {
		?>
		<style type="text/css">
		#ID, .ID.column-ID {
			text-align: center;
			width: 7%;
		}
		#membership,
		.membership.column-membership {
			text-align: center;
			width: 10%;
		}
		#membership_id,
		.membership_id.column-membership_id {
			text-align: center;
			width: 8%;
		}
		</style>
		<?php
	}

}

// WP_List_Table is not loaded automatically so we need to load it in our application
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class PMPro_Member_List extends WP_List_Table {
	/**
	 * [REQUIRED] You must declare constructor and give some basic params
	 */
	function __construct() {
		global $status, $page;
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		parent::__construct(
			array(
				'singular' => 'member',
				'plural' => 'members',
			)
		);
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->sql_table_data();
		usort( $data, array( $this, 'sort_data' ) );

		$per_page = $this->get_items_per_page( 'members_per_page', 15 );
		$currentPage = $this->get_pagenum();
		$total_items = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $per_page ), $per_page );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		   $columns = array(
			   'cb'            => '<input type="checkbox" />',
			   'ID'            => 'ID',
			   'display_name'  => 'Display Name',
			   'user_email'    => 'Email',
			   'membership'    => 'Level Name',
			   'membership_id' => 'Level ID',
			   'startdate'     => 'Subscribe Date',
			   'enddate'       => 'End Date',
			   'joindate'      => 'Initial Date',
		   );
		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array(
			'ID' => array(
				'ID',
				false,
			),
			'display_name' => array(
				'display_name',
				false,
			),
			'user_email' => array(
				'user_email',
				false,
			),
			'membership' => array(
				'membership',
				false,
			),
			'membership_id' => array(
				'membership_id',
				false,
			),
			'startdate' => array(
				'startdate',
				false,
			),
			'enddate' => array(
				'enddate',
				false,
			),
			'joindate' => array(
				'joindate',
				false,
			),
		);
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function sql_table_data() {
		global $wpdb;
		$sql_table_data = array();
		$mysqli_query = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.user_nicename, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta umh ON umh.meta_key = 'pmpromd_hide_directory' AND u.ID = umh.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id";
		$mysqli_query .= " WHERE mu.status = 'active' AND (umh.meta_value IS NULL OR umh.meta_value <> '1') AND mu.membership_id > 0 ";
		$sql_table_data = $wpdb->get_results( $mysqli_query, ARRAY_A );
		return $sql_table_data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array  $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'ID':
			case 'display_name':
			case 'user_email':
			case 'membership':
			case 'membership_id':
			case 'cycle_period':
			case 'cycle_number':
				return $item[ $column_name ];
			case 'startdate':
				$startdate = $item[ $column_name ];
				return date( 'Y-m-d', $startdate );
			case 'enddate':
				if ( 0 == $item[ $column_name ] ) {
					return 'Recurring';
				} else {
					return date( 'Y-m-d', $item[ $column_name ] );
				}
			case 'joindate':
				if ( $item['startdate'] == $item['joindate'] ) {
					return 'Join = Start';
				} else {
					return date( 'Y-m-d', $item[ $column_name ] );
				}

			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'startdate';
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if ( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( $order === 'asc' ) {
			return $result;
		}

		return -$result;
	}
}
