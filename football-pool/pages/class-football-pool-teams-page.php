<?php

/*
 * Football Pool WordPress plugin
 *
 * @copyright Copyright (c) 2024 Antoine Hurkmans
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

class Football_Pool_Teams_Page {
	public function page_content() {
		global $pool;

		$output = '';
		
		$team_id = Football_Pool_Utils::get_integer( 'team' );
		$team = new Football_Pool_Team( $team_id );
		
		// Show details for team or show links for all teams
		if ( is_object( $team ) && $team->id != 0 && $team->is_real == 1 && $team->is_active == 1 ) {
			// Team details
			/** @noinspection HtmlUnknownTarget */
			$output .= sprintf(
				'<h3 class="football-pool team name"><a href="%s" title="%s">%s</a></h3>',
				esc_attr( $team->link ),
				__( 'go to the team site', 'football-pool' ),
				Football_Pool_Utils::xssafe( $team->name )
			);
			
			if ( $team->comments !== '' ) {
				$output .= sprintf(
					'<p class="football-pool team bio">%s</p>',
					nl2br( Football_Pool_Utils::xssafe( $team->comments ) )
				);
			}

			/** @noinspection HtmlUnknownTarget */
			$output .= sprintf(
				'<table class="football-pool team info">
					<tr>
						<th>%s:</th>
						<td><a href="%s">%s</a></td>
					</tr>',
				__( 'plays in', 'football-pool' ),
				esc_url( add_query_arg( ['group' => $team->group_id], Football_Pool::get_page_link('groups') ) ),
				Football_Pool_Utils::xssafe( $team->group_name )
			);
						
			// Show venues?
			if ( Football_Pool_Utils::get_fp_option( 'show_venues_on_team_page', true ) ) {
				$stadiums = $team->get_stadiums();
				if ( is_array( $stadiums ) && count( $stadiums ) > 0 ) {
					$output .= sprintf(
						'<tr><th>%s:</th><td><ol class="football-pool stadium-list">',
						__( 'venues', 'football-pool' )
					);
					$stadium_page = Football_Pool::get_page_link( 'stadiums' );
					while ( $stadium = array_shift( $stadiums ) ) {
						/** @noinspection HtmlUnknownTarget */
						$output .= sprintf(
							'<li><a href="%s">%s</a></li>',
							esc_url( add_query_arg( ['stadium' => $stadium->id], $stadium_page ) ),
							Football_Pool_Utils::xssafe( $stadium->name )
						);
					}
					$output .= '</ol></td></tr>';
				}
			}

			if ( $team->photo !== '' ) {
				$output .= sprintf( '<tr><th>%s:</th>', __( 'photo', 'football-pool' ) );
				$output .= sprintf( '<td>%s</td></tr>', $team->HTML_thumb() );
			}

			$output .= '</table>';
			
			// The games for this team
			$plays = $team->get_plays();
			if ( count( $plays ) > 0 ) {
				$output .= sprintf( '<h4>%s</h4>', __( 'matches', 'football-pool' ) );
				$output .= $pool->matches->print_matches( $plays, 'page team-page' );
			}

			/** @noinspection HtmlUnknownTarget */
			$output .= sprintf(
				'<p><a href="%s">%s</a></p>',
				get_page_link(),
				__( 'view all teams', 'football-pool' )
			);
		} else {
			// show all teams
			$teams = new Football_Pool_Teams();
			$output .= '<ol class="football-pool team-list">';
			$all_teams = $teams->get_teams();
			$output .= $teams->print_lines( $all_teams );
			$output .= '</ol>';
		}
		
		return apply_filters( 'footballpool_teams_page_html', $output, $team );
	}
}
