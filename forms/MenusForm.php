<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms;

use app\models\Model;

class MenusForm extends Model
{
    public $currentRouteInfo = [];
    public $currentRoute;
    public $id = 0;
    public $isExist = false;

    private $userIdentity;

    /**
     * 有实际页面且不菜单列表中的路由填写在此处
     */
    const existList = [
        'admin/cache/clean',
    ];

    //初始化数据，方便外部调用本类
    public function init()
    {
        $this->userIdentity = \Yii::$app->user->identity->identity;
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getMenus()
    {
        $menus = Menus::getMallMenus();

        // TODO 需要加入插件菜单
        $userPermissions = [];

        // 标识菜单是否显示
        $checkMenus = $this->checkMenus($menus, $userPermissions);

        // 去除不需显示的菜单
        $menus = $this->deleteMenus($checkMenus);

        // 菜单列表
        $newMenus = $this->resetMenus($menus);

        if (!$this->isExist) {
            if (!in_array($this->currentRoute, self::existList)) {
                throw new \Exception('页面路由未正常配置（会导致员工账号无法进入该页面）,请检查');
            }
        }

        return [
            'menus' => $newMenus,
            'currentRouteInfo' => $this->currentRouteInfo,
        ];
    }

    /**
     * 给自定义路由列表 追加ID 及 PID
     * @param array $list 自定义的多维路由数组
     * @param int $id 权限ID
     * @param int $pid 权限PID
     * @return mixed
     */
    private function resetMenus(array $list, &$id = 1, $pid = 0)
    {
        foreach ($list as $key => $item) {
            $list[$key]['id'] = (string)$id;
            $list[$key]['pid'] = (string)$pid;

            // 前端选中的菜单
            if (isset($list[$key]['route']) && $this->currentRoute === $list[$key]['route']) {
                $this->currentRouteInfo = $list[$key];
                $list[$key]['is_active'] = true;
                $this->isExist = true;
            }
            if (isset($list[$key]['action'])) {
                foreach ($list[$key]['action'] as $aItem) {
                    if (isset($aItem['route']) && $aItem['route'] === $this->currentRoute) {
                        $list[$key]['is_active'] = true;
                        $this->isExist = true;
                    }
                }
            }

            if (isset($item['children'])) {
                $id++;
                $list[$key]['children'] = $this->resetMenus($item['children'], $id, $id - 1);
                foreach ($list[$key]['children'] as $child) {
                    if (isset($child['is_active']) && $child['is_active']) {
                        $list[$key]['is_active'] = true;
                    }
                }
            }

            if (isset($item['action'])) {
                $id++;
                $list[$key]['action'] = $this->resetMenus($item['action'], $id, $id - 1);
            }

            !isset($item['children']) && !isset($item['action']) ? $id++ : $id;
        }

        return $list;
    }

    /**
     * 根据权限 标识菜单是否可用
     * 2020年6月30日 16:18:36  开发给数据统计插件菜单使用
     * @param $menus
     * @param $permissions
     * @return mixed
     */
    public function checkMenus($menus, $permissions)
    {
        foreach ($menus as $k => $item) {
            if ($this->userIdentity->is_super_admin === 1) {
                // 超级管理员
                $menus[$k]['is_show'] = true;
            } elseif ($this->userIdentity->is_admin === 1) {
                // 子账号管理员
                $menus[$k]['is_show'] = true;
            } else {
                // 员工账号
                if ($item['route'] && in_array($item['route'], $permissions)) {
                    $menus[$k]['is_show'] = true;
                } else {
                    $menus[$k]['is_show'] = false;
                }
            }

            if (isset($item['children'])) {
                $menus[$k]['children'] = $this->checkMenus($item['children'], $permissions);
                foreach ($menus[$k]['children'] as $i) {
                    if ($i['is_show']) {
                        $menus[$k]['route'] = $i['route'];
                        $menus[$k]['is_show'] = true;
                        break;
                    }
                }
            }
        }

        return $menus;
    }

    /**
     * 根据是否显示标识、去除不显示的菜单.
     * 2020年6月30日 16:18:36  开发给数据统计插件菜单使用
     * @param $menus
     * @return mixed
     */
    public function deleteMenus($menus)
    {
        foreach ($menus as $k => $item) {
            if (isset($item['is_show']) && !$item['is_show']) {
                unset($menus[$k]);
                continue;
            }

            if (isset($item['children'])) {
                $menus[$k]['children'] = $this->deleteMenus($item['children']);
            }
        }
        return array_values($menus);
    }
}
