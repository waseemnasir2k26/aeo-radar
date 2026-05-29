<?php
/**
 * AEO content analyzer — pure, dependency-free scoring engine.
 *
 * No WordPress calls, no network. Given a post's HTML + title + excerpt it
 * returns an Answer-Engine-Optimization score (0-100), a grade, and a list of
 * checks with advice. Kept pure so it is fully unit-testable.
 *
 * @package AEO_Radar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Static analyzer.
 */
class AEOR_Analyzer {

	/**
	 * Analyze content for AEO readiness.
	 *
	 * @param string $html    Post content HTML.
	 * @param string $title   Post title.
	 * @param string $excerpt Meta description / excerpt.
	 * @return array { score:int, grade:string, words:int, checks:array }
	 */
	public static function analyze( $html, $title, $excerpt = '' ) {
		$html    = (string) $html;
		$title   = trim( (string) $title );
		$excerpt = trim( (string) $excerpt );

		$text  = self::plain( $html );
		$words = self::word_count( $text );

		$headings  = self::headings( $html );
		$first_par = self::first_paragraph( $html, $text );
		$fp_words  = self::word_count( $first_par );

		$checks = array();

		// Pre-compute shared signals.
		$q_headings = 0;
		foreach ( $headings as $h ) {
			if ( self::is_question( $h ) ) {
				$q_headings++;
			}
		}

		// 1. Answer-first (18) — concise answer in the opening paragraph.
		$checks[] = self::check(
			'answer_first',
			__( 'Answer-first opening', 'aeo-radar' ),
			18,
			( '' !== $first_par && $fp_words > 0 && $fp_words <= 60 ),
			__( 'Open with a direct, self-contained answer in the first ~50 words. AI engines quote the opening when it stands alone.', 'aeo-radar' )
		);

		// 2. Evidence (17) — statistics, cited sources, or quotes. The strongest
		// empirically-proven driver of AI citations (Princeton GEO study, KDD 2024).
		$checks[] = self::check(
			'evidence',
			__( 'Evidence: stats, sources or quotes', 'aeo-radar' ),
			17,
			self::has_evidence( $html, $text ),
			__( 'Back claims with a statistic, a linked source, or a quote. Evidence is the strongest proven driver of AI citations.', 'aeo-radar' )
		);

		// 3. Structured content (15) — lists or tables.
		$has_struct = (bool) preg_match( '/<(ul|ol|table)[\s>]/i', $html );
		$checks[] = self::check(
			'structured',
			__( 'Lists or tables', 'aeo-radar' ),
			15,
			$has_struct,
			__( 'Use bullet lists or a table for steps, comparisons, or specs. Structured data is far easier for AI to lift accurately.', 'aeo-radar' )
		);

		// 4. Headings (12) — at least two subheadings.
		$h_count  = count( $headings );
		$checks[] = self::check(
			'headings',
			__( 'Clear heading structure', 'aeo-radar' ),
			12,
			( $h_count >= 2 ),
			__( 'Add at least two descriptive H2/H3 subheadings so engines can extract sections.', 'aeo-radar' )
		);

		// 5. FAQ (10).
		$has_faq  = self::has_faq( $html, $headings, $q_headings );
		$checks[] = self::check(
			'faq',
			__( 'FAQ section', 'aeo-radar' ),
			10,
			$has_faq,
			__( 'Add a short FAQ (question + concise answer) — a content shape answer engines cite readily.', 'aeo-radar' )
		);

		// 6. Depth (10).
		$checks[] = self::check(
			'depth',
			__( 'Enough depth', 'aeo-radar' ),
			10,
			( $words >= 300 ),
			__( 'Aim for 300+ words of substance. Thin pages rarely get cited.', 'aeo-radar' )
		);

		// 7. Question headings (8) — at least one heading phrased as a question.
		$checks[] = self::check(
			'question_headings',
			__( 'Question-style headings', 'aeo-radar' ),
			8,
			( $q_headings >= 1 ),
			__( 'Phrase at least one heading as the question a user would ask (e.g. "How does X work?"). It maps directly to answer-engine queries.', 'aeo-radar' )
		);

		// 8. Meta description (5) — present and well-sized.
		$ex_len   = self::strlen( $excerpt );
		$meta_ok  = ( $ex_len >= 50 && $ex_len <= 160 );
		$checks[] = self::check(
			'meta_description',
			__( 'Meta description', 'aeo-radar' ),
			5,
			$meta_ok,
			__( 'Write a 50–160 character summary/excerpt. It feeds both search snippets and AI summaries.', 'aeo-radar' )
		);

		// 9. Title length (5).
		$t_len    = self::strlen( $title );
		$title_ok = ( $t_len >= 20 && $t_len <= 65 );
		$checks[] = self::check(
			'title',
			__( 'Focused title', 'aeo-radar' ),
			5,
			$title_ok,
			__( 'Keep the title 20–65 characters and specific. Vague or overlong titles get skipped by engines.', 'aeo-radar' )
		);

		$score = 0;
		foreach ( $checks as $c ) {
			if ( $c['pass'] ) {
				$score += $c['weight'];
			}
		}
		$score = (int) min( 100, max( 0, $score ) );

		return array(
			'score'  => $score,
			'grade'  => self::grade( $score ),
			'words'  => $words,
			'checks' => $checks,
		);
	}

	/**
	 * Build a single check row.
	 *
	 * @param string $id     Check id.
	 * @param string $label  Human label.
	 * @param int    $weight Points.
	 * @param bool   $pass   Passed?
	 * @param string $advice Fix advice.
	 * @return array
	 */
	private static function check( $id, $label, $weight, $pass, $advice ) {
		return array(
			'id'     => $id,
			'label'  => $label,
			'weight' => (int) $weight,
			'pass'   => (bool) $pass,
			'advice' => $advice,
		);
	}

	/**
	 * Letter grade from score.
	 *
	 * @param int $score Score.
	 * @return string
	 */
	private static function grade( $score ) {
		if ( $score >= 85 ) {
			return 'A';
		}
		if ( $score >= 70 ) {
			return 'B';
		}
		if ( $score >= 50 ) {
			return 'C';
		}
		return 'D';
	}

	/**
	 * Strip tags + collapse whitespace.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	private static function plain( $html ) {
		$text = preg_replace( '/<(script|style)[^>]*>.*?<\/\1>/is', ' ', $html );
		if ( null === $text ) { // PCRE limit hit on pathological input.
			$text = $html;
		}
		$text = aeor_strip( $text );
		$text = html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' );
		$text = preg_replace( '/\s+/', ' ', $text );
		if ( null === $text ) {
			$text = '';
		}
		return trim( $text );
	}

	/**
	 * Word count of plain text.
	 *
	 * @param string $text Text.
	 * @return int
	 */
	private static function word_count( $text ) {
		$text = trim( $text );
		if ( '' === $text ) {
			return 0;
		}
		$parts = preg_split( '/\s+/', $text );
		return is_array( $parts ) ? count( $parts ) : 0;
	}

	/**
	 * Multibyte-safe string length with graceful fallback.
	 *
	 * @param string $s String.
	 * @return int
	 */
	private static function strlen( $s ) {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $s ) : strlen( $s );
	}

	/**
	 * Extract heading text nodes (H1-H6).
	 *
	 * @param string $html HTML.
	 * @return array List of plain-text headings.
	 */
	private static function headings( $html ) {
		$out = array();
		// Backreference \1 enforces matching open/close tags (no <h2>…</h3>).
		if ( preg_match_all( '/<(h[1-6])[^>]*>(.*?)<\/\1>/is', $html, $m ) ) {
			foreach ( $m[2] as $h ) {
				$t = trim( aeor_strip( $h ) );
				if ( '' !== $t ) {
					$out[] = $t;
				}
			}
		}
		return $out;
	}

	/**
	 * Get the first paragraph's text, or a fallback slice of the body.
	 *
	 * @param string $html HTML.
	 * @param string $text Plain text fallback.
	 * @return string
	 */
	private static function first_paragraph( $html, $text ) {
		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $html, $m ) ) {
			$p = trim( aeor_strip( $m[1] ) );
			if ( '' !== $p ) {
				return $p;
			}
		}
		// No <p> (e.g. classic content) — use the first sentence, not a raw
		// 400-char slice (which would over-count words and fail answer-first).
		$slice = aeor_substr( $text, 0, 400 );
		if ( preg_match( '/^(.*?[.!?])(?:\s|$)/', $slice, $mm ) ) {
			return trim( $mm[1] );
		}
		return trim( aeor_substr( $text, 0, 220 ) );
	}

	/**
	 * Is a heading phrased as a question?
	 *
	 * @param string $h Heading text.
	 * @return bool
	 */
	private static function is_question( $h ) {
		$h = trim( $h );
		if ( '' === $h ) {
			return false;
		}
		// Catch ASCII "?" and Spanish "¿".
		if ( false !== strpos( $h, '?' ) || false !== strpos( $h, "\xc2\xbf" ) ) {
			return true;
		}
		$starters = array( 'what', 'how', 'why', 'when', 'where', 'who', 'which', 'can', 'is', 'are', 'do', 'does', 'should', 'will' );
		// First alphabetic run, skipping leading numbers/punctuation ("5. How…").
		$first = '';
		if ( preg_match( '/[a-zA-Z]+/', $h, $mm ) ) {
			$first = strtolower( $mm[0] );
		}
		return in_array( $first, $starters, true );
	}

	/**
	 * Detect an FAQ section.
	 *
	 * @param string $html       HTML.
	 * @param array  $headings   Heading texts.
	 * @param int    $q_headings Count of question headings.
	 * @return bool
	 */
	private static function has_faq( $html, $headings, $q_headings ) {
		if ( false !== stripos( $html, '[aeo_faq' ) ) {
			return true;
		}
		foreach ( $headings as $h ) {
			if ( false !== stripos( $h, 'faq' ) || false !== stripos( $h, 'frequently asked' ) ) {
				return true;
			}
		}
		return $q_headings >= 2;
	}

	/**
	 * Detect evidence: a statistic, a cited/linked source, or a quote.
	 *
	 * @param string $html HTML.
	 * @param string $text Plain text.
	 * @return bool
	 */
	private static function has_evidence( $html, $text ) {
		// A percentage or a multi-digit number reads as a statistic.
		$has_stat = (bool) preg_match( '/\d+(?:\.\d+)?\s?%/', $text ) || (bool) preg_match( '/\b\d{2,}\b/', $text );
		// An outbound link, blockquote, or <cite> reads as a cited source/quote.
		$has_src  = (bool) preg_match( '#<a\s[^>]*href=["\']https?://#i', $html ) || (bool) preg_match( '/<(blockquote|cite)[\s>]/i', $html );
		return $has_stat || $has_src;
	}
}

if ( ! function_exists( 'aeor_strip' ) ) {
	/**
	 * Tag-strip that works with or without WordPress loaded (keeps the analyzer
	 * unit-testable in isolation).
	 *
	 * @param string $s HTML.
	 * @return string
	 */
	function aeor_strip( $s ) {
		if ( function_exists( 'wp_strip_all_tags' ) ) {
			return wp_strip_all_tags( $s );
		}
		$s = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $s );
		return trim( preg_replace( '/<[^>]*>/', '', $s ) );
	}
}

if ( ! function_exists( 'aeor_substr' ) ) {
	/**
	 * Multibyte-safe substr with fallback.
	 *
	 * @param string $s     String.
	 * @param int    $start Start.
	 * @param int    $len   Length.
	 * @return string
	 */
	function aeor_substr( $s, $start, $len ) {
		return function_exists( 'mb_substr' ) ? mb_substr( $s, $start, $len ) : substr( $s, $start, $len );
	}
}
