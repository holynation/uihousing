<?php
/**
 * This is a trait helper class for crud, such that we
 */
namespace App\Models;

trait CrudInfo{

	public function totalEntityCount(string $tablename,string $whereClause='')
	{
		$tablename = strtolower($tablename);
		$query = "SELECT count(*) as total from $tablename $whereClause";
		$result = $this->query($query);
		return ($result) ? $result[0]['total'] : 0;
	}

	public function totalEntitySum(string $tablename,string $column,string $whereClause='')
	{
		$tablename = strtolower($tablename);
		$query = "SELECT sum($column) as total from $tablename $whereClause";
		$result = $this->query($query);
		return ($result) ? $result[0]['total'] : 0;
	}
}