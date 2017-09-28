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
            $req_params['method']   = strtolower($req_params['method'] ?? 'get');
            $req_params['param']    = $req_params['param'] ?? [];
            $req_params['setting']  = $req_params['setting'] ?? [];
            $post_params['method']  = strtolower($post_params['method'] ?? 'post');
            $post_params['param']   = $post_params['param'] ?? [];
            $post_params['setting'] = $post_params['setting'] ?? [];
            $method                 = $req_params['method'];
            $post_method            = $post_params['method'];
            $form                   = $this->$method($url, $req_params['param'], $req_params['setting'])->find('form');
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
            $html = $this->$post_method($this->absoluteUrlHelper($url, $action), $postDatas, $post_params['setting'])->getHtml();

            return $this->html($html);
        });
    }
}
