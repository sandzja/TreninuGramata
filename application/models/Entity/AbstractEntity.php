<?php

namespace Entity;

abstract class AbstractEntity {
	
	public function populate(array $values) {
		$reflect = new \ReflectionClass($this);
		$properties = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED);
		foreach ($properties as $property) {
			if ($property->isStatic()) {
				continue;
			}
			$methodName = 'set' . ucfirst($property->name);
			if (method_exists($this, $methodName)) {
				if (isset($values[$property->name])) {
					$this->$methodName($values[$property->name]);
				}
			} else {
				if (isset($values[$property->name])) {
					$this->{$property->name} = $values[$property->name];
				}
			}
		}
	}
	
	public function toArray() {
		$array = array ();
		$reflect = new \ReflectionClass($this);
		$properties = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED);
		foreach ($properties as $property) {
			if ($property->isStatic()) {
				continue;
			}
			$methodName = 'get' . ucfirst($property->name);
			if (method_exists($this, $methodName)) {
				$array[$property->name] = $this->$methodName();
			} else {
				$array[$property->name] = $this->{$property->name};
			}
		}
		
		return $array;
	}

	public static function getClassName() {
		return get_called_class();
	}
}