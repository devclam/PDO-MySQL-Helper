# PDO MySQL Helper

轻量的 MySQL 查询构造工具

## Introduction

现代 PHP 已经有了诸如 Laravel 等高级框架，这些框架极大的方便了大工程的组织和管理，但是简单的项目却很难从中受益。

对于那些只包括几个简单的 PHP 文件，使用页面控制器（Page Controller）的小项目。如果为了执行简单的 CRUD，引入重量级的框架带来的项目复杂度是得不偿失的。但是直接写 PDO 又很啰嗦。

这个助手类就是对 PDO 做了一个简单的封装，来简化编程。



## Usage & Example

```php

$db_conf = array(
    "HOST" => "127.0.0.1",
    "NAME" => "db_name",
    "USER" => "db_user",
    "PWD" => "db_passwd",
    "PORT" => "3306",
    "PREFIX" => "tbl_"
);

$db = db_helper($db_conf);

// select * from tbl_list
$results = $db->table("list")->select()->exec();

// select id, name from tbl_list
$results = $db->table("list")->select("id, name")->exec();

// select id, name from tbl_list limit 10
$results = $db->table("list")->select("id, name")->limit(10)->exec();

// select name from tbl_list where id = 1
$id = 1;
$results = $db->table("list")->select("name")
    ->where("id = :id")->bind(array("id" => $id))->exec();

// insert into tbl_list (id auto increment)
$name = "Clam";
$db->table("list")->insert("(`id`, `name`)")
    ->values("(NULL, :name)")
    ->bind(array("name" => $name))->exec();

// start transaction mode
$db->tx_on();

// update tbl_list where id = $id
$db->table("list")->update("name = :name")->where("id = :id")
    ->bind(array(
        "name" => $name,
        "id" => $id
    ))->exec();

// delete from tbl_list where id = $id
$db->table("list")->delete()->where("id = :id")->bind(array("id" => $id))->exec();

// transaction commit
$db->tx_commit();

```



## References

http://php.net/manual/zh/book.pdo.php

