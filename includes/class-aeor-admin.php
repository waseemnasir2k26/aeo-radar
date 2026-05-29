<?php
/**
 * Site-wide AEO dashboard (Tools → AEO Radar).
 *
 * @package AEO_Radar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists published content ranked by AEO score, worst first.
 */
class AEOR_Admin {

	/**
	 * Register the page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	/**
	 * Add under Tools.
	 */
	public function add_page() {
		add_management_page(
			__( 'AEO Radar', 'aeo-radar' ),
			__( 'AEO Radar', 'aeo-radar' ),
			'edit_others_posts',
			'aeo-radar',
			array( $this, 'render' )
		);
	}

	/**
	 * Render the dashboard.
	 */
	public function render() {
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			return;
		}

		$query = new WP_Query(
			array(
				'post_type'              => array( 'post', 'page' ),
				'post_status'            => 'publish',
				'posts_per_page'         => 100,
				'orderby'                => 'modified',
				'order'                  => 'DESC',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
			)
		);

		$rows  = array();
		$total = 0;
		foreach ( $query->posts as $p ) {
			$report = AEOR_Analyzer::analyze( (string) $p->post_content, get_the_title( $p ), (string) $p->post_excerpt );
			$total += (int) $report['score'];
			$rows[] = array(
				'id'    => $p->ID,
				'title' => get_the_title( $p ),
				'type'  => $p->post_type,
				'score' => (int) $report['score'],
				'grade' => $report['grade'],
				'words' => (int) $report['words'],
			);
		}
		wp_reset_postdata();

		// Worst score first — that is the work queue.
		usort(
			$rows,
			function ( $a, $b ) {
				return $a['score'] <=> $b['score'];
			}
		);

		$count = count( $rows );
		$avg   = $count ? (int) round( $total / $count ) : 0;
		?>
		<div class="wrap aeor-wrap">
			<h1><?php esc_html_e( 'AEO Radar — Answer Engine Audit', 'aeo-radar' ); ?></h1>
			<p class="aeor-sub">
				<?php esc_html_e( 'How quotable your content is by AI answer engines. Worst-scoring content is listed first — that is where a small fix moves the needle most. Everything is analyzed locally; nothing leaves your server.', 'aeo-radar' ); ?>
			</p>

			<?php if ( ! $count ) : ?>
				<p><?php esc_html_e( 'No published posts or pages found yet.', 'aeo-radar' ); ?></p>
			<?php else : ?>
				<div class="aeor-summary">
					<div class="aeor-summary__avg" style="--aeor-color: <?php echo esc_attr( AEO_Radar::color( $avg ) ); ?>;">
						<span class="aeor-summary__num"><?php echo esc_html( $avg ); ?></span>
						<span class="aeor-summary__lbl"><?php esc_html_e( 'avg AEO score', 'aeo-radar' ); ?></span>
					</div>
					<p>
						<?php
						printf(
							/* translators: %d: number of items analyzed */
							esc_html__( '%d items analyzed (latest 100).', 'aeo-radar' ),
							(int) $count
						);
						?>
					</p>
				</div>

				<table class="widefat striped aeor-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Score', 'aeo-radar' ); ?></th>
							<th><?php esc_html_e( 'Title', 'aeo-radar' ); ?></th>
							<th><?php esc_html_e( 'Type', 'aeo-radar' ); ?></th>
							<th><?php esc_html_e( 'Words', 'aeo-radar' ); ?></th>
							<th><?php esc_html_e( 'Action', 'aeo-radar' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td>
									<span class="aeor-pill" style="--aeor-color: <?php echo esc_attr( AEO_Radar::color( $row['score'] ) ); ?>;">
										<?php echo esc_html( $row['score'] ); ?> · <?php echo esc_html( $row['grade'] ); ?>
									</span>
								</td>
								<td><?php echo esc_html( $row['title'] ); ?></td>
								<td><?php echo esc_html( $row['type'] ); ?></td>
								<td><?php echo esc_html( $row['words'] ); ?></td>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $row['id'] ) ); ?>"><?php esc_html_e( 'Edit', 'aeo-radar' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<p class="aeor-footer">
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: 1: AEO Kit link, 2: SkynetLabs link */
						__( 'Tip: pair with %1$s to emit llms.txt + schema. Part of the AEO Suite by %2$s.', 'aeo-radar' ),
						'<a href="https://github.com/waseemnasir2k26/aeo-kit" target="_blank" rel="noopener">AEO Kit</a>',
						'<a href="https://www.skynetjoe.com" target="_blank" rel="noopener">SkynetLabs</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}
}
