<?php
// +---------------------------------------------------------------------+
// | MamiTianshi    | [ CREATE BY WF_RT TEAM ]                           |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Fjwcoder <fjwcoder@gmail.com>                          |
// +---------------------------------------------------------------------+
// | Repository | git@github.com:fjwcoder/mamitianshi_server.git         |
// +---------------------------------------------------------------------+

namespace app\api\controller;

/**
 * 文章聚合接口控制器
 */
class Combination extends ApiBase
{
    
    /**
     * 首页接口
     */
    public function index()
    {
        
        $article_category_list = $this->logicArticle->getArticleCategoryList();
        $article_list          = $this->logicArticle->getArticleList($this->param);
        
        return $this->apiReturn(compact('article_category_list', 'article_list'));
    }
    
    /**
     * 详情接口
     */
    public function details()
    {
        
        $article_category_list = $this->logicArticle->getArticleCategoryList();
        $article_details       = $this->logicArticle->getArticleInfo($this->param);
        
        return $this->apiReturn(compact('article_category_list', 'article_details'));
    }
}
