<?php
/**
 * Per-post AEO audit meta box.
 *
 * @package AEO_Radar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the live AEO score + checks on the edit screen.
 */
class AEOR_Meta_Box {

	/**
	 * Hook registration.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
	}

	/**
	 * Register on public post types.
	 */
	public function register() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );
		unset( $post_types['attachment'] );

		foreach ( $post_types as $pt ) {
			add_meta_box(
				'aeor_meta',
				__( 'AEO Radar — Answer Engine Score', 'aeo-radar' ),
				array( $this, 'render' ),
				$pt,
				'side',
				'high'
			);
		}
	}

	/**
	 * Render the score panel for the current post.
	 *
	 * @param WP_Post $post Post.
	 */
	public function render( $post ) {
		$content = (string) $post->post_content;
		$excerpt = (string) $post->post_excerpt;
		$report  = AEOR_Analyzer::analyze( $content, get_the_title( $post ), $excerpt );

		$score = (int) $report['score'];
		$color = AEO_Radar::color( $score );
		?>
		<div class="aeor-box">
			<div class="aeor-score" style="--aeor-color: <?php echo esc_attr( $color ); ?>;">
				<span class="aeor-score__num"><?php echo esc_html( $score ); ?></span>
				<span class="aeor-score__grade"><?php echo esc_html( $report['grade'] ); ?></span>
			</div>
			<p class="aeor-score__cap">
				<?php
				printf(
					/* translators: %d: word count */
					esc_html__( 'AEO score · %d words', 'aeo-radar' ),
					(int) $report['words']
				);
				?>
			</p>
			<ul class="aeor-checks">
				<?php foreach ( $report['checks'] as $check ) : ?>
					<li class="aeor-check <?php echo esc_attr( $check['pass'] ? 'is-pass' : 'is-fail' ); ?>">
						<span class="aeor-check__icon" aria-hidden="true"><?php echo $check['pass'] ? '&#10003;' : '&#10007;'; ?></span>
						<span class="aeor-check__label"><?php echo esc_html( $check['label'] ); ?></span>
						<?php if ( ! $check['pass'] ) : ?>
							<span class="aeor-check__advice"><?php echo esc_html( $check['advice'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="aeor-note">
				<?php esc_html_e( 'Save the post to refresh the score. 100% local — nothing is sent anywhere.', 'aeo-radar' ); ?>
			</p>
		</div>
		<?php
	}
}
