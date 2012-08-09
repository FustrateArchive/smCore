<?php

/**
 * smCore Router Class
 *
 * @package smCore
 * @author smCore Dev Team
 * @license MPL 1.1
 * @version 1.0 Alpha
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1
 * (the "License"); you may not use this package except in compliance with the
 * License. You may obtain a copy of the License at http://www.mozilla.org/MPL/
 *
 * The Original Code is smCore.
 *
 * The Initial Developer of the Original Code is the smCore project.
 *
 * Portions created by the Initial Developer are Copyright (C) 2011
 * the Initial Developer. All Rights Reserved.
 */

namespace smCore;

class Router
{
	protected $_routes = null;
	protected $_matches = array();

	protected $_disallowed_methods = array();

	public function __construct()
	{
	}

	/**
	 * Try to match a path to one of our routes. A literal match is tried first, after which we attempt
	 * to find a regex that fits.
	 *
	 * @param string $path The path to find a route for.
	 *
	 * @return mixed An array of route data if one was found, false otherwise.
	 */
	public function match($path)
	{
		// Normalize the path - we don't want to miss a match because of a stray slash.
		$path = trim($path, '/?');

		if (empty($path))
		{
			if (!empty($this->_routes['default']))
			{
				return end($this->_routes['default']);
			}
		}
		else if (array_key_exists($path, $this->_routes['literal']))
		{
			return $this->_routes['literal'][$path];
		}
		else if (!empty($this->_routes['regex']))
		{
			foreach ($this->_routes['regex'] as $regex => $route)
			{
				if (preg_match('/^' . $regex . '$/i', $path, $matches))
				{
					$this->_matches = $matches;

					return $route;
				}
			}
		}

		return 404;
	}

	public function getMatch($name)
	{
		if (array_key_exists($name, $this->_matches))
		{
			return $this->_matches[$name];
		}

		return null;
	}

	/**
<<<<<<< HEAD
=======
	 * Load the routes from the cache, or load each module's config data and feed the routes to _addRoutes().
	 */
	protected function _loadRoutes()
	{
		$this->_routes = Application::get('cache')->load('core_routes');
		if (is_array($this->_routes))
		{
			return;
		}

		// We don't want to do a regex match if we don't have to
		$this->_routes = array(
			'default' => array(),
			'literal' => array(),
			'regex' => array(),
		);

		$modules = Application::get('modules');
		$identifiers = $modules->getIdentifiers();

		foreach ($identifiers as $id)
		{
			$config = $modules->getModuleConfig($id);

			// Doesn't have any routes… which is weird, but okay.
			if (empty($config['routes']))
			{
				continue;
			}

			self::_addRoutes($config['routes'], $id);
		}

		// @todo: Use app constants so we don't have to remember different tags. Application::DEPENDENCY_MODULE_REGISTRY = '...';
		Application::get('cache')->save('core_routes', $this->_routes, array('dependency_module_registry'));
	}

	/**
>>>>>>> 5f3db7b2e03a35927aa2969a3296a479a4b9a2af
	 * Test each route's "match" value to see if it's a literal or a regex, and put them in
	 * the appropriate category.
	 *
	 * @param array  $routes     An array of config route data
	 * @param string $identifier The unique identifier for these routes
	 */
	public function addRoutes(array $routes, $identifier)
	{
		/* @todo: use these?

		// You're not allowed to give your methods the names of generic Controller class methods.
		if ($this->_disallowed_methods === null)
		{
			$this->_disallowed_methods = get_class_methods('\smCore\Module\Controller');
		}

		array(
			// Reserved words, PHP will choke on them anyways
			'__CLASS__',
			'__DIR__',
			'__FILE__',
			'__FUNCTION__',
			'__halt_compiler',
			'__LINE__',
			'__METHOD__',
			'__NAMESPACE__',
			'abstract',
			'and',
			'array',
			'as',
			'break',
			'case',
			'catch',
			'class',
			'clone',
			'const',
			'continue',
			'declare',
			'default',
			'die',
			'do',
			'echo',
			'else',
			'elseif',
			'empty',
			'enddeclare',
			'endfor',
			'endforeach',
			'endif',
			'endswitch',
			'endwhile',
			'eval',
			'exit',
			'extends',
			'final',
			'for',
			'foreach',
			'function',
			'global',
			'goto',
			'if',
			'implements',
			'include_once',
			'include',
			'instanceof',
			'interface',
			'isset',
			'list',
			'namespace',
			'new',
			'or',
			'print',
			'private',
			'protected',
			'public',
			'require_once',
			'require',
			'return',
			'static',
			'switch',
			'throw',
			'try',
			'unset',
			'use',
			'var',
			'while',
			'xor',
			// Magic method names, don't allow these as route methods
			'__construct',
			'__destruct',
			'__call',
			'__callStatic',
			'__sleep',
			'__wakeup',
			'__get',
			'__set',
			'__isset',
			'__unset',
			'__toString',
			'__invoke',
			'__set_state',
			'__clone',
		);
		*/

		foreach ($routes as $name => $route)
		{
			// You can add quick return codes via ->addRoutes(array('match/this(.*)' => 403))
			if (is_int($route))
			{
				$route = array(
					'match' => $name,
					'controller' => null,
					'method' => (int) $route,
				);
			}
			else
			{
				if (empty($route['controller']))
				{
					continue;
				}

				if (empty($route['method']))
				{
					$route['method'] = $name;
				}

				// @todo: throw an Exception
				if (in_array($route['method'], $this->_disallowed_methods))
				{
					continue;
				}
			}

			if (!is_array($route['match']))
			{
				$route['match'] = array($route['match']);
			}

			// @todo: clean the regexes?
			foreach ($route['match'] as $match)
			{
				$type = 'literal';
				$match = trim($match, '/ ');

				// If either of these characters is in the route, it has to be a regex
				if (false !== strpos($match, '(') || false !== strpos($match, '['))
				{
					$type = 'regex';
					$match = str_replace('/', '\\/', $match);

					// Test for a valid regex... @todo: throw an Exception?
					if (false === preg_match('/' . $match . '/', ''))
					{
						continue;
					}
				}
				else if (false !== strpos($match, ':'))
				{
					$type = 'regex';
					$match = preg_replace('/:([^\/]+)/', '(?<$1>[^/]+)', $match);
					$match = str_replace('/', '\\/', $match);
				}
				else if (empty($match) && 0 === strlen($match))
				{
					$type = 'default';
				}

				$this->_routes[$type][$match] = array(
					'module' => $identifier,
					'controller' => $route['controller'],
					'method' => $route['method'],
				);
			}
		}
	}
}