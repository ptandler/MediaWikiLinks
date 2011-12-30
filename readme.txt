What does the MediaWikiLinks Plugin do?

The plugin allows to create links to media wiki pages (double brackets) [[...]] to be used in mantis issue descriptions.

This way you can use the issue description a little bit more like a wiki page. This is especially handy, if you have linked a mediawiki to your mantis anyway.

V0.3
* now also supports to configure multiple external sources, similar to MediaWiki's interwiki-feature, such as [[wikipedia-en:Mantis Bug Tracker]]
* The configuration URL may contain "*" as a placeholder for linked page identifier.

V0.4
* support to specify the text to be used for the link with "|", e.g. [[wikipedia-en:Mantis Bug Tracker|more about mantis]]