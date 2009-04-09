<?php
/*
 * this is an example of how to use the Query object 
 */
require_once('autoloader.php');
//NewDao::connect('mysql','localhost','root','pass','tree-forum');

$query = NewDao::getGenerator();

/*
 * ====================================
 * adding a SELECT statement
 * ====================================
 * can be done multiple times with multiple tables:
 */
$query->addSelect('table1',array()); #`table1`.*

/* using aliases: */
$query->addSelect('table2',array('id'=>'t2_id')); #`table2`.`id` as `t2_id`

/* using MYSQL functions:*/ 
$query->addSelectFunction('LCASE','lcased_name','table2','name') ;
#LCASE(`table2`.`name`) as `lcased_name`

/* using functions with passed arguments: */
$query->addSelectFunction(
	array('SUBSTRING','0,10'),
	'substr_name',
	'table2',
	'name'
);#SUBSTRING(`table2`.`name`,1,10) as `substr_name`

/*
 * ==============
 *  inner joins
 * ==============
 */
$query->addInnerJoin(array('table1'=>'id'),array('table2'=>'id'));
# Inner Join `table2` ON `table1`.`id` = `table2`.`id`

/*
 * ================
 * 	condition sets
 * ================
 	 condition sets are guled with AND, and separated with OR
  	 conditions are created thus:
 */
  
 $set1[] = $query->createCondition('table1','id','IN',array(1,2,3));
 # (`table1`.`id` IN (1,2,3))
 $set2[] = $query->createCondition('table1','name','=','Arieh');
 # (table1.name = 'Arieh')
 $set2[] = $query->createCondition('table1','name','LIKE','%arieh%','LCASE');
 # (LCASE(table1.name) LIKE '%arieh%')
 
 /* 
  we can either pass condition sets as arrays, or as lists of conditions.
  if passed by arrays (as in this example) each array will be passed as a separate
  condition set. 
  */
  
  $query->addConditionSet($set1,$set2);
  
echo $query->generate();
