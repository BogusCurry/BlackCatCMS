/**
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author          Black Cat Development
 *   @copyright       2013, Black Cat Development
 *   @link            http://blackcat-cms.org
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Core
 *   @package         mojito
 *
 */


$(document).ready(function()
{
	var s_box	= $('#search_box'),
		s_input	= $('#searchInput');
	$('#toggleSearch').click( function(e)
	{
		s_box.toggleClass('visible');
		if ( s_box.hasClass('visible') )
		{
			s_input.focus();
		}
	});
 	var l_box	= $('#login_box');
	$('#toggleLogin').click( function(e)
	{
		l_box.toggleClass('visible');
		if ( l_box.hasClass('visible') )
		{
			l_input.focus();
		}
	});
});