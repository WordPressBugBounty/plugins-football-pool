<?php

/*
 * Football Pool WordPress plugin
 *
 * @copyright Copyright (c) 2026 Antoine Hurkmans
 * @link https://wordpress.org/plugins/football-pool/
 * @license https://plugins.svn.wordpress.org/football-pool/trunk/COPYING
 *
 * This file is part of Football pool.
 *
 * Football pool is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * Football pool is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Football pool.
 * If not, see <https://www.gnu.org/licenses/>.
 */

class Football_Pool_Stadiums_Page {
	public function page_content() {
		$pool = footballpool();
		$output = '';
		$stadiums = new Football_Pool_Stadiums;

		$stadium_id = Football_Pool_Utils::get_string( 'stadium' );

		$stadium = $stadiums->get_stadium_by_id( $stadium_id );
		if ( is_object( $stadium ) ) {
			// show details for stadium
			$output .= sprintf( '<h1>%s</h1>', Football_Pool_Utils::xssafe( $stadium->name ) );
			
			if ( $stadium->comments != '' ) {
				$output .= sprintf( '<p class="stadium bio">%s</p>'
									, nl2br( Football_Pool_Utils::xssafe( $stadium->comments ) ) 
								);
			}
			
			$output .= sprintf( '<p>%s</p>', $stadium->HTML_image() );

			// the games played in this stadium
			$plays = $stadium->get_plays();
			if ( count( $plays ) > 0 ) {
				$output .= sprintf( '<h4>%s</h4>', __( 'matches', 'football-pool' ) );
				$output .= $pool->matches->print_matches( $plays, 'page stadium-page' );
			}

			/** @noinspection HtmlUnknownTarget */
			$output .= sprintf( '<p><a href="%s">%s</a></p>'
								, get_page_link()
								, __( 'view all venues', 'football-pool' )
						);
		} else {
			// show all stadiums
			$output .= '<div class="football-pool stadium-list">';
			$all_stadiums = $stadiums->get_stadiums();
			$output .= $stadiums->print_lines( $all_stadiums );
			$output .= '</div>';
		}
		
		return apply_filters( 'footballpool_stadiums_page_html', $output, $stadium );
	}
}
