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

class Football_Pool_Groups_Page {
	public function page_content() {
		$pool = footballpool();

		$group_id = Football_Pool_Utils::get_string( 'group' );
		
		$groups = new Football_Pool_Groups;
		$output = $groups->print_group_standing( $group_id );
		
		if ( $group_id ) {
			// the games for this group
			$output .= sprintf( '<h2 style="clear: both;">%s</h2>'
								, __( 'matches in the group stage', 'football-pool' ) 
						);
			$plays = $groups->get_plays( $group_id );
			
			$output .= $pool->matches->print_matches( $plays, 'page group-page' );
			
			$group_names = $groups->get_group_names();
			if ( count( $group_names ) > 1 ) {
				/** @noinspection HtmlUnknownTarget */
				$output .= sprintf( '<p style="clear: both;"><a href="%s">%s</a></p>'
									, get_page_link()
									, __( 'view all groups', 'football-pool' )
							);
			}
		}
		
		return apply_filters( 'footballpool_groups_page_html', $output );
	}
}
