<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Logic;

/**
 * Description of NestedSetsMysql
 *
 * @author qingf
 */
class NestedSetsMysqlLogic implements DbMysql{
    
    public function connect() {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function disconnect() {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function free($result) {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAll($sql, array $args = array()) {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAssoc($sql, array $args = array()) {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function getCol($sql, array $args = array()) {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

    public function getOne($sql, array $args = array()) {
        //获取实参列表
        $params = func_get_args();
        //获取sql语句结构
        $sql = array_shift($params);
        //将占位符拆分成数组
        $sqls = preg_split('/\?[NTF]/', $sql);
        //删除最后一个空字符串元素
        array_pop($sqls);
        $sql = '';
        //拼凑sql语句
        foreach($sqls as $key=>$value){
            $sql .= $value . $params[$key];
        }
        //执行查询操作
        $rows = M()->query($sql);
        //返回第一行结果
        return array_shift(array_shift($rows));
    }

    /**
     * 获取一行记录,使用关联数组的方式
     * @param string $sql
     * @param array $args
     * @return array
     */
    public function getRow($sql, array $args = array()) {
        //获取实参列表
        $params = func_get_args();
        //获取sql语句结构
        $sql = array_shift($params);
        //将占位符拆分成数组
        $sqls = preg_split('/\?[NTF]/', $sql);
        //删除最后一个空字符串元素
        array_pop($sqls);
        $sql = '';
        //拼凑sql语句
        foreach($sqls as $key=>$value){
            $sql .= $value . $params[$key];
        }
        //执行查询操作
        $rows = M()->query($sql);
        //返回第一行结果
        return array_shift($rows);
    }

    /**
     * 插入一条新纪录,并返回自增的id
     * @param type $sql
     * @param array $args
     * @return integer|boolean
     */
    public function insert($sql, array $args = array()) {
        //获取实参列表.
        $params = func_get_args();
        //获取sql语句结构.
        $sql = array_shift($params);
        //获取表名
        $table_name = array_shift($params);
        //获取字段名和字段值列表
        $args = array_shift($params);
        //填入表名
        $sql = str_replace('?T', '`'.$table_name.'`', $sql);
        //用于保存每个字段名和字段值子语句字符串
        $tmp_sql=[];
        foreach($args as $key=>$value){
            $tmp_sql[] = '`'.$key.'`="' . $value . '"'; 
        }
        $tmp_sql = implode(',', $tmp_sql);
        //填入字段名字段值
        $sql = str_replace('?%', $tmp_sql, $sql);
        if(M()->execute($sql) === false){
            return false;
        } else{
            return M()->getLastInsID();
        }
    }

    /**
     * 执行一个sql语句,返回结果.
     * @param type $sql
     * @param array $args
     * @return integer
     */
    public function query($sql, array $args = array()) {
        //获取实参列表
        $params = func_get_args();
        //获取sql语句结构
        $sql = array_shift($params);
        //使用占位符切割字符串成数组
        $sqls = preg_split('/\?[NTF]/', $sql);
        //删除最后一个空元素
        array_pop($sqls);
        $sql = '';
        foreach($sqls as $key=>$value){
            $sql .= $value . $params[$key];
        }
        //获取执行结果,语句是一个update语句
        return M()->execute($sql);
    }

    public function update($sql, array $args = array()) {
        echo __METHOD__ .'<br />';
        dump(func_get_args());
        echo '<hr />';
    }

}
