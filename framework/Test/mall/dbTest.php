<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * db test case.
 */
class dbTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var db
	 */
	private $db;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated dbTest::setUp()
		
		//$this->db = new db(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated dbTest::tearDown()
		//$this->db = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
}

