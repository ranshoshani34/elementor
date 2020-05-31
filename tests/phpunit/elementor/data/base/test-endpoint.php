<?php
namespace Elementor\Tests\Phpunit\Elementor\Data\Base;

use Elementor\Data\Manager;
use Elementor\Testing\Elementor_Test_Base;
use Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Simple\Controller as ControllerSimple;
use Exception;

class Test_Endpoint extends Elementor_Test_Base {

	/**
	 * @var \Elementor\Data\Manager
	 */
	protected $manager;

	public function setUp() {
		parent::setUp();

		$this->manager = Manager::instance();
		$this->manager->kill_server();
	}

	public function test_create_simple() {
		$test_controller_instance = new ControllerSimple();
		$this->manager->run_server();

		// Validate `$this->>register()`.
		$this->assertCount( 1, $test_controller_instance->endpoints );
	}

	public function test_create_invalid_controller() {
		$this->expectException( Exception::class );

		new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( null );
	}

	public function test_get_base_route() {
		$controller_instance = new ControllerSimple();
		$this->manager->run_server();

		$endpoint_instance = array_values( $controller_instance->endpoints )[ 0 ];

		$this->assertEquals( '/' . $controller_instance->get_name() . '/' . $endpoint_instance->get_name(), $endpoint_instance->get_base_route() );
	}

	public function test_get_base_route_index_endpoint() {
		$this->manager->run_server();
		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();

		$endpoint_instance = $controller_instance->do_register_endpoint( \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint\Index::class );

		$this->assertEquals( '/' . $controller_instance->get_name() . '/' , $endpoint_instance->get_base_route() );
	}

	public function test_register() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );

		$endpoint_instance->set_test_data( 'get_items', 'valid' );

		// Validate register did 'register_items_route'.
		$data = $this->manager->run_endpoint( $controller_instance->get_name() . '/' . $endpoint_instance->get_name() );

		$this->assertEquals( $data, 'valid' );
	}

	public function test_register_sub_endpoint() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );

		$sub_endpoint_instance = $endpoint_instance->do_register_sub_endpoint( 'test-route/', \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\SubEndpoint::class );
		$sub_endpoint_instance->set_test_data( 'get_items', 'valid' );

		$endpoint = $controller_instance->get_name() .  '/test-route/' . $sub_endpoint_instance->get_name();

		$data = $this->manager->run_endpoint( $endpoint  );

		$this->assertEquals( $data, 'valid' );
	}

	public function test_register_sub_endpoint_invalid_endpoint() {
		$this->expectException( Exception::class );

		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );

		$endpoint_instance->do_register_sub_endpoint( 'test-route/', \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint::class );
	}

	public function test_get_items() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );

		$endpoint_instance->set_test_data( 'get_items', 'valid' );

		$this->assertEquals( 'valid', $endpoint_instance->get_items( null ) );
	}

	public function test_get_items_simulated() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$endpoint_instance = $controller_instance->do_register_endpoint( \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint::class );

		$endpoint_instance->set_test_data( 'get_items', 'valid' );

		$this->assertEquals( 'valid', $this->manager->run_endpoint( trim( $endpoint_instance->get_base_route(), '/' ) ) );
	}

	// TODO: Add test_get_permission_callback(), when get_permission_callback() function is completed.
	// TODO: Add test_create_item(), when create_item() function is completed.
	// TODO: Add test_create_items(), when create_items() function is completed.

	public function test_register_item_route() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$controller_instance->bypass_original_register();

		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );
		$endpoint_instance->register_item_route();

		$except_route = '/' . $controller_instance->get_controller_route() . '/' . $endpoint_instance->get_name() . '/(?P<id>[\w]+)';

		$data = $controller_instance->get_controller_index()->get_data();

		$this->assertArrayHaveKeys( [ $except_route ], $data['routes'] );
	}

	public function test_register_items_route() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$controller_instance->bypass_original_register();

		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );
		$endpoint_instance->register_items_route();

		$data = $controller_instance->get_controller_index()->get_data();
		$except_route = '/' . $controller_instance->get_controller_route() . '/' . $endpoint_instance->get_name();

		$this->assertArrayHaveKeys( [ $except_route ], $data['routes'] );
	}

	public function test_register_route() {
		$this->manager->run_server();

		$controller_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Controller();
		$controller_instance->bypass_original_register();

		$endpoint_instance = new \Elementor\Tests\Phpunit\Elementor\Data\Base\Mock\Template\Endpoint( $controller_instance );
		$endpoint_instance->register_route( '/custom-route' );

		$data = $controller_instance->get_controller_index()->get_data();
		$except_route = '/' . $controller_instance->get_controller_route() . '/' . $endpoint_instance->get_name() . '/custom-route';

		$this->assertArrayHaveKeys( [ $except_route ], $data['routes'] );
	}
}
