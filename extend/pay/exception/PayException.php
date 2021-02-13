<?php
namespace pay\exception;

class PayException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}