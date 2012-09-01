<?php

/**
 * smCore Menu Class
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

use ArrayAccess, ArrayIterator, IteratorAggregate;

class Menu implements ArrayAccess, IteratorAggregate
{
	protected $_children = array();

	public function __construct()
	{
	}

	public function setActive()
	{
		$active = func_get_args();
		$current = array_shift($active);

		if (!empty($current) && isset($this->_children[$current]))
		{
			$this->_children[$current]->setActive(true, $active);
		}

		return $this;
	}

	public function addItem(MenuItem $item)
	{
		$this->_children[$item->getName()] = $item;
	}

	public function removeItem($name)
	{
		unset($this->_children[$name]);

		return $this;
	}

	public function hasChildren()
	{
		return !empty($this->_children);
	}

	public function offsetGet($offset)
	{
		if (isset($this->_children[$offset]))
		{
			return $this->_children[$offset];
		}

		return null;
	}

	public function offsetSet($offset, $value)
	{
		throw new Exception('Please use MenuItem::addItem to add items to the menu.');
	}

	public function offsetUnset($offset)
	{
		unset($this->_children[$offset]);
	}

	public function offsetExists($offset)
	{
		return isset($this->_children[$offset]);
	}

	public function getIterator()
	{
		$items = $this->_children;

		// Copy the array because the keys will be overwritten
		usort($items, function($a, $b)
		{
			if ($a->getOrder() === $b->getOrder())
			{
				return 0;
			}

			return $a->getOrder() > $b->getOrder() ? 1 : -1;
		});

		return new ArrayIterator($items);
	}
}