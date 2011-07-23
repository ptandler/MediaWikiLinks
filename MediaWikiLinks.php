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

/**
 * Changelog
 *
 * 0.1: initial version that supports [[...]] links to a wiki
 * 0.2: added support for interwiki links [[wpe:CSCW]]
 *
 */

require_once( config_get( 'class_path' ) . 'MantisFormattingPlugin.class.php' );

class MediaWikiLinksPlugin extends MantisFormattingPlugin {

	/**
	 *  A method that populates the plugin information and minimum requirements.
	 */
	function register( ) {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config';

		$this->version = '0.2';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);

		$this->author = 'Peter Tandler, teambits GmbH';
		$this->contact = 'info@teambits.de';
		$this->url = 'http://www.teambits.de';
	}

	/**
	 * Default plugin configuration.
	 */
	function config() {
		return array(
			'wikiUrl'		=> 'http://en.wikipedia.org/wiki/',
			'interwikiConfig'	=> "wpe:http://en.wikipedia.org/wiki/\nwpd:http://de.wikipedia.org/wiki/",
		);
	}

	/**
	 * Plain text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @param boolean Multiline text
	 * @return multi Array with formatted text and multiline paramater
	 */
	function text( $p_event, $p_string, $p_multiline = true ) {
		return $this->replace_wiki_links($p_string);
	}

	/**
	 * Formatted text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @param boolean Multiline text
	 * @return multi Array with formatted text and multiline paramater
	 */
	function formatted( $p_event, $p_string, $p_multiline = true ) {
		return $this->replace_wiki_links($p_string);
	}

	/**
	 * RSS text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @return string Formatted text
	 */
	function rss( $p_event, $p_string ) {
		return $this->replace_wiki_links($p_string);
	}

	/**
	 * Email text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @return string Formatted text
	 */
	function email( $p_event, $p_string ) {
		return $this->replace_wiki_links($p_string);
	}

	/**
	 * process the $p_string and convert media-wiki-links of the format
	 * [[Some Page]] to a html link
	 * if $p_include_anchor is true, include an <a href="..."> tag,
	 *  otherwise, just insert the URL as text
	 * @param string $p_string
	 * @param bool $p_include_anchor
	 * @return string
	 */
	function replace_wiki_links( $p_string, $p_include_anchor = true  ) {
		$t_url = plugin_config_get( 'wikiUrl' );

		if( $p_include_anchor ) {
			// todo: quote "/" .....
			$t_replace_with = '<a href="' . $t_url . '$1" target="_new">$1</a>\\5';
		} else {
			$t_replace_with = $t_url . '$1';
		}

		// first, look for interwiki links [[key:value]] and replace those
		$t_config = $this->interwikiConfig();
		// look for all keys and replace
		if( $t_config ) {
			$t_replace_with .= ' config: ' . array_keys($t_config) . ' --- ' . $t_config;
		} else {
			$t_replace_with .= ' no config ';
		}

		// now replace wiki links [[...]] by the URL
		$t_result = preg_replace( '/\[\[(.+)\]\]/i', $t_replace_with, $p_string );

		return $t_result;
	}

	/**
	 * parse the interwikiConfig and return it as a map
	 *
	 * @return array
	 */
	function interwikiConfig() {
		$t_lines = preg_split("/\n/", plugin_config_get( 'interwikiConfig' ) );
		
		$t_config = array();
		foreach( $t_lines as $t_line ) {
			$t_kv = split(':', trim($t_line), 1);
			if( count($t_kv) > 1) {
				echo "found ", $t_kv[0], " = ", $t_kv[1];
				$t_config[$t_kv[0]] = $t_kv[1];
			}
		}
		return $t_config;
	}
}
