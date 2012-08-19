<?php

require 'config.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{

	public function testGetSet()
	{
		$this->assertEquals(Config::init(__DIR__), realpath(__DIR__).'/');
		Config::set('users.bob', 1);
		$this->assertEquals(Config::get('users.bob'), 1);
		Config::set('users.sally', 2);
		$this->assertEquals(Config::get('users.sally'), 2);
		$users = Config::get('users');
		$this->assertCount(2, $users);
		$this->assertArrayHasKey('bob', $users);
		$this->assertArrayHasKey('sally', $users);
	}

}
