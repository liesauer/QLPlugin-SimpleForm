<?php

namespace liesauer\QLPlugin;

use QL\Contracts\PluginContract;
use QL\Ext\AbsoluteUrl;
use QL\QueryList;

class SimpleForm implements PluginContract
{
    public static function install(QueryList $querylist, ...$opts)
    {
        $querylist->use(AbsoluteUrl::class);
        $querylist->bind('simpleForm', function ($url, $req_params = [], $post_params = [], ...$args) {
            $req_params['method']  = strtolower($req_params['method'] ?? 'get');
            $post_params['method'] = strtolower($post_params['method'] ?? 'get');
            $form                  = ($this->$req_params['method'])($url, $req_params['param'], $req_params['setting'])->find('form');
            // $inputs    = $form->find('input[name]');
            $action    = $form->attr('action');
            $formDatas = $form->serializeArray();
            $postDatas = [];
            foreach ($formDatas as $formData) {
                if (isset($post_params['param'][$formData['name']])) {
                    $postDatas[$formData['name']] = $post_params['param'][$formData['name']];
                } else {
                    $postDatas[$formData['name']] = $formData['value'];
                }
            }
            ($this->$post_params['method'])($this->absoluteUrlHelper($url, $action), $post_params['param'], $post_params['setting']);
        });
    }
}
