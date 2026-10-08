<?php
/**
 * Warnings in the block editor while an editor writes a post or page: images without alt text, a Heading 1, a heading
 * level that is skipped and link texts that say nothing ("read more"). PHP admin notices are hidden in the block
 * editor, so a small inline script reads the content and shows editor notices that update as the text changes. The
 * same rules as chargenet_images_without_alt() and chargenet_content_problems() (inc/blog.php, tested by test-blog).
 * Messages come from PHP, so they follow the editor's language.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the check script to the block editor.
 */
function chargenet_editor_checks(): void {
	$messages = array(
		/* translators: %d: number of images. */
		'alt1' => sprintf( _n( '%d image in this text has no alt text. Add a short description of the image, or choose "decorative" if it only decorates.', '%d images in this text have no alt text. Add a short description of each image, or choose "decorative" if it only decorates.', 1, 'chargenet' ), 1 ),
		/* translators: %d: number of images. */
		'alt2' => _n( '%d image in this text has no alt text. Add a short description of the image, or choose "decorative" if it only decorates.', '%d images in this text have no alt text. Add a short description of each image, or choose "decorative" if it only decorates.', 2, 'chargenet' ),
		'h1'   => __( 'The text contains a Heading 1. The page title already is the h1: use Heading 2 and lower.', 'chargenet' ),
		'skip' => __( 'A heading skips a level (for example Heading 2 followed by Heading 4). Keep the levels in order.', 'chargenet' ),
		'link' => __( 'A link says only "here" or "read more". Use words that describe where the link goes.', 'chargenet' ),
	);
	$script   = 'window.chargenetEditorChecks=' . wp_json_encode( $messages ) . ';' . <<<'JS'
( function ( wp, messages ) {
	var data = wp.data;
	var timer;
	var vague = /^(click here|here|read more|more|lees meer|klik hier|hier|meer)$/i;
	function problems( html ) {
		var found = { alt: 0, h1: 0, skip: 0, link: 0 };
		var previous = 1;
		var doc = new DOMParser().parseFromString( html, 'text/html' );
		doc.querySelectorAll( 'img' ).forEach( function ( img ) {
			if ( ! img.getAttribute( 'alt' ) ) {
				found.alt++;
			}
		} );
		doc.querySelectorAll( 'h1,h2,h3,h4,h5,h6' ).forEach( function ( heading ) {
			var level = Number( heading.tagName.charAt( 1 ) );
			if ( 1 === level ) {
				found.h1++;
			} else if ( level > previous + 1 ) {
				found.skip++;
			}
			previous = Math.max( 2, level );
		} );
		doc.querySelectorAll( 'a' ).forEach( function ( link ) {
			if ( vague.test( link.textContent.trim() ) ) {
				found.link++;
			}
		} );
		return found;
	}
	function show( key, text ) {
		var id = 'chargenet-check-' + key;
		var notices = data.dispatch( 'core/notices' );
		if ( text ) {
			notices.createNotice( 'warning', text, { id: id, isDismissible: false } );
		} else {
			notices.removeNotice( id );
		}
	}
	function check() {
		var editor = data.select( 'core/editor' );
		if ( ! editor || -1 === [ 'post', 'page' ].indexOf( editor.getCurrentPostType() ) ) {
			return;
		}
		var found = problems( editor.getEditedPostContent() );
		show( 'alt', found.alt ? ( 1 === found.alt ? messages.alt1 : messages.alt2 ).replace( '%d', found.alt ) : '' );
		show( 'h1', found.h1 ? messages.h1 : '' );
		show( 'skip', found.skip ? messages.skip : '' );
		show( 'link', found.link ? messages.link : '' );
	}
	var last;
	data.subscribe( function () {
		var editor = data.select( 'core/editor' );
		var content = editor && editor.getEditedPostContent ? editor.getEditedPostContent() : undefined;
		if ( undefined === content || content === last ) {
			return;
		}
		last = content;
		clearTimeout( timer );
		timer = setTimeout( check, 400 );
	} );
} )( window.wp, window.chargenetEditorChecks );
JS;
	wp_add_inline_script( 'wp-edit-post', $script );
}
add_action( 'enqueue_block_editor_assets', 'chargenet_editor_checks' );
