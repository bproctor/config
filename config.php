<?php

/**
 *
 */
abstract class Config
{

	static $path;
	static $vars = [];

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * <code>
	 *		// Get the $array['user']['name'] value from the array
	 *		$name = static::arrayGet($array, 'user.name');
	 *
	 *		// Return a default from if the specified item doesn't exist
	 *		$name = static::arrayGet($array, 'user.name', 'Taylor');
	 * </code>
	 *
	 * This function stolen from Laravel
	 * https://github.com/laravel/laravel/blob/master/laravel/helpers.php
	 *
	 * @access protected 
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	protected static function arrayGet($array, $key, $default = null)
	{
		if (is_null($key)) return $array;

		// To retrieve the array item using dot syntax, we'll iterate through
		// each segment in the key and look for that value. If it exists, we
		// will return it, otherwise we will set the depth of the array and
		// look for the next segment.
		foreach (explode('.', $key) as $segment) {
			if ( ! is_array($array) or ! array_key_exists($segment, $array)) {
				return (is_callable($default) and ! is_string($default)) ?
					call_user_func($default) : $default;
			}
			$array = $array[$segment];
		}
		return $array;
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * <code>
	 *		// Set the $array['user']['name'] value on the array
	 *		static::arraySet($array, 'user.name', 'Taylor');
	 *
	 *		// Set the $array['user']['name']['first'] value on the array
	 *		static::arraySet($array, 'user.name.first', 'Michael');
	 * </code>
	 *
	 * This function stolen from Laravel
	 * https://github.com/laravel/laravel/blob/master/laravel/helpers.php
	 *
	 * @access protected
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	protected static function arraySet(&$array, $key, $value)
	{
		if (is_null($key)) return $array = $value;
		$keys = explode('.', $key);

		// This loop allows us to dig down into the array to a dynamic depth by
		// setting the array value for each level that we dig into. Once there
		// is one key left, we can fall out of the loop and set the value as
		// we should be at the proper depth.
		while (count($keys) > 1) {
			$key = array_shift($keys);

			// If the key doesn't exist at this depth, we will just create an
			// empty array to hold the next value, allowing us to create the
			// arrays to hold the final value.
			if ( ! isset($array[$key]) or ! is_array($array[$key])) {
				$array[$key] = [];
			}
			$array =& $array[$key];
		}
		$array[array_shift($keys)] = $value;
	}

	/**
	 * Set the path to the config files
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public static function init($path = __DIR__)
	{
		return static::$path = realpath($path) . '/';
	}

	/**
	 * Load a config file
	 *
	 * @access public
	 * @param string $filename
	 * @param string|null $group
	 * @return boolean
	 */
	public static function load($filename, $group = null)
	{
		if (file_exists(static::$path . $filename)) {
			$data = include static::$path . $filename;
			if ($group === null) {
				static::$vars = array_merge(static::$vars, $data);
			} else {
				static::$vars[$group] = $data;
			}
		}
		return true;
	}

	/**
	 * Get a config variable
	 *
	 * @access public
	 * @param string $key
	 * @param string|null $default
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		return static::arrayGet(static::$vars, $key, $default);
	}

	/**
	 * Set a config variable
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public static function set($key, $value)
	{
		return static::arraySet(static::$vars, $key, $value);
	}

}
