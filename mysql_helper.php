<?php

//implementation

function db_helper($conf)
{
	return new MysqlHelper($conf);
}

class MysqlHelper
{
	function __construct($conf)
	{
		$this->conf_ = $conf;
		$this->pdo_ = null;
		$this->table_ = "";
		$this->item_ = "*";
		$this->cond_ = "";
		$this->param_ = "";
		$this->mode_ = "";
		$this->limit_ = "";
		$this->value_ = "";
		$this->transaction_mode_ = false;
		
		try
		{
			$this->pdo_ = new PDO('mysql:host='.$this->conf_["HOST"].';dbname='.$this->conf_["NAME"].';port='.$this->conf_["PORT"].';', $this->conf_["USER"], $this->conf_["PWD"]);
			$this->pdo_->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo_->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}
		catch (Exception $e)
		{
			die("Unable to connect: " . $e->getMessage());
		}
	}
	
	function __destruct()
	{
		$this->pdo_ = null;
	}
	
	function tx_on()
	{
		$this->transaction_mode_ = true;
		$this->pdo_->beginTransaction();
	}
	
	function tx_commit()
	{
		if ($this->transaction_mode_)
		{
			$this->pdo_->commit();
		}
		else
		{
			die("calling commit() when transaction mode OFF.");
		}
	}
	
	function table($tbl_name)
	{
		$this->table_ = $tbl_name;
		return $this;
	}
	
	function select($item = "*")
	{
		$this->mode_ = "SELECT";
		$this->item_ = $item;
		return $this;
	}
	
	function insert($item)
	{
		$this->mode_ = "INSERT";
		$this->item_ = $item;
		return $this;
	}
	
	function update($item)
	{
		$this->mode_ = "UPDATE";
		$this->item_ = $item;
		return $this;
	}
	
	function delete()
	{
		$this->mode_ = "DELETE";
		return $this;
	}
	
	function values($val)
	{
		$this->value_ = $val;
		return $this;
	}
	
	function limit($num)
	{
		$this->limit_ = $num;
		return $this;
	}
	
	function where($cond)
	{
		$this->cond_ = $cond;
		return $this;
	}
	
	function bind($param)
	{
		$this->param_ = $param;
		return $this;
	}
	
	function exec()
	{
		if ($this->table_ == "")
		{
			die("TABLE NAME not specified.\n");
		}
		
		if ($this->mode_ == "")
		{
			die("DATABASE ACCESS MODE not specified.\n");
		}
		else if ($this->mode_ == "SELECT")
		{
			$sql = "SELECT ".$this->item_." FROM ".$this->conf_["PREFIX"].$this->table_." ";
			if ($this->cond_ != "")
			{
				$sql .= "WHERE ".$this->cond_." ";
			}
			if ($this->limit_ != "")
			{
				$sql .= "LIMIT ".$this->limit_." ";
			}
			try
			{
				//echo($sql);
				$query = $this->pdo_->prepare($sql);
				if ($this->param_ != "")
				{
					$query->execute($this->param_);
				}
				else
				{
					$query->execute();
				}
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (PDOException $e)
			{
				if ($this->transaction_mode_)
				{
					$this->pdo_->rollBack();
				}
				echo("DATABASE ERROR .".$e->getMessage());
			}
		}
		else if ($this->mode_ == "INSERT")
		{
			$sql = "INSERT INTO ".$this->conf_["PREFIX"].$this->table_." ".$this->item_." "." VALUES ".$this->value_." ";
			try
			{
				//echo($sql);
				$query = $this->pdo_->prepare($sql);
				if ($this->param_ != "")
				{
					$query->execute($this->param_);
				}
				else
				{
					$query->execute();
				}
			}
			catch (PDOException $e)
			{
				if ($this->transaction_mode_)
				{
					$this->pdo_->rollBack();
				}
				echo("DATABASE ERROR .".$e->getMessage());
			}
		}
		else if ($this->mode_ == "UPDATE")
		{
			$sql = "UPDATE ".$this->conf_["PREFIX"].$this->table_." "." "." SET ".$this->item_." ";
			if ($this->cond_ != "")
			{
				$sql .= "WHERE ".$this->cond_." ";
			}
			try
			{
				//echo($sql);
				//die();
				$query = $this->pdo_->prepare($sql);
				if ($this->param_ != "")
				{
					$query->execute($this->param_);
				}
				else
				{
					$query->execute();
				}
			}
			catch (PDOException $e)
			{
				if ($this->transaction_mode_)
				{
					$this->pdo_->rollBack();
				}
				echo("DATABASE ERROR .".$e->getMessage());
			}
		}
		else if ($this->mode_ == "DELETE")
		{
			$sql = "DELETE FROM ".$this->conf_["PREFIX"].$this->table_." ";
			$sql .= "WHERE ".$this->cond_." ";
			try
			{
				//echo($sql);
				$query = $this->pdo_->prepare($sql);
				if ($this->param_ != "")
				{
					$query->execute($this->param_);
				}
				else
				{
					$query->execute();
				}
			}
			catch (PDOException $e)
			{
				if ($this->transaction_mode_)
				{
					$this->pdo_->rollBack();
				}
				echo("DATABASE ERROR .".$e->getMessage());
			}
		}
		else
		{
			die("unexpected DATABASE ACCESS MODE.\n");
		}
	}
	
	private $conf_;
	private $pdo_;
	private $table_;
	private $item_;
	private $cond_;
	private $param_;
	private $mode_;
	private $limit_;
	private $value_;
	private $transaction_mode_;
}
