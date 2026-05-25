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

class Football_Pool_Tournament_Page {
	public function page_content() {
		$pool = footballpool();
		$filtered_matches = apply_filters( 'footballpool_filtered_matches', $pool->matches->matches );
		$output = $pool->matches->print_matches( $filtered_matches, 'page matches-page' );
		return apply_filters( 'footballpool_matches_page_html', $output, $pool->matches->matches );
	}
}
