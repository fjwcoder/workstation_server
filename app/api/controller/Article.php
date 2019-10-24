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
 * 文章接口控制器
 */
class Article extends ApiBase
{
    
    /**
     * 文章分类接口
     */
    public function categoryList()
    {
        
        return $this->apiReturn($this->logicArticle->getArticleCategoryList());
    }
    
    /**
     * 文章列表接口
     */
    public function articleList()
    {
        
        return $this->apiReturn($this->logicArticle->getArticleList($this->param));
    }
}
