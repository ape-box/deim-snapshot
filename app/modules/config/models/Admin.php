<?php

class config_models_Admin
{
	public function save(array $values)
	{
		foreach ($values as $section => $values)
			if (is_array($values))
				foreach ($values as $name => $value)
					if (preg_match('/^[a-z]+[_]{1}[a-z_]+$/', $name))
					{
						$name = preg_replace('/^([a-z]+)[_]{1}([a-z_]+)$/', '$1/$2', $name);
						$name = str_replace('__', '_', $name);
						$name = "$section/$name";
						config_Registry::set($name, $value);
					}
					else throw new Exception('Invalid config param');
		return $this;
	}
}