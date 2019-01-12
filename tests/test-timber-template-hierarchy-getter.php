<?php

class TestTimberTemplateHierarchyGetter extends Timber_UnitTestCase {

	function testGetPageTemplateHierarchy() {
		$page_id = $this->factory->post->create( array( 'post_title' => 'Sample Name', 'post_type' => 'page' ) );
		add_post_meta( $page_id, '_wp_page_template', 'custom-template-from-page.php', true );
		add_filter( "page_template_hierarchy", function( $templates ) {
			array_unshift( $templates, 'custom-template-from-filter.php' );
			return $templates;
		} );
		$this->go_to( home_url( "/?page_id={$page_id}" ) );
		$template_hierarchy = Timber::get_template_hierarchy();
		$this->assertEquals( 'custom-template-from-filter.twig', $template_hierarchy[0] );
		$this->assertEquals( 'custom-template-from-page.twig', $template_hierarchy[1] );
		$this->assertEquals( 'page-sample-name.twig', $template_hierarchy[2] );
		$this->assertEquals( "page-{$page_id}.twig", $template_hierarchy[3] );
		$this->assertEquals( 'page.twig', $template_hierarchy[4] );
		$this->assertEquals( 'singular.twig', $template_hierarchy[5] );
		$this->assertEquals( 'index.twig', $template_hierarchy[6] );
		$this->assertEquals( 7, count( $template_hierarchy ) );
	}

	function testGet404TemplateHierarchy() {
		$this->go_to( home_url( '/?page_id=9999' ) );
		$template_hierarchy = Timber::get_template_hierarchy();
		$this->assertEquals( '404.twig', $template_hierarchy[0] );
		$this->assertEquals( 'index.twig', $template_hierarchy[1] );
		$this->assertEquals( 2, count( $template_hierarchy ) );
	}

	function testGetDateArchiveTemplateHierarchy() {
		$this->factory->post->create( array( 'post_title' => 'Sample Post', 'post_date' => '2000-01-30' ) );
		$this->go_to( home_url( '/?year=2000' ) );
		$template_hierarchy = Timber::get_template_hierarchy();
		$this->assertEquals( 'date.twig', $template_hierarchy[0] );
		$this->assertEquals( 'archive.twig', $template_hierarchy[1] );
		$this->assertEquals( 'index.twig', $template_hierarchy[2] );
		$this->assertEquals( 3, count( $template_hierarchy ) );
	}

	function testGetTermTemplateHierarchy() {
		register_taxonomy( 'genre', 'post' );
		$category_id = $this->factory->term->create( array( 'taxonomy' => 'genre', 'name' => 'Jazz' ) );
		$cat = new TimberTerm( $category_id );
		$this->go_to( $cat->path() );
		$template_hierarchy = Timber::get_template_hierarchy();
		$this->assertEquals( 'taxonomy-genre-jazz.twig', $template_hierarchy[0] );
		$this->assertEquals( 'taxonomy-genre.twig', $template_hierarchy[1] );
		$this->assertEquals( 'taxonomy.twig', $template_hierarchy[2] );
		$this->assertEquals( 'archive.twig', $template_hierarchy[3] );
		$this->assertEquals( 'index.twig', $template_hierarchy[4] );
		$this->assertEquals( 5, count( $template_hierarchy ) );
	}

}
