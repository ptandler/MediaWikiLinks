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
#
# $Id: MediaWikiLinks.php 6033 2011-12-30 12:00:32Z tandler $
#

/**
 * Changelog
 *
 * 0.1: initial version that supports [[...]] links to a wiki
 * 0.2: added support for interwiki links [[wpe:CSCW]]
 * 0.3: support for interwiki links is now implemented
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

		$this->version = '0.3';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);

		$this->author = 'Peter Tandler, teambits GmbH';
		$this->contact = 'info@teambits.de';
		$this->url = 'http://www.teambits.de';
		
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			exit('PHP 5.3.0 is required for this plugin (uses anonymous function)');
		}
	}

	/**
	 * Default plugin configuration.
	 */
	function config() {
		return array(
			'wikiUrl'		=> 'http://en.wikipedia.org/wiki/',
			'interwikiConfig'	=> "wikipedia-en:http://en.wikipedia.org/wiki/\nwikipedia-de:http://de.wikipedia.org/wiki/Spezial:Suche?search=*&go=Artikel",
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
		$t_default_url = plugin_config_get( 'wikiUrl' );

		// look for interwiki links [[key:value]]
		$t_interwiki = $this->interwikiConfig();

		// now replace wiki links [[...]] by the URL
		$t_result = preg_replace_callback(
			'/\[\[(.+)\]\]/i',
			function ($match) use ($t_default_url, $t_interwiki, $p_include_anchor)  {
				$t_text = $match[1];
				$t_id = $match[1];
				$t_url = $t_default_url;
				
				// check interwiki prefix
				$t_kv = explode(':', $t_id, 2);
				if( count($t_kv) > 1 && $t_interwiki[$t_kv[0]] ) {
					// it has a "interwiki:" prefix -> use replacement from interwiki table
					$t_url = $t_interwiki[$t_kv[0]];
					$t_id = $t_kv[1];
					// keep the prefix in the text?
				}
				
				// format url
				$t_urlplaceholder = explode('*', $t_url, 2);
				if( count($t_urlplaceholder) > 1 ) {
					$t_url = $t_urlplaceholder[0] . $t_id . $t_urlplaceholder[1];
				} else {
					$t_url .= $t_id;
				}
				
				if( $p_include_anchor ) {
					return '<a href="' . $t_url . '" target="_new">' . $t_text. '</a>';
				} else {
					return $t_url;
				}
			}, $p_string );

		return $t_result;
	}

	/**
	 * parse the interwikiConfig and return it as a map
	 *
	 * @return array
	 */
	function interwikiConfig() {
		$t_lines = explode("\n", plugin_config_get('interwikiConfig'));
		
		$t_config = array();
		foreach( $t_lines as $t_line ) {
			//array_push($t_config, $t_line);
			$t_kv = explode(':', trim($t_line), 2);
			//array_push($t_config, "(((" . implode(",", array_keys($t_kv)) . ")))");
			if( count($t_kv) > 1) {
				$t_config[$t_kv[0]] = $t_kv[1];
			}
		}
		return $t_config;
	}
}
