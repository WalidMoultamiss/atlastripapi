<?php

namespace App\Models;

class Model
{

  public function __construct()
  {
    //
  }

  public static function all()
  {
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));
    $dbh = (new \App\Config\Database())->connect();

    $sth = $dbh->prepare("SELECT * FROM $table;");
    $sth->execute();
    return $sth->fetchAll();
  }


  public static function showAllByquery($q)
  {
    $dbh = (new \App\Config\Database())->connect();

    $sth = $dbh->prepare($q);
    $sth->execute();
    return $sth->fetchAll();
  }

  public static function create(object $qq)
  {
    $dbh = (new \App\Config\Database())->connect();
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

    $columns = implode(',', array_keys((array)$qq));
    $values = implode(',', array_map(function ($val) {
      return is_string($val) ? "'$val'" : $val;
    }, array_values((array)$qq)));
    $query = "INSERT INTO $table ($columns) VALUES ($values);";
    $sth = $dbh->prepare($query);
    $sth->execute();
    return  $dbh->lastInsertId();
  }

  public static function delete(int $id)
  {
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

    $dbh = (new \App\Config\Database())->connect();

    $dbh->prepare("DELETE FROM $table where id = ?;")->execute([$id]);
  }

  public static function find(int $id)
  {
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

    $dbh = (new \App\Config\Database())->connect();

    $sth = $dbh->prepare("SELECT * FROM $table WHERE id = ?;");
    $sth->execute([$id]);
    return $sth->fetchAll();
  }

  public static function update(object $qq, int $id)
  {
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

    $dbh = (new \App\Config\Database())->connect();

    $value = (array) ($qq);

    $values = implode(',', array_map(
      function ($v, $k) {
        $v = is_numeric($v) ? $v : "'$v'";
        return "$k=$v";
      },
      $value,
      array_keys($value)
    ));

    $query = "UPDATE $table SET $values WHERE id = $id;";
    $sth = $dbh->prepare($query);
    $sth->execute();
  }

  public static function findBy($colVal, $colFilter = [])
  {
    $table = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

    $dbh = (new \App\Config\Database())->connect();

    $cols = empty($colFilter) ? "*" : implode(',', $colFilter);
    $condition = substr(array_reduce(array_keys($colVal), function ($acc, $cur) {
      return $acc = $acc . "$cur = ?,";
    }, ""), 0, -1);
    $sth = $dbh->prepare("SELECT $cols FROM $table WHERE $condition;");
    $sth->execute(array_values($colVal));
    return $sth->fetchAll();
  }
}
