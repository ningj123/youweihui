<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台内容控制器
 * @author huajie <banhuajie@163.com>
 */
class LineController extends AdminController {
    private $site_id        =   null; //文档分类id
    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu(){
        //获取站点id
        $site_id        =   I('param.site_id', 0, 'intval');
        //获取动态分类
        $site_auth  =   AuthGroupModel::getAuthSiteies(UID); //获取当前用户所有的内容权限节点
        $site_auth  =   $site_auth == null ? array() : $site_auth;
        $site_list  =   C('SITE_LIST');
        if (!IS_ROOT && !in_array($site_id, $site_auth)) {
            $site_id = 0;
        }
        //没有权限的站点则不显示
        $nodes = array();
        foreach ($site_list as $key=>$val){
            if(IS_ROOT || in_array($key, $site_auth)){
                $nodes[$key]['title']   =  $val . '线路';
                $nodes[$key]['url']   =   U('Line/index', array('site_id'=>$key));
                if($site_id && $site_id == $key){
                    $nodes[$key]['current'] = 1;
                }else{
                    $nodes[$key]['current'] = 0;
                }
            }
        }
        if (!IS_ROOT && empty($site_id)) {
            if (count($nodes)) {
                $i = 1;
                foreach ($nodes as $key => $value) {
                    if ($i == 1) {
                        $site_id = $key;
                        $nodes[$key]['current'] = 1;
                        break;
                    }
                    $i++;
                }
            } else {
                $this->redirect('Visa/index');
            }
        }

        // echo '<pre>'; print_r($nodes); echo '</pre>';
        // 扩展菜单
        // $this->assign('_extra_menu', array('旅游线路'=>$nodes));
        $this->assign('nodes', $nodes);
        $this->site_id = $site_id;
        $this->assign('site_id', $site_id);
    }

    /**
     * 线路列表
     * @param integer $cate_id 分类id
     * @param integer $model_id 模型id
     * @param integer $position 推荐标志
     * @param integer $group_id 分组id
     */
    public function index(){
        //获取左边菜单
        $this->getMenu();

        $map['status'] = array('egt', 0);
        $title = I('title');
        if(is_numeric($title)){
            $map['line_id|title'] = array(intval($title),array('like','%'.$title.'%'),'_multi'=>true);
        }else{
            $map['title'] = array('like', '%'.(string)$title.'%');
        }
        $l_type = I('l_type', null);
        if(!is_null($l_type)){
            $map['l_type'] = $l_type;
        }
        $tc_type = I('tc_type', null);
        if(!is_null($tc_type)){
            $map['tc_type'] = $tc_type;
        }

        $list   = $this->lists('Line', $map);

        int_to_string($list);
        // print_r($list);

        $this->assign('_list', $list);
        $this->meta_title = '线路列表';
        $this->display();
    }

    // 更新状态
    public function changeStatus($line_id = 0, $status = 0){
        if (empty($line_id)) {
            $this->error('非法参数');
        }
        $map = array(
            'line_id' => $line_id,
        );
        $data = array(
            'update_time' => NOW_TIME,
            'status' => $status,
        );
        $result = M('Line')->where($map)->save($data);
        if ($result) {
            $this->success('成功');
        } else {
            $this->error('失败');
        }
    }

    /**
     * 线路新增
     */
    public function add(){
        //获取左边菜单
        $this->getMenu();

        $cate_id    =   I('get.cate_id',0);
        $model_id   =   I('get.model_id',0);
		$group_id	=	I('get.group_id','');

        empty($cate_id) && $this->error('参数不能为空！');
        empty($model_id) && $this->error('该分类未绑定模型！');

        //检查该分类是否允许发布
        $allow_publish = check_category($cate_id);
        !$allow_publish && $this->error('该分类不允许发布内容！');

        // 获取当前的模型信息
        $model    =   get_document_model($model_id);

        //处理结果
        $info['pid']            =   $_GET['pid']?$_GET['pid']:0;
        $info['model_id']       =   $model_id;
        $info['category_id']    =   $cate_id;
		$info['group_id']		=	$group_id;

        if($info['pid']){
            // 获取上级文档
            $article            =   M('Document')->field('id,title,type')->find($info['pid']);
            $this->assign('article',$article);
        }

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('info',       $info);
        $this->assign('fields',     $fields);
        $this->assign('type_list',  get_type_bycate($cate_id));
        $this->assign('model',      $model);
        $this->meta_title = '新增'.$model['title'];
        $this->display();
    }

    /**
     * 线路修改
     */
    public function edit(){
        $line_id     =   I('line_id');
        if(empty($line_id)){
            $this->error('参数不能为空！');
        }
        $Line = D('Line');
        if (IS_POST) {
            $result = $Line->update();
            if ($result) {
                $categorys = array_unique((array)I('categorys',0));
                if ($categorys) {
                    // 产品分类
                    $LineType = M('LineType');
                    $LineType->where(array('line_id'=>$line_id))->delete();
                    // 批量添加数据
                    $dataList = array();
                    foreach ($categorys as $key => $value) {
                        $dataList[] = array('line_id'=>$line_id,'type_id'=>$value);
                    }
                    $LineType->addAll($dataList);
                }
                $this->success('成功');
            } else {
                $this->error($Line->getError());
            }
        } else  {
            $this->getMenu();
            // 操作步骤
            $setp = array(
                1 => array('title'=>'线路描述', 'url'=>U('edit', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>1),
                2 => array('title'=>'线路价格', 'url'=>U('edit2', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0),
                3 => array('title'=>'行程内容', 'url'=>U('edit3', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0)
            );
            $this->assign('setp', $setp);
            // 产品分类
            $categorys = D('LineCate')->getTree(0,'id,title,sort,pid,display,status');
            $this->assign('categorys', $categorys);
            // 线路描述
            $data = $Line->where(array('line_id'=>$line_id))->find();
            if ($data) {
                $line_type = M('LineType')->field('type_id')->where(array('line_id'=>$line_id))->select();
                if ($line_type) {
                    $data['line_type'] = implode(',', array_column($line_type, 'type_id'));
                }
                if ($data['images']) {
                    $data['images'] = explode(',', $data['images']);
                }
                $this->assign('data', $data);
            } else {
                $this->error('该线路不存在');
            }
            $this->assign('line_tc', $line_tc);
            $this->assign('line_id', $line_id);
            $this->meta_title   =   '编辑线路1';
            $this->display();
        }
    }

    public function edit2(){
        $line_id     =   I('line_id');
        if(empty($line_id)){
            $this->error('参数不能为空！');
        }
        $Line = M('Line');
        if (IS_POST) {

        } else  {
            $this->getMenu();
            // 操作步骤
            $setp = array(
                1 => array('title'=>'线路描述', 'url'=>U('edit', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0),
                2 => array('title'=>'线路价格', 'url'=>U('edit2', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>1),
                3 => array('title'=>'行程内容', 'url'=>U('edit3', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0)
            );
            $this->assign('setp', $setp);
            // 线路套餐
            $line_tc = M('LineTc')->where(array('line_id'=>$line_id))->select();
            $this->assign('line_tc', $line_tc);
            $this->meta_title   =   '编辑线路2';
            $this->display();
        }
    }

    public function edit3(){
        $line_id     =   I('line_id');
        if(empty($line_id)){
            $this->error('参数不能为空！');
        }
        $Line = M('Line');
        if (IS_POST) {

        } else  {
            $this->getMenu();
            // 操作步骤
            $setp = array(
                1 => array('title'=>'线路描述', 'url'=>U('edit', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0),
                2 => array('title'=>'线路价格', 'url'=>U('edit2', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>0),
                3 => array('title'=>'行程内容', 'url'=>U('edit3', array('site_id'=>$this->site_id,'line_id'=>$line_id)), 'current'=>1)
            );
            $this->assign('setp', $setp);

            $this->meta_title   =   '编辑线路3';
            $this->display();
        }
    }

    /**
     *  套餐修改
     */
    public function tcEdit(){
        $line_id     =   I('line_id');
        $tc_id     =   I('tc_id');
        if(empty($line_id)){
            $this->error('参数不能为空！');
        }
        $Line = M('Line');
        $LineTc = M('LineTc');
        if (IS_POST) {


        } else  {

            // 线路套餐
            $data = $LineTc->where(array('tc_id'=>$tc_id))->find();
            $this->assign('data', $data);
            $this->meta_title   =   '价格方案';
            $this->display();
        }
    }


}
