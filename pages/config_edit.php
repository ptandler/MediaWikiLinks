<?php
# MantisBT - a php based bugtracking system
# Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

form_security_validate( 'plugin_mediawikilinks_config_edit' );

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_wiki_url = gpc_get_string( 'wikiUrl', 'http://en.wikipedia.org/wiki/' );

//echo '$f_wiki_url: ', $f_wiki_url;

if( plugin_config_get( 'wikiUrl' ) != $f_wiki_url ) {
//echo " ... set wiki_url ... ";
    plugin_config_set( 'wikiUrl', $f_wiki_url );
//echo plugin_config_get( 'wiki_url' ), "<br/><br/>";
// todo: config_set does not work
}

$f_interwiki_config = gpc_get_string( 'interwikiConfig', '' );
if( plugin_config_get( 'interwikiConfig' ) != $f_interwiki_config ) {
    plugin_config_set( 'interwikiConfig', $f_interwiki_config );
}

form_security_purge( 'plugin_mediawikilinks_config_edit' );

print_successful_redirect( plugin_page( 'config', true ) );
